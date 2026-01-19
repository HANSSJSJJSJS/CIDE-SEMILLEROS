# CIDE – Sistema de Gestión de Semilleros

##  Proyecto desarrollado en Laravel para la gestión de
##  Semilleros de Investigación del CIDE.
####  Guía paso a paso para desplegar el proyecto en una
###  máquina nueva.


###  PASO 1. REQUISITOS DEL SISTEMA
###  Navegador web (Chrome, Edge, Firefox)

###  Verificar versiones:

```bash
 php -v
````

 ```bash
 composer -V
 ```

 ```bash
 mysql --version
 ```

 ```bash
 node -v
 ```

 ```bash
 npm -v
 ```

### PASO 2. CLONAR EL REPOSITORIO

 ```bash
 git clone https://github.com/HANSSJSJJSJS/CIDE-SEMILLEROS.git
 ```

###  PASO 3. ENTRAR AL DIRECTORIO DEL PROYECTO

 ```bash
 cd CIDE-SEMILLEROS
 ```


###  PASO 4. INSTALAR DEPENDENCIAS DE PHP

 ```bash
 composer install
 ```

###  Opcional si hay problemas de memoria en Windows:

 ```bash
 php -d memory_limit=-1 composer install
 ```

###  PASO 5. CREAR ARCHIVO .env

###  Crear manualmente el archivo .env con el siguiente contenido:

```bash
APP_NAME="Semilleros CIDE SENA"
APP_ENV=local
APP_KEY=base64: se tiene que generar 
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# -------------------------------------------------
# BASE DE DATOS
# -------------------------------------------------
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cide_sena
DB_USERNAME=root
DB_PASSWORD=

# -------------------------------------------------
# CACHE / SESSION
# -------------------------------------------------
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# -------------------------------------------------
# CORREO (DESARROLLO - MAILTRAP)
# -------------------------------------------------
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=semillerossenacide@gmail.com
MAIL_PASSWORD=vrbrnoyvprqpnhoc
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=semillerossenacide@gmail.com
MAIL_FROM_NAME="Semillero CIDE"

# -------------------------------------------------
# PASSWORD RESET
# -------------------------------------------------
PASSWORD_RESET_TIMEOUT=60

# -------------------------------------------------
# OTROS
# -------------------------------------------------
TIMEZONE=America/Bogota
LOCALE=es
FALLBACK_LOCALE=es

```

###  PASO 6. GENERAR CLAVE DE LA APLICACIÓN

 ```bash
 php artisan key:generate
 ```

###  PASO 7. LIMPIAR CACHÉ DE LARAVEL
 ```bash
 php artisan config:clear
 php artisan cache:clear
 php artisan view:clear
 ```
###  PASO 8. MIGRACIONES
 ```bash
 php artisan migrate
 ```

####  Si incluye seeders:

```bash
 php artisan migrate --seed
 ```

## # PASO 9. FRONTEND (VITE)

```bash
 npm install
 ```
#
```bash
 npm run dev
 ```


### PASO 10. EJECUTAR LARAVEL

 ```bash
 php artisan serve
 ```


###  PASO 11. ABRIR EN EL NAVEGADOR

```bash
http://127.0.0.1:8000
```


