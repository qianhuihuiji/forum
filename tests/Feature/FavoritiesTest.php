<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FavoritiesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function au_authenticated_user_can_favorite_any_reply()
    {
        $reply = create('App\Reply');

        // If I post a "favorite" endpoint
        $this->post('replies/' . $reply->id . '/favorites');

        // It Should be recored in the database
        $this->assertCount(1,$reply->favorites);
    }
}
