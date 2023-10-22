<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateArticlesTable extends AbstractMigration
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
        $table = $this->table('articles');
        $table->addColumn('name', 'string')
                ->addColumn('ref', 'string')
                ->addColumn('maderId', 'integer', ['null' => true])
                ->addColumn('pvc', 'float', ['null' => true])
                ->addColumn('pvp', 'float', ['null' => true])
                ->addColumn('observations', 'string', ['null' => true])
                ->addColumn('accesories', 'string', ['null' => true])
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime', ['null' => true])
                ->addColumn('deleted_at', 'datetime', ['null' => true])
                ->addForeignKey(['maderId'], 'maders', ['id'])
                ->create();      
    }
}