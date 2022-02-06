<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        $unlockedAchievements = [];
        $nextAvailableAchievements = [];

        if ($user->unlocked_watched_achievement_name)
            $unlockedAchievements[] = $user->unlocked_watched_achievement_name;
        if ($user->unlocked_written_achievement_name)
            $unlockedAchievements[] = $user->unlocked_written_achievement_name;
        if ($user->available_watched_achievement_name)
            $nextAvailableAchievements[] = $user->available_watched_achievement_name;
        if ($user->available_written_achievement_name)
            $nextAvailableAchievements[] = $user->available_written_achievement_name;

        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailableAchievements,
            'current_badge' => $user->current_badge_name,
            'next_badge' => $user->next_badge_name,
            'remaing_to_unlock_next_badge' => $user->remaining_achievements,
        ]);
    }
}
