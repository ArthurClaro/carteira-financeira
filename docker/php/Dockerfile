FROM php:8.3-fpm-bookworm

# Dependências de sistema + extensões PHP necessárias para Laravel/MySQL/Redis/testes.
# $PHPIZE_DEPS (compiladores) é instalado para compilar as extensões e removido em seguida
# para manter a imagem enxuta; as libs de runtime (icu, zip, sqlite, oniguruma) permanecem.
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        $PHPIZE_DEPS \
        git unzip \
        libzip-dev libicu-dev libsqlite3-dev libonig-dev; \
    docker-php-ext-install -j"$(nproc)" \
        pdo_mysql pdo_sqlite mbstring bcmath zip intl opcache pcntl; \
    pecl install redis; \
    docker-php-ext-enable redis; \
    apt-get purge -y --auto-remove $PHPIZE_DEPS; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

# Composer (binário oficial)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY entrypoint.sh /usr/local/bin/entrypoint
# Normaliza fim de linha (evita quebra se o arquivo vier com CRLF do Windows) e torna executável
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint && chmod +x /usr/local/bin/entrypoint

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
