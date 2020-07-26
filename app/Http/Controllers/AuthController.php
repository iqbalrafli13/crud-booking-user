<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\User;
class AuthController extends Controller
{

    public function signup(Request $request)
    {
        $this->validate($request,[
            "email"=>"required|unique:users",
            "nohp"=>"required|unique:users",
            "password"=>"required",
            "name"=>"required",
        ]);

        $user = User::create([
            "name"=>$request->json("name"),
            "email"=>$request->json("email"),
            "nohp"=>$request->json("nohp"),
            "password"=>bcrypt($request->json("password"))
        ]);

        return [
            "name"=>$request->json("name"),
            "email"=>$request->json("email"),
            "nohp"=>$request->json("nohp"),
        ];
    }
    public function signin(Request $request)
    {
        $this->validate($request,[
            "nohp"=>"required",
            "password"=>"required"
        ]);
        // grab credentials from the request
        $credentials = $request->only('nohp', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = $this->guard()->attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json([
            'user_id' => $request->user()->id,
            'token'   => $token
        ]);
    }

    public function me()
    {
        return response()->json($this->guard()->user());
    }

    public function guard()
    {
        return Auth::guard();
    }


}
