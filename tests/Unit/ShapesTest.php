<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;
use App\UserCoordinates;

class ShapesTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserRelation()
    {
        $user = factory(User::class)->create();
        $userShapes = factory(UserCoordinates::class)->create(['user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $userShapes->user);
    }
    
    public function testDecodesCoordinatesOnRetrievingAttribute()
    {
        $user = factory(User::class)->create();
        $userShapes = factory(UserCoordinates::class)->create(['user_id' => $user->id]);
        
        $userShapesAttributes = $userShapes->getAttributes();
        
        $this->assertInternalType('string',$userShapesAttributes['coordinates']);
        $this->assertInternalType('array',$userShapes->coordinates);
        
    }
}
