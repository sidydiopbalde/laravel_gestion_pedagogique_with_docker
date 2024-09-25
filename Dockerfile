# Utiliser une image officielle de PHP avec FPM (FastCGI Process Manager)
FROM php:8.1-fpm

# Installer les dépendances du système
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    apt-utils \
    libc-ares-dev \
    libssl-dev \
    zlib1g-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip \
    && pecl install grpc \
    && docker-php-ext-enable grpc \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copier le reste du code
COPY . /var/www

# Définir le répertoire de travail
WORKDIR /var/www

# Assurer que l'utilisateur www-data a les bonnes permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exposer le port 9000 pour PHP-FPM
EXPOSE 80
EXPOSE 8080

# Copie le script de démarrage
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Lancer le script de démarrage quand le conteneur démarre
CMD ["sh", "/usr/local/bin/start.sh"]
# Ajouter un healthcheck pour PHP-FPM
# HEALTHCHECK --interval=30s --timeout=10s \
#   CMD curl --fail http://localhost:8080 || exit 1

# Démarrer PHP-FPM
# CMD ["php-fpm"]
