<?php

namespace App\Http\Controllers\api\v1;

use App\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request) {
        $login = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($login, true))
            return response(['message' => 'Invalid login credentials.'], config('httpcodes.UNAUTHORIZED'));

        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        return response(['user' => Auth::user(), 'accessToken' => $accessToken], config('httpcodes.OK'));
    }

    public function register(Request $request) {
        if (!env('ALLOW_REGISTRATION')) return response(['message' => 'Register has been manually desactivated.'], config('httpcodes.FORBIDDEN'));
        $login = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if (User::where('email', '=', $login['email'])->exists()) return response(['message' => 'A user already exist with given email address.'], config('httpcodes.CONFLICT'));

        $user = User::create([
            'name' => $login['name'],
            'email' => $login['email'],
            'password' => bcrypt($login['password'])
        ]);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response(['user' => $user, 'accessToken' => $accessToken], config('httpcodes.CREATED'));
    }
}
