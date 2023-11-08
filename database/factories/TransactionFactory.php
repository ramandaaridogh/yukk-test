<?php

namespace Database\Factories;

use App\Models\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = date('Y-m-d');
        $type = TransactionType::query()->inRandomOrder()->first();
        $code = $type->code . str_replace('-', '', $date) . fake()->numberBetween(1000000, 9999999);

        return [
            'code' => $code,
            'ammount' => fake()->randomFloat(2, 0, 9999999),
            'note' => fake()->paragraph(1),
            'image' => $type->has_image == true ? fake()->imageUrl() : null,
            'transaction_type_id' => $type->id,
        ];
    }
}
