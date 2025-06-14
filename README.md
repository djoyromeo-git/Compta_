# Compta-ERP

Compta-ERP est une application web open source de gestion comptable et de planification des ressources d’entreprise (ERP) réalisée avec Laravel 10. Elle permet de suivre les transactions financières de plusieurs sites tout en offrant une interface moderne et des rapports PDF.

[![GitHub Repository](https://img.shields.io/badge/GitHub-Compta--ERP-blue?style=flat&logo=github)](https://github.com/djoyromeo-git/Compta_.git)

## Fonctionnalités

- Gestion des transactions (création, modification, suppression)
- Gestion des devises et des types de transaction (débit ou crédit)
- Administration des sites et des utilisateurs (rôle administrateur ou responsable de site)
- Tableau de bord avec statistiques et graphiques (Chart.js)
- Export des transactions et du tableau de bord au format PDF (DomPDF)

## Prérequis

- PHP ≥ 8.1 et Composer
- Node.js et npm pour la compilation des assets (Vite)
- Base de données MySQL ou PostgreSQL
- Serveur web (Apache ou Nginx)

## Installation

```bash
git clone https://github.com/djoyromeo-git/Compta_.git
cd Compta_
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configurez ensuite la connexion à votre base de données dans `.env` puis lancez&nbsp;:

```bash
php artisan migrate --seed    # structure et données de démonstration
npm run build                 # ou `npm run dev` pour un environnement de développement
php artisan serve
```

L’application est maintenant disponible sur `http://localhost:8000`.

## Structure du projet

- `app/` &mdash; contrôleurs, modèles et services Laravel
- `resources/` &mdash; vues Blade, JavaScript et SASS
- `database/` &mdash; migrations et seeders
- `public/` &mdash; fichiers accessibles publiquement
- `routes/` &mdash; définition des routes Web et API

## Tests

L’exécution de la suite de tests se fait avec&nbsp;:

```bash
php artisan test
```

## Contribution

Les demandes de contribution sont les bienvenues. Forkez le dépôt, créez votre branche (`git checkout -b ma-fonctionnalite`) puis ouvrez une Pull Request.

## Licence

Ce projet est proposé sous licence MIT.


