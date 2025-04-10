<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SiteCategory;

class SiteCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Siège social', 'description' => 'Siège principal de l\'entreprise'],
            ['name' => 'Agence', 'description' => 'Agence locale'],
            ['name' => 'Entrepôt', 'description' => 'Site de stockage'],
            ['name' => 'Point de vente', 'description' => 'Magasin ou boutique'],
        ];

        foreach ($categories as $category) {
            SiteCategory::create($category);
        }
    }
}
