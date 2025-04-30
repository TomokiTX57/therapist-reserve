@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">プロフィール設定</h2>

    {{-- プロフィール編集フォーム（名前、画像、紹介文など） --}}
    {{-- 今後拡張予定 --}}

    <hr>

    {{-- Googleカレンダー連携セクション --}}
    <div class="mb-4">
        <h4>Googleカレンダー連携</h4>

        @if(Auth::user()->google_token)
        <p class="text-success">✅ Googleカレンダーと連携済み</p>
        @else
        <a href="{{ route('login.google') }}" class="btn btn-outline-danger">
            Googleカレンダーと連携する
        </a>
        @endif
        @if(Auth::user()->google_token)
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="google_event_summary" class="form-label">Googleカレンダーの予定タイトル</label>
                <input type="text" name="google_event_summary" id="google_event_summary"
                    class="form-control" value="{{ old('google_event_summary', Auth::user()->google_event_summary) }}">
                <small class="form-text text-muted">例：〇〇セラピスト出勤 など</small>
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
        </form>
        @endif
    </div>
</div>
@endsection