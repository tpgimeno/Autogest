<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSellOffersTable extends AbstractMigration
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
        $table = $this->table('selloffers');
        $table->addColumn('offerNumber', 'string')
                ->addColumn('offerDate', 'date')
                ->addColumn('customerId', 'string', ['null' => true])
                ->addColumn('vehicleId', 'string', ['null' => true])
                ->addColumn('vehiclePvp', 'float', ['null' => true])
                ->addColumn('vehicleTva', 'float', ['null' => true])
                ->addColumn('vehicleTotal', 'float', ['null' => true])                
                ->addColumn('pvp', 'float', ['null' => true])
                ->addColumn('discount', 'float', ['null' => true])
                ->addColumn('tva', 'float', ['null' => true])
                ->addColumn('total', 'float', ['null' => true])
                ->addColumn('observations', 'string', ['null' => true])
                ->addColumn('texts', 'string', ['null' => true])  
                ->addColumn('vehicleComments', 'string', ['null' => true]) 
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime', ['null' => true])
                ->addColumn('deleted_at', 'datetime', ['null' => true])
                ->addIndex('offerNumber', ['unique' => true])
                ->create();
    }
}
