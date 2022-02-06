<?php

namespace Tests\Unit\Models;

use App\Events\BadgeUnlocked;
use App\Events\AchievementUnlocked;
use App\Events\LessonWatched;
use App\Listeners\LessonWatchedListener;
use App\Models\User;
use App\Models\Lesson;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonWatchedTest extends TestCase
{
    use RefreshDatabase;

    function testListenLessonWatchedEvent()
    {
        Event::fake();

        Event::assertListening(
            LessonWatched::class,
            LessonWatchedListener::class,
        );
    }

    function testDispatchAchievementUnlockedEvent()
    {
        Event::fake();

        $user = User::factory()->create();
        $lesson = Lesson::factory()->hasAttached($user, ['watched' => 1])->create();
        $user->refresh();
        $listner = new LessonWatchedListener();
        $listner->handle(new LessonWatched($lesson, $user));
        Event::assertDispatched(function (AchievementUnlocked $event) use ($user) {
            return $event->achievement_name === $user->unlocked_watched_achievement_name && $event->user->id === $user->id;
        });
    }

    function testNotDispatchAchievementUnlockedEvent()
    {
        Event::fake();

        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(2)->hasAttached($user, ['watched' => 1])->create();
        $user->refresh();
        $listner = new LessonWatchedListener();
        $listner->handle(new LessonWatched($lessons->last(), $user));

        Event::assertNotDispatched(AchievementUnlocked::class);
    }

    function testBadgeUnlockedEvent()
    {
        Event::fake();

        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(25)->hasAttached($user, ['watched' => 1])->create();
        $user->refresh();
        $listner = new LessonWatchedListener();
        $listner->handle(new LessonWatched($lessons->last(), $user));

        Event::assertDispatched(function (BadgeUnlocked $event) use ($user) {
            return $event->badge_name === $user->current_badge_name && $event->user->id === $user->id;
        });
    }

    function testNotBadgeUnlockedEvent()
    {
        Event::fake();

        $user = User::factory()->create();
        $lesson = Lesson::factory()->hasAttached($user, ['watched' => 1])->create();
        $user->refresh();
        $listner = new LessonWatchedListener();
        $listner->handle(new LessonWatched($lesson, $user));
        Event::assertNotDispatched(BadgeUnlocked::class);
    }
}
