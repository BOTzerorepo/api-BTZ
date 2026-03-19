<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'username'           => $this->faker->unique()->userName(),
            'name'               => $this->faker->firstName(),
            'last_name'          => $this->faker->lastName(),
            'email'              => $this->faker->unique()->safeEmail(),
            'email_verified_at'  => now(),
            'pass'               => bcrypt('password'),
            'empresa'            => $this->faker->company(),
            'remember_token'     => Str::random(10),
        ];
    }
}
