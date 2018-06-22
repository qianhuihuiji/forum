<?php


namespace App;

use Illuminate\Support\Facades\Redis;

trait RecordsVisits
{

    public function recordVisit()
    {
        Redis::incr($this->visitsCacheKey());

        return $this;
    }

    public function visits()
    {
        return Redis::get($this->visitsCacheKey());
    }

    public function resetVisits()
    {
        Redis::del($this->visitsCacheKey());

        return $this;
    }

    public function visitsCacheKey()
    {
        return "threads.{$this->id}.visits";
    }
}