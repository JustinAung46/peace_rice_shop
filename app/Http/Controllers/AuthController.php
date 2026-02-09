<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function checkAccount(Request $request)
    {
        $request->validate([
            'account_id' => ['required', 'integer'],
        ]);

        $user = User::where('account_id', $request->account_id)->first();

        if ($user) {
            return response()->json([
                'exists' => true,
                'name' => $user->name,
            ]);
        }

        return response()->json([
            'exists' => false,
            'message' => 'Account ID not found.',
        ], 404);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'account_id' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['account_id' => $request->account_id, 'password' => $request->password])) {
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'account_id' => 'The provided credentials do not match our records.',
        ])->onlyInput('account_id');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
