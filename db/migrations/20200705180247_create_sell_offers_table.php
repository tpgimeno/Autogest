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
                ->addColumn('customerId', 'string')
                ->addColumn('vehicleId', 'string')
                ->addColumn('vehiclePvp', 'float')
                ->addColumn('vehicleTva', 'float')
                ->addColumn('vehicleTotal', 'float')                
                ->addColumn('pvp', 'float')
                ->addColumn('discount', 'float')
                ->addColumn('tva', 'float')
                ->addColumn('total', 'float')
                ->addColumn('observations', 'string')
                ->addColumn('texts', 'string')  
                ->addColumn('vehicleComments', 'string') 
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->addColumn('deleted_at', 'datetime')
                ->addIndex('offerNumber', ['unique' => true])
                ->create();
    }
}
