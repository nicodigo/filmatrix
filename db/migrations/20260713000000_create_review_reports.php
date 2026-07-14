<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateReviewReports extends AbstractMigration
{
    public function up(): void
    {
        $this->table('review_reports', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'integer', ['identity' => true])
            ->addColumn('review_id', 'integer', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('created_at', 'timestamp', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('review_id', 'reviews', 'id', ['delete' => 'CASCADE'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE'])
            ->addIndex(['review_id', 'user_id'], ['unique' => true])
            ->addIndex(['review_id'])
            ->create();
    }

    public function down(): void
    {
        $this->table('review_reports')->drop()->save();
    }
}