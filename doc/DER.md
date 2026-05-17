# Database Schema

## users

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | INTEGER | NO | | PK |
| username | CITEXT | NO | | UNIQUE |
| email | CITEXT | NO | | UNIQUE |
| password_hash | VARCHAR(255) | NO | | |
| role | VARCHAR(20) | NO | 'user' | CHECK (role IN ('user','admin')) |
| created_at | TIMESTAMP | YES | | |
| updated_at | TIMESTAMP | YES | | |

- **PK**: id
- **UNIQUE**: username, email

## genres

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | INTEGER | NO | | PK |
| tmdb_genre_id | INTEGER | NO | | UNIQUE |
| name | VARCHAR(100) | NO | | |

- **PK**: id
- **UNIQUE**: tmdb_genre_id

## people

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | INTEGER | NO | | PK |
| tmdb_person_id | INTEGER | NO | | UNIQUE |
| name | VARCHAR(150) | NO | | |
| profile_url | VARCHAR(255) | YES | | |
| cached_at | TIMESTAMP | NO | | |

- **PK**: id
- **UNIQUE**: tmdb_person_id

## titles

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | INTEGER | NO | | PK |
| tmdb_id | INTEGER | NO | | UNIQUE |
| type | VARCHAR(20) | NO | | CHECK (type IN ('movie','series')) |
| title | VARCHAR(255) | NO | | |
| synopsis | TEXT | YES | | |
| poster_url | VARCHAR(255) | YES | | |
| trailer_url | VARCHAR(255) | YES | | |
| release_year | SMALLINT | YES | | |
| language | VARCHAR(10) | YES | | |
| duration_minutes | SMALLINT | YES | | |
| tmdb_rating | NUMERIC(3,1) | YES | | |
| cached_at | TIMESTAMP | NO | | |

- **PK**: id
- **UNIQUE**: tmdb_id

## title_genres

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| title_id | INTEGER | NO | | FK |
| genre_id | INTEGER | NO | | FK |

- **PK**: (title_id, genre_id)
- **FK**: title_id → titles(id) ON DELETE CASCADE
- **FK**: genre_id → genres(id) ON DELETE CASCADE

## title_cast

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| title_id | INTEGER | NO | | FK |
| person_id | INTEGER | NO | | FK |
| role | VARCHAR(20) | NO | | CHECK (role IN ('actor','director')) |
| character_name | VARCHAR(150) | YES | | |
| billing_order | SMALLINT | NO | | |

- **PK**: (title_id, person_id)
- **FK**: title_id → titles(id) ON DELETE CASCADE
- **FK**: person_id → people(id) ON DELETE CASCADE

## watchlist_items

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | INTEGER | NO | | PK |
| user_id | INTEGER | NO | | FK |
| title_id | INTEGER | NO | | FK |
| status | VARCHAR(20) | NO | | CHECK (status IN ('pending','watching','watched')) |
| added_at | TIMESTAMP | NO | | |
| updated_at | TIMESTAMP | NO | | |

- **PK**: id
- **UNIQUE**: (user_id, title_id)
- **FK**: user_id → users(id) ON DELETE CASCADE
- **FK**: title_id → titles(id) ON DELETE CASCADE

## discarded_titles

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| user_id | INTEGER | NO | | FK |
| title_id | INTEGER | NO | | FK |
| discarded_at | TIMESTAMP | NO | | |

- **PK**: (user_id, title_id)
- **FK**: user_id → users(id) ON DELETE CASCADE
- **FK**: title_id → titles(id) ON DELETE CASCADE

## reviews

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | INTEGER | NO | | PK |
| user_id | INTEGER | NO | | FK |
| title_id | INTEGER | NO | | FK |
| score | NUMERIC(3,1) | NO | | |
| body | TEXT | YES | | |
| is_flagged | BOOLEAN | NO | false | |
| is_visible | BOOLEAN | NO | true | |
| created_at | TIMESTAMP | YES | | |
| updated_at | TIMESTAMP | YES | | |

- **PK**: id
- **UNIQUE**: (user_id, title_id)
- **FK**: user_id → users(id) ON DELETE CASCADE
- **FK**: title_id → titles(id) ON DELETE CASCADE

## user_genre_preferences

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| user_id | INTEGER | NO | | FK |
| genre_id | INTEGER | NO | | FK |
| weight | NUMERIC(5,4) | NO | 0 | |
| updated_at | TIMESTAMP | NO | | |

- **PK**: (user_id, genre_id)
- **FK**: user_id → users(id) ON DELETE CASCADE
- **FK**: genre_id → genres(id) ON DELETE CASCADE

## featured_titles

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | INTEGER | NO | | PK |
| title_id | INTEGER | NO | | FK |
| added_by | INTEGER | NO | | FK |
| position | SMALLINT | NO | | |
| active | BOOLEAN | NO | true | |
| created_at | TIMESTAMP | NO | | |

- **PK**: id
- **FK**: title_id → titles(id) ON DELETE CASCADE
- **FK**: added_by → users(id) ON DELETE CASCADE

## lists

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | INTEGER | NO | | PK |
| user_id | INTEGER | NO | | FK |
| name | VARCHAR(150) | NO | | |
| is_public | BOOLEAN | NO | false | |
| created_at | TIMESTAMP | YES | | |
| updated_at | TIMESTAMP | YES | | |

- **PK**: id
- **FK**: user_id → users(id) ON DELETE CASCADE

## list_items

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| list_id | INTEGER | NO | | FK |
| title_id | INTEGER | NO | | FK |
| added_at | TIMESTAMP | NO | | |

- **PK**: (list_id, title_id)
- **FK**: list_id → lists(id) ON DELETE CASCADE
- **FK**: title_id → titles(id) ON DELETE CASCADE

## films_lists

| Column | Type | Nullable | Default | Constraints |
|--------|------|----------|---------|-------------|
| id | INTEGER | NO | | PK |
| section | VARCHAR(20) | NO | | |
| title_id | INTEGER | NO | | FK |
| position | SMALLINT | NO | | |
| synced_at | TIMESTAMP | NO | | |

- **PK**: id
- **UNIQUE**: (section, title_id)
- **FK**: title_id → titles(id) ON DELETE CASCADE

## Relationships

- title_genres.title_id → titles.id
- title_genres.genre_id → genres.id
- title_cast.title_id → titles.id
- title_cast.person_id → people.id
- watchlist_items.user_id → users.id
- watchlist_items.title_id → titles.id
- discarded_titles.user_id → users.id
- discarded_titles.title_id → titles.id
- reviews.user_id → users.id
- reviews.title_id → titles.id
- user_genre_preferences.user_id → users.id
- user_genre_preferences.genre_id → genres.id
- featured_titles.title_id → titles.id
- featured_titles.added_by → users.id
- lists.user_id → users.id
- list_items.list_id → lists.id
- list_items.title_id → titles.id
- films_lists.title_id → titles.id
