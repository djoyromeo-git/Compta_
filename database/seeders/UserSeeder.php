<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Person;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupération de la personne admin
        $adminPerson = Person::where('first_name', 'Admin')->first();

        // Création de l'utilisateur admin
        User::create([
            'username' => 'admin',
            'email' => 'admin@compta-erp.com',
            'password' => Hash::make('admin123'),
            'person_id' => $adminPerson->id,
        ]);

        // Création d'utilisateurs de test
        $people = Person::where('first_name', '!=', 'Admin')->get();
        foreach ($people as $person) {
            User::create([
                'username' => strtolower($person->first_name . '.' . $person->last_name),
                'email' => strtolower($person->first_name . '.' . $person->last_name . '@compta-erp.com'),
                'password' => Hash::make('password123'),
                'person_id' => $person->id,
            ]);
        }
    }
}
