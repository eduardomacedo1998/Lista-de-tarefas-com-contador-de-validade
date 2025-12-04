<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'is_completed' => $this->faker->boolean(20),
            'priority' => $this->faker->numberBetween(1, 3),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+30 days'),
            'user_id' => function() {
                $user = User::inRandomOrder()->first();
                return $user ? $user->id : User::factory()->create()->id;
            },
        ];
    }
}
