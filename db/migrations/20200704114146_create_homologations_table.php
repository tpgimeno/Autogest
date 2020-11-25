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
                ->addColumn('homologationCode', 'string')
                ->addColumn('prototypeId', 'string')
                ->addColumn('large', 'integer')
                ->addColumn('width', 'integer')
                ->addColumn('height', 'integer')
                ->addColumn('doorsNumber', 'integer')
                ->addColumn('heightRearDoor', 'integer')
                ->addColumn('widthRearDoor', 'integer')
                ->addColumn('heightLateralDoor', 'integer') 
                ->addColumn('widthLateralDoor', 'integer')
                ->addColumn('weight', 'integer')
                ->addColumn('coeficientK', 'float')
                ->addColumn('essay', 'string')
                ->addColumn('dateEssay', 'date')
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->addColumn('deleted_at', 'datetime')
                ->addIndex('homologationCode', ['unique' => true])
                ->create();
    }
}
