<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateFilmatrixSchema extends AbstractMigration
{
      public function up(): void
      {
            $this->execute('CREATE EXTENSION IF NOT EXISTS citext');

            // 1. users
            $table = $this->table('users');
            $table->addColumn('username', 'string', ['limit' => 100, 'null' => false])
                  ->addColumn('email', 'string', ['limit' => 150, 'null' => false])
                  ->addColumn('password_hash', 'string', ['limit' => 255, 'null' => false])
                  ->addColumn('role', 'string', ['limit' => 20, 'values' => ['user', 'admin'], 'default' => 'user', 'null' => false])
                  ->addTimestamps()
                  ->addIndex('username', ['unique' => true])
                  ->addIndex('email', ['unique' => true])
                  ->create();
            $this->execute('ALTER TABLE users ALTER COLUMN username TYPE citext');
            $this->execute('ALTER TABLE users ALTER COLUMN email TYPE citext');

            // 2. genres
            $table = $this->table('genres');
            $table->addColumn('tmdb_genre_id', 'integer', ['null' => false])
                  ->addColumn('name', 'string', ['limit' => 100, 'null' => false])
                  ->addIndex('tmdb_genre_id', ['unique' => true])
                  ->create();

            // 3. people
            $table = $this->table('people');
            $table->addColumn('tmdb_person_id', 'integer', ['null' => false])
                  ->addColumn('name', 'string', ['limit' => 150, 'null' => false])
                  ->addColumn('profile_url', 'string', ['limit' => 255, 'null' => true])
                  ->addColumn('cached_at', 'datetime', ['null' => false])
                  ->addIndex('tmdb_person_id', ['unique' => true])
                  ->create();

            // 4. titles
            $table = $this->table('titles');
            $table->addColumn('tmdb_id', 'integer', ['null' => false])
                  ->addColumn('type', 'string', ['limit' => 20, 'values' => ['movie', 'series'], 'null' => false])
                  ->addColumn('title', 'string', ['limit' => 255, 'null' => false])
                  ->addColumn('synopsis', 'text', ['null' => true])
                  ->addColumn('poster_url', 'string', ['limit' => 255, 'null' => true])
                  ->addColumn('trailer_url', 'string', ['limit' => 255, 'null' => true])
                  ->addColumn('release_year', 'smallinteger', ['null' => true])
                  ->addColumn('language', 'string', ['limit' => 10, 'null' => true])
                  ->addColumn('duration_minutes', 'smallinteger', ['null' => true])
                  ->addColumn('tmdb_rating', 'decimal', ['precision' => 3, 'scale' => 1, 'null' => true])
                  ->addColumn('cached_at', 'datetime', ['null' => false])
                  ->addIndex('tmdb_id', ['unique' => true])
                  ->create();

            // 5. title_genres (composite PK)
            $table = $this->table('title_genres', ['id' => false, 'primary_key' => ['title_id', 'genre_id']]);
            $table->addColumn('title_id', 'integer', ['null' => false])
                  ->addColumn('genre_id', 'integer', ['null' => false])
                  ->addForeignKey('title_id', 'titles', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->addForeignKey('genre_id', 'genres', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->create();

            // 6. title_cast (composite PK)
            $table = $this->table('title_cast', ['id' => false, 'primary_key' => ['title_id', 'person_id']]);
            $table->addColumn('title_id', 'integer', ['null' => false])
                  ->addColumn('person_id', 'integer', ['null' => false])
                  ->addColumn('role', 'string', ['limit' => 20, 'values' => ['actor', 'director'], 'null' => false])
                  ->addColumn('character_name', 'string', ['limit' => 150, 'null' => true])
                  ->addColumn('billing_order', 'smallinteger', ['null' => false])
                  ->addForeignKey('title_id', 'titles', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->addForeignKey('person_id', 'people', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->create();

            // 7. watchlist_items
            $table = $this->table('watchlist_items');
            $table->addColumn('user_id', 'integer', ['null' => false])
                  ->addColumn('title_id', 'integer', ['null' => false])
                  ->addColumn('status', 'string', ['limit' => 20, 'values' => ['pending', 'watching', 'watched'], 'null' => false])
                  ->addColumn('added_at', 'datetime', ['null' => false])
                  ->addColumn('updated_at', 'datetime', ['null' => false])
                  ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->addForeignKey('title_id', 'titles', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->addIndex(['user_id', 'title_id'], ['unique' => true])
                  ->create();

            // 8. discarded_titles (composite PK)
            $table = $this->table('discarded_titles', ['id' => false, 'primary_key' => ['user_id', 'title_id']]);
            $table->addColumn('user_id', 'integer', ['null' => false])
                  ->addColumn('title_id', 'integer', ['null' => false])
                  ->addColumn('discarded_at', 'datetime', ['null' => false])
                  ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->addForeignKey('title_id', 'titles', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->create();

            // 9. reviews
            $table = $this->table('reviews');
            $table->addColumn('user_id', 'integer', ['null' => false])
                  ->addColumn('title_id', 'integer', ['null' => false])
                  ->addColumn('score', 'decimal', ['precision' => 3, 'scale' => 1, 'null' => false])
                  ->addColumn('body', 'text', ['null' => true])
                  ->addColumn('is_flagged', 'boolean', ['default' => false, 'null' => false])
                  ->addColumn('is_visible', 'boolean', ['default' => true, 'null' => false])
                  ->addTimestamps()
                  ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->addForeignKey('title_id', 'titles', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->addIndex(['user_id', 'title_id'], ['unique' => true])
                  ->create();

            // 10. user_genre_preferences (composite PK)
            $table = $this->table('user_genre_preferences', ['id' => false, 'primary_key' => ['user_id', 'genre_id']]);
            $table->addColumn('user_id', 'integer', ['null' => false])
                  ->addColumn('genre_id', 'integer', ['null' => false])
                  ->addColumn('weight', 'decimal', ['precision' => 5, 'scale' => 4, 'default' => 0, 'null' => false])
                  ->addColumn('updated_at', 'datetime', ['null' => false])
                  ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->addForeignKey('genre_id', 'genres', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->create();

            // 11. featured_titles
            $table = $this->table('featured_titles');
            $table->addColumn('title_id', 'integer', ['null' => false])
                  ->addColumn('added_by', 'integer', ['null' => false])
                  ->addColumn('position', 'smallinteger', ['null' => false])
                  ->addColumn('active', 'boolean', ['default' => true, 'null' => false])
                  ->addColumn('created_at', 'datetime', ['null' => false])
                  ->addForeignKey('title_id', 'titles', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->addForeignKey('added_by', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->create();

            // 12. lists
            $table = $this->table('lists');
            $table->addColumn('user_id', 'integer', ['null' => false])
                  ->addColumn('name', 'string', ['limit' => 150, 'null' => false])
                  ->addColumn('is_public', 'boolean', ['default' => false, 'null' => false])
                  ->addTimestamps()
                  ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->create();

            // 13. list_items (composite PK)
            $table = $this->table('list_items', ['id' => false, 'primary_key' => ['list_id', 'title_id']]);
            $table->addColumn('list_id', 'integer', ['null' => false])
                  ->addColumn('title_id', 'integer', ['null' => false])
                  ->addColumn('added_at', 'datetime', ['null' => false])
                  ->addForeignKey('list_id', 'lists', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->addForeignKey('title_id', 'titles', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->create();
      }

      public function down(): void
      {
            $this->table('list_items')->drop()->save();
            $this->table('lists')->drop()->save();
            $this->table('featured_titles')->drop()->save();
            $this->table('user_genre_preferences')->drop()->save();
            $this->table('reviews')->drop()->save();
            $this->table('discarded_titles')->drop()->save();
            $this->table('watchlist_items')->drop()->save();
            $this->table('title_cast')->drop()->save();
            $this->table('title_genres')->drop()->save();
            $this->table('titles')->drop()->save();
            $this->table('people')->drop()->save();
            $this->table('genres')->drop()->save();
            $this->table('users')->drop()->save();
      }
}
