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
                ->addColumn('large', 'integer', ['null' => true])
                ->addColumn('width', 'integer', ['null' => true])
                ->addColumn('height', 'integer', ['null' => true])
                ->addColumn('doorsNumber', 'integer', ['null' => true])
                ->addColumn('heightRearDoor', 'integer', ['null' => true])
                ->addColumn('widthRearDoor', 'integer', ['null' => true])
                ->addColumn('heightLateralDoor', 'integer', ['null' => true]) 
                ->addColumn('widthLateralDoor', 'integer', ['null' => true])
                ->addColumn('weight', 'integer', ['null' => true])
                ->addColumn('coeficientK', 'float', ['null' => true])
                ->addColumn('essay', 'string', ['null' => true])
                ->addColumn('dateEssay', 'date', ['null' => true])
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime', ['null' => true])
                ->addColumn('deleted_at', 'datetime', ['null' => true])
                ->addIndex('homologationCode', ['unique' => true])
                ->create();
    }
}
