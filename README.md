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
├── .env.example                      # Variables de entorno de ejemplo
├── .github/
│   └── workflows/
│       └── deploy_a_dockerhub_railway.yml  # CI/CD a DockerHub y Railway
├── .gitignore
├── README.md                         # Este archivo
├── Dockerfile
├── docker-compose.yml
├── docker/
│   ├── apache.conf                   # Configuración de Apache
│   └── entrypoint.sh                 # Script de arranque del contenedor
├── composer.json                     # Dependencias de Composer
├── composer.lock
├── phinx.php                         # Configuración de Phinx
├── jsconfig.json
├── bin/
│   └── sync-titles.php               # Script CLI para poblar la DB desde TMDB
├── db/
│   └── migrations/                   # Migraciones de Phinx
│       ├── 20260506125601_create_filmatrix_schema.php
│       ├── 20260506130000_create_films_lists.php
│       └── 20260517000000_rename_films_lists_to_title_lists.php
├── doc/
│   ├── DER.md
│   ├── PROJECT_OVERVIEW.md
│   └── imgs/
│       ├── logoPAW.svg
│       ├── sitemap.png
│       └── Tp_integrador-DERsvg.svg  # Diagrama entidad-relación
├── public/                           # Web root (Apache apunta aquí)
│   ├── .htaccess                     # Reescritura de URLs al front controller
│   ├── favicon.ico
│   ├── index.php                     # Front controller
│   └── assets/
│       ├── css/
│       │   ├── auth.css
│       │   ├── base.css              # Tokens y estilos globales
│       │   ├── detalle_pelicula.css
│       │   ├── editar_perfil.css
│       │   ├── films.css
│       │   ├── footer.css
│       │   ├── header.css
│       │   ├── hero.css
│       │   ├── home.css
│       │   ├── miPerfil.css
│       │   ├── movie-card.css
│       │   ├── title-card.css
│       │   ├── title-detail.css
│       │   ├── titles.css
│       │   └── watchlist.css
│       ├── img/
│       │   ├── filmatrix_isotipo.webp
│       │   ├── Filmatrix_logo.png
│       │   ├── Filmatrix_logo.webp
│       │   ├── hero-bg.webp
│       │   ├── tmdb_logo.svg
│       │   └── user_avatar.png
│       └── js/
│           ├── app.js                # Entry point JS
│           ├── modules/
│           │   ├── CatalogFilters.js
│           │   ├── NavMenu.js
│           │   ├── ReviewEdit.js
│           │   ├── SearchToggle.js
│           │   ├── Toast.js
│           │   ├── utils.js
│           │   └── WatchlistActions.js
│           └── pages/
│               ├── home.js
│               ├── TitleDetails.js
│               └── Titles.js
├── src/
│   ├── bootstrap.php                 # Composición del contenedor, rutas y arranque
│   ├── Controllers/
│   │   ├── ErrorController.php       # Manejo de errores HTTP (404, 500)
│   │   ├── PageController.php        # Páginas estáticas (home, etc.)
│   │   ├── ReviewController.php      # CRUD de reseñas
│   │   ├── TitleController.php       # Catálogo y detalle de títulos
│   │   ├── UserController.php        # Perfil, registro y autenticación
│   │   └── WatchlistController.php   # Gestión de watchlist
│   ├── Core/
│   │   ├── Config.php                # Lectura de variables de entorno
│   │   ├── Request.php               # Abstracción de la petición HTTP
│   │   ├── Router.php                # Enrutador HTTP
│   │   ├── Database/
│   │   │   └── ConnectionBuilder.php # Construcción de la conexión PDO
│   │   ├── Exceptions/               # Excepciones de dominio
│   │   │   ├── EmailAlreadyTakenException.php
│   │   │   ├── InvalidPasswordException.php
│   │   │   ├── InvalidValueFormatException.php
│   │   │   ├── ReviewAlreadyExistException.php
│   │   │   ├── RouteNotFoundException.php
│   │   │   ├── TmdbApiException.php
│   │   │   ├── UsernameAlreadyExistsException.php
│   │   │   ├── UserNotFoundException.php
│   │   │   ├── WatchlistItemAlreadyExistsException.php
│   │   │   └── WatchlistItemNotFoundException.php
│   │   └── Traits/
│   │       └── Loggable.php          # Trait para inyectar logger Monolog
│   ├── Infrastructure/
│   │   └── Tmdb/
│   │       └── TmdbClient.php        # Cliente HTTP para la API de TMDB
│   ├── Middleware/
│   │   └── AuthMiddleware.php        # Protección de rutas autenticadas
│   ├── Models/                       # DTOs y entidades de dominio
│   │   ├── CatalogQuery.php
│   │   ├── CatalogResult.php
│   │   ├── Genre.php
│   │   ├── People.php
│   │   ├── Review.php
│   │   ├── Title.php
│   │   ├── TitleCardDto.php
│   │   ├── User.php
│   │   ├── WatchlistEntry.php
│   │   └── WatchlistItem.php
│   ├── Repository/                   # Acceso a datos (solo SQL con PDO)
│   │   ├── GenreRepository.php
│   │   ├── PeopleRepository.php
│   │   ├── ReviewRepository.php
│   │   ├── TitleListRepository.php
│   │   ├── TitleRepository.php
│   │   ├── UserRepository.php
│   │   └── WatchlistRepository.php
│   └── Services/                     # Lógica de negocio
│       ├── AuthService.php
│       ├── GenreService.php
│       ├── PeopleService.php
│       ├── ReviewService.php
│       ├── TitleListService.php
│       ├── TitleService.php
│       ├── UserService.php
│       └── WatchlistService.php
├── storage/
│   ├── cache/                        # Caché de Twig
│   ├── logs/
│   │   └── app.log
│   └── uploads/
├── tests/                            # (pendiente)
└── views/                            # Plantillas Twig
    ├── layout/
    │   └── main.html.twig            # Layout base
    ├── macros/
    │   └── title-cards.html.twig     # Macro reutilizable de tarjetas
    ├── pages/
    │   ├── change-password.html.twig
    │   ├── edit-profile.html.twig
    │   ├── error-404.html.twig
    │   ├── error-500.html.twig
    │   ├── home.html.twig
    │   ├── login.html.twig
    │   ├── my-reviews.html.twig
    │   ├── profile.html.twig
    │   ├── register.html.twig
    │   ├── title-detail.html.twig
    │   ├── titles.html.twig
    │   └── watchlist.html.twig
    └── partials/
        ├── footer.html.twig
        └── header.html.twig
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
docker compose exec app php bin/sync-titles.php --section=popular --pages=3
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
