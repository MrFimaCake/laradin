<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserGetsToken()
    {
    	$user = factory(User::class)->create();
        $userPassword = uniqid();
        $user->password = Hash::make($userPassword);
        $user->save();
        
        $response = $this->json('POST', '/api/login', [
            'username' => $user->email,
            'password' => $userPassword,
        ]);
        
        $userToken = DB::table('oauth_access_tokens')
            ->where('user_id', $user->id)
            ->first();
        
        $response->assertJsonStructure(['status']);
        $response->assertJson(['status' => '200']);
    }
    
    public function testValidatesUsernameAsEmail()
    {
        $user = factory(User::class)->create();
        $usernameNotEmail = uniqid();
        $userPassword = uniqid();
        $user->email = $usernameNotEmail;
        $user->password = Hash::make($userPassword);
        $user->save();
        
        $response = $this->json('POST', '/api/login', [
            'username' => $usernameNotEmail,
            'password' => $userPassword,
        ]);
        
        $response->assertJsonValidationErrors(['username']);
    }
}
