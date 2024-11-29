<?php

namespace Database\Factories;

use App\Models\AdminsUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminsUserFactory extends Factory
{
    protected $model = AdminsUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_name' => $this->faker->userName,
        ];
    }
}
