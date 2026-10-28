# Carolina

> Sistema de gestión de loteos organizado por campañas.

Carolina permite administrar compradores, manzanas, lotes, promesas de compraventa, pagos y escrituras. También incluye proyección de cuotas (amortización), reportes en Excel, recordatorios de pago por SMS (Labsmobile) y una consulta pública para descargar estados de cuenta en PDF.

## Índice

- [Tecnologías](#tecnologías)
- [Requisitos](#requisitos)
- [Instalación para desarrollo](#instalación-para-desarrollo)
- [Variables de entorno](#variables-de-entorno)
- [Tests](#tests)
- [Comandos útiles](#comandos-útiles)
- [Despliegue en producción](#despliegue-en-producción)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Problemas comunes](#problemas-comunes)

## Tecnologías

| Área | Tecnologías |
|:--|:--|
| Backend | PHP 8.2+, Laravel 11 |
| Interfaz | Livewire 3, Alpine.js, WireUI, Tailwind CSS 3, Vite 5 |
| Autenticación | Jetstream + Fortify |
| Roles y permisos | `spatie/laravel-permission` |
| Excel y PDF | `maatwebsite/excel`, `barryvdh/laravel-dompdf` |
| Mapas | Leaflet + Leaflet-Geoman (CDN), `matanyadaev/laravel-eloquent-spatial` |
| Base de datos | SQLite en desarrollo, PostgreSQL en producción |

## Requisitos

- **PHP:** 8.2 o superior (probado con 8.4), con las extensiones:
  `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `intl`, `mbstring`,
  `pdo_sqlite` (desarrollo), `pdo_pgsql` (producción), `xml` y `zip`.
- **Composer:** versión 2.
- **Node.js y npm:** Node.js 18 o superior (probado con 20).
- **Base de datos:** SQLite para desarrollo. PostgreSQL requiere **PostGIS**,
  porque las tablas `blocks` y `parcels` usan columnas `geometry` y `geography`.

En Windows, [Laravel Herd](https://herd.laravel.com) incluye PHP, Composer y las extensiones necesarias. Comprueba las herramientas instaladas con:

```bash
php -v
php -m
composer -V
node -v
```

> [!TIP]
> **Git Bash + Herd:** si `php` o `composer` aparece como `command not found`, ejecuta los comandos de Herd a través de Windows:

```bash
cmd.exe //c composer -V
cmd.exe //c composer install
cmd.exe //c php artisan --version
```

Aplica el prefijo `cmd.exe //c` a los demás comandos `composer` y `php artisan` de esta guía. `npm` funciona directamente en Git Bash.

## Instalación para desarrollo

Los comandos funcionan en Git Bash, macOS y Linux. En Windows con Git Bash y Herd, aplica la nota anterior a los comandos de PHP y Composer. Cuando un comando cambia en PowerShell, se indica.

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio> carolina
cd carolina
```

### 2. Instalar dependencias

```bash
composer install
npm install
```

### 3. Configurar el entorno

```bash
cp .env.example .env
php artisan key:generate
```

En PowerShell, la primera línea es `Copy-Item .env.example .env`.

### 4. Configurar la base de datos

#### Opción A: SQLite (recomendada para desarrollo)

En `.env`, cambia `DB_CONNECTION=mysql` (el valor que trae `.env.example`) por `DB_CONNECTION=sqlite` y **comenta o borra** las demás líneas `DB_*`.
Si `DB_DATABASE=laravel` queda activa, Laravel busca un archivo llamado `laravel` y falla.

```dotenv
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

Crea el archivo de la base de datos:

```bash
touch database/database.sqlite
```

En PowerShell: `New-Item database/database.sqlite`.

#### Opción B: PostgreSQL

Crea la base de datos con PostGIS habilitado (`CREATE EXTENSION postgis;`) y configura `.env`:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=carolina
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña
```

### 5. Crear las tablas

```bash
php artisan migrate
```

### 6. Cargar los datos iniciales

Ejecuta estos tres pasos en este orden:

```bash
# Roles y permisos (crea el rol "Administrador" y todos los permisos)
php artisan db:seed --class=Permissions

# Primera campaña. El usuario administrador necesita al menos una.
php artisan tinker --execute="App\Models\Category::create(['name' => 'Rivarca']);"

# Usuario administrador con el rol "Administrador"
php artisan db:seed --class=ResetAdmin
```

El correo y la contraseña del administrador están en [database/seeders/ResetAdmin.php](database/seeders/ResetAdmin.php). Cambia la contraseña desde el perfil después de entrar por primera vez.

> [!WARNING]
> **No uses `php artisan db:seed` sin `--class`.** El seeder por defecto falla en una instalación nueva con `Attempt to read property "currentCategory" on null`. Los modelos con campaña (`HasCategory`) toman la campaña del usuario autenticado, y en consola no hay ninguno. Por el mismo motivo, los seeders que importan los Excel de `public/imports/` tampoco funcionan todavía. Está registrado como mejora pendiente.

### 7. Enlazar el almacenamiento público

Este enlace permite mostrar las fotos de perfil:

```bash
php artisan storage:link
```

### 8. Compilar los assets

Elige una opción:

```bash
# Desarrollo: recompila al guardar; deja esta terminal abierta.
npm run dev
```

```bash
# Compilación única; necesaria para correr los tests.
npm run build
```

### 9. Levantar la aplicación

```bash
php artisan serve
```

Abre [http://localhost:8000](http://localhost:8000). La raíz redirige al login.

Con Herd no hace falta `php artisan serve`. Si la carpeta está dentro de un directorio que Herd sirve, la app queda en [http://carolina.test](http://carolina.test). En ese caso pon `APP_URL=http://carolina.test` en `.env`.

## Variables de entorno

| Variable | Descripción |
|:--|:--|
| `APP_URL` | URL base, usada en los enlaces firmados del estado de cuenta público. |
| `APP_ENV` | Con `production` se fuerza HTTPS en todas las URLs. |
| `DB_*` | Conexión a la base de datos (paso 4). |
| `SESSION_DRIVER` | `database` por defecto. La tabla `sessions` se crea con las migraciones. |
| `QUEUE_CONNECTION` | `sync`; actualmente no hay trabajos en cola. |
| `LABSMOBILE_USERNAME`, `LABSMOBILE_PASSWORD` | Credenciales de Labsmobile para enviar SMS y consultar el saldo. No vienen en `.env.example`; agrégalas a mano. Sin ellas, la sección de Notificaciones no puede consultar saldo ni enviar mensajes. |
| `MAIL_*` | Envío de correos (recuperación de contraseña). En desarrollo puedes usar `MAIL_MAILER=log`. |
| `PULSE_ENABLED` | Activa o desactiva Laravel Pulse (`/pulse`). |

## Tests

Los tests usan SQLite en memoria y no modifican tu base de datos. Necesitan los assets compilados; si no, las pantallas de login y registro devuelven error 500 (`Vite manifest not found`).

```bash
npm run build
php artisan test
```

## Comandos útiles

| Comando | Uso |
|:--|:--|
| `php artisan optimize:clear` | Limpia cachés de configuración, rutas y vistas. |
| `vendor/bin/pint` | Formatea el código PHP. |
| `php artisan view:cache` | Compila las vistas Blade y ayuda a detectar errores de sintaxis. |
| `php artisan tinker` | Abre la consola interactiva de Laravel. |

## Despliegue en producción

1. PostgreSQL con PostGIS habilitado.
2. En `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` con `https://`, credenciales de base de datos, Labsmobile y correo.
3. Instala dependencias, compila los assets y prepara la aplicación:

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   php artisan migrate --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Estructura del proyecto

| Carpeta | Contenido |
|:--|:--|
| `app/Livewire/` | Pantallas y componentes, agrupados por módulo (`Buyer`, `Parcel`, `Promise`, `Payment`, `Deed`…). |
| `app/Livewire/Forms/` | Formularios con su validación. |
| `app/Models/` | Modelos. Los que pertenecen a una campaña usan el trait `HasCategory`. |
| `app/Enums/` | Estados, métodos de pago, tipos de documento, etc., con sus etiquetas en español. |
| `app/Exports/`, `app/Imports/` | Reportes y cargas masivas en Excel. |
| `app/Services/Labsmobile/` | Cliente de la API de SMS. |
| `app/Http/Controllers/Api/` | Endpoints que alimentan los selects de búsqueda (`/select/...`). |
| `resources/views/livewire/` | Vistas de cada componente Livewire. |
| `database/seeders/` | Permisos, usuario administrador e importación inicial desde Excel. |
| `public/templates/` | Plantillas de Excel para las importaciones. |

## Problemas comunes

| Problema | Solución |
|:--|:--|
| `Vite manifest not found` | Ejecuta `npm run build` o deja `npm run dev` corriendo. |
| `Attempt to read property "currentCategory" on null` al sembrar datos | Usa los seeders con `--class` del paso 6, no `php artisan db:seed` a secas. |
| `Database file at path [laravel] does not exist` | Con SQLite, comenta `DB_DATABASE` en `.env` (paso 4). |
| Las pantallas no muestran datos después de entrar | El usuario necesita una campaña asignada y el rol "Administrador" (paso 6). |
| El mapa de lotes sale en gris o incompleto | Revisa la consola del navegador. Leaflet y Leaflet-Geoman se cargan desde unpkg.com, así que hace falta conexión a internet. |
