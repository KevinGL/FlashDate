FROM php:8.4-cli

# Installer les dépendances système et les extensions PHP requises par Symfony
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    curl \
    && docker-php-ext-install \
        intl \
        pdo \
        pdo_mysql \
        zip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copier d'abord les fichiers de configuration pour profiter du cache Docker
COPY composer.json composer.lock ./

# Si vous avez un dossier symfony, assurez-vous de copier le reste après ou tout d'un coup
COPY . /var/www/html

# Lancer composer
RUN APP_ENV=prod composer install --no-dev --optimize-autoloader -vvv

# Installer les dépendances du sous-répertoire WebSocket
RUN cd ws && npm install

# Exposer les ports (Le port HTTP de Symfony et le port du WebSocket)
EXPOSE 8080
EXPOSE 3001

# Script de démarrage pour lancer les deux processus
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]