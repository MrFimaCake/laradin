<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * @return void
     */
    public function testShapesHasRelation()
    {
        $user = factory(User::class)->create();
        $this->assertInstanceOf(Collection::class, $user->shapes);
    }
}
