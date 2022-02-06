<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Events\LessonWatched;

class LessonWatchedListener
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
     * @param  LessonWatched  $event
     * @return void
     */
    public function handle(LessonWatched $event)
    {
        $user = $event->user;

        if ($user->is_unlocked_watched_achievement) {
            AchievementUnlocked::dispatch(
                $user->unlocked_watched_achievement_name,
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
