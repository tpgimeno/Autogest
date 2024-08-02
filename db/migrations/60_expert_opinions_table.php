<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ExpertOpinionsTable extends AbstractMigration
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
        $table = $this->table('expertopinions');
        $table->addColumn('opinionId', 'string')
                ->addColumn('expertId', 'string')
                ->addColumn('expertName', 'string')
                ->addColumn('expertSurname', 'string')
                ->addColumn('expertAddress', 'string')
                ->addColumn('expertState', 'string')
                ->addColumn('expertCP', 'integer')
                ->addColumn('expertCity', 'string')
                ->addColumn('expertPhone', 'string')
                ->addColumn('expertEmail', 'string')                
                ->addColumn('date', 'date')
                ->addColumn('demander', 'string')
                ->addColumn('object', 'string')
                ->addColumn('actions', 'text')
                ->addColumn('conclusions', 'text')
                ->addColumn('anexed', 'string')
                ->addColumn('opinionPvp', 'float')
                ->addColumn('opinionTva', 'float')
                ->addColumn('opinionTotal', 'float')
                ->addColumn('vehicle_id', 'integer')
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime', ['null' => true])
                ->addColumn('deleted_at', 'datetime', ['null' => true])
                ->addIndex('OpinionId', ['unique' => true])
                ->create();
    }
}
