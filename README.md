# 🌱 CIDE – Sistema de Gestión de Semilleros

Proyecto desarrollado en Laravel para la gestión de Semilleros de Investigación del CIDE.  
Permite administrar semilleros, usuarios, roles y módulos asociados.

Guía paso a paso para desplegar el proyecto en una máquina nueva.

---

## 📋 Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

- 🐘 PHP 8.1 o 8.2
- 📦 Composer
- 🐬 MySQL o MariaDB
- ⚡ Node.js (para Vite)
- 🌿 Git
- 🌐 Navegador web (Chrome, Edge, Firefox)

Verificar versiones instaladas:

```bash php -v ```
```bash composer -V ```
```bash mysql --version ```
```bash node -v ```
```bash npm -v ```

---

## 🛠️ Instrucciones de Instalación

### 1. Clonar el proyecto

```bash git clone https://github.com/TU_USUARIO/TU_REPOSITORIO.git ```

---

### 2. Entrar al directorio del proyecto

```bash cd CIDE-SEMILLEROS ```

---

### 3. Instalar dependencias de PHP

```bash composer install ```

Si en Windows se queda cargando o hay error de memoria:

```bash php -d memory_limit=-1 composer install ```

---

### 4. Crear el archivo de configuración `.env`

Crear el archivo `.env` en la raíz del proyecto con el siguiente contenido:

```bash APP_NAME=CIDE-Semilleros ```
```bash APP_ENV=local ```
```bash APP_KEY= ```
```bash APP_DEBUG=true ```
```bash APP_URL=http://127.0.0.1:8000 ```
```bash LOG_CHANNEL=stack ```
```bash LOG_LEVEL=debug ```
```bash DB_CONNECTION=mysql ```
```bash DB_HOST=127.0.0.1 ```
```bash DB_PORT=3306 ```
```bash DB_DATABASE=semilleros ```
```bash DB_USERNAME=root ```
```bash DB_PASSWORD= ```

---

### 5. Generar la clave de la aplicación

```bash php artisan key:generate ```

---

### 6. Limpiar caché de Laravel

```bash php artisan config:clear ```
```bash php artisan cache:clear ```
```bash php artisan view:clear ```

---

### 7. Configurar la base de datos

Crear la base de datos llamada `semilleros` desde MySQL o phpMyAdmin.

Ejecutar migraciones:

```bash php artisan migrate ```

Si el proyecto incluye datos iniciales:

```bash php artisan migrate --seed ```

---

### 8. Instalar dependencias de frontend (Vite)

```bash npm install ```

---

### 9. Iniciar Vite

```bash npm run dev ```

Mantener esta consola abierta mientras el proyecto esté en ejecución.

---

### 10. Iniciar el servidor de Laravel

```bash php artisan serve ```

---

### 11. Abrir en el navegador (Brexer 🌐)

```bash http://127.0.0.1:8000 ```

---

## 🗂️ Estructura del Proyecto

```bash /CIDE-SEMILLEROS/ ```
```bash ├── app/                    # Lógica principal ```
```bash ├── database/               # Migraciones y seeders ```
```bash ├── resources/              # Vistas Blade, CSS y JS ```
```bash ├── routes/                 # Rutas web ```
```bash ├── public/                 # Archivos públicos ```
```bash ├── .env                    # Configuración (no versionado) ```
```bash ├── artisan                 # CLI de Laravel ```
```bash └── composer.json           # Dependencias ```

---

## 🧰 Tecnologías

- 🐘 Laravel
- 🐬 MySQL / MariaDB
- 🔗 Eloquent ORM
- 🌐 Blade + CSS + JS
- ⚡ Vite
- 🚀 Artisan

---

## ⚠️ Notas Importantes

- El archivo `.env` no debe subirse al repositorio
- En una máquina nueva el `.env` debe crearse manualmente
- Si Vite no está activo, los estilos no cargarán
- Extensiones PHP requeridas:
```bash openssl pdo pdo_mysql mbstring fileinfo curl ```

---

## 📄 Licencia

Proyecto desarrollado con fines académicos e institucionales para la gestión de Semilleros de Investigación del CIDE.

---

## 👨‍💻 Autor

CIDE – Sistema de Semilleros  
Desarrollado por **[Tu nombre]**


