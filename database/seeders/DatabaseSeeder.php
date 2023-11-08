<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TransactionTypeSeeder::class,
        ]);

        User::factory(100)
        ->has(Transaction::factory(rand(5, 20)), 'transactions')
        ->create();
    }
}
