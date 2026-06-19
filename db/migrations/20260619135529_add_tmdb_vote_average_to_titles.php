<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddTmdbVoteAverageToTitles extends AbstractMigration
{
    public function up(): void
    {
        $this->table('titles')
            ->addColumn('tmdb_vote_average', 'decimal', [
                'precision' => 3,
                'scale' => 1,
                'null' => true,
                'after' => 'duration_minutes',
            ])
            ->update();
    }

    public function down(): void
    {
        $this->table('titles')
            ->removeColumn('tmdb_vote_average')
            ->update();
    }
}