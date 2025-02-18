# syntax=docker/dockerfile:1

#########################################
# Étape 1 : Construction de la base commune
#########################################
FROM php:8.3-fpm AS php_base

WORKDIR /app
VOLUME /app/var/

# Mise à jour et installation des dépendances système, y compris unzip et libzip-dev
RUN apt-get update && apt-get install -y --no-install-recommends \
    acl \
    file \
    gettext \
    git \
    curl \
    unzip \
    libzip-dev \
    libmagickwand-dev \
    pkg-config \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

#########################################
# Étape 2 : Installation des extensions PHP
#########################################

# Extension GD avec support de FreeType, JPEG et WebP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install gd

# Extension ZIP (nécessite libzip-dev et unzip)
RUN docker-php-ext-install zip

# Installation manuelle de l'extension Imagick (version 3.6.0)
RUN pecl download imagick-3.6.0 && \
    tar xf imagick-3.6.0.tgz && \
    sed -i '$a#endif' imagick-3.6.0/Imagick.stub.php && \
    cd imagick-3.6.0 && \
    phpize && \
    ./configure --with-php-config=/usr/local/bin/php-config --with-imagick && \
    make && make install && \
    docker-php-ext-enable imagick && \
    cd .. && rm -rf imagick-3.6.0*

# Extension PDO MySQL
RUN docker-php-ext-install pdo_mysql

#########################################
# Étape 3 : Installation d’outils supplémentaires
#########################################

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer Node.js et npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm

#########################################
# Étape 4 : Copie des fichiers de configuration
#########################################
# Copie d’un fichier de configuration PHP personnalisé
COPY docker/config/php/conf.d/10-app.ini /usr/local/etc/php/conf.d/
# Copie d’un script d’entrypoint personnalisé
COPY docker/config/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

#########################################
# Étape 5 : Cibles pour Dev et Prod
#########################################
FROM php_base AS php_dev
# (Ajoutez ici d'éventuelles personnalisations pour l'environnement de développement, par exemple Xdebug)

FROM php_base AS php_prod
# Optimisations spécifiques pour la production
RUN mkdir -p /app/var/cache && chown -R www-data:www-data /app/var

#########################################
# Étape 6 : Instructions finales
#########################################
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
