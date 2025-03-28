CinéHall - Plateforme de Réservation de Cinéma
📽️ À Propos du Projet
CinéHall est une application web moderne permettant la réservation de billets de cinéma en ligne, offrant une expérience utilisateur fluide et intuitive.
🚀 Fonctionnalités Principales
Gestion des Utilisateurs

Création et authentification de compte
Authentification JWT
Gestion complète du profil utilisateur

Gestion des Films et Séances

Informations détaillées sur les films
Création et gestion des séances
Filtrage avancé des séances

Gestion des Salles et Sièges

Configuration flexible des salles
Options de sièges VIP et couples
Gestion dynamique des sièges en temps réel

Réservation et Paiement

Réservation de sièges simplifiée
Intégration de paiement en ligne (Stripe/PayPal)
Expiration automatique des réservations non payées

Billets Électroniques

Génération de billets avec QR Code
Téléchargement en PDF

Dashboard Administrateur

Vue d'ensemble des statistiques
Analyse des performances
Gestion des utilisateurs et des films

🛠 Technologies Utilisées

Backend: Laravel (PHP)
API: RESTful API
Base de Données: PostgreSQL
Authentification: JWT
Tests: Postman
Documentation API: Swagger/OpenAPI

📋 Prérequis

PHP 8.1+
Composer
PostgreSQL
Laravel 9/10
Compte Stripe/PayPal (pour paiements)

🔧 Installation

Clonez le dépôt

bashCopiergit clone https://github.com/charafeddine-Web/Cin-Hall-API-
cd cinehall

Installez les dépendances

bashCopiercomposer install

Configurez l'environnement

bashCopiercp .env.example .env
php artisan key:generate

Configurez la base de données PostgreSQL

bashCopier# Modifiez .env avec vos identifiants PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=cinehall
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe

Migrez et seedez la base de données

bashCopierphp artisan migrate
php artisan db:seed

Lancez le serveur

bashCopierphp artisan serve
🧪 Tests

Tests unitaires Laravel
Collection de tests Postman disponible dans /tests/postman

bashCopierphp artisan test
📚 Documentation API
La documentation complète de l'API est disponible via Swagger:

URL: /api/documentation
Format: OpenAPI Specification

🔐 Authentification
Authentification basée sur JWT avec support optionnel des connexions via réseaux sociaux.



🤝 Contribution

Forkez le projet
Créez votre branche de fonctionnalité (git checkout -b feature/AmazingFeature)
Commitez vos modifications (git commit -m 'Add some AmazingFeature')
Poussez sur la branche (git push origin feature/AmazingFeature)
Ouvrez une Pull Request

📄 Licence
Distribué sous licence MIT. Voir LICENSE pour plus d'informations.
📧 Contact
Tbibzat Charaf Eddine - charafeddinetbibzat.email@example.com
