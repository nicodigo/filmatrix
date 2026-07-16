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
- Twig (motor de plantillas)
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
│   ├── migrations/                   # Migraciones de Phinx
│   │   ├── 20260506125601_create_filmatrix_schema.php
│   │   ├── 20260506130000_create_films_lists.php
│   │   ├── 20260517000000_rename_films_lists_to_title_lists.php
│   │   ├── 20260619135529_add_tmdb_vote_average_to_titles.php
│   │   ├── 20260620140001_create_upcoming_releases.php
│   │   ├── 20260624144101_add_release_date_to_titles_and_drop_upcoming_releases.php
│   │   ├── 20260712000000_create_login_attempts.php
│   │   ├── 20260713000000_create_review_reports.php
│   │   └── 20260714000000_create_api_tokens.php
│   └── seeds/
│       └── AdminUserSeed.php          # Seeder para usuario admin inicial
├── doc/
│   ├── DER.md                         # Documentación del esquema de BD
│   ├── PROJECT_OVERVIEW.md            # Visión general del proyecto
│   └── imgs/
│       ├── logoPAW.svg
│       ├── sitemap.png
│       └── Tp_integrador-DERsvg.svg   # Diagrama entidad-relación
├── public/                            # Web root (Apache apunta aquí)
│   ├── .htaccess                      # Reescritura de URLs al front controller
│   ├── favicon.ico
│   ├── index.php                      # Front controller
│   ├── robots.txt
│   └── assets/
│       ├── css/
│       │   ├── about.css
│       │   ├── admin-reviews.css
│       │   ├── auth.css               # Estilos de autenticación
│       │   ├── base.css               # Tokens y estilos globales
│       │   ├── contact.css
│       │   ├── detalle_pelicula.css
│       │   ├── editar_perfil.css
│       │   ├── films.css
│       │   ├── footer.css
│       │   ├── header.css
│       │   ├── hero.css
│       │   ├── home.css
│       │   ├── lists.css
│       │   ├── miPerfil.css
│       │   ├── misResenas.css
│       │   ├── movie-card.css
│       │   ├── recommendations.css
│       │   ├── title-card.css
│       │   ├── title-detail.css
│       │   ├── titles.css
│       │   ├── upcoming-releases.css
│       │   └── watchlist.css
│       ├── img/
│       │   ├── filmatrix_isotipo.webp
│       │   ├── Filmatrix_logo.png
│       │   ├── Filmatrix_logo.webp
│       │   ├── hero-bg.webp
│       │   ├── tmdb_logo.svg
│       │   └── user_avatar.png
│       └── js/
│           ├── app.js                 # Entry point JS
│           ├── modules/
│           │   ├── Carousel.js        # Carrusel de imágenes
│           │   ├── CatalogFilters.js  # Filtros del catálogo
│           │   ├── ListActions.js     # Acciones sobre listas de usuario
│           │   ├── NavMenu.js         # Menú de navegación
│           │   ├── ReviewEdit.js      # Edición de reseñas
│           │   ├── SearchToggle.js    # Toggle de búsqueda
│           │   ├── Toast.js           # Notificaciones toast
│           │   ├── utils.js           # Utilidades compartidas
│           │   └── WatchlistActions.js# Acciones sobre watchlist
│           └── pages/
│               ├── home.js
│               ├── TitleDetails.js
│               └── Titles.js
├── src/
│   ├── bootstrap.php                  # Composición del contenedor, rutas y arranque
│   ├── Controllers/
│   │   ├── AdminReviewController.php  # Moderación de reseñas (admin)
│   │   ├── Api/
│   │   │   ├── AuthTokenController.php    # API de tokens de autenticación
│   │   │   ├── ReviewApiController.php    # API de reseñas
│   │   │   └── WatchlistApiController.php # API de watchlist
│   │   ├── ErrorController.php        # Manejo de errores HTTP (403, 404, 500)
│   │   ├── PageController.php         # Páginas estáticas (home, about, contact)
│   │   ├── RecommendationController.php # Motor de recomendaciones
│   │   ├── ReviewController.php       # CRUD de reseñas
│   │   ├── SitemapController.php      # Generación de sitemap.xml
│   │   ├── TitleController.php        # Catálogo y detalle de títulos
│   │   ├── UpcomingReleaseController.php # Próximos lanzamientos
│   │   ├── UserController.php         # Perfil, registro y autenticación
│   │   ├── UserListController.php     # Gestión de listas de usuario
│   │   └── WatchlistController.php    # Gestión de watchlist
│   ├── Core/
│   │   ├── Config.php                 # Lectura de variables de entorno
│   │   ├── Request.php                # Abstracción de la petición HTTP
│   │   ├── Router.php                 # Enrutador HTTP
│   │   ├── Database/
│   │   │   └── ConnectionBuilder.php  # Construcción de la conexión PDO
│   │   ├── Exceptions/                # Excepciones de dominio
│   │   │   ├── EmailAlreadyTakenException.php
│   │   │   ├── ForbiddenAccessException.php
│   │   │   ├── InvalidApiTokenException.php
│   │   │   ├── InvalidCredentialsException.php
│   │   │   ├── InvalidPasswordException.php
│   │   │   ├── InvalidValueFormatException.php
│   │   │   ├── ListItemAlreadyExistsException.php
│   │   │   ├── ListNotFoundException.php
│   │   │   ├── ReviewAlreadyExistException.php
│   │   │   ├── ReviewAlreadyReportedException.php
│   │   │   ├── RouteNotFoundException.php
│   │   │   ├── TmdbApiException.php
│   │   │   ├── TooManyLoginAttemptsException.php
│   │   │   ├── UnauthorizedListAccessException.php
│   │   │   ├── UsernameAlreadyExistsException.php
│   │   │   ├── UserNotFoundException.php
│   │   │   ├── WatchlistItemAlreadyExistsException.php
│   │   │   └── WatchlistItemNotFoundException.php
│   │   ├── Http/
│   │   │   ├── ApiResponse.php        # Estructura de respuesta JSON para API
│   │   │   └── Links.php              # Helper de links HATEOAS para API
│   │   └── Traits/
│   │       └── Loggable.php           # Trait para inyectar logger Monolog
│   ├── Dtos/                          # Objetos de transferencia de datos
│   │   ├── CatalogQuery.php
│   │   ├── CatalogResult.php
│   │   ├── ListCardDto.php
│   │   ├── ListDetailResult.php
│   │   ├── ListItemEntry.php
│   │   ├── ReviewResource.php
│   │   ├── TitleCardDto.php
│   │   ├── WatchlistEntry.php
│   │   ├── WatchlistQuery.php
│   │   ├── WatchlistResource.php
│   │   └── WatchlistResult.php
│   ├── Infrastructure/
│   │   └── Tmdb/
│   │       └── TmdbClient.php         # Cliente HTTP para la API de TMDB
│   ├── Middleware/
│   │   ├── AdminMiddleware.php        # Protección de rutas de administrador
│   │   ├── ApiAuthMiddleware.php      # Autenticación por token para API
│   │   └── AuthMiddleware.php         # Protección de rutas autenticadas
│   ├── Models/                        # Entidades de dominio
│   │   ├── Genre.php
│   │   ├── ListItem.php
│   │   ├── People.php
│   │   ├── Review.php
│   │   ├── Title.php
│   │   ├── User.php
│   │   ├── UserList.php
│   │   └── WatchlistItem.php
│   ├── Repository/                    # Acceso a datos (solo SQL con PDO)
│   │   ├── ApiTokenRepository.php
│   │   ├── GenrePreferenceRepository.php
│   │   ├── GenreRepository.php
│   │   ├── LoginAttemptRepository.php
│   │   ├── PeopleRepository.php
│   │   ├── RecommendationRepository.php
│   │   ├── ReviewReportRepository.php
│   │   ├── ReviewRepository.php
│   │   ├── TitleListRepository.php
│   │   ├── TitleRepository.php
│   │   ├── UserListRepository.php
│   │   ├── UserRepository.php
│   │   └── WatchlistRepository.php
│   └── Services/                      # Lógica de negocio
│       ├── ApiTokenService.php
│       ├── AuthService.php
│       ├── GenrePreferenceService.php
│       ├── GenreService.php
│       ├── PeopleService.php
│       ├── RecommendationService.php
│       ├── ReviewService.php
│       ├── TitleListService.php
│       ├── TitleService.php
│       ├── UserListService.php
│       ├── UserService.php
│       └── WatchlistService.php
├── storage/
│   ├── cache/                         # Caché de Twig
│   └── uploads/                       # Archivos subidos por usuarios
├── tests/                             # (pendiente)
└── views/                             # Plantillas Twig
    ├── layout/
    │   └── main.html.twig             # Layout base
    ├── macros/
    │   ├── stars.html.twig            # Macro de estrellas para puntuación
    │   └── title-cards.html.twig      # Macro reutilizable de tarjetas
    ├── pages/
    │   ├── about.html.twig
    │   ├── admin/
    │   │   └── reviews.html.twig      # Panel de moderación de reseñas
    │   ├── change-password.html.twig
    │   ├── contact.html.twig
    │   ├── edit-profile.html.twig
    │   ├── error-403.html.twig
    │   ├── error-404.html.twig
    │   ├── error-500.html.twig
    │   ├── home.html.twig
    │   ├── list-detail.html.twig
    │   ├── login.html.twig
    │   ├── my-lists.html.twig
    │   ├── my-reviews.html.twig
    │   ├── profile.html.twig
    │   ├── recommendations.html.twig
    │   ├── register.html.twig
    │   ├── title-detail.html.twig
    │   ├── titles.html.twig
    │   ├── upcoming-releases.html.twig
    │   └── watchlist.html.twig
    ├── partials/
    │   ├── footer.html.twig
    │   └── header.html.twig
    └── sitemap.xml.twig                # Plantilla del sitemap
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
