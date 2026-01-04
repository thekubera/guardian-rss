# Guardian RSS Feed Service

A Laravel 12-based RSS feed service exposing the latest articles from The Guardian by section.

Users can access feeds via URLs like:

- `http://localhost:8000/politics`
- `http://localhost:8000/movies`
- `http://localhost:8000/lifeandstyle`

The service fetches data from the Guardian API (JSON), converts it to W3C-compliant RSS, and serves it with proper caching, error handling, and logging.

---

## Features

- Dynamic RSS feeds per section
- Caching (10 minutes)
- JSON API requests to The Guardian
- W3C-compliant RSS
- PSR-standard codebase (Laravel Pint)
- JSON-formatted logging (multiple levels)
- Dockerized (PHP-FPM + Nginx, Alpine-based)
- Unit and integration tests with Pest

---

## Requirements

- PHP 8.3+
- Composer 2
- Docker & Docker Compose (optional)
- Guardian API key (free at https://open-platform.theguardian.com/)

---

## Setup Instructions

### 1. Clone the repository

```bash
git clone https://github.com/thekubera/guardian-rss.git
cd guardian-rss
```

### 2. Environment setup

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` and add your Guardian API key:

```env
GUARDIAN_API_KEY=b47ef130-xxxx-xxxx-xxxx-xxxxxxxxxxxx
```


### 3. Running locally (without Docker)

```bash
composer install
php artisan serve
```

Access feeds at `http://127.0.0.1:8000/section-name` eg. politics

---

### 4. Running with Docker (Recommended)

#### Build and start containers

```bash
docker compose up -d --build
```

The application will be available at `http://localhost:8000`


#### Test the RSS feed

```bash
curl http://localhost:8000/politics
```

---

### 5. Running tests

```bash
php artisan test
```

Or inside Docker:

```bash
docker compose exec app php artisan test
```

---

### 6. Linting

Code is formatted with **Laravel Pint**:

```bash
./vendor/bin/pint
```

---

### 7. Logging

- JSON-formatted logs are stored in `storage/logs/laravel.log`
- Logs include info (fetching sections) and error (API failures, invalid sections)

---

### 8. Notes

- Invalid section names (not lowercase letters/hyphens) return **HTTP 400**
- Feeds are cached for 10 minutes to reduce Guardian API usage
---

### 9. Directory structure

```
app/
├── Http/Controllers/RssFeedController.php
├── Services/
│   ├── GuardianApiService.php
│   └── RssFeedBuilder.php
bootstrap/
config/
docker/
├── nginx/default.conf
└── php/Dockerfile
routes/web.php
storage/
├── logs/laravel.log
└── database.sqlite (Docker-managed)
tests/
```

