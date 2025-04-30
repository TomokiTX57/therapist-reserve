<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->scopes([
            'https://www.googleapis.com/auth/calendar.events',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
        ])->with([
            'prompt' => 'consent',
            'access_type' => 'offline',
        ])->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // ログイン中ユーザーを取得
        $user = Auth::user();

        if (!$user) {
            // （もし何らかの理由で未ログインだったらエラー返す）
            return redirect()->route('login')->withErrors(['auth' => 'ログインしてください。']);
        }

        // ログイン中のユーザーにGoogle情報を紐づける
        $user->update([
            'google_id' => $googleUser->getId(),
            'google_token' => json_encode([
                'access_token' => $googleUser->token,
                'refresh_token' => $googleUser->refreshToken,
                'expires_in' => $googleUser->expiresIn,
            ]),
        ]);

        return redirect()->route('schedules.index')->with('success', 'Google連携が完了しました！');
    }
}
