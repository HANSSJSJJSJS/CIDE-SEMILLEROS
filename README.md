🌱 CIDE – Sistema de Gestión de Semilleros

Proyecto desarrollado en Laravel para la gestión de Semilleros de Investigación del CIDE.
Permite administrar semilleros, usuarios, roles y módulos asociados.

Guía paso a paso para desplegar el proyecto en una máquina nueva.

📋 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

🐘 PHP 8.1 o 8.2

📦 Composer

🐬 MySQL o MariaDB

⚡ Node.js (requerido para Vite)

🌿 Git

🌐 Navegador web (Chrome, Edge, Firefox)

Verificar versiones instaladas:

php -v

composer -V

mysql --version

node -v

npm -v

🛠️ Instrucciones de Instalación
1. Clona el proyecto
git clone https://github.com/TU_USUARIO/TU_REPOSITORIO.git

2. Entra en el directorio del proyecto
cd CIDE-SEMILLEROS

3. Instala las dependencias de PHP
composer install

3.1 Opcional – Si hay problemas de memoria en Windows
php -d memory_limit=-1 composer install

4. Crea el archivo de configuración .env

Laravel no incluye el archivo .env por seguridad.
Crea manualmente el archivo .env en la raíz del proyecto con el siguiente contenido:

APP_NAME=CIDE-Semilleros
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=semilleros
DB_USERNAME=root
DB_PASSWORD=

5. Genera la clave de la aplicación
php artisan key:generate

6. Limpia la caché de Laravel
php artisan config:clear

php artisan cache:clear

php artisan view:clear

7. Configura la base de datos

Crea la base de datos llamada semilleros desde MySQL o phpMyAdmin.

Ejecuta las migraciones:

php artisan migrate

7.1 Opcional – Cargar datos iniciales
php artisan migrate --seed

8. Instala dependencias de frontend (Vite)
npm install

9. Inicia Vite
npm run dev


⚠️ Mantén esta consola abierta mientras el proyecto esté en ejecución.

10. Inicia el servidor de Laravel
php artisan serve

11. Abre el proyecto en el navegador (Brexer 🌐)
http://127.0.0.1:8000

🗂️ Estructura del Proyecto
/CIDE-SEMILLEROS/
├── app/                    # Lógica principal de la aplicación
│   ├── Http/
│   │   ├── Controllers/    # Controladores
│   │   └── Middleware/     # Middlewares
│   └── Models/             # Modelos Eloquent
├── database/
│   ├── migrations/         # Migraciones de la base de datos
│   └── seeders/            # Datos iniciales
├── resources/
│   ├── views/              # Vistas Blade
│   ├── css/                # Estilos
│   └── js/                 # Scripts JavaScript
├── routes/
│   └── web.php             # Rutas web
├── public/                 # Archivos públicos
├── .env                    # Configuración del entorno (no versionado)
├── artisan                 # CLI de Laravel
└── composer.json           # Dependencias del proyecto

🧰 Tecnologías
Componente	Tecnología	Descripción
Backend	🐘 Laravel	Framework PHP para aplicaciones web
Base de datos	🐬 MySQL / MariaDB	Sistema de base de datos relacional
ORM	🔗 Eloquent	ORM de Laravel
Frontend	🌐 Blade + CSS + JS	Renderizado del lado del servidor
Assets	⚡ Vite	Gestión y compilación de recursos
Servidor	🚀 Artisan	Servidor de desarrollo local
⚠️ Notas Importantes

El archivo .env no debe subirse al repositorio

En una máquina nueva el .env debe crearse manualmente

Si Vite no está activo, los estilos no cargarán

Verifica que PHP tenga habilitadas las extensiones:

openssl

pdo

pdo_mysql

mbstring

fileinfo

curl

📄 Licencia

Proyecto desarrollado con fines académicos e institucionales para la gestión de Semilleros de Investigación del CIDE.

👨‍💻 Autor

CIDE – Sistema de Semilleros
Desarrollado por [Tu nombre]

