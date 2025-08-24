# Proyecto Ventas (Laravel 10)

## Requisitos
- PHP >= 8.1, Composer
- Node.js >= 18, npm
- MySQL/MariaDB

## Instalación
1) Clonar repo
2) Instalar dependencias
   - composer install
   - npm install
3) Variables de entorno
   - cp .env.example .env
   - Configurar DB_*, APP_URL, etc.
   - php artisan key:generate
4) Migraciones y seeders
   - php artisan migrate --seed
5) Almacenamiento
   - php artisan storage:link
6) Frontend
   - npm run dev (desarrollo) o npm run build (producción)
7) Servir
   - php artisan serve
   - o configurar en Apache/Nginx apuntando a public/

## Notas
- No se suben /vendor, /node_modules, /public/build, /storage (seguro).
- Si manejas imágenes/diseños, verifica que se guarden en storage/app/public y se accedan con asset('storage/...').

## Tests
- php artisan test