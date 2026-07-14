<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateApiTokens extends AbstractMigration
{
    public function up(): void
    {
        $this->table('api_tokens')
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('token_hash', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('label', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('last_used_at', 'timestamp', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('revoked_at', 'timestamp', ['null' => true])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addIndex(['token_hash'], ['unique' => true])
            ->create();
    }

    public function down(): void
    {
        $this->table('api_tokens')->drop()->save();
    }
}
