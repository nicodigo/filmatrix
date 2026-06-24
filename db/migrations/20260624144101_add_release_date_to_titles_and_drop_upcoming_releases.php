<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddReleaseDateToTitlesAndDropUpcomingReleases extends AbstractMigration
{
    public function up(): void
    {
        $this->table('titles')
            ->addColumn('release_date', 'date', [
                'null' => true,
                'after' => 'release_year',
            ])
            ->addIndex(['release_date'])
            ->update();

        $this->table('upcoming_releases')->drop()->save();
    }

    public function down(): void
    {
        $this->table('upcoming_releases')
            ->addColumn('tmdb_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('poster_url', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('synopsis', 'text', [
                'null' => true,
            ])
            ->addColumn('release_date', 'date', [
                'null' => false,
            ])
            ->addColumn('synced_at', 'timestamp', [
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->addIndex(['tmdb_id'], ['unique' => true])
            ->addIndex(['release_date'])
            ->create();

        $this->table('titles')
            ->removeColumn('release_date')
            ->update();
    }
}