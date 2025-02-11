#syntax=docker/dockerfile:1

# Versions
FROM dunglas/frankenphp:1-php8.3 AS frankenphp_upstream

# Base FrankenPHP image
FROM frankenphp_upstream AS frankenphp_base

WORKDIR /app
VOLUME /app/var/

# Installation des dépendances système, y compris celles nécessaires à GD et zlib
RUN apt-get update && apt-get install -y --no-install-recommends \
    acl \
    file \
    gettext \
    git \
    curl \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

# Créer manuellement un fichier zlib.pc s'il n'existe pas
RUN mkdir -p /usr/lib/pkgconfig && \
    if [ ! -f /usr/lib/pkgconfig/zlib.pc ]; then \
      echo 'prefix=/usr' > /usr/lib/pkgconfig/zlib.pc && \
      echo 'exec_prefix=${prefix}' >> /usr/lib/pkgconfig/zlib.pc && \
      echo 'libdir=${exec_prefix}/lib' >> /usr/lib/pkgconfig/zlib.pc && \
      echo 'includedir=${prefix}/include' >> /usr/lib/pkgconfig/zlib.pc && \
      echo '' >> /usr/lib/pkgconfig/zlib.pc && \
      echo 'Name: zlib' >> /usr/lib/pkgconfig/zlib.pc && \
      echo 'Description: zlib compression library' >> /usr/lib/pkgconfig/zlib.pc && \
      echo 'Version: 1.2.13' >> /usr/lib/pkgconfig/zlib.pc && \
      echo 'Libs: -L${libdir} -lz' >> /usr/lib/pkgconfig/zlib.pc && \
      echo 'Cflags: -I${includedir}' >> /usr/lib/pkgconfig/zlib.pc; \
    fi

# Définir PKG_CONFIG_PATH pour inclure le répertoire où zlib.pc est créé
ENV PKG_CONFIG_PATH="/usr/lib/pkgconfig:/usr/lib/aarch64-linux-gnu/pkgconfig:/usr/lib/pkgconfig"

# Installation des extensions PHP via install-php-extensions (sauf GD)
RUN set -eux; \
    install-php-extensions @composer apcu intl opcache zip pdo_pgsql

# Installation manuelle de GD en passant les variables d'environnement pour zlib
RUN ZLIB_CFLAGS="-I/usr/include" ZLIB_LIBS="-L/usr/lib -lz" \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# Installation de Node.js et npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm

# Variables d'environnement PHP
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PHP_INI_SCAN_DIR=":$PHP_INI_DIR/app.conf.d"

# Configuration de l'application
COPY --link docker/config/frankenphp/conf.d/10-app.ini $PHP_INI_DIR/app.conf.d/
COPY --link --chmod=755 docker/config/frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link docker/config/frankenphp/Caddyfile /etc/caddy/Caddyfile

ENTRYPOINT ["docker-entrypoint"]

HEALTHCHECK --start-period=60s CMD curl -f http://localhost:2019/metrics || exit 1
CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile" ]

# -----------------------
# Dev FrankenPHP image
# -----------------------
FROM frankenphp_base AS frankenphp_dev

ENV APP_ENV=dev XDEBUG_MODE=off

# Utilisation du php.ini de développement
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Installation d'extensions spécifiques au mode dev (xdebug)
RUN set -eux; \
    install-php-extensions xdebug
    # GD est déjà installé dans frankenphp_base

COPY --link docker/config/frankenphp/conf.d/20-app.dev.ini $PHP_INI_DIR/app.conf.d/

# Installation des dépendances Node.js en mode développement
COPY --link package*.json yarn.lock ./
RUN npm install && npm cache clean --force

CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--watch" ]

# ------------------------
# Prod FrankenPHP image
# ------------------------
FROM frankenphp_base AS frankenphp_prod

ENV APP_ENV=prod
ENV FRANKENPHP_CONFIG="import worker.Caddyfile"

# Utilisation du php.ini de production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --link docker/config/frankenphp/conf.d/20-app.prod.ini $PHP_INI_DIR/app.conf.d/
COPY --link docker/config/frankenphp/worker.Caddyfile /etc/caddy/worker.Caddyfile

# Installation des dépendances Node.js en mode production
COPY --link package*.json yarn.lock ./
RUN npm install --omit=dev --no-audit && npm cache clean --force

# Installation des dépendances PHP (via Composer)
COPY --link composer.* symfony.* ./
RUN set -eux; \
    composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

# Copie du code source
COPY --link . ./
RUN rm -Rf frankenphp/

# Finalisation : création des dossiers et autoload
RUN set -eux; \
    mkdir -p var/cache var/log; \
    composer dump-autoload --classmap-authoritative --no-dev; \
    composer dump-env prod; \
    composer run-script --no-dev post-install-cmd; \
    chmod +x bin/console; sync;

CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile" ]
