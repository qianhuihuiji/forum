<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateThreadsTest extends TestCase
{
    /** @test */
    public function an_authenticated_user_can_create_new_forum_threads()
    {
        // Given we have a signed in user
        $this->actingAs(factory('App\User')->create());  // 已登录用户

        // When we hit the endpoint to cteate a new thread
        $thread = factory('App\Thread')->raw();
        // Then,when we visit the thread
        // We should see the new thread
    }
}
