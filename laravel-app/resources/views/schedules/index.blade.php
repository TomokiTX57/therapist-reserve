@extends('layouts.app')

@php
$header = '出勤スケジュール登録';
@endphp

@section('header')
{{ $header }}
@endsection

@section('content')
<div class="container">
    <h2 class="mb-4">出勤スケジュール登録</h2>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('schedules.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="work_date" class="form-label">出勤日</label>
            <input type="date" name="work_date" id="work_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="start_time" class="form-label">開始時間</label>
            <div class="d-flex">
                <select name="start_hour" class="form-control me-2" required>
                    @for ($h = 0; $h < 24; $h++)
                        @php $hour=sprintf('%02d', $h); @endphp
                        <option value="{{ $hour }}" {{ old('start_hour') == $hour ? 'selected' : '' }}>{{ $hour }}</option>
                        @endfor
                </select>
                <select name="start_minute" class="form-control" required>
                    @foreach ([0, 15, 30, 45] as $m)
                    @php $minute = sprintf('%02d', $m); @endphp
                    <option value="{{ $minute }}" {{ old('start_minute') == $minute ? 'selected' : '' }}>{{ $minute }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="end_time" class="form-label">終了時間</label>
            <div class="d-flex">
                <select name="end_hour" class="form-control me-2" required>
                    @for ($h = 0; $h < 24; $h++)
                        @php $hour=sprintf('%02d', $h); @endphp
                        <option value="{{ $hour }}" {{ old('end_hour') == $hour ? 'selected' : '' }}>{{ $hour }}</option>
                        @endfor
                </select>
                <select name="end_minute" class="form-control" required>
                    @foreach ([0, 15, 30, 45] as $m)
                    @php $minute = sprintf('%02d', $m); @endphp
                    <option value="{{ $minute }}" {{ old('end_minute') == $minute ? 'selected' : '' }}>{{ $minute }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">登録</button>
    </form>

    <hr>

    <h3 class="mt-5">登録済みスケジュール</h3>
    <ul class="list-group mt-3">
        @foreach($schedules as $schedule)
        <li class="list-group-item">
            {{ $schedule->work_date }}（{{ $schedule->start_time }}〜{{ $schedule->end_time }}）
            <div class="d-flex gap-2 mt-2">
                <a href="{{ route('schedules.edit', $schedule->id) }}" class="btn btn-outline-primary btn-sm me-2">編集</a>
                <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('本当に削除しますか？')">削除</button>
                </form>
            </div>
        </li>
        @endforeach
    </ul>
</div>
@endsection

@section('footer')
@endsection