<?php

namespace Database\Factories;

use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pengumuman>
 */
class PengumumanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_id' => User::factory()->admin(),
            'judul' => fake()->sentence(3),
            'type' => fake()->randomElement(['informasi', 'umum', 'penting']),
            'deskripsi' => fake()->paragraph(),
            'tanggal_publish' => now()->toDateString(),
        ];
    }
}
