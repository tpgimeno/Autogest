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
                ->addColumn('brand', 'integer', ['null' => true])
                ->addColumn('model', 'integer', ['null' => true])
                ->addColumn('description', 'string', ['null' => true])
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
                ->addColumn('providor', 'integer', ['null' => true, 'default' => null])
                ->addColumn('cost', 'string', ['null' => true, 'default' => null])
                ->addColumn('pvp', 'string', ['null' => true, 'default' => null])
                ->addColumn('accesories', 'string', ['null' => true, 'default' => null])
                ->addColumn('transference', 'string', ['null' => true, 'default' => null])
                ->addColumn('service', 'string', ['null' => true, 'default' => null])
                ->addColumn('secondKey', 'boolean', ['default' => false])
                ->addColumn('rebu', 'boolean', ['default' => false])
                ->addColumn('technicCard', 'boolean', ['default' => false])
                ->addColumn('permission', 'boolean', ['default' => false])
                ->addColumn('arrival', 'date', ['null' => true, 'default' => null])
                ->addColumn('buyDate', 'date', ['null' => true, 'default' => null])
                ->addColumn('sellDate', 'date', ['null' => true, 'default' => null])
                ->addColumn('appointDate', 'date', ['null' => true, 'default' => null])
                ->addColumn('customer', 'integer', ['null' => true, 'default' => null])
                ->addColumn('seller', 'integer', ['null' => true, 'default' => null])
                ->addColumn('state', 'string', ['null' => true, 'default' => null])
                ->addColumn('dataType', 'string', ['null' => true, 'default' => null])
                ->addColumn('variant', 'string', ['null' => true, 'default' => null])
                ->addColumn('version', 'string', ['null' => true, 'default' => null])
                ->addColumn('comercialName', 'string', ['null' => true, 'default' => null])                
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => null])
                ->addColumn('deleted_at', 'datetime', ['null' => true, 'default' => null])
                ->addIndex('vin', ['unique' => true])
                ->addIndex('providor', ['unique' => false])                
                ->create();                
    }
}
