<?php

use Phinx\Migration\AbstractMigration;

class CreateVehiclesTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('vehicles');
        $table->addColumn('brand', 'integer')
                ->addColumn('model', 'integer')
                ->addColumn('description', 'string')
                ->addColumn('registryDate', 'date', ['null' => true])
                ->addColumn('plate', 'string', ['null' => true])
                ->addColumn('vin', 'string', ['null' => true])
                ->addColumn('store', 'integer', ['null' => true])
                ->addColumn('location', 'string', ['null' => true])
                ->addColumn('type', 'integer', ['null' => true])
                ->addColumn('color', 'string', ['null' => true])
                ->addColumn('places', 'string', ['null' => true])
                ->addColumn('doors', 'string', ['null' => true])
                ->addColumn('power', 'string', ['null' => true])
                ->addColumn('km', 'string', ['null' => true])
                ->addColumn('cost', 'string', ['null' => true])
                ->addColumn('pvp', 'string' , ['null' => true])
                ->addColumn('accesories', 'string' , ['null' => true])
                ->addColumn('created_at', 'datetime', ['null' => true])
                ->addColumn('updated_at', 'datetime', ['null' => true])
                ->addColumn('deleted_at', 'datetime', ['null' => true])
                ->addIndex('vin', ['unique' => true])
                ->create();                
    }
}
