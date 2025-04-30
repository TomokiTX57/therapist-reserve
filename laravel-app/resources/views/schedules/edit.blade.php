@extends('layouts.app')

@php
$header = 'スケジュール編集';
@endphp

@section('header')
{{ $header }}
@endsection

@section('content')
<div class="container">
    <h2 class="mb-4">出勤スケジュール編集</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('schedules.update', $schedule->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="work_date" class="form-label">出勤日</label>
            <input type="date" name="work_date" id="work_date" class="form-control"
                value="{{ old('work_date', $schedule->work_date) }}" required>
        </div>

        <div class="mb-3">
            <label for="start_time" class="form-label">開始時間</label>
            <div class="d-flex">
                <select name="start_hour" class="form-control me-2" required>
                    @for ($h = 0; $h < 24; $h++)
                        @php
                        $hour=sprintf('%02d', $h);
                        $selectedHour=old('start_hour', \Carbon\Carbon::parse($schedule->start_time)->format('H'));
                        @endphp
                        <option value="{{ $hour }}" {{ $selectedHour == $hour ? 'selected' : '' }}>{{ $hour }}</option>
                        @endfor
                </select>
                <select name="start_minute" class="form-control" required>
                    @foreach ([0, 15, 30, 45] as $m)
                    @php
                    $minute = sprintf('%02d', $m);
                    $selectedMinute = old('start_minute', \Carbon\Carbon::parse($schedule->start_time)->format('i'));
                    @endphp
                    <option value="{{ $minute }}" {{ $selectedMinute == $minute ? 'selected' : '' }}>{{ $minute }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="end_time" class="form-label">終了時間</label>
            <div class="d-flex">
                <select name="end_hour" class="form-control me-2" required>
                    @for ($h = 0; $h < 24; $h++)
                        @php
                        $hour=sprintf('%02d', $h);
                        $selectedHour=old('end_hour', \Carbon\Carbon::parse($schedule->end_time)->format('H'));
                        @endphp
                        <option value="{{ $hour }}" {{ $selectedHour == $hour ? 'selected' : '' }}>{{ $hour }}</option>
                        @endfor
                </select>
                <select name="end_minute" class="form-control" required>
                    @foreach ([0, 15, 30, 45] as $m)
                    @php
                    $minute = sprintf('%02d', $m);
                    $selectedMinute = old('end_minute', \Carbon\Carbon::parse($schedule->end_time)->format('i'));
                    @endphp
                    <option value="{{ $minute }}" {{ $selectedMinute == $minute ? 'selected' : '' }}>{{ $minute }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="is_public" id="is_public" class="form-check-input"
                {{ old('is_public', $schedule->is_public) ? 'checked' : '' }} value="1">
            <label for="is_public" class="form-check-label">公開する</label>
        </div>

        <button type="submit" class="btn btn-primary">更新する</button>
        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary ms-2">戻る</a>
    </form>
</div>
@endsection