<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Person;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création de l'administrateur
        Person::create([
            'first_name' => 'Admin',
            'last_name' => 'System',
            'phone' => '+33600000000',
            'address' => '1 rue de l\'Administration',
            'city' => 'Paris',
            'postal_code' => '75000',
            'country' => 'France',
        ]);

        // Création de quelques personnes de test
        $people = [
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'phone' => '+33611111111',
                'address' => '2 rue des Lilas',
                'city' => 'Lyon',
                'postal_code' => '69000',
                'country' => 'France',
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Martin',
                'phone' => '+33622222222',
                'address' => '3 avenue des Roses',
                'city' => 'Marseille',
                'postal_code' => '13000',
                'country' => 'France',
            ],
        ];

        foreach ($people as $person) {
            Person::create($person);
        }
    }
}
