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
                ->addColumn('inDate', 'datetime')
                ->addColumn('effectDate', 'datetime')
                ->addColumn('duration', 'integer')
                ->addColumn('getter_id', 'integer')
                ->addColumn('owner_id', 'integer')
                ->addColumn('object_id', 'integer')
                ->addColumn('discount', 'float')
                ->addColumn('price', 'float')
                ->addColumn('description', 'text')
                ->addColumn('options', 'text')
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime',['null' => true])
                ->addColumn('deleted_at', 'datetime',['null' => true])
                ->create();
    }
}
