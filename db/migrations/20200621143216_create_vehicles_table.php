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
        $table = $this->table('vehicles')
                ->addColumn('brand', 'integer')
                ->addColumn('model', 'integer')
                ->addColumn('description', 'string')
                ->addColumn('plate', 'string', ['null' => true])
                ->addColumn('registryDate', 'date', ['null' => true])
                ->addColumn('vin', 'string', ['null' => true, 'default' => null])
                ->addColumn('store', 'integer', ['null' => true, 'default' => null])
                ->addColumn('location', 'string', ['null' => true, 'default' => null])
                ->addColumn('type', 'integer', ['null' => true, 'default' => null])
                ->addColumn('color', 'string', ['null' => true, 'default' => null])
                ->addColumn('places', 'string', ['null' => true, 'default' => null])
                ->addColumn('doors', 'string', ['null' => true, 'default' => null])
                ->addColumn('power', 'string', ['null' => true, 'default' => null])
                ->addColumn('km', 'string', ['null' => true, 'default' => null])
                ->addColumn('cost', 'string', ['null' => true, 'default' => null])
                ->addColumn('pvp', 'string', ['null' => true, 'default' => null])
                ->addColumn('accesories', 'string', ['null' => true, 'default' => null])
                ->addColumn('state', 'string')
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => null])
                ->addColumn('deleted_at', 'datetime', ['null' => true, 'default' => null])
                ->addIndex('vin', ['unique' => true])
                ->create();                
    }
}
