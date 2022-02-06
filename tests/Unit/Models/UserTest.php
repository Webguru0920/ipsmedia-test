<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Lesson;
use App\Models\Comment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    function testUnlockedWatchedLessonsAttribute()
    {
        $user = User::factory()->create();
        $this->assertNull($user->unlocked_watched_lessons);
        $achievements = User::LESSONS_WATCHED_ACHIEVEMENTS;
        Lesson::factory()
            ->count(rand(1, end($achievements)))
            ->hasAttached($user, ['watched' => 1])
            ->create();
        $user->refresh();
        $this->assertContains($user->unlocked_watched_lessons, $achievements);
    }

    function testUnlockedWrittenCommentsAttribute()
    {
        $user = User::factory()->create();
        $this->assertNull($user->unlocked_written_comments);
        $achievements = User::COMMENTS_WRITTEN_ACHIEVEMENTS;
        Comment::factory()
            ->count(rand(1, end($achievements)))
            ->for($user)
            ->create();
        $user->refresh();
        $this->assertContains($user->unlocked_written_comments, $achievements);
    }

    function testAvailableWatchedLessonsAttribute()
    {
        $user = User::factory()->create();
        $achievements = User::LESSONS_WATCHED_ACHIEVEMENTS;
        $this->assertSame($user->available_watched_lessons, $achievements[0]);
        Lesson::factory()
            ->count(end($achievements) + 1)
            ->hasAttached($user, ['watched' => 1])
            ->create();
        $user->refresh();
        $this->assertNull($user->available_watched_lessons);
    }

    function testAvailableWrittenCommentsAttribute()
    {
        $user = User::factory()->create();
        $achievements = User::COMMENTS_WRITTEN_ACHIEVEMENTS;
        $this->assertSame($user->available_written_comments, $achievements[0]);
        Comment::factory()
            ->count(end($achievements) + 1)
            ->for($user)
            ->create();
        $user->refresh();
        $this->assertNull($user->available_written_comments);
    }

    function testUnlockedWatchedAchievementAttribute()
    {
        $user = User::factory()->create();
        $this->assertNull($user->unlocked_watched_achievement);
        $achievements = User::LESSONS_WATCHED_ACHIEVEMENTS;
        Lesson::factory()
            ->count(rand(1, end($achievements)))
            ->hasAttached($user, ['watched' => 1])
            ->create();
        $user->refresh();
        $this->assertNotNull($user->unlocked_watched_achievement);
    }

    function testUnlockedWrittenAchievementAttribute()
    {
        $user = User::factory()->create();
        $this->assertNull($user->unlocked_written_achievement);
        $achievements = User::COMMENTS_WRITTEN_ACHIEVEMENTS;
        Comment::factory()
            ->count(rand(1, end($achievements)))
            ->for($user)
            ->create();
        $user->refresh();
        $this->assertNotNull($user->unlocked_written_achievement);
    }

    function testIsUnlockedWatchedAchievementAttribute()
    {
        $achievements = User::LESSONS_WATCHED_ACHIEVEMENTS;
        foreach ($achievements as $achievement) {
            $user = User::factory()->create();
            $this->assertFalse($user->is_unlocked_watched_achievement);
            Lesson::factory()
                ->count($achievement)
                ->hasAttached($user, ['watched' => 1])
                ->create();
            $user->refresh();
            $this->assertTrue($user->is_unlocked_watched_achievement);
            Lesson::factory()
                ->hasAttached($user, ['watched' => 1])
                ->create();
            $user->refresh();
            $this->assertFalse($user->is_unlocked_watched_achievement);
        }
    }

    function testIsUnlockedWrittenAchievementAttribute()
    {
        $achievements = User::COMMENTS_WRITTEN_ACHIEVEMENTS;
        foreach ($achievements as $achievement) {
            $user = User::factory()->create();
            $this->assertFalse($user->is_unlocked_written_achievement);
            Comment::factory()
                ->count($achievement)
                ->for($user)
                ->create();
            $user->refresh();
            $this->assertTrue($user->is_unlocked_written_achievement);
            Comment::factory()
                ->for($user)
                ->create();
            $user->refresh();
            $this->assertFalse($user->is_unlocked_written_achievement);
        }
    }

    function testIsUnlockedBadgeAttribute()
    {
        $user = User::factory()->create();
        $this->assertFalse($user->is_unlocked_badge);
        Lesson::factory()
            ->hasAttached($user, ['watched' => 1])
            ->create();
        Comment::factory()
            ->count(5)
            ->for($user)
            ->create();
        $user->refresh();
        $this->assertTrue($user->is_unlocked_badge);
    }

    function testCurrentBadgeAttribute()
    {
        $user = User::factory()->create();
        $badges = User::BADGES;
        $watchedAchievements = User::LESSONS_WATCHED_ACHIEVEMENTS;
        $writtenAchievements = User::COMMENTS_WRITTEN_ACHIEVEMENTS;
        $this->assertSame($user->current_badge, $badges[0]);
        Lesson::factory()
            ->count(rand(1, end($watchedAchievements)))
            ->hasAttached($user, ['watched' => 1])
            ->create();
        Comment::factory()
            ->count(rand(1, end($writtenAchievements)))
            ->for($user)
            ->create();
        $user->refresh();
        $this->assertContains($user->current_badge, $badges);
    }

    function testNextBadgeAttribute()
    {
        $user = User::factory()->create();
        $badges = User::BADGES;
        $watchedAchievements = User::LESSONS_WATCHED_ACHIEVEMENTS;
        $writtenAchievements = User::COMMENTS_WRITTEN_ACHIEVEMENTS;
        $this->assertSame($user->next_badge, $badges[1]);
        Lesson::factory()
            ->count(end($watchedAchievements))
            ->hasAttached($user, ['watched' => 1])
            ->create();
        Comment::factory()
            ->count(end($writtenAchievements))
            ->for($user)
            ->create();
        $user->refresh();
        $this->assertNull($user->next_badge);
    }

    function testRemainingAchievementsAttribute()
    {
        $user = User::factory()->create();
        $badges = User::BADGES;
        $watchedAchievements = User::LESSONS_WATCHED_ACHIEVEMENTS;
        $writtenAchievements = User::COMMENTS_WRITTEN_ACHIEVEMENTS;
        $this->assertSame($user->remaining_achievements, $badges[1] - $badges[0]);
        Lesson::factory()
            ->count(end($watchedAchievements))
            ->hasAttached($user, ['watched' => 1])
            ->create();
        Comment::factory()
            ->count(end($writtenAchievements))
            ->for($user)
            ->create();
        $user->refresh();
        $this->assertSame($user->remaining_achievements, 0);
    }
}
