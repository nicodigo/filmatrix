# Filmatrix

Aplicación web de catálogo de películas y series, desarrollada como proyecto integrador de la materia Programación en Ambiente Web (UNLu, 2026). Se encuentra en etapa MVP.

## Descripción

Filmatrix permite a los usuarios explorar un catálogo de títulos (películas y series) obtenidos desde la API de TMDB, consultar detalles, leer y escribir reseñas, y gestionar su perfil. Está orientada a estudiantes y docentes de la materia como caso de estudio práctico de una aplicación web con arquitectura MVC, sin frameworks externos.

## Tecnologías

- PHP 8.2+
- PostgreSQL
- Composer
- Phinx (migraciones)
- Monolog (logging)
- HTML / CSS / JavaScript vanilla

## Diagrama Entidad-Relacion

![DER](doc/imgs/Tp_integrador-DERsvg.svg)

## Estructura del proyecto

```
.
├── .env.example                 # Variables de entorno de ejemplo
├── README.md                    # Este archivo
├── composer.json                # Dependencias de Composer
├── composer.lock                # Lock de dependencias
├── phinx.php                    # Configuración de Phinx
├── bootstrap.php                # Punto de entrada de la aplicación (carga de rutas, contenedor, etc.)
├── public/
│   └── index.php                # Front controller
├── src/
│   ├── Controllers/
│   │   ├── ErrorController.php  # Manejo de errores HTTP (404, 500)
│   │   └── UserController.php   # Acciones relacionadas con el usuario (perfil, etc.)
│   ├── Core/
│   │   ├── Config.php           # Configuración de la aplicación (rutas, DB, etc.)
│   │   ├── Router.php           # Enrutador HTTP
│   │   └── Traits/
│   │       └── Loggable.php     # Trait para inyectar logger Monolog
│   ├── Models/
│   │   ├── Genre.php            # Modelo de género
│   │   ├── People.php           # Modelo de persona (actor/director)
│   │   ├── Review.php           # Modelo de reseña
│   │   ├── Title.php            # Modelo de título (película/serie)
│   │   └── User.php             # Modelo de usuario
│   ├── Repository/
│   │   ├── CatalogListRepository.php  # Repositorio de listas de catálogo
│   │   ├── ReviewRepository.php       # Repositorio de reseñas
│   │   └── UserRepository.php         # Repositorio de usuarios
│   └── Services/
│       ├── TitleService.php     # Lógica de negocio para títulos
│       └── UserService.php      # Lógica de negocio para usuarios
├── storage/
│   └── logs/
│       └── .gitkeep             # Directorio de logs (Monolog)
├── doc/
│   └── imgs/
│       ├── sitemap.png          # Mapa del sitio
│       └── Tp_integrador-DERsvg.svg  # Diagrama entidad-relación
└── migrations/                  # Migraciones de Phinx
    └── (archivos de migración)
```

## Requisitos previos

- PHP 8.2 o superior
- PostgreSQL (servidor en ejecución)
- Composer

## Instalación y ejecución

1. Clonar el repositorio:

```bash
git clone <url-del-repositorio> filmatrix
cd filmatrix
```

2. Copiar el archivo de variables de entorno y editarlo con los datos de conexión a la base de datos:

```bash
cp .env.example .env
```

**Editar las variables `DB_USERNAME`, `DB_PASSWORD` y, si corresponde, `DB_HOSTNAME`, `DB_DBNAME`, `DB_PORT`  , `TMDB_READ_ACCESS_TOKEN`.**

3. Iniciar el servidor de desarrollo:

```bash
 docker compose up --build
```
La aplicación estará disponible en `http://localhost:8000`.

4. (Opcional) poblar DB desde tmdb:
```bash
docker compose exec app php bin/sync_catalog.php --section=popular --pages=3
```


## Para desarrolladores

### Agregar nuevas rutas

Las rutas se definen en `bootstrap.php` mediante el método `loadRoutes()` del enrutador. Ejemplo:

```php
$router->loadRoutes('/perfil', [UserController::class, 'showProfile']);
```

### Agregar controladores, servicios y repositorios

- **Controladores**: en `src/Controllers/`. Deben ser clases con métodos públicos que reciban un objeto `Request` y devuelvan una respuesta.
- **Servicios**: en `src/Services/`. Contienen la lógica de negocio y son llamados desde los controladores.
- **Repositorios**: en `src/Repository/`. Encapsulan el acceso a la base de datos mediante PDO. Nunca se debe escribir SQL fuera de esta capa.

### Migraciones con Phinx

Para crear una nueva migración:

```bash
vendor/bin/phinx create NombreDeLaMigracion
```

Editar el archivo generado en `migrations/` implementando los métodos `change()`, `up()` y `down()` según corresponda.

### Lógica de negocio vs. lógica de controlador

- Los controladores se encargan de recibir la petición, validar datos de entrada, llamar al servicio correspondiente y devolver la respuesta (renderizado de vista o redirección).
- Los servicios contienen la lógica de negocio (cálculos, reglas, orquestación de repositorios).
- Los repositorios solo realizan operaciones CRUD sobre la base de datos.

### Logging con Monolog

El trait `Loggable` inyecta un logger PSR-3. Dentro de cualquier clase que lo use:

```php
$this->logger->info('Mensaje informativo');
$this->logger->error('Error al procesar', ['exception' => $e]);
```

### Autenticación por sesión

La aplicación utiliza sesiones nativas de PHP. El estado de autenticación se almacena en `$_SESSION['user_id']`. Los controladores verifican la existencia de este valor para determinar si el usuario está logueado.

### Restricciones de SQL

No se debe escribir SQL directamente en controladores ni servicios. Todo el acceso a datos debe realizarse a través de los repositorios, que utilizan sentencias preparadas con PDO.

## Integrantes
- Ausqui Mateo
- Cacciatore Bautista
- Huici Nicolás
- Jaime Leandro
