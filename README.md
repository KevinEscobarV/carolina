# Carolina

> Sistema de gestión de loteos organizado por campañas.

Carolina permite administrar compradores, manzanas, lotes, promesas de compraventa, pagos y escrituras. También incluye proyección de cuotas (amortización), reportes en Excel, recordatorios de pago por SMS (Labsmobile) y una consulta pública para descargar estados de cuenta en PDF.

## Índice

- [Tecnologías](#tecnologías)
- [Requisitos](#requisitos)
- [Instalación para desarrollo](#instalación-para-desarrollo)
- [Variables de entorno](#variables-de-entorno)
- [Homepage](#homepage)
- [Proyección de pagos y mora](#proyección-de-pagos-y-mora)
- [Tests](#tests)
- [Comandos útiles](#comandos-útiles)
- [Despliegue en producción](#despliegue-en-producción)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Problemas comunes](#problemas-comunes)

## Tecnologías

| Área | Tecnologías |
|:--|:--|
| Backend | PHP 8.3+, Laravel 13 |
| Interfaz | Livewire 3, Alpine.js, WireUI, Tailwind CSS 3, Vite 5 |
| Autenticación | Jetstream + Fortify |
| Roles y permisos | `spatie/laravel-permission` |
| Excel y PDF | `maatwebsite/excel`, `barryvdh/laravel-dompdf` |
| Mapas | Leaflet + Leaflet-Geoman (CDN), `matanyadaev/laravel-eloquent-spatial` |
| Base de datos | SQLite en desarrollo, MariaDB 10.11 en producción |

## Requisitos

- **PHP:** 8.3 o superior (probado con 8.4), con las extensiones:
  `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `intl`, `mbstring`,
  `pdo_sqlite` (desarrollo), `pdo_mysql` (producción), `xml` y `zip`.
- **Composer:** versión 2.
- **Node.js y npm:** Node.js 18 o superior (probado con 20).
- **Base de datos:** SQLite para desarrollo y MariaDB 10.11 en producción. Las tablas `blocks` y `parcels`
  usan columnas espaciales (los mapas), que MariaDB y MySQL traen de serie. PostgreSQL también funciona,
  pero necesita la extensión **PostGIS**.

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

#### Opción B: MariaDB (igual que producción)

Crea la base de datos en `utf8mb4` (`CREATE DATABASE carolina CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`) y configura `.env`:

```dotenv
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=carolina
DB_USERNAME=root
DB_PASSWORD=tu_contraseña
DB_TIMEZONE=-05:00
```

Con Docker: `docker run -d --name carolina-mariadb -e MARIADB_ROOT_PASSWORD=root -e MARIADB_DATABASE=carolina -p 3306:3306 mariadb:10.11 --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci`.

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

El correo del administrador está en [database/seeders/ResetAdmin.php](database/seeders/ResetAdmin.php). La contraseña sale de `ADMIN_PASSWORD`; si está vacía, el seeder genera una aleatoria y la muestra en la consola. Si el usuario ya existe, el seeder le restablece la contraseña. Cámbiala desde el perfil después de entrar.

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

Abre [http://localhost:8000](http://localhost:8000). La raíz es el homepage público; el panel está en `/login`.

Con Herd no hace falta `php artisan serve`. Si la carpeta está dentro de un directorio que Herd sirve, la app queda en [http://carolina.test](http://carolina.test). En ese caso pon `APP_URL=http://carolina.test` en `.env`.

## Variables de entorno

| Variable | Descripción |
|:--|:--|
| `APP_URL` | URL base de la aplicación. |
| `ADMIN_PASSWORD` | Contraseña que asigna el seeder `ResetAdmin`. Vacía: se genera una aleatoria. |
| `APP_ENV` | Con `production` se fuerza HTTPS en todas las URLs. |
| `DB_*` | Conexión a la base de datos (paso 4). |
| `DB_TIMEZONE` | Zona horaria de la conexión a MariaDB/MySQL. En producción, `-05:00` (Colombia, sin horario de verano): las columnas `TIMESTAMP` se convierten según la zona de la conexión, y la del servidor puede tener horario de verano. |
| `SESSION_SECURE_COOKIE` | `true` en producción: la cookie de sesión solo viaja por HTTPS. |
| `TRUSTED_PROXIES` | Proxies cuyos encabezados `X-Forwarded-*` se creen: `*` detrás de un balanceador (por ejemplo, DigitalOcean App Platform). Vacía cuando la app se sirve directo, como en el cPanel de producción. |
| `SESSION_DRIVER` | `database` por defecto. La tabla `sessions` se crea con las migraciones. |
| `QUEUE_CONNECTION` | `sync`; actualmente no hay trabajos en cola. |
| `LABSMOBILE_USERNAME`, `LABSMOBILE_PASSWORD` | Credenciales de Labsmobile para enviar SMS y consultar el saldo. No vienen en `.env.example`; agrégalas a mano. Sin ellas, la sección de Notificaciones no puede consultar saldo ni enviar mensajes. |
| `MAIL_*` | Envío de correos (recuperación de contraseña). En desarrollo puedes usar `MAIL_MAILER=log`. |
| `PULSE_ENABLED` | Activa o desactiva Laravel Pulse (`/pulse`). |
| `HOME_SAMPLE_CONTENT` | `true` (por defecto) muestra en el homepage el aviso "Vista previa: algunas fotos y textos son de ejemplo". Ponla en `false` cuando el contenido sea el real. |

## Homepage

La raíz (`/`) es la página pública de la empresa: proyectos, cómo comprar, ubicación, contacto, la consulta del estado de cuenta y el enlace al panel administrativo.

- **Cifras** (proyectos, lotes, lotes vendidos, familias, m², disponibilidad por proyecto): salen de la base de datos, de todas las campañas, y se guardan en caché 10 minutos.
- **Textos, contacto y datos de cada proyecto** (descripción, características, foto, coordenadas del mapa): están en [config/home.php](config/home.php). Los proyectos se relacionan con las campañas por su nombre; una campaña sin entrada ahí aparece con una foto y un texto genéricos. Los valores marcados `EJEMPLO` esperan los datos reales.
- **Fotos**: en `public/img/home/`, en WebP. Las actuales son de ejemplo (Unsplash). Para el enlace compartido en redes se usa `og.jpg` (1200 × 630).
- El estado de cuenta se busca con el componente Livewire `Public\Buyer\Statement\Lookup`, que usan tanto el homepage como `/personal-account-statement`.

## Proyección de pagos y mora

Cada promesa tiene su tabla de amortización en la tabla `quotas`: una fila por cuota, en pesos enteros, con fecha, valor, interés, capital, saldo y si es extraordinaria o un abono.

- **Cálculo** ([app/Services/Amortization](app/Services/Amortization)): `ScheduleCalculator` genera la tabla (por número de cuotas o por valor de cuota, con cuotas extraordinarias opcionales) y la recalcula cuando se cambia una cuota. Sistema de cuota fija; la última cuota cierra el saldo en cero. Las fechas no se desbordan (31 de enero, 28 de febrero, 31 de marzo).
- **Interés**: cada promesa guarda la tasa y su tipo, mensual o efectiva anual; para cada periodicidad se usa la tasa equivalente. En la periodicidad irregular (calendario libre) el interés corre por días.
- **Al cambiar una cuota**: las siguientes se ajustan según la opción de abonos de la promesa (`switch_payment`): reducir el número de cuotas o reducir su valor. Al cambiar una fecha, las siguientes se corren con la periodicidad.
- **Pagos y mora, por montos**: lo pagado (sin la cuota inicial) cubre las cuotas en orden. Una cuota está en mora cuando pasaron su vencimiento y los días de gracia de la campaña, y lo pagado no la cubre; las diferencias menores de $1.000 (redondeo) no cuentan. Esto alimenta Notificaciones (SMS), el informe de cartera, el autocompletado de pagos y las cuotas pagadas de los listados y del estado de cuenta.
- **Abonos**: lo pagado por encima de lo vencido se puede aplicar como abono a capital («Aplicar abono» en la proyección): queda una fila de abono y las cuotas siguientes se ajustan según la opción de la promesa.
- **Configuración**: los días de gracia (por campaña) y la tasa de usura vigente (nacional, se actualiza cada mes) están en Configuración. El formulario de promesa avisa si la tasa supera la de usura.
- Mientras exista la columna `promises.projection` (JSON), cada cambio de la proyección también se copia ahí como respaldo; ningún módulo la lee.

## Tests

Los tests usan SQLite en memoria y no modifican tu base de datos. Necesitan los assets compilados; si no, la pantalla de login devuelve error 500 (`Vite manifest not found`).

```bash
npm run build
php artisan test
```

Para correrlos contra MariaDB, como en producción, crea una base vacía (por ejemplo, `carolina_test`) y pasa la conexión por variables de entorno:

```bash
DB_CONNECTION=mariadb DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=carolina_test DB_USERNAME=root DB_PASSWORD=root DB_TIMEZONE=-05:00 php artisan test
```

## Comandos útiles

| Comando | Uso |
|:--|:--|
| `php artisan optimize:clear` | Limpia cachés de configuración, rutas y vistas. |
| `vendor/bin/pint` | Formatea el código PHP. |
| `php artisan view:cache` | Compila las vistas Blade y ayuda a detectar errores de sintaxis. |
| `php artisan tinker` | Abre la consola interactiva de Laravel. |

## Despliegue en producción

Producción corre en un hosting compartido con cPanel (iFastNet, CloudLinux, PHP 8.4, MariaDB 10.11), en el dominio principal. La raíz del sitio es la carpeta `public/` de una copia del repositorio. No hay despliegue automático: cada actualización se publica a mano por SSH.

Los datos del servidor (usuario, host, puerto SSH, contraseñas) no van aquí, porque este repositorio es público.

> [!NOTE]
> Hasta el 6 de noviembre de 2026 la app corría en DigitalOcean App Platform con PostgreSQL y PostGIS. Ese día se migró a este hosting y a MariaDB: los datos se copiaron con un script que comparó fila por fila y los totales de dinero. Por eso el código conserva las ramas para `pgsql` y la variable `TRUSTED_PROXIES`.

### Lo que git no trae

| Qué | Cómo llega al servidor |
|:--|:--|
| `vendor/` | `composer install --no-dev --optimize-autoloader` en el servidor |
| `public/build/` | `npm run build` en tu PC y `scp` |
| `.env` | Se escribe a mano en el servidor, una sola vez |

### Primera vez

1. **PHP 8.4.** El servidor usa alt-php. Para la web, elige 8.4 en *Select PHP Version* de cPanel (o `selectorctl --set-user-current=8.4` por SSH). La consola no sigue ese selector: pon `/opt/alt/php84/usr/bin` al principio del `PATH` en `~/.bashrc`, que también trae `composer`.
2. **Código:** `git clone https://github.com/KevinEscobarV/carolina.git ~/apps/carolina` y, dentro, `composer install --no-dev --optimize-autoloader`.
3. **Raíz del sitio.** En el dominio principal la raíz es `public_html` y cPanel no deja cambiarla. Renómbrala, crea un enlace en su lugar y pasa a la app los archivos de PHP que dejó cPanel:

   ```bash
   cd ~ && mv public_html public_html.cpanel-orig && ln -s apps/carolina/public public_html
   cp -p public_html.cpanel-orig/.user.ini public_html.cpanel-orig/php.ini apps/carolina/public/
   printf '/public/.user.ini\n/public/php.ini\n/public/.well-known/\n/public/cgi-bin/\n' >> apps/carolina/.git/info/exclude
   ```

   `.well-known/` es donde AutoSSL deja sus archivos de validación; con la exclusión, `git status` queda limpio.
4. **Base de datos** MariaDB y su usuario, desde cPanel. Pásala a `utf8mb4`, porque el servidor crea las bases en `latin1`:

   ```sql
   ALTER DATABASE <base> CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

5. **`.env`** (permisos `600`), con estas diferencias frente al de desarrollo:

   | Variable | Valor |
   |:--|:--|
   | `APP_ENV` / `APP_DEBUG` / `APP_URL` | `production` / `false` / `https://loteoscarolinariveros.com` |
   | `LOG_CHANNEL` / `LOG_LEVEL` | `daily` (guarda 14 días) / `info` |
   | `DB_CONNECTION`, `DB_HOST`, `DB_PORT` | `mariadb`, `localhost`, `3306` |
   | `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Los de la base creada en cPanel |
   | `DB_TIMEZONE` | `-05:00` (ver [Variables de entorno](#variables-de-entorno)) |
   | `SESSION_SECURE_COOKIE` | `true` |
   | `TRUSTED_PROXIES` | Sin definir |
   | `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT` | `smtp`, `mail.loteoscarolinariveros.com`, `465` |
   | `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` | El buzón `no-reply@loteoscarolinariveros.com` del cPanel |
   | `LABSMOBILE_USERNAME`, `LABSMOBILE_PASSWORD` | Las credenciales de Labsmobile |

6. **Arranque**, con el build ya subido (ver *Cada actualización*). En una instalación nueva, carga además los datos iniciales del [paso 6](#6-cargar-los-datos-iniciales) con `--force`:

   ```bash
   php artisan migrate --force
   php artisan storage:link
   php artisan optimize
   ```

7. **Copias de seguridad** (ver más abajo).

### HTTPS y direcciones

El certificado lo emite y renueva AutoSSL de cPanel. De `http://` a `https://`, y de `www.` al dominio principal, redirige la aplicación misma (`RedirectToHttps` y `RedirectToAppHost`), siempre que `APP_URL` empiece por `https://`. La opción *Forzar redirección HTTPS* de cPanel no está disponible en este hosting.

> [!WARNING]
> El proxy del hosting toma como IP del visitante la que este declare en `X-Forwarded-For`, así que cualquiera puede falsear su IP. No se arregla desde la app. Por eso el login, además del límite por correo e IP, tiene uno solo por correo (10 intentos por minuto). Cualquier límite nuevo que dependa de la IP se puede eludir en este hosting.

### Cada actualización

En tu PC, con `main` al día:

```bash
npm run build
php artisan test
scp -P <puerto> -r public/build <usuario>@<servidor>:~/apps/carolina/public/
```

`scp` copia encima sin borrar, así que el sitio sigue funcionando mientras sube. En Windows, usa el `ssh.exe` y el `scp.exe` del sistema (`C:\Windows\System32\OpenSSH\`) si tu llave está en el agente de Windows: los de Git Bash no lo ven.

En el servidor:

```bash
cd ~/apps/carolina
php artisan down
git pull
composer install --no-dev --optimize-autoloader   # si cambió composer.lock
php artisan migrate --force                       # si hay migraciones nuevas
php artisan optimize
php artisan up
```

`php artisan optimize` vuelve a leer `.env` y `config/`: sin él, el servidor sigue con la configuración y las rutas viejas.

### Copias de seguridad

Un cron del servidor saca un dump comprimido de la base todos los días a las 3:15 (hora del servidor) en `~/backups/db/` y borra los de más de 30 días. El script vive en `~/bin/backup-carolina-db.sh` y lee la conexión de un archivo de cliente MySQL con permisos `600` (`~/.carolina-db.cnf`, con `user`, `password` y `host`):

```bash
#!/bin/bash
set -euo pipefail
DEST="$HOME/backups/db"
FILE="$DEST/carolina-$(date +%Y%m%d-%H%M).sql.gz"
mysqldump --defaults-extra-file="$HOME/.carolina-db.cnf" --single-transaction \
  --default-character-set=utf8mb4 --no-tablespaces <base> | gzip -9 > "$FILE.tmp"
mv "$FILE.tmp" "$FILE"
find "$DEST" -name 'carolina-*.sql.gz' -mtime +30 -delete
echo "$(date '+%F %T') OK $(basename "$FILE") $(du -h "$FILE" | cut -f1)"
```

```cron
MAILTO=""
15 3 * * * $HOME/bin/backup-carolina-db.sh >> $HOME/backups/backup.log 2>&1
```

Instala el cron desde un archivo (`crontab archivo.txt`): en este hosting, `crontab -` desde una tubería no lo guarda.

Para restaurar una copia (reemplaza las tablas):

```bash
php artisan down
gunzip -c ~/backups/db/carolina-AAAAMMDD-HHMM.sql.gz | mysql --defaults-extra-file=~/.carolina-db.cnf <base>
php artisan up
```

Las copias solo están en el servidor: descarga alguna de vez en cuando.

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
| "Página expirada" (419) al iniciar sesión en producción | Entraste por `http://`: con `SESSION_SECURE_COOKIE=true` la cookie solo viaja por HTTPS. Revisa que `APP_URL` empiece por `https://` para que la app redirija. |
| Un cambio en `.env` no se nota en el servidor | La configuración está en caché: corre `php artisan optimize`. |
| Las horas de creación aparecen corridas en producción | Falta `DB_TIMEZONE=-05:00` en `.env`. |
| Las pantallas no muestran datos después de entrar | El usuario necesita una campaña asignada y el rol "Administrador" (paso 6). |
| El mapa de lotes sale en gris o incompleto | Revisa la consola del navegador. Leaflet y Leaflet-Geoman se cargan desde unpkg.com, así que hace falta conexión a internet. |
