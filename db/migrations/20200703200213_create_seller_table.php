<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSellerTable extends AbstractMigration
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
        $table = $this->table('sellers');
        $table->addColumn('name', 'string')
                ->addColumn('fiscalId', 'string')
                ->addColumn('address', 'string')
                ->addColumn('city', 'string')
                ->addColumn('postalCode', 'integer')
                ->addColumn('state', 'string')
                ->addColumn('country', 'string')
                ->addColumn('phone', 'string')
                ->addColumn('email', 'string')
                ->addColumn('birthDate', 'date')
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->addColumn('deleted_at', 'datetime')
                ->addIndex('fiscalId', ['unique' => true])
                ->create();
    }
}
