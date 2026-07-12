<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateLoginAttempts extends AbstractMigration
{
    public function up(): void
    {
        $this->table('login_attempts')
            ->addColumn('ip_address', 'string', ['limit' => 45, 'null' => false])
            ->addColumn('successful', 'boolean', ['default' => false, 'null' => false])
            ->addColumn('attempted_at', 'timestamp', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['ip_address', 'attempted_at'])
            ->create();
    }

    public function down(): void
    {
        $this->table('login_attempts')->drop()->save();
    }
}
