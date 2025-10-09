#  🌿  CIDE-SEMILLEROS

## 📋 Requisitos Previos

"listar requerimientos antes de ejecutar el proyecto "
Antes de comenzar, asegúrate de tener instalado:

Herramienta	Versión recomendada	Descripción
PHP		8.1 o superior		Lenguaje principal usado por Laravel.
Composer	Última versión		Gestor de dependencias de PHP.
MySQL 		5.7+ 			Base de datos típica para Laravel.
Node.js + NPM	Node 18+		Para compilar los assets (CSS, JS, etc.).
Git	—				Para clonar y administrar el repositorio.




" listar el  paso a paso para ejecutar el poryecto de manera local "


## 🛠️ Instrucciones de Instalación


#### 1. Clona el proyecto

```bash
  git clone https://github.com/HANSSJSJJSJS/CIDE-SEMILLEROS.git
```

#### 2. 

```bash
ingresar paso 
```

#### 3. 

```bash
ingresar paso 
```








## 📋 guia de caerpetas 
```bash
📁 CIDE-SEMILLEROS/
│
├── 📁 app/
│   ├── 📁 Console/            → Comandos personalizados (Artisan)
│   ├── 📁 Exceptions/         → Manejo de errores
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/    → Controladores (lógica de rutas)
│   │   ├── 📁 Middleware/     → Filtros de autenticación, etc.
│   │   └── Kernel.php         → Registro de middlewares
│   ├── 📁 Models/             → Modelos (Eloquent ORM)
│   └── 📁 Providers/          → Configuración de servicios
│
├── 📁 bootstrap/
│   ├── app.php                → Inicializa Laravel
│   └── 📁 cache/              → Cache de compilación
│
├── 📁 config/
│   ├── app.php                → Configuración general
│   ├── database.php           → Conexión a la BD
│   ├── mail.php               → Configuración de correo
│   ├── auth.php               → Autenticación
│   └── ...                    → Otros archivos de configuración
│
├── 📁 database/
│   ├── 📁 migrations/         → Migraciones (estructura de tablas)
│   ├── 📁 seeders/            → Datos iniciales (usuarios, roles, etc.)
│   └── 📁 factories/          → Generadores de datos falsos (testing)
│
├── 📁 public/
│   ├── index.php              → Punto de entrada del proyecto
│   ├── 📁 css/                → Archivos de estilo
│   ├── 📁 js/                 → Scripts compilados
│   ├── 📁 images/             → Imágenes públicas
│   └── 📁 storage/ (link simbólico)
│
├── 📁 resources/
│   ├── 📁 views/              → Plantillas Blade (.blade.php)
│   ├── 📁 lang/               → Archivos de idioma (es, en, etc.)
│   ├── 📁 js/                 → Scripts del frontend
│   ├── 📁 sass/               → Estilos fuente (SASS)
│   └── 📁 components/         → Componentes reutilizables (opcional)
│
├── 📁 routes/
│   ├── web.php                → Rutas web (HTML / vistas)
│   ├── api.php                → Rutas API (JSON / AJAX)
│   ├── console.php            → Comandos artisan
│   └── channels.php           → Canales broadcast (notificaciones)
│
├── 📁 storage/
│   ├── 📁 app/                → Archivos cargados por el usuario
│   ├── 📁 framework/          → Cache, sesiones, vistas compiladas
│   └── 📁 logs/               → Registro de errores (laravel.log)
│
├── 📁 tests/                  → Pruebas automáticas
│
├── 📁 vendor/                 → Dependencias instaladas por Composer
│
├── .env                       → Configuración del entorno (base de datos, mail, etc.)
├── artisan                    → CLI de Laravel (php artisan ...)
├── composer.json              → Dependencias PHP
├── package.json               → Dependencias frontend (NPM)
├── vite.config.js             → Configuración de compilación frontend
└── README.md                  → Documentación del proyecto

```


## 🧩 Flujo de datos (Laravel ↔ PostgreSQL)
```bash


┌──────────────────────────────┐
│         Usuario / Cliente     │
│ (Navegador o API Request)    │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│        public/               │
│ - index.php (punto de entrada)│
│ - CSS / JS / imágenes         │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│        routes/               │
│ - web.php  → rutas web        │
│ - api.php  → rutas API        │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│   app/Http/Controllers/      │
│ - Reciben la petición        │
│ - Aplican lógica de negocio  │
│ - Llaman a los modelos       │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│       app/Models/            │
│ - Representan tablas de BD   │
│ - Usan Eloquent ORM          │
│ - Consultan database/        │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│       database/              │
│ - migrations/ → estructura   │
│ - seeders/ → datos iniciales │
│ - factories/ → datos de test │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│        config/ + .env        │
│ - Conexión BD, correo, etc.  │
│ - Variables del entorno      │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│       resources/views/       │
│ - Vistas Blade (HTML)        │
│ - Forman la respuesta final  │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│         storage/             │
│ - logs/ → errores del sistema│
│ - framework/ → cache, sesiones│
│ - app/public → archivos del usuario │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│         vendor/              │
│ - Librerías de Composer      │
│ - Framework Laravel completo │
