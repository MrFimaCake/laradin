<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserLoginRequest;

use App\UserCoordinates;
use App\User;

class AuthController extends Controller
{
    const OAUTH_TABLE = 'oauth_clients';
    
    public function register(UserRegisterRequest $request)
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
            'status' => 400
        ], 400);
    }

    private function getOAuthAppId()
    {
        return env('OAUTH_CLIENT_APP_ID');
    }
    
    public function login(UserLoginRequest $request)
    {
        $user = User::whereEmail(request('username'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return $this->errorLoginResponse();
        }
        
        $clientRecord = DB::table(self::OAUTH_TABLE)
            ->where('id', $this->getOAuthAppId())
            ->first();
        
        if (!$clientRecord) {
            throw new \Illuminate\Validation\UnauthorizedException("Unable to get app");
        }

        $innerRequest = Request::create('/oauth/token', 'POST', [
            'grant_type' => 'password',
            'client_id' => $clientRecord->id,
            'client_secret' => $clientRecord->secret,
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        ]);
        
        $response = app()->handle($innerRequest);
        
        if ($response->getStatusCode() != 200) {
            return $this->errorLoginResponse();
        }
        
        $responseData = json_decode($response->getContent());
        
        return response()->json([
            'token'  => $responseData->access_token,
            'user'   => $user,
            'shapes' => ['count' => UserCoordinates::where('user_id', $user->id)->count()],
            'status' => 200
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
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $token->id)
            ->update([
                'revoked' => true
            ]);
        $token->revoke();
        return response()->json(['status' => 200]);
    }

}
