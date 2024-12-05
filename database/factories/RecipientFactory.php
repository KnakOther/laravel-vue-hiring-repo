<?php

namespace Database\Factories;

use App\Models\Recipient;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecipientFactory extends Factory
{
    public function definition()
    {
        return [
            'bamboo_id' => $this->faker->numberBetween(1000, 9999),
            'email' => $this->faker->email,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'department' => $this->faker->word,
            'position' => $this->faker->word,
            'location' => $this->faker->word,
            'avatar_url' => $this->faker->imageUrl(),
            'supervisor_id' => $this->faker->numberBetween(1000, 9999),
        ];
    }
}
