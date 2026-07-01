#!/bin/sh
set -e

cd /var/www/html

if [ ! -d vendor ]; then
    echo "[entrypoint] Instalando dependências PHP (composer install)..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ ! -f .env ]; then
    echo "[entrypoint] Criando .env a partir de .env.example..."
    cp .env.example .env
fi

if ! grep -q "^APP_KEY=base64:" .env; then
    echo "[entrypoint] Gerando APP_KEY..."
    php artisan key:generate --force
fi

# Garante diretórios graváveis
mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache/data \
    storage/logs \
    bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "[entrypoint] Rodando migrations..."
php artisan migrate --force || echo "[entrypoint] migrate falhou (tentará novamente no próximo boot)"

exec "$@"
