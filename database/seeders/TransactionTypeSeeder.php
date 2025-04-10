<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TransactionType;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Vente',
                'description' => 'Vente de produits ou services',
                'is_credit' => true,
            ],
            [
                'name' => 'Achat',
                'description' => 'Achat de produits ou services',
                'is_credit' => false,
            ],
            [
                'name' => 'Salaire',
                'description' => 'Paiement des salaires',
                'is_credit' => false,
            ],
            [
                'name' => 'Taxe',
                'description' => 'Paiement des taxes et impôts',
                'is_credit' => false,
            ],
            [
                'name' => 'Investissement',
                'description' => 'Investissement dans l\'entreprise',
                'is_credit' => true,
            ],
        ];

        foreach ($types as $type) {
            TransactionType::create($type);
        }
    }
}
