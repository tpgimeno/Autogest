<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class WorkSheetsTable extends AbstractMigration
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
        $table = $this->table('worksheets');
        $table->addColumn('workSheetNumber', 'string')
                ->addColumn('inDate', 'datetime', ['null' => true])
                ->addColumn('outDate', 'datetime', ['null' => true])
                ->addColumn('order_id', 'integer')
                ->addColumn('customer_id', 'integer', ['null' => true])
                ->addColumn('vehicle_id', 'integer', ['null' => true])
                ->addColumn('workId', 'integer', ['null' => true])
                ->addColumn('text', 'text', ['null' => true])
                ->addColumn('observations', 'text', ['null' => true])
                ->addColumn('created_at', 'datetime', ['null' => true])
                ->addColumn('updated_at', 'datetime', ['null' => true])
                ->addColumn('deleted_at', 'datetime', ['null' => true])
                ->addIndex('workSheetNumber', ['unique' => true]) 
                ->create();
    }
}
