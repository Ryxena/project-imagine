<?php

namespace Database\Factories;

use App\Models\Kamar;
use App\Models\Penghunian;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Penghunian>
 */
class PenghunianFactory extends Factory
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
            'kamar_id' => Kamar::factory(),
            'last_kamar_id' => fn (array $attributes) => $attributes['kamar_id'],
            'tanggal_masuk' => now()->startOfMonth()->toDateString(),
        ];
    }

    /**
     * Penghunian tanpa kamar, untuk menguji alur assign kamar.
     */
    public function tanpaKamar(): static
    {
        return $this->state(fn () => [
            'kamar_id' => null,
            'last_kamar_id' => null,
        ]);
    }

    /**
     * Penghunian yang sudah checkout dari kamarnya.
     */
    public function checkout(): static
    {
        return $this->state(fn () => [
            'kamar_id' => null,
            'tanggal_checkout' => now()->toDateString(),
        ]);
    }
}
