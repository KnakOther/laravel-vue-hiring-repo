<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    public function definition()
    {
        return [
            'send_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'subject' => $this->faker->sentence,
            'email_name' => $this->faker->word,
            'knak_email_id' => $this->faker->uuid,
            'html' => $this->faker->word,
            'from_name'=>$this->faker->name,
            'from_email'=>$this->faker->email,
            'reply_email'=>$this->faker->email,
            'knak_version'=>$this->faker->uuid,
        ];
    }
}
