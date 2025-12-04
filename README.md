# 🌱 EcoGarden API

API REST pour une application de jardinage proposant des conseils saisonniers et des informations météo personnalisées.

## Technologies

- **Framework** : Symfony 7
- **PHP** : 8.2+
- **Base de données** : PostgreSQL
- **Authentification** : JWT (LexikJWTAuthenticationBundle)
- **Documentation** : OpenAPI / Swagger (NelmioApiDocBundle)
- **API Météo** : OpenWeatherMap

## Installation

```bash
# Cloner le projet
git clone https://github.com/CGirr/EcoGarden.git
cd EcoGarden

# Installer les dépendances
composer install

# Configurer les variables d'environnement
cp .env .env.local
# Éditer .env.local avec vos paramètres (voir section Configuration)

# Générer les clés JWT
php bin/console lexik:jwt:generate-keypair

# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Charger les données de test (optionnel)
php bin/console doctrine:fixtures:load
```

## Configuration

Variables d'environnement requises dans `.env.local` :

```env
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/ecogarden"
JWT_PASSPHRASE="votre_passphrase"
OPENWEATHERMAP_API_KEY="votre_clé_api"
```

## Données de test

Après `doctrine:fixtures:load`, deux comptes sont disponibles :

| Utilisateur | Mot de passe | Rôle |
|-------------|--------------|------|
| `Isydia` | `123456` | USER |
| `Admin` | `123456` | ADMIN |

## Endpoints

### Authentification

| Méthode | Route | Description | Accès |
|---------|-------|-------------|-------|
| POST | `/api/auth` | Obtenir un token JWT | Public |

### Utilisateurs

| Méthode | Route | Description | Accès |
|---------|-------|-------------|-------|
| POST | `/api/user` | Créer un compte | Public |
| PUT | `/api/user/{id}` | Modifier un utilisateur | Admin |
| DELETE | `/api/user/{id}` | Supprimer un utilisateur | Admin |

### Conseils

| Méthode | Route | Description | Accès |
|---------|-------|-------------|-------|
| GET | `/api/advice` | Conseils du mois en cours | Authentifié |
| GET | `/api/advice/{month}` | Conseils d'un mois spécifique (1-12) | Authentifié |
| POST | `/api/advice` | Créer un conseil | Admin |
| PUT | `/api/advice/{id}` | Modifier un conseil | Admin |
| DELETE | `/api/advice/{id}` | Supprimer un conseil | Admin |

### Météo

| Méthode | Route | Description | Accès |
|---------|-------|-------------|-------|
| GET | `/api/weather` | Météo de la ville de l'utilisateur | Authentifié |
| GET | `/api/weather/{city}` | Météo d'une ville (code postal) | Authentifié |

## Documentation API

Une documentation Swagger est disponible à l'adresse :

```
GET /api/doc
```

## Authentification

L'API utilise JWT. Pour accéder aux routes protégées :

1. Créer un compte via `POST /api/user`
2. Obtenir un token via `POST /api/auth`
3. Inclure le token dans le header : `Authorization: Bearer <token>`

## Exemples de requêtes

### Créer un compte

```bash
curl -X POST http://localhost:8000/api/user \
  -H "Content-Type: application/json" \
  -d '{"username": "jardinier", "password": "secret123", "city": "75001"}'
```

### S'authentifier

```bash
curl -X POST http://localhost:8000/api/auth \
  -H "Content-Type: application/json" \
  -d '{"username": "jardinier", "password": "secret123"}'
```

### Récupérer les conseils du mois

```bash
curl http://localhost:8000/api/advice \
  -H "Authorization: Bearer <votre_token>"
```

### Récupérer la météo

```bash
curl http://localhost:8000/api/weather/75001 \
  -H "Authorization: Bearer <votre_token>"
```

## Licence

MIT
