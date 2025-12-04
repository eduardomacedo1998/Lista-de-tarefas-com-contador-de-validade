<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure some users exist
        if (User::count() === 0) {
            User::factory()->count(5)->create();
        }

        // Create tasks grouped by user to make data realistic
        foreach (User::all() as $user) {
            Task::factory()->count(rand(3, 8))->create([ 'user_id' => $user->id ]);
        }
    }
}
