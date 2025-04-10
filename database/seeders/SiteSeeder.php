<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Site;
use App\Models\SiteCategory;
use App\Models\Person;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupération des catégories
        $siegeSocial = SiteCategory::where('name', 'Siège social')->first();
        $agence = SiteCategory::where('name', 'Agence')->first();
        $entrepot = SiteCategory::where('name', 'Entrepôt')->first();
        $pointVente = SiteCategory::where('name', 'Point de vente')->first();

        // Récupération des personnes pour les gérants
        $adminPerson = Person::where('first_name', 'Admin')->first();
        $jeanDupont = Person::where('first_name', 'Jean')->first();
        $marieMartin = Person::where('first_name', 'Marie')->first();

        // Création des sites
        $sites = [
            [
                'name' => 'Siège Paris',
                'address' => '1 rue de la Paix',
                'city' => 'Paris',
                'postal_code' => '75001',
                'country' => 'France',
                'phone' => '+33123456789',
                'email' => 'contact@siege-paris.com',
                'site_category_id' => $siegeSocial->id,
                'person_id' => $adminPerson->id, // L'admin gère le siège social
            ],
            [
                'name' => 'Agence Lyon',
                'address' => '15 rue de la République',
                'city' => 'Lyon',
                'postal_code' => '69002',
                'country' => 'France',
                'phone' => '+33456789123',
                'email' => 'contact@agence-lyon.com',
                'site_category_id' => $agence->id,
                'person_id' => $jeanDupont->id, // Jean Dupont gère l'agence de Lyon
            ],
            [
                'name' => 'Entrepôt Lille',
                'address' => '50 rue de l\'Industrie',
                'city' => 'Lille',
                'postal_code' => '59000',
                'country' => 'France',
                'phone' => '+33345678912',
                'email' => 'contact@entrepot-lille.com',
                'site_category_id' => $entrepot->id,
                'person_id' => $marieMartin->id, // Marie Martin gère l'entrepôt de Lille
            ],
            [
                'name' => 'Boutique Marseille',
                'address' => '25 rue du Port',
                'city' => 'Marseille',
                'postal_code' => '13001',
                'country' => 'France',
                'phone' => '+33412345678',
                'email' => 'contact@boutique-marseille.com',
                'site_category_id' => $pointVente->id,
                'person_id' => $jeanDupont->id, // Jean Dupont gère aussi la boutique de Marseille
            ],
        ];

        foreach ($sites as $site) {
            Site::create($site);
        }
    }
}
