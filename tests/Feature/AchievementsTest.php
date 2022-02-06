<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementsTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAchievementsOfBeginner()
    {
        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}/achievements");

        $response
            ->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => [],
                'next_available_achievements' => [
                    'First Lesson Watched',
                    'First Comment Written',
                ],
                'current_badge' => 'Beginner',
                'next_badge' => 'Intermediate',
                'remaing_to_unlock_next_badge' => 4
            ]);
    }

    public function testGetAchievementsOfIntermediate()
    {
        $user = User::factory()->create();
        Lesson::factory()
            ->hasAttached($user, ['watched' => 1])
            ->create();
        Comment::factory()
            ->count(5)
            ->for($user)
            ->create();
        $response = $this->get("/users/{$user->id}/achievements");

        $response
            ->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => [
                    'First Lesson Watched',
                    '5 Comments Written',
                ],
                'next_available_achievements' => [
                    '5 Lessons Watched',
                    '10 Comments Written',
                ],
                'current_badge' => 'Intermediate',
                'next_badge' => 'Advanced',
                'remaing_to_unlock_next_badge' => 4
            ]);
    }

    public function testGetAchievementsOfAdvanced()
    {
        $user = User::factory()->create();
        Lesson::factory()
            ->count(25)
            ->hasAttached($user, ['watched' => 1])
            ->create();
        Comment::factory()
            ->count(10)
            ->for($user)
            ->create();
        $response = $this->get("/users/{$user->id}/achievements");

        $response
            ->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => [
                    '25 Lessons Watched',
                    '10 Comments Written',
                ],
                'next_available_achievements' => [
                    '50 Lessons Watched',
                    '20 Comments Written',
                ],
                'current_badge' => 'Advanced',
                'next_badge' => 'Master',
                'remaing_to_unlock_next_badge' => 2
            ]);
    }

    public function testGetAchievementsOfMaster()
    {
        $user = User::factory()->create();
        Lesson::factory()
            ->count(50)
            ->hasAttached($user, ['watched' => 1])
            ->create();
        Comment::factory()
            ->count(25)
            ->for($user)
            ->create();
        $response = $this->get("/users/{$user->id}/achievements");

        $response
            ->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => [
                    '50 Lessons Watched',
                    '20 Comments Written',
                ],
                'next_available_achievements' => [],
                'current_badge' => 'Master',
                'next_badge' => null,
                'remaing_to_unlock_next_badge' => 0
            ]);
    }
}
