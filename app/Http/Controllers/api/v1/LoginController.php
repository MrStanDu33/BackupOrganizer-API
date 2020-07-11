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

        if (!Auth::attempt($login))
            return response(['message' => 'invalid login credentials.'], config('httpcodes.UNAUTHORIZED'));

        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        return response(['user' => Auth::user(), 'accessToken' => $accessToken], config('httpcodes.OK'));
    }

    public function register(Request $request) {
        return response(['message' => 'Register has been manually desactivated.'], config('httpcodes.FORBIDDEN'));
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        return response()->json($user);
    }
}
