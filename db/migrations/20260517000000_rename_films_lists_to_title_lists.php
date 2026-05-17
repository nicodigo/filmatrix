<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RenameFilmsListsToTitleLists extends AbstractMigration
{
    public function up(): void
    {
        $this->execute('ALTER TABLE films_lists RENAME TO title_lists');
    }

    public function down(): void
    {
        $this->execute('ALTER TABLE title_lists RENAME TO films_lists');
    }
}
