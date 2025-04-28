@extends('layouts.app')

@php
$header = '出勤スケジュール一覧（カレンダー表示）';

$events = $schedules->map(function($schedule) {
return [
'title' => '出勤',
'start' => $schedule->work_date . 'T' . $schedule->start_time,
'end' => $schedule->work_date . 'T' . $schedule->end_time,
'color' => $schedule->is_public ? '#3788d8' : '#6c757d', // 色もここで付与
];
});
@endphp

@section('header')
{{ $header }}
@endsection

@section('content')
<div class="container">
    <h2 class="mb-4">カレンダー表示</h2>

    <div id="calendar"></div>

    <!-- モーダル -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">スケジュール詳細</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
                </div>
                <div class="modal-body">
                    <p><strong>開始:</strong> <span id="modalStart"></span></p>
                    <p><strong>終了:</strong> <span id="modalEnd"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendarのCSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

<!-- FullCalendar本体 -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
    window.scheduleEvents = @json($events);
</script>
<script src="{{ asset('js/calendar.js') }}"></script>
@endsection