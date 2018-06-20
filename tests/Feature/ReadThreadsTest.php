<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->thread = create('App\Thread');
    }

    /** @test */
    public function a_user_can_view_all_threads()
    {
        $this->get('/threads')
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_read_single_thread()
    {
        $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_filter_threads_according_to_a_channel()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread',['channel_id' => $channel->id]);
        $threadNotInChanne = create('App\Thread');

        $this->get('/threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChanne->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create('App\User',['name' => 'NoNo1']));

        $threadByNoNo1 = create('App\Thread',['user_id' => auth()->id()]);
        $threadNotByNoNo1 = create('App\Thread');

        $this->get('threads?by=NoNo1')
            ->assertSee($threadByNoNo1->title)
            ->assertDontSee($threadNotByNoNo1->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_popularity()
    {
        // Given we have three threads
        // With 2 replies,3 replies,0 replies, respectively
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply',['thread_id'=>$threadWithTwoReplies->id],2);

        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply',['thread_id'=>$threadWithThreeReplies->id],3);

        $threadWithNoReplies = $this->thread;

        // When I filter all threads by popularity
        $response = $this->getJson('threads?popularity=1')->json();

        // Then they should be returned from most replies to least.
        $this->assertEquals([3,2,0],array_column($response['data'],'replies_count'));
    }

    /** @test */
    public function a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $thread = create('App\Thread');
        create('App\Reply',['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1,$response['data']);
    }

    /** @test */
    public function a_user_can_request_all_replies_for_a_given_thread()
    {
        $thread = create('App\Thread');
        create('App\Reply',['thread_id' => $thread->id],40);

        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(20,$response['data']);
        $this->assertEquals(40,$response['total']);
    }
}
