<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use mysql_xdevapi\Exception;

class AuthController extends Controller
{
    public function login(Request $request){
        try{
            $http = new \GuzzleHttp\Client();

            $response = $http->post(\config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => \config('services.passport.client_id'),
                    'client_secret' =>  \config('services.passport.client_secret'),
                    'username' => $request->username,
                    'password' => $request->password,
                    'scope' => '',
                ],
            ]);

            return json_decode((string) $response->getBody(), true);
        }catch (\Guzzlehttp\Exception\BadResponseException $e){

            return response()->json('invalid username or password', $e->getCode());

   }


    }

    public function register(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }

    public function logout(){
        auth()->user()->tokens->each(function ($token, $key){
            $token->delete();
        });
        return response('logged out successfully',200);
    }

}
