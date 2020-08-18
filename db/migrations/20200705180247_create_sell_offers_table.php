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
        $table->addColumn('offer_number', 'string')
                ->addColumn('customer_id', 'string')
                ->addColumn('vehicle_id', 'string')
                ->addColumn('pvp', 'float')
                ->addColumn('discount', 'float')
                ->addColumn('tva', 'float')
                ->addColumn('total', 'float')
                ->addColumn('observations', 'string')
                ->addColumn('texts', 'string')  
                ->addColumn('vehicle_comments', 'string') 
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->addColumn('deleted_at', 'datetime')
                ->create();
    }
}
