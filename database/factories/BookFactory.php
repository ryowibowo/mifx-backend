<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker = $this->faker;
        return [
            'title' => $faker->sentence,
            'description' => $faker->text(),
            'isbn' => $faker->isbn13,
            'price' => $faker->numberBetween(5, 25),
            'published_year' => $faker->numberBetween(1900, 2020)
        ];
    }
}
