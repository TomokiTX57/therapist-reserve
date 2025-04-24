FROM php:8.2-apache

# システムツール・PHP拡張のインストール
RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev nodejs npm \
    && docker-php-ext-install pdo_mysql bcmath

# mod_rewrite有効化
RUN a2enmod rewrite

# Node.jsのバージョン確認（古ければ更新）
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Composerコピー（マルチステージに対応していなければinstallでも可）
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# LaravelのDocumentRootを設定
RUN sed -i 's|/var/www/html|/var/www/laravel/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/laravel/public|g' /etc/apache2/apache2.conf

# 作業ディレクトリ
WORKDIR /var/www/laravel