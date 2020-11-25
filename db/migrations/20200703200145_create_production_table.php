<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateProductionTable extends AbstractMigration
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
        $table = $this->table('productions');
        $table->addColumn('vehicleId', 'integer')
                ->addColumn('homologationId', 'integer')
                ->addColumn('productionNumber', 'string')
                ->addColumn('description', 'string')
                ->addColumn('dateIn', 'date')
                ->addColumn('dateOut', 'date')
                ->addColumn('accesories', 'string')
                ->addColumn('observations', 'string')
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->addColumn('deleted_at', 'datetime')
                ->addIndex('productionNumber', ['unique' => true])
                ->create();
    }
}
