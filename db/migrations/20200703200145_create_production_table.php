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
        $table->addColumn('vehicle_id', 'integer')
                ->addColumn('homologation_id', 'integer')
                ->addColumn('description', 'string')
                ->addColumn('date_in', 'date')
                ->addColumn('date_out', 'date')
                ->addColumn('accesories', 'string')
                ->addColumn('observations', 'string')
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->addColumn('deleted_at', 'datetime')
                ->addForeignKey('vehicle_id', 'vehicles', 'id')
                ->addForeignKey('homologation_id', 'homologations', 'id')
                ->create();
    }
}
