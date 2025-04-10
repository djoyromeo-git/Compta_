<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Site;
use App\Models\Currency;
use App\Models\User;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupération des types de transactions
        $vente = TransactionType::where('name', 'Vente')->first();
        $achat = TransactionType::where('name', 'Achat')->first();
        $salaire = TransactionType::where('name', 'Salaire')->first();
        $taxe = TransactionType::where('name', 'Taxe')->first();
        $investissement = TransactionType::where('name', 'Investissement')->first();

        // Récupération des sites
        $sites = Site::all();
        
        // Récupération des devises
        $eur = Currency::where('code', 'EUR')->first();
        $usd = Currency::where('code', 'USD')->first();

        // Récupération des utilisateurs
        $users = User::all();
        
        // Date de début (12 mois avant aujourd'hui)
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();
        $endDate = Carbon::now();

        // Pour chaque mois
        for ($date = $startDate->copy(); $date <= $endDate; $date->addMonth()) {
            $daysInMonth = $date->daysInMonth;
            
            // Pour chaque jour du mois
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = Carbon::create($date->year, $date->month, $day);
                
                // Ne pas créer de transactions le weekend
                if ($currentDate->isWeekend()) {
                    continue;
                }

                // Pour chaque site
                foreach ($sites as $site) {
                    // Récupérer l'utilisateur du gérant du site
                    $siteManager = $site->person->user;
                    
                    // 1-3 ventes par jour (crédit)
                    $nbVentes = rand(1, 3);
                    for ($i = 0; $i < $nbVentes; $i++) {
                        $devise = (rand(1, 100) <= 20) ? $usd : $eur; // 20% de chance d'être en USD
                        $montant = rand(100, 10000) / 100;
                        if ($devise->id === $usd->id) {
                            $montant = $montant * 1.09; // Conversion approximative EUR -> USD
                        }
                        
                        // Sélectionner aléatoirement un utilisateur pour la transaction
                        $user = $users->random();
                        
                        Transaction::create([
                            'date' => $currentDate,
                            'description' => 'Vente #' . uniqid() . ' en ' . $devise->code,
                            'amount' => $montant,
                            'transaction_type_id' => $vente->id,
                            'site_id' => $site->id,
                            'currency_id' => $devise->id,
                            'user_id' => $user->id, // Ajout de l'utilisateur qui a fait la transaction
                        ]);
                    }

                    // 1-2 achats par jour (débit)
                    $nbAchats = rand(1, 2);
                    for ($i = 0; $i < $nbAchats; $i++) {
                        $devise = (rand(1, 100) <= 20) ? $usd : $eur; // 20% de chance d'être en USD
                        $montant = rand(500, 5000) / 100;
                        if ($devise->id === $usd->id) {
                            $montant = $montant * 1.09; // Conversion approximative EUR -> USD
                        }

                        // Sélectionner aléatoirement un utilisateur pour la transaction
                        $user = $users->random();
                        
                        Transaction::create([
                            'date' => $currentDate,
                            'description' => 'Achat #' . uniqid() . ' en ' . $devise->code,
                            'amount' => $montant,
                            'transaction_type_id' => $achat->id,
                            'site_id' => $site->id,
                            'currency_id' => $devise->id,
                            'user_id' => $user->id, // Ajout de l'utilisateur qui a fait la transaction
                        ]);
                    }

                    // Salaire à la fin du mois (débit) - Toujours en EUR
                    if ($day === $daysInMonth) {
                        Transaction::create([
                            'date' => $currentDate,
                            'description' => 'Salaire du mois de ' . $currentDate->format('F Y'),
                            'amount' => 2500.00,
                            'transaction_type_id' => $salaire->id,
                            'site_id' => $site->id,
                            'currency_id' => $eur->id,
                            'user_id' => $siteManager->id, // Le gérant du site effectue le paiement du salaire
                        ]);
                    }

                    // Taxe trimestrielle (débit) - Toujours en EUR
                    if ($day === $daysInMonth && in_array($date->month, [3, 6, 9, 12])) {
                        Transaction::create([
                            'date' => $currentDate,
                            'description' => 'Taxe trimestrielle Q' . ceil($date->month / 3) . ' ' . $date->year,
                            'amount' => 1500.00,
                            'transaction_type_id' => $taxe->id,
                            'site_id' => $site->id,
                            'currency_id' => $eur->id,
                            'user_id' => $siteManager->id, // Le gérant du site effectue le paiement de la taxe
                        ]);
                    }

                    // Investissement annuel (débit) - Toujours en EUR
                    if ($day === $daysInMonth && $date->month === 12) {
                        Transaction::create([
                            'date' => $currentDate,
                            'description' => 'Investissement annuel ' . $date->year,
                            'amount' => 10000.00,
                            'transaction_type_id' => $investissement->id,
                            'site_id' => $site->id,
                            'currency_id' => $eur->id,
                            'user_id' => $siteManager->id, // Le gérant du site effectue l'investissement
                        ]);
                    }
                }
            }
        }
    }
}
