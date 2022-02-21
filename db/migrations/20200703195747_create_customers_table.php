<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCustomersTable extends AbstractMigration
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
        $table = $this->table('customers');
        $table->addColumn('name', 'string')
                ->addColumn('fiscalId', 'string')                
                ->addColumn('address', 'string', ['null' => true])
                ->addColumn('city', 'string', ['null' => true])
                ->addColumn('postalCode', 'integer', ['null' => true])
                ->addColumn('state', 'string', ['null' => true])
                ->addColumn('country', 'string', ['null' => true])
                ->addColumn('phone', 'string', ['null' => true])
                ->addColumn('email', 'string', ['null' => true])
                ->addColumn('birthDate', 'date', ['null' => true])
                ->addColumn('customerType', 'integer', ['null' => true])
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime', ['null' => true])
                ->addColumn('deleted_at', 'datetime', ['null' => true])
                ->addIndex('fiscalId' , ['unique' => true])
                ->create();
    }
}
