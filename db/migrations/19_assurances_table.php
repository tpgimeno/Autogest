<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Illuminate\Database\Eloquent\SoftDeletes;

final class AssurancesTable extends AbstractMigration
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
    
    
    use SoftDeletes;
    
    public function change(): void
    {
        
        $table = $this->table('assurances');
        $table->addColumn('ref', 'string')
                ->addColumn('date', 'datetime')
                ->addColumn('duration', 'integer')
                ->addColumn('owner', 'string')
                ->addColumn('object', 'string')
                ->addColumn('price', 'timestamp')
                ->addColumn('description', 'timestamp')
                ->addColumn('options', 'timestamp')
                ->addColumn('created_at', 'timestamp')
                ->addColumn('updated_at', 'timestamp')
                ->addColumn('deleted_at', 'timestamp')
                
                
                
                ->addColumn('created_at', 'timestamp')
                ->addColumn('updated_at', 'timestamp')
                ->create();
    }
}
