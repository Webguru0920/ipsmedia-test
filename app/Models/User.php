<?php

namespace App\Models;

use App\Models\Comment;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * lessons watched achievements
     * @var array
     */
    const LESSONS_WATCHED_ACHIEVEMENTS = [
        1, 5, 10, 25, 50
    ];

    /**
     * comment written achievements
     * @var array
     */
    const COMMENTS_WRITTEN_ACHIEVEMENTS = [
        1, 3, 5, 10, 20
    ];

    /**
     * determined by the number of achievements they have unlocked.
     * @var array
     */
    const BADGES = [
        0, 4, 8, 10
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The comments that belong to the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched()
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', 1);
    }

    /**
     * get unlocked watched lessons
     *
     * @return int
     */
    public function getUnlockedWatchedLessonsAttribute(): ?int
    {
        return collect(self::LESSONS_WATCHED_ACHIEVEMENTS)->last(function ($value) {
            return $value <= $this->watched->count();
        });
    }

    /**
     * get unlocked written comments
     *
     * @return int
     */
    public function getUnlockedWrittenCommentsAttribute(): ?int
    {
        return collect(self::COMMENTS_WRITTEN_ACHIEVEMENTS)->last(function ($value) {
            return $value <= $this->comments->count();
        });
    }

    /**
     * get available watched lessons
     *
     * @return int
     */
    public function getAvailableWatchedLessonsAttribute(): ?int
    {
        return collect(self::LESSONS_WATCHED_ACHIEVEMENTS)->first(function ($value) {
            return $value > $this->watched->count();
        });
    }

    /**
     * get available written comments
     *
     * @return int
     */
    public function getAvailableWrittenCommentsAttribute(): ?int
    {
        return collect(self::COMMENTS_WRITTEN_ACHIEVEMENTS)->first(function ($value) {
            return $value > $this->comments->count();
        });
    }

    /**
     * get unlocked watched achievement
     *
     * @return int
     */
    public function getUnlockedWatchedAchievementAttribute(): ?int
    {
        if (!$this->unlocked_watched_lessons) return null;
        return collect(self::LESSONS_WATCHED_ACHIEVEMENTS)->search($this->unlocked_watched_lessons) + 1;
    }

    /**
     * get unlocked written achievement
     *
     * @return int
     */
    public function getUnlockedWrittenAchievementAttribute(): ?int
    {
        if (!$this->unlocked_written_comments) return null;
        return collect(self::COMMENTS_WRITTEN_ACHIEVEMENTS)->search($this->unlocked_written_comments) + 1;
    }

    /**
     * showing whether unlocked watched achievement
     * 
     * @return bool
     */
    public function getIsUnlockedWatchedAchievementAttribute(): bool
    {
        return collect(self::LESSONS_WATCHED_ACHIEVEMENTS)->contains($this->watched->count());
    }

    /**
     * showing whether unlocked written achievement
     *
     * @return bool
     */
    public function getIsUnlockedWrittenAchievementAttribute(): bool
    {
        return collect(self::COMMENTS_WRITTEN_ACHIEVEMENTS)->contains($this->comments->count());
    }

    /**
     * showing whether unlocked badge
     *
     * @return bool
     */
    public function getIsUnlockedBadgeAttribute(): bool
    {
        if (!$this->is_unlocked_watched_achievement && !$this->is_unlocked_written_achievement) return false;
        $watchedAchievement = $this->unlocked_watched_achievement ?? 0;
        $writtenAchievement = $this->unlocked_written_achievement ?? 0;
        $achievement = $watchedAchievement + $writtenAchievement;
        return collect(self::BADGES)->contains($achievement);
    }

    /**
     * get current badge
     *
     * @return int
     */
    public function getCurrentBadgeAttribute(): int
    {
        $watchedAchievement = $this->unlocked_watched_achievement ?? 0;
        $writtenAchievement = $this->unlocked_written_achievement ?? 0;
        $achievement = $watchedAchievement + $writtenAchievement;
        return collect(self::BADGES)->last(function ($value) use ($achievement) {
            return $value <= $achievement;
        });
    }

    /**
     * get next badge
     *
     * @return int
     */
    public function getNextBadgeAttribute(): ?int
    {
        $watchedAchievement = $this->unlocked_watched_achievement ?? 0;
        $writtenAchievement = $this->unlocked_written_achievement ?? 0;
        $achievement = $watchedAchievement + $writtenAchievement;
        return collect(self::BADGES)->first(function ($value) use ($achievement) {
            return $value > $achievement;
        });
    }

    /**
     * get remaining achievements for next badge
     *
     * @return int
     */
    public function getRemainingAchievementsAttribute(): int
    {
        $watchedAchievement = $this->unlocked_watched_achievement ?? 0;
        $writtenAchievement = $this->unlocked_written_achievement ?? 0;
        $achievement = $watchedAchievement + $writtenAchievement;
        return $this->next_badge ? $this->next_badge - $achievement : 0;
    }

    /**
     * get unlocked watched achievement name
     *
     * @return string
     */
    public function getUnlockedWatchedAchievementNameAttribute(): ?string
    {
        if (!$this->unlocked_watched_lessons) return null;
        return trans_choice('default.lessons_watched_achievements', $this->unlocked_watched_lessons, [
            'lessons' => $this->unlocked_watched_lessons
        ]);
    }

    /**
     * get available watched achievement name
     *
     * @return string
     */
    public function getAvailableWatchedAchievementNameAttribute(): ?string
    {
        if (!$this->available_watched_lessons) return null;
        return trans_choice('default.lessons_watched_achievements', $this->available_watched_lessons, [
            'lessons' => $this->available_watched_lessons
        ]);
    }

    /**
     * get unlocked written achievement name
     *
     * @return string
     */
    public function getUnlockedWrittenAchievementNameAttribute(): ?string
    {
        if (!$this->unlocked_written_comments) return null;
        return trans_choice('default.comments_written_achievements', $this->unlocked_written_comments, [
            'comments' => $this->unlocked_written_comments
        ]);
    }

    /**
     * get available written achievement name
     *
     * @return string
     */
    public function getAvailableWrittenAchievementNameAttribute(): ?string
    {
        if (!$this->available_written_comments) return null;
        return trans_choice('default.comments_written_achievements', $this->available_written_comments, [
            'comments' => $this->available_written_comments
        ]);
    }

    /**
     * get current badge name
     *
     * @return int
     */
    public function getCurrentBadgeNameAttribute(): string
    {
        return trans('default.badges.' . $this->current_badge);
    }

    /**
     * get next badge name
     *
     * @return int
     */
    public function getNextBadgeNameAttribute(): ?string
    {
        if (!$this->next_badge) return null;
        return trans('default.badges.' . $this->next_badge);
    }
}
