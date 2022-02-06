<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $lessons = Lesson::factory()
            ->count(20)
            ->hasAttached(
                User::factory()
                    ->count(rand(1, 5))
                    ->hasComments(rand(1, 5)),
                ['watched' => 1]
            )
            ->create();
    }
}
