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
                ->addColumn('dateIn', 'date', ['null' => true])
                ->addColumn('dateOut', 'date', ['null' => true])
                ->addColumn('vehicleId', 'integer', ['null' => true])
                ->addColumn('customerId', 'integer', ['null' => true])
                ->addColumn('kmIn', 'integer', ['null' => true])
                ->addColumn('kmOut', 'integer', ['null' => true])
                ->addColumn('works', 'string', ['null' => true])
                ->addColumn('articles', 'string', ['null' => true])
                ->addColumn('priceWorks', 'string', ['null' => true])
                ->addColumn('priceArticles', 'string', ['null' => true])
                ->addColumn('observations', 'string', ['null' => true])
                ->addColumn('text', 'string', ['null' => true])
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime', ['null' => true])
                ->addColumn('deleted_at', 'datetime', ['null' => true])
                ->addIndex('orderNumber', ['unique' => true])
                ->create();
    }
}
