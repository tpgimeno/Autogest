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
        $table->addColumn('brand_id', 'integer')
                ->addColumn('model_id', 'integer')
                ->addColumn('description', 'string', ['null' => true])
                ->addColumn('plate', 'string', ['null' => true])
                ->addColumn('registryDate', 'date', ['null' => true])
                ->addColumn('vin', 'string', ['null' => true, 'default' => null])
                ->addColumn('store_id', 'integer')
                ->addColumn('location', 'string', ['null' => true, 'default' => null])
                ->addColumn('type_id', 'integer')
                ->addColumn('color', 'string', ['null' => true, 'default' => null])
                ->addColumn('places', 'string', ['null' => true, 'default' => null])
                ->addColumn('doors', 'string', ['null' => true, 'default' => null])
                ->addColumn('power', 'string', ['null' => true, 'default' => null])
                ->addColumn('km', 'string', ['null' => true, 'default' => null])
                ->addColumn('providor_id', 'integer')
                ->addColumn('cost', 'string', ['null' => true, 'default' => null])
                ->addColumn('pvp', 'string', ['null' => true, 'default' => null])               
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
                ->addColumn('customer_id', 'integer')
                ->addColumn('seller_id', 'integer')                
                ->addColumn('dataType', 'string', ['null' => true, 'default' => null])
                ->addColumn('variant', 'string', ['null' => true, 'default' => null])
                ->addColumn('version', 'string', ['null' => true, 'default' => null])
                ->addColumn('comercialName', 'string', ['null' => true, 'default' => null])   
                ->addColumn('mma', 'integer', ['null' => true, 'default' => null])
                ->addColumn('mmaAxe1', 'integer', ['null' => true, 'default' => null])
                ->addColumn('mmaAxe2', 'integer', ['null' => true, 'default' => null])
                ->addColumn('mmac', 'integer', ['null' => true, 'default' => null])
                ->addColumn('mmar', 'integer', ['null' => true, 'default' => null])
                ->addColumn('mmarf', 'integer', ['null' => true, 'default' => null])
                ->addColumn('mom', 'integer', ['null' => true, 'default' => null])
                ->addColumn('momAxe1', 'integer', ['null' => true, 'default' => null])
                ->addColumn('momAxe2', 'integer', ['null' => true, 'default' => null])
                ->addColumn('large', 'integer', ['null' => true, 'default' => null])
                ->addColumn('width', 'integer', ['null' => true, 'default' => null])
                ->addColumn('height', 'integer', ['null' => true, 'default' => null])
                ->addColumn('frontOverhang', 'integer', ['null' => true, 'default' => null])
                ->addColumn('rearOverhang', 'integer', ['null' => true, 'default' => null])
                ->addColumn('axeDistance', 'integer', ['null' => true, 'default' => null])
                ->addColumn('chargeLength', 'integer', ['null' => true, 'default' => null])
                ->addColumn('deposit', 'integer', ['null' => true, 'default' => null])
                ->addColumn('initCharge', 'integer', ['null' => true, 'default' => null])
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime', ['null' => true, 'default' => null])
                ->addColumn('deleted_at', 'datetime', ['null' => true, 'default' => null])
                ->addIndex('vin', ['unique' => true])
                ->addIndex('plate', ['unique' => true])
                ->addForeignKey(['brand_id'], 'brands', ['id'],['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
                ->addForeignKey(['model_id'], 'models', ['id'],['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
                ->addForeignKey(['store_id'], 'stores', ['id'],['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
                ->addForeignKey(['type_id'], 'vehicleTypes', ['id'],['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
                ->addForeignKey(['providor_id'], 'providers', ['id'],['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
                ->addForeignKey(['customer_id'], 'customers', ['id'],['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])
                ->addForeignKey(['seller_id'], 'sellers', ['id'],['delete' => 'SET_NULL', 'update' => 'NO_ACTION'])                
                ->create();                
    }
}
