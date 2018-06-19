<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;
use App\Notifications\YouWereMentioned;
use App\User;

class NotifyMentionedUsers
{
    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply  $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        // Inspect the body of the reply for the username mentions
        preg_match_all('/\@([^\s\.]+)/',$event->reply->body,$matches);

        // And then notify user
        foreach ($matches[1] as $name){
            $user = User::whereName($name)->first();

            if($user){
                $user->notify(new YouWereMentioned($event->reply));
            }
        }
    }
}
