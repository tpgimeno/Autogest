<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateStoreTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('stores');
        $table->addColumn('name', 'string')
                ->addColumn('address', 'string')
                ->addColumn('city', 'string')
                ->addColumn('postal_code', 'integer')
                ->addColumn('state', 'string')
                ->addColumn('country', 'string')
                ->addColumn('phone', 'string')
                ->addColumn('email', 'string')
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->addColumn('deleted_at', 'datetime')
                ->addIndex('name', ['unique' => true])
                ->create();
    }
}
