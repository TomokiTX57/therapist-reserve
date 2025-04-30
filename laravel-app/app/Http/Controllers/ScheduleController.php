<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\Response;
use Google\Client;
use Google\Service\Calendar;
use App\Models\User;


class ScheduleController extends Controller
{

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $schedules = $user->schedules()->orderBy('work_date')->get();
        return view('schedules.index', compact('schedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_date' => 'required|date',
            'start_hour' => 'required|numeric',
            'start_minute' => 'required|numeric',
            'end_hour' => 'required|numeric',
            'end_minute' => 'required|numeric',
        ]);

        $start_time = sprintf('%02d:%02d', $validated['start_hour'], $validated['start_minute']);
        $end_time = sprintf('%02d:%02d', $validated['end_hour'], $validated['end_minute']);
        $user = auth()->user();
        $work_date = $validated['work_date'];

        // Combine date and time to check against current datetime
        $startDateTime = strtotime("$work_date $start_time");
        if ($startDateTime < time()) {
            return back()->withErrors(['start_time' => '過去の時間は登録できません。'])->withInput();
        }

        // Check for overlapping schedules
        $overlap = $user->schedules()
            ->where('work_date', $work_date)
            ->where(function ($query) use ($start_time, $end_time) {
                $query->whereBetween('start_time', [$start_time, $end_time])
                    ->orWhereBetween('end_time', [$start_time, $end_time])
                    ->orWhere(function ($query2) use ($start_time, $end_time) {
                        $query2->where('start_time', '<=', $start_time)
                            ->where('end_time', '>=', $end_time);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['start_time' => 'この時間帯はすでに登録されています。'])->withInput();
        }

        $schedule = $user->schedules()->create([
            'work_date' => $work_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'is_public' => $request->has('is_public'),
        ]);

        // Googleカレンダー　API連携
        if ($user->hasGoogleCalendarToken()) {
            app(\App\Http\Controllers\GoogleCalendarController::class)->createEvent($schedule);
        }


        return redirect()->route('schedules.index')->with('success', 'スケジュールを登録しました。');
    }

    public function edit($id)
    {
        $schedule = auth()->user()->schedules()->findOrFail($id);
        $start_hour = (int)substr($schedule->start_time, 0, 2);
        $start_minute = (int)substr($schedule->start_time, 3, 2);
        $end_hour = (int)substr($schedule->end_time, 0, 2);
        $end_minute = (int)substr($schedule->end_time, 3, 2);
        return view('schedules.edit', compact('schedule', 'start_hour', 'start_minute', 'end_hour', 'end_minute'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'work_date' => 'required|date',
            'start_hour' => 'required',
            'start_minute' => 'required',
            'end_hour' => 'required',
            'end_minute' => 'required',
            'is_public' => 'sometimes|boolean'
        ]);

        $start_time = sprintf('%02d:%02d:00', $request->start_hour, $request->start_minute);
        $end_time = sprintf('%02d:%02d:00', $request->end_hour, $request->end_minute);

        // 開始 < 終了のチェック（オプション）
        if (strtotime($start_time) >= strtotime($end_time)) {
            return back()->withErrors(['end_time' => '終了時間は開始時間より後である必要があります。'])->withInput();
        }

        $schedule->update([
            'work_date' => $request->work_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'is_public' => $request->has('is_public'),
        ]);

        if ($user->hasGoogleCalendarToken()) {
            app(GoogleCalendarController::class)->updateEvent($schedule);
        }

        return redirect()->route('schedules.index')->with('success', 'スケジュールを更新しました。');
    }

    public function destroy($id)
    {
        $schedule = auth()->user()->schedules()->findOrFail($id);
        if (auth()->user()->hasGoogleCalendarToken()) {
            app(\App\Http\Controllers\GoogleCalendarController::class)->deleteEvent($schedule);
        }
        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'スケジュールを削除しました。');
    }

    public function export()
    {
        $schedules = auth()->user()->schedules()->orderBy('work_date')->get();

        $content = "BEGIN:VCALENDAR\r\n";
        $content .= "VERSION:2.0\r\n";
        $content .= "PRODID:-//YourCompany//TherapistSchedule//EN\r\n";

        foreach ($schedules as $schedule) {
            $start = \Carbon\Carbon::parse($schedule->work_date . ' ' . $schedule->start_time)->format('Ymd\THis\Z');
            $end = \Carbon\Carbon::parse($schedule->work_date . ' ' . $schedule->end_time)->format('Ymd\THis\Z');

            $content .= "BEGIN:VEVENT\r\n";
            $content .= "UID:" . uniqid() . "@yourdomain.com\r\n";
            $content .= "DTSTAMP:" . now()->format('Ymd\THis\Z') . "\r\n";
            $content .= "DTSTART:" . $start . "\r\n";
            $content .= "DTEND:" . $end . "\r\n";
            $content .= "SUMMARY:出勤予定\r\n";
            $content .= "END:VEVENT\r\n";
        }

        $content .= "END:VCALENDAR\r\n";

        return Response::make($content, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="schedule.ics"',
        ]);
    }

    public function showCalendar()
    {
        $schedules = auth()->user()->schedules()->orderBy('work_date')->get();

        $events = $schedules->map(function ($schedule) {
            return [
                'title' => '出勤',
                'start' => $schedule->work_date . 'T' . $schedule->start_time,
                'end' => $schedule->work_date . 'T' . $schedule->end_time,
                'color' => $schedule->is_public ? '#3788d8' : '#6c757d',
            ];
        });

        return view('schedules.show', compact('schedules', 'events'));
    }
}
