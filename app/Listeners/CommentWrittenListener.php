<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Events\CommentWritten;

class CommentWrittenListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CommentWritten  $event
     * @return void
     */
    public function handle(CommentWritten $event)
    {
        $user = $event->comment->user;

        if ($user->is_unlocked_written_achievement) {
            AchievementUnlocked::dispatch(
                $user->unlocked_written_achievement_name,
                $user
            );
        }

        if ($user->is_unlocked_badge) {
            BadgeUnlocked::dispatch(
                $user->current_badge_name,
                $user,
            );
        }
    }
}
