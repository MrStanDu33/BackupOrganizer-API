<?php

namespace App\Http\Controllers\api\v1;

use App\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request) {
        $login = Validator::make($request->all(),[
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // check if parameters are corrects
        if ($validation->fails()) return response(['message' => $validation->messages()], config('httpcodes.UNPROCESSABLE_ENTITY'));

        // check if user can log in
        if (!Auth::attempt($login, true)) return response(['message' => 'Invalid login credentials.'], config('httpcodes.UNAUTHORIZED'));

        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        return response(['user' => Auth::user(), 'accessToken' => $accessToken], config('httpcodes.OK'));
    }

    public function register(Request $request) {
        // check if regristation enabled
        if (!env('ALLOW_REGISTRATION')) return response(['message' => 'Register has been manually desactivated.'], config('httpcodes.FORBIDDEN'));

        $validation = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        // check if parameters are corrects
        if ($validation->fails()) return response(['message' => $validation->messages()], config('httpcodes.UNPROCESSABLE_ENTITY'));

        // check if user already exists
        if (User::where('email', '=', $request->email)->exists()) return response(['message' => 'A user already exist with given email address.'], config('httpcodes.CONFLICT'));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response(['user' => $user, 'accessToken' => $accessToken], config('httpcodes.CREATED'));
    }
}
