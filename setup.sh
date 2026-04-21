#!/bin/sh

cp .env.example .env 2>/dev/null || true
mkdir -p bootstrap/cache
chmod -R 775 bootstrap storage

docker compose up -d --build
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate
docker compose run --rm app php artisan migrate:fresh --seed
docker compose run --rm node npm install
docker compose run --rm node npm run build
