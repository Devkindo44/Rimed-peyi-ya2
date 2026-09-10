# RIMED PÉYI YA

## Prérequis
* PHP >= 8.2
* Composer
* MariaDB / MySQL (via XAMPP)
* Symfony CLI

## Installation
1. Cloner le projet : `git clone <URL_DU_DEPOT>`
2. Installer les dépendances PHP : `composer install`
3. Configurer l'environnement : copier `.env` vers `.env.local` et ajuster `DATABASE_URL`
4. Créer la base de données : `php bin/console doctrine:database:create`
5. Exécuter les migrations : `php bin/console doctrine:migrations:migrate`
6. Charger les jeux de données : `php bin/console doctrine:fixtures:load`
7. Lancer le serveur local : `symfony server:start`