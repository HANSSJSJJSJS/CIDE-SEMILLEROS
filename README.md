# CIDE - Sistema de Gestión de Semilleros
#
# Proyecto desarrollado en Laravel para la gestión de Semilleros de Investigación del CIDE.
# Guía paso a paso para desplegar el proyecto en una máquina nueva.
#
# =====================================================
# PASO 1. REQUISITOS DEL SISTEMA
# =====================================================
# - PHP 8.1 o 8.2
# - Composer
# - MySQL o MariaDB
# - Node.js (requerido para Vite)
# - Git
# - Navegador web (Chrome, Edge, Firefox)
#
# Verificar versiones instaladas:
php -v
composer -V
mysql --version
node -v
npm -v
#
# =====================================================
# PASO 2. CLONAR EL REPOSITORIO
# =====================================================
git clone https://github.com/TU_USUARIO/TU_REPOSITORIO.git
cd CIDE-SEMILLEROS
#
# =====================================================
# PASO 3. INSTALAR DEPENDENCIAS DE PHP
# =====================================================
composer install
#
# Si en Windows se queda cargando o hay error de memoria:
php -d memory_limit=-1 composer install
#
# =====================================================
# PASO 4. CREAR ARCHIVO DE CONFIGURACIÓN (.env)
# =====================================================
# Crear manualmente el archivo .env en la raíz del proyecto
# y configurar los siguientes valores:
#
# APP_NAME=CIDE-Semilleros
# APP_ENV=local
# APP_KEY=
# APP_DEBUG=true
# APP_URL=http://127.0.0.1:8000
# LOG_CHANNEL=stack
# LOG_LEVEL=debug
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=semilleros
# DB_USERNAME=root
# DB_PASSWORD=
#
# =====================================================
# PASO 5. GENERAR CLAVE DE LA APLICACIÓN
# =====================================================
php artisan key:generate
#
# =====================================================
# PASO 6. LIMPIAR CACHÉ DE LARAVEL
# =====================================================
php artisan config:clear
php artisan cache:clear
php artisan view:clear
#
# =====================================================
# PASO 7. CONFIGURAR BASE DE DATOS
# =====================================================
# Crear la base de datos llamada "semilleros" en MySQL
#
php artisan migrate
#
# Si el proyecto incluye seeders:
php artisan migrate --seed
#
# =====================================================
# PASO 8. INSTALAR DEPENDENCIAS FRONTEND (VITE)
# =====================================================
npm install
#
# =====================================================
# PASO 9. LEVANTAR VITE
# =====================================================
npm run dev
#
# Mantener esta consola abierta mientras el proyecto esté en uso
#
# =====================================================
# PASO 10. EJECUTAR LARAVEL
# =====================================================
php artisan serve
#
# =====================================================
# PASO 11. ABRIR EN EL NAVEGADOR (BREXER)
# =====================================================
# Abrir el navegador web y acceder a:
# http://127.0.0.1:8000
#
# =====================================================
# NOTAS IMPORTANTES
# =====================================================
# - El archivo .env nunca debe subirse al repositorio
# - En una máquina nueva siempre debe crearse manualmente
# - Si Vite no está activo, los estilos no cargarán
# - Verificar extensiones PHP:
#   openssl, pdo, pdo_mysql, mbstring, fileinfo, curl
#
# =====================================================
# FIN
# =====================================================

