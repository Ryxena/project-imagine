<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->penghuni(),
            'judul' => fake()->sentence(3),
            'pesan' => fake()->sentence(),
            'tipe' => fake()->randomElement(['tagihan', 'pembayaran']),
            'dibaca' => false,
        ];
    }
}
