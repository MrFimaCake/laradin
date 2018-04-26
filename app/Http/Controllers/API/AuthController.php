<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    const OAUTH_CLIENT_APP_NAME = 'Laravel Password Grant Client';
    const OAUTH_TABLE = 'oauth_clients';
    
    public function register(Request $request)
    {
        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);

        return response()->json(['status' => 201, 'user' => $user]);
    }
    
    private function errorLoginResponse()
    {
        return response()->json([
            'message' => 'Wrong email or password',
            'status' => 422
        ], 422);
    }
    
    public function login(Request $request)
    {
        $user = User::whereEmail(request('username'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return $this->errorLoginResponse();
        }
        
        $clientRecord = DB::table(self::OAUTH_TABLE)
            ->where('name', self::OAUTH_CLIENT_APP_NAME)
            ->first();
        
        $data = [
            'grant_type' => 'password',
            'client_id' => $clientRecord->id,
            'client_secret' => $clientRecord->secret,
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        ];

        $innerRequest = Request::create('/oauth/token', 'POST', $data);
        $response = app()->handle($innerRequest);
        if ($response->getStatusCode() != 200) {
            return $this->errorLoginResponse();
        }
        $responseData = json_decode($response->getContent());
        
        return response()->json([
            'token' => $responseData->access_token,
            'user'  => $user,
            'status'=> 200
        ]);
    }
    
    public function getUser()
    {
        return auth()->user();
    }
    
    public function logout()
    {
        /** @var \Laravel\Passport\Token $token */
        $token = auth()->user()->token();
        $refreshToken = DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $token->id)
            ->update([
                'revoked' => true
            ]);
        $token->revoke();
        return response()->json(['status' => 200]);
    }

}
