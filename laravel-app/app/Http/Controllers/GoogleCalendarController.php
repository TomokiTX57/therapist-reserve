<?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Calendar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Exception;

class GoogleCalendarController extends Controller
{
    public function createEvent($schedule)
    {
        $user = Auth::user();

        if (!$user || !$user->google_token) {
            throw new Exception('Googleアカウントが連携されていません。');
        }

        $client = new Client();
        $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
        $client->addScope(Calendar::CALENDAR_EVENTS);
        $client->setAccessType('offline');

        $token = json_decode($user->google_token, true);
        if (!isset($token['access_token'])) {
            throw new Exception('アクセストークンが無効です。');
        }

        $client->setAccessToken($token);

        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                $user->google_token = json_encode($client->getAccessToken());
                $user->save();
            } else {
                throw new Exception('Googleトークンの更新に失敗しました。再認証が必要です。');
            }
        }

        $service = new Calendar($client);

        $event = new Calendar\Event([
            'summary' => '出勤スケジュール',
        ]);

        $event->setStart(new Calendar\EventDateTime([
            'dateTime' => Carbon::parse("{$schedule->work_date} {$schedule->start_time}")->toRfc3339String(),
            'timeZone' => 'Asia/Tokyo',
        ]));

        $event->setEnd(new Calendar\EventDateTime([
            'dateTime' => Carbon::parse("{$schedule->work_date} {$schedule->end_time}")->toRfc3339String(),
            'timeZone' => 'Asia/Tokyo',
        ]));

        $calendarId = 'primary';
        $createdEvent = $service->events->insert($calendarId, $event);

        // ここで eventId を保存
        $schedule->google_event_id = $createdEvent->id;
        $schedule->save();

        return true;
    }

    public function updateEvent($schedule)
    {
        $user = Auth::user();
        if (!$user || !$user->google_token || !$schedule->google_event_id) {
            return;
        }

        $client = new Client();
        $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
        $client->addScope(Calendar::CALENDAR_EVENTS);
        $client->setAccessType('offline');
        $client->setAccessToken(json_decode($user->google_token, true));

        if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $user->google_token = json_encode($client->getAccessToken());
            $user->save();
        }

        $service = new Calendar($client);

        $event = $service->events->get('primary', $schedule->google_event_id);
        $event->setSummary('出勤スケジュール');
        $event->setStart(new Calendar\EventDateTime([
            'dateTime' => \Carbon\Carbon::parse("{$schedule->work_date} {$schedule->start_time}")->toRfc3339String()
        ]));
        $event->setEnd(new Calendar\EventDateTime([
            'dateTime' => \Carbon\Carbon::parse("{$schedule->work_date} {$schedule->end_time}")->toRfc3339String()
        ]));

        $service->events->update('primary', $event->getId(), $event);
    }

    public function deleteEvent($schedule)
    {
        $user = Auth::user();
        if (!$user || !$user->google_token || !$schedule->google_event_id) {
            return;
        }

        $client = new Client();
        $client->setAuthConfig(storage_path('app/google-calendar/credentials.json'));
        $client->addScope(Calendar::CALENDAR_EVENTS);
        $client->setAccessType('offline');
        $client->setAccessToken(json_decode($user->google_token, true));

        if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $user->google_token = json_encode($client->getAccessToken());
            $user->save();
        }

        $service = new Calendar($client);
        $service->events->delete('primary', $schedule->google_event_id);
    }
}
