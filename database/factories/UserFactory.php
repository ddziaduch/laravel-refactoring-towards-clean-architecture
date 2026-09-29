<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            // Hashed by User::setPasswordAttribute().
            'password' => 'password',
            'image' => $this->faker->imageUrl,
            'bio' => $this->faker->text,
        ];
    }
}
