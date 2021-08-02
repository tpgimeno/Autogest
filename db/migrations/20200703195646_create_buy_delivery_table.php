<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBuyDeliveryTable extends AbstractMigration
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
        $table = $this->table('buyDelivery');
        $table->addColumn('deliveryNumber', 'string')
                ->addColumn('date', 'date')
                ->addColumn('providorId', 'integer')
                ->addColumn('articles', 'string', ['null' => true])
                ->addColumn('base', 'float', ['null' => true])
                ->addColumn('tva', 'float', ['null' => true])
                ->addColumn('total', 'float', ['null' => true])
                ->addColumn('observations', 'string', ['null' => true])
                ->addColumn('text', 'string', ['null' => true])
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime', ['null' => true])
                ->addColumn('deleted_at', 'datetime', ['null' => true])
                ->addIndex('deliveryNumber', ['unique' => true])
                ->create();
    }
}
