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
        $code = $type->code . str_replace('-', '', $date) . fake()->unique()->numberBetween(1000000, 9999999);
        $ammount = $type->type->value == 'in' ? fake()->randomFloat(2, 1000, 99999) : fake()->randomFloat(2, 0, 9999);

        return [
            'code' => $code,
            'ammount' => $ammount,
            'note' => fake()->paragraph(1),
            'image' => $type->has_image == true ? fake()->imageUrl() : null,
            'transaction_type_id' => $type->id,
        ];
    }
}
