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
    </div>
</div>
@endsection