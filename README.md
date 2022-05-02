<h1>Api Gestor Tasques</h1>

## About

API REST del projecte de final de master per a un gestor de tasques col·laboratiu. Permet l'alta d'usuaris, la creació de grups i la gestió de tasques i recompenses.

## Requisits

- PHP >= 8.0 (Extensions: BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML)
- Servidor web php
- Mysql >= 5.7
- Composer >= 2.3.5

## Desenvolupament

Desplegar el servidor de desenvolupament amb la instrucció `./vendor/bin/sail up -d`. apagar amb `./vendor/bin/sail down`

## Producció

Pasos a seguir per a desplegar el projecte a un servidor de producció.

- Renombrar el fitxer `.env.example` a `.env`
- Modificar les propietats adients, especialment les relacionades amb la BD.
- Instal·lar laravel: `composer install --optimize-autoloader –no-dev`
- Generar els keys: `php artisan key:generate`
- Instalar passport: `php artisan passport:install`
- Optimitzacions de laravel per a producció: `php artisan config:cache` , `php artisan route:cache` , `php artisan view:cache`. 
- Executar les migracions: `php artisan migrate` 
