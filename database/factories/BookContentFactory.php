<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\BookContent>
 */
class BookContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'label' => $this->faker->sentence(1),
            'title' => $this->faker->sentence(5),
            'page_number' => $this->faker->numberBetween(1,100)
        ];
    }
}
