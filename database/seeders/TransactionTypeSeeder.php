<?php

namespace Database\Seeders;

use App\Models\TransactionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = array(
            ['TU', 'TOPUP', 'in', 1],
            ['TR', 'TRANSACTION', 'out', 0],
        );

        for ($i=0; $i < count($json); $i++) {
            TransactionType::create([
                'code'  => $json[$i][0],
                'name'  => $json[$i][1],
                'type'  => $json[$i][2],
                'has_image'  => $json[$i][3],
            ]);
        }
    }
}
