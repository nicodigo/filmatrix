<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class AdminUserSeed extends AbstractSeed
{
    public function run(): void
    {
        $exists = $this->fetchRow(
            "SELECT id FROM users WHERE email = 'admin@filmatrix.com'"
        );

        if ($exists) {
            return;
        }

        $this->table('users')->insert([
            'username' => 'admin',
            'email' => 'admin@filmatrix.com',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ])->saveData();
    }
}
