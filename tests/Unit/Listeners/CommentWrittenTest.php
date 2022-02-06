<?php

namespace Tests\Unit\Models;

use App\Events\BadgeUnlocked;
use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Listeners\CommentWrittenListener;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentWrittenTest extends TestCase
{
    use RefreshDatabase;

    function testListenCommentWrittenEvent()
    {
        Event::fake();

        Event::assertListening(
            CommentWritten::class,
            CommentWrittenListener::class,
        );
    }

    function testDispatchAchievementUnlockedEvent()
    {
        Event::fake();

        $user = User::factory()->create();
        $comment = Comment::factory()->for($user)->create();
        $user->refresh();
        $listner = new CommentWrittenListener();
        $listner->handle(new CommentWritten($comment));

        Event::assertDispatched(function (AchievementUnlocked $event) use ($user) {
            return $event->achievement_name === $user->unlocked_written_achievement_name && $event->user->id === $user->id;
        });
    }

    function testNotDispatchAchievementUnlockedEvent()
    {
        Event::fake();

        $user = User::factory()->create();
        $comments = Comment::factory()->count(2)->for($user)->create();
        $user->refresh();
        $listner = new CommentWrittenListener();
        $listner->handle(new CommentWritten($comments->last()));

        Event::assertNotDispatched(AchievementUnlocked::class);
    }

    function testBadgeUnlockedEvent()
    {
        Event::fake();

        $user = User::factory()->create();
        $comments = Comment::factory()->count(10)->for($user)->create();
        $user->refresh();
        $listner = new CommentWrittenListener();
        $listner->handle(new CommentWritten($comments->last()));

        Event::assertDispatched(function (BadgeUnlocked $event) use ($user) {
            return $event->badge_name === $user->current_badge_name && $event->user->id === $user->id;
        });
    }

    function testNotBadgeUnlockedEvent()
    {
        Event::fake();

        $user = User::factory()->create();
        $comment = Comment::factory()->for($user)->create();
        $user->refresh();
        $listner = new CommentWrittenListener();
        $listner->handle(new CommentWritten($comment));

        Event::assertNotDispatched(BadgeUnlocked::class);
    }
}
