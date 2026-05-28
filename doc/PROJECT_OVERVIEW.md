# Filmatrix

Una aplicación web desarrollada en PHP vanilla para la reseña y gestión de películas.

## Resumen General

Filmatrix permite a los usuarios registrarse, iniciar sesión (autenticación nativa con sesiones de PHP), gestionar su perfil y escribir reseñas de películas. El sistema se integra con la API externa de **The Movie Database (TMDB)** para obtener metadatos detallados de películas, implementando una capa de caché local en la base de datos para optimizar los tiempos de carga y evitar el consumo innecesivo de cuotas de la API.

La arquitectura sigue el patrón **Controlador-Servicio-Repositorio** sin utilizar ningún framework externo de PHP, promoviendo una separación estricta de responsabilidades.

---

## Estructura del Proyecto

```
├── config/               # Archivos de configuración de la aplicación
├── db/migrations/        # Migraciones de base de datos administradas con Phinx
├── doc/                  # Documentación del proyecto (DER, overview)
├── public/               # Raíz pública del servidor web (entry point, CSS, JS, imágenes)
│   └── index.php         # Controlador frontal (inicia sesión, carga el bootstrap)
├── src/                  # Lógica interna de la aplicación
│   ├── bootstrap.php     # Configuración de dependencias, inicialización y ruteo
│   ├── Controllers/      # Controladores que reciben peticiones HTTP y manejan las respuestas
│   ├── Services/         # Capa de negocio (lógica, validación y coordinación de repositorios)
│   ├── Repository/       # Capa de acceso a datos (consultas SQL parametrizadas con PDO)
│   ├── Models/           # Objetos del dominio o entidades (User, Title, Review, etc.)
│   ├── Middleware/        # Filtros de peticiones (ej: verificación de autenticación)
│   ├── Core/             # Clases del núcleo (Router, Request, Config, DB, Excepciones)
│   │   ├── Database/     # Gestión de conexiones y construcción de PDO
│   │   ├── Exceptions/   # Definición de excepciones de negocio y de sistema
│   │   └── Traits/       # Traits reutilizables (ej: Loggable)
│   └── Infrastructure/   # Adaptadores de infraestructura externa
│       └── Tmdb/         # Cliente HTTP y excepciones para la API de TMDB
├── storage/              # Almacenamiento local (logs, caché de plantillas)
├── tests/                # Pruebas unitarias e integración (esqueleto)
├── views/                # Plantillas de renderizado usando el motor Twig
│   ├── layout/           # Estructura base (layout global)
│   ├── macros/           # Macros auxiliares de Twig
│   ├── pages/            # Plantillas de páginas específicas (home, login, detail, etc.)
│   └── partials/         # Componentes de interfaz reutilizables (cabecera, pie de página)
├── .env.example          # Plantilla para variables de entorno
└── composer.json         # Declaración de dependencias y mapeo de clases PSR-4
```

---

## Flujo Básico de una Petición

1. La petición HTTP entrante es capturada por el controlador frontal `public/index.php`.
2. Se configuran los parámetros de la sesión y se inicia mediante `session_start()`. Posteriormente, se requiere `src/bootstrap.php`.
3. `bootstrap.php` carga las variables de entorno, inicializa el Logger, establece la conexión PDO a la base de datos, instancia todos los repositorios, clientes externos (como `TmdbClient`), servicios, controladores y configura las rutas del `Router`.
4. El `Router` analiza el path y el método HTTP. Si la petición es de tipo `POST`, realiza automáticamente una validación de seguridad contra ataques **CSRF** comparando el token enviado con el almacenado en la sesión.
5. El `Router` despacha la petición al controlador correspondiente.
6. El controlador invoca la capa de servicios para procesar la lógica de negocio.
7. Los servicios coordinan las consultas con los repositorios, que ejecutan sentencias preparadas (prepared statements) de PDO contra la base de datos PostgreSQL.
8. El controlador procesa la respuesta e indica al motor de plantillas **Twig** que renderice la página (`views/pages/*.html.twig`) o ejecuta una redirección HTTP.

---

## Conceptos Clave de Arquitectura y Negocio

### 1. Motor de Plantillas Twig
Toda la capa de presentación se separa del código PHP utilizando **Twig**. Esto proporciona una sintaxis limpia, herencia de plantillas y funciones automáticas de escape para prevenir vulnerabilidades XSS.

### 2. Integración y Caché de la API de TMDB
Los datos de las películas no se pre-cargan masivamente. En su lugar, el sistema implementa una caché perezosa (lazy cache) basada en base de datos:
* Cuando un usuario accede al detalle de una película (`/titles/detail?tmdb_id=...`), el sistema busca el título localmente en la tabla `titles`.
* Si existe y su marca de tiempo (`cached_at`) está dentro del tiempo de vida establecido en la configuración (por defecto, un TTL de 30 días), los datos locales se retornan directamente.
* Si el título no existe en la base de datos o la caché expiró, `TitleService` realiza múltiples llamadas HTTP al `TmdbClient` de forma secuencial (`getMovie`, `getVideos`, `getCredits`), procesa la respuesta (extrayendo géneros, elenco principal, directores y URLs de posters/trailers) y hace un **upsert** en la base de datos local para renovar la caché antes de retornar el título.

### 3. Restricción Absoluta de IDs Externos en Lógica Interna
Para mantener la integridad referencial y desacoplar el modelo de negocio de proveedores externos, **ninguna tabla interna que represente interacciones del usuario (como `reviews`, `watchlist_items` o `list_items`) almacena ni referencia directamente el `tmdb_id`**.
* Todas las relaciones internas hacen uso exclusivo de la clave primaria autoincremental interna (`titles.id`, guardada como `title_id`).
* El `tmdb_id` se utiliza estrictamente en la interfaz de usuario (URLs de navegación como `/titles/detail?tmdb_id=123`) e infraestructura para comunicarse con la API de TMDB.
* Al procesar o guardar una reseña, el controlador recibe tanto el `title_id` interno para asociar la reseña en la tabla `reviews` como el `tmdb_id` para realizar la redirección final del navegador.

### 4. Seguridad de Cuentas y Unicidad de Datos
* **Contraseñas**: Se encriptan utilizando el algoritmo nativo de PHP `password_hash` con `PASSWORD_DEFAULT` (bcrypt) y se validan mediante `password_verify`.
* **Campos de Texto Insensibles a Mayúsculas/Minúsculas**: La base de datos PostgreSQL utiliza la extensión `citext` para las columnas `username` y `email` de la tabla `users`. Esto garantiza la unicidad absoluta a nivel de motor de base de datos sin importar si se ingresan caracteres en mayúsculas o minúsculas.

---

## Primeros Pasos

### Requisitos
* PHP 8.2 o superior (con extensión cURL activada)
* PostgreSQL (con la extensión `citext` instalada o disponible)
* Composer

### Inicio Rápido
1. Duplicar el archivo de configuración de entorno:
   ```bash
   cp .env.example .env
   ```
2. Editar el archivo `.env` e ingresar las credenciales de la base de datos y el token de lectura de TMDB (`TMDB_READ_ACCESS_TOKEN`).
3. Instalar las dependencias de Composer:
   ```bash
   composer install
   ```
4. Ejecutar las migraciones de base de datos utilizando Phinx:
   ```bash
   php vendor/bin/phinx migrate
   ```
5. Iniciar el servidor web local de PHP apuntando a la raíz pública:
   ```bash
   php -S localhost:8000 -t public
   ```
6. Abrir `http://localhost:8000` en el navegador.

---

## Análisis de Diseño, Antipatrones y Puntos de Conflicto

Durante una revisión exhaustiva del código del proyecto, se identificaron los siguientes puntos de fricción, antipatrones y comportamientos a corregir en futuras fases de desarrollo:

### 1. Limitación de Búsqueda Local (Antipatrón de Búsqueda)
* **Conflicto**: El método `TitleController::index` delega la búsqueda a `TitleService::search`, la cual realiza una consulta exclusivamente sobre la tabla local `titles` (`WHERE title ILIKE :query`). Si un usuario busca una película que aún no ha sido accedida o guardada en la caché por ningún otro usuario en el sistema, la película **no aparecerá en los resultados**, a pesar de existir en el catálogo global de TMDB.
* **Solución Propuesta**: Modificar la búsqueda para que consulte en primera instancia la API de búsqueda de TMDB (`/search/movie`). Los resultados deben mostrarse al usuario y, en caso de que este decida interactuar con alguno de ellos (ej. hacer clic en el detalle), sincronizar e incorporar dicho título a la base de datos local bajo demanda.

### 2. Cuello de Botella Secuencial y Sincrónico (Antipatrón de Rendimiento)
* **Conflicto**: El método `TitleService::syncTitleWithTmdb` realiza tres llamadas HTTP sincrónicas y consecutivas a la API de TMDB (`getMovie`, `getVideos`, `getCredits`) en caso de una pérdida de caché. Esto congela el hilo de ejecución de PHP, aumentando drásticamente el tiempo de respuesta del servidor (latencia) percibido por el usuario en el navegador.
* **Solución Propuesta**: Optimizar las consultas a la API realizando llamadas paralelas (por ejemplo, con `curl_multi` o promesas HTTP) o reestructurar el flujo para procesar la información complementaria (elenco secundario, videos de trailers) de forma asíncrona o perezosa.

### 3. Fragilidad del Sistema ante Caídas de la API Externa (Antipatrón de Tolerancia a Fallos)
* **Conflicto**: Si el TTL de 30 días de una película expira, el repositorio la excluye de las consultas normales de caché (`findByTmdbId` retorna `null`), obligando al servicio a resincronizarla contra TMDB de manera inmediata. Si la API de TMDB se encuentra fuera de línea, tiene problemas de conexión o el token ha expirado, el sistema lanzará una excepción y detendrá el renderizado de la página, a pesar de contar con toda la información anterior almacenada en la base de datos local.
* **Solución Propuesta**: Implementar un patrón de tolerancia a fallos como *Stale-While-Revalidate* o un bloque `try-catch` en la llamada a la API que devuelva los datos locales expirados de la base de datos a modo de contingencia en lugar de fallar la petición completa del usuario.

### 4. Tablas Huérfanas / Esquemas sin Implementación ("Ghost Tables")
* **Conflicto**: La base de datos posee esquemas y migraciones creados para las tablas `watchlist_items`, `discarded_titles` y `user_genre_preferences` (que definen las preferencias de los usuarios para el motor de recomendación). Sin embargo, **ninguna de estas tablas cuenta con clases de modelo, repositorios, servicios ni controladores asociados en la base de código actual**. Representan lógica de negocio sin desarrollar que contamina el DER.
* **Solución Propuesta**: Desarrollar los módulos correspondientes o documentar formalmente que se trata de tablas reservadas para futuras implementaciones.

### 5. Inconsistencias de Nomenclatura en Documentación y Comentarios
* **Conflicto**: 
  * En la documentación de base de datos (`doc/DER.md`), la tabla que asocia los listados de películas se describe como `films_lists`. Sin embargo, en el código y en las migraciones de Phinx, dicha tabla se renombró a `title_lists` mediante la migración `RenameFilmsListsToTitleLists`.
  * En la cabecera de `PageController.php`, los comentarios documentan el uso de `FilmListRepository` y sugieren un reemplazo futuro por `FilmSyncService`, nombres obsoletos que no coinciden con `TitleListRepository` y `TitleListService` / `TitleService`.
  * La documentación anterior indicaba falsamente que las vistas se manejaban mediante archivos PHP planos, cuando en realidad se utiliza Twig.
* **Solución Propuesta**: Mantener sincronizada la documentación técnica y corregir las referencias obsoletas en los docstrings de los controladores y repositorios para evitar confusiones en el equipo de desarrollo.
