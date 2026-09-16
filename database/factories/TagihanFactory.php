<?php

namespace Database\Factories;

use App\Models\Penghunian;
use App\Models\Tagihan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tagihan>
 */
class TagihanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'penghunian_id' => Penghunian::factory(),
            'bulan_tagihan' => now()->format('Y-m'),
            'jumlah' => fake()->randomFloat(2, 500000, 2500000),
        ];
    }
}
