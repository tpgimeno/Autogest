<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateGarageOrderTable extends AbstractMigration
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
        $table = $this->table('garage_orders');
        $table->addColumn('orderNumber', 'string')
                ->addColumn('inDate', 'datetime', ['null' => true])
                ->addColumn('outDate', 'datetime', ['null' => true])
                ->addColumn('vehicle_id', 'integer', ['null' => true])
                ->addColumn('customer_id', 'integer', ['null' => true])
                ->addColumn('inKm', 'integer', ['null' => true])
                ->addColumn('outKm', 'integer', ['null' => true])
                ->addColumn('description', 'text', ['null' => true])
                ->addColumn('observations', 'text', ['null' => true])
                ->addColumn('text', 'text', ['null' => true])
                ->addColumn('baseComponents', 'string', ['null' => true])
                ->addColumn('discountComponents', 'string', ['null' => true])
                ->addColumn('baseSupplies', 'string', ['null' => true])
                ->addColumn('discountSupplies', 'string', ['null' => true])
                ->addColumn('baseWorks', 'string', ['null' => true])
                ->addColumn('discountWorks', 'string', ['null' => true])
                ->addColumn('baseOrder', 'string', ['null' => true])
                ->addColumn('discountOrder', 'string', ['null' => true])
                ->addColumn('tvaOrder', 'string', ['null' => true])
                ->addColumn('totalOrder', 'string', ['null' => true])               
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime', ['null' => true])
                ->addColumn('deleted_at', 'datetime', ['null' => true])
                ->addIndex('orderNumber', ['unique' => true])                
                ->create();
    }
}
