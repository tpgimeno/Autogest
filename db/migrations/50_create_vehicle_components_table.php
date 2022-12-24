<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateVehicleComponentsTable extends AbstractMigration
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
        $table = $this->table('vehicleComponents');
        $table->addColumn('vehicleId', 'integer')  
                ->addColumn('componentId', 'integer')
                ->addColumn('cantity' , 'integer')
                ->addColumn('pvp' , 'float')
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime', ['null' => true])
                ->addForeignKey(['vehicleId'], 'vehicles', ['id'])
                ->addForeignKey(['componentId'], 'components', ['id'])                
                ->create();
    }
}
