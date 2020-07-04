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
                ->addColumn('plate', 'string')
                ->addColumn('vin', 'string')
                ->addColumn('type', 'integer')
                ->addColumn('color', 'string')
                ->addColumn('places', 'string')
                ->addColumn('doors', 'string')
                ->addColumn('power', 'string')
                ->addColumn('km', 'string')
                ->addColumn('cost', 'string')
                ->addColumn('pvp', 'string')
                ->addColumn('accesories', 'string')
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->addColumn('deleted_at', 'datetime')
<<<<<<< HEAD
                ->addForeignKey('brand', 'brands', 'id')
                ->addForeignKey('model', 'models', 'id')
=======
>>>>>>> 4bfc5db00600c9c9450fde31dab0a73b487d845d
                ->create();                
    }
}
