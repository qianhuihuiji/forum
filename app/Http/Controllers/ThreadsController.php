<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use App\Filters\ThreadsFilters;
use App\Channel;
use App\Thread;
use Illuminate\Http\Request;

class ThreadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index','show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Channel $channel
     * @param ThreadsFilters $filters
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel,ThreadsFilters $filters)
    {
        $threads = $this->getThreads($channel, $filters);

        if(request()->wantsJson()){
            return $threads;
        }

        $trending = array_map('json_decode',Redis::zrevrange('trending_threads',0,4));

        return view('threads.index',compact('threads','trending'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
           'title' => 'required|spamfree',
            'body' => 'required|spamfree',
            'channel_id' => 'required|exists:channels,id'
        ]);

        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body'),
        ]);

        return redirect($thread->path())
            ->with('flash','Your thread has been published!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channel,Thread $thread)
    {
        if(auth()->check()){
            auth()->user()->read($thread);
        }

        Redis::zincrby('trending_threads',1,json_encode([
            'title' => $thread->title,
            'path' => $thread->path()
        ]));

        return view('threads.show',compact('thread'));
    }


    public function destroy($channel,Thread $thread)
    {
        $this->authorize('update',$thread);

        $thread->delete();

        if(request()->wantsJson()){
            return response([],204);
        }

        return redirect('/threads');
    }

    /**
     * @param Channel $channel
     * @param ThreadsFilters $filters
     * @return mixed
     */
    protected function getThreads(Channel $channel, ThreadsFilters $filters)
    {
        $threads = Thread::with('channel')->latest()->filter($filters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        $threads = $threads->paginate(20);

        return $threads;
    }
}
