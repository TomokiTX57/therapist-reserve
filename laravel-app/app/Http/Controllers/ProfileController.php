<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function editProfile()
    {
        $user = auth()->user();
        return view('profile', compact('user'));
    }

    public function edit()
    {
        return $this->editProfile();
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'google_event_summary' => 'nullable|string|max:255',
            // 他のバリデーション項目があればここに追加
        ]);

        $user->update([
            'google_event_summary' => $request->google_event_summary,
        ]);

        return redirect()->route('profile.edit')->with('success', 'プロフィールを更新しました。');
    }
}
