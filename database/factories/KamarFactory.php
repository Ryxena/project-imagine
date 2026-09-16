<?php

namespace Database\Factories;

use App\Models\Kamar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kamar>
 */
class KamarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor_kamar' => fake()->unique()->numerify('A###'),
            'tipe_kamar' => fake()->randomElement(['standar', 'deluxe', 'vip']),
            'harga' => fake()->randomFloat(2, 500000, 2500000),
            'deskripsi' => fake()->sentence(),
        ];
    }
}
