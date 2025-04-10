<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 1.00, // Taux de base
            ],
            [
                'code' => 'USD',
                'name' => 'Dollar américain',
                'symbol' => '$',
                'exchange_rate' => 1.09,
            ],
            [
                'code' => 'GBP',
                'name' => 'Livre sterling',
                'symbol' => '£',
                'exchange_rate' => 0.85,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
