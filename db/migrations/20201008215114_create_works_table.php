<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateWorksTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('works');
        $table->addColumn('reference', 'string')
                ->addColumn('description', 'string')               
                ->addColumn('pvp', 'float')
                ->addColumn('observations', 'string')                
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->addColumn('deleted_at', 'datetime')
                ->addIndex('reference' , ['unique' => true])
                ->create();
    }
}
