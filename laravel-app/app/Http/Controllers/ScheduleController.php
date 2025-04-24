<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Schedule;


class ScheduleController extends Controller
{

    public function index()
    {
        $schedules = auth()->user()->schedules()->orderBy('work_date')->get();
        return view('schedules.index', compact('schedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'work_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        auth()->user()->schedules()->create($validated);

        return redirect()->route('schedules.index')->with('success', 'スケジュールを登録しました。');
    }
}
