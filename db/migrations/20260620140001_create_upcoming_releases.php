<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUpcomingReleases extends AbstractMigration
{
    public function up(): void
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
    }

    public function down(): void
    {
        $this->table('upcoming_releases')->drop()->save();
    }
}