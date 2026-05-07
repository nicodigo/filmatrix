<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCatalogLists extends AbstractMigration
{
      public function up(): void
      {
            $table = $this->table('catalog_lists');
            $table->addColumn('section', 'string', ['limit' => 20, 'null' => false])
                  ->addColumn('title_id', 'integer', ['null' => false])
                  ->addColumn('position', 'smallinteger', ['null' => false])
                  ->addColumn('synced_at', 'datetime', ['null' => false])
                  ->addForeignKey('title_id', 'titles', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
                  ->addIndex(['section', 'title_id'], ['unique' => true])
                  ->addIndex('section')
                  ->create();
      }

      public function down(): void
      {
            $this->table('catalog_lists')->drop()->save();
      }
}
