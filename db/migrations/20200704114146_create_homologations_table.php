<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateHomologationsTable extends AbstractMigration
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
        $table = $this->table('homologations');
        $table->addColumn('description', 'string')
                ->addColumn('homologation_code', 'string')
                ->addColumn('prototype_id', 'string')
                ->addColumn('large', 'integer')
                ->addColumn('width', 'integer')
                ->addColumn('height', 'integer')
                ->addColumn('doors_number', 'integer')
                ->addColumn('height_rear_door', 'integer')
                ->addColumn('width_rear_door', 'integer')
                ->addColumn('height_lateral_door', 'integer') 
                ->addColumn('width_lateral_door', 'integer')
                ->addColumn('weight', 'integer')
                ->addColumn('coeficient_k', 'float')
                ->addColumn('essay', 'string')
                ->addColumn('date_essay', 'date')
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->addColumn('deleted_at', 'datetime')
                ->create();
    }
}
