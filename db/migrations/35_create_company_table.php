<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCompanyTable extends AbstractMigration
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
        $table = $this->table('company');
        $table->addColumn('name', 'string')
                ->addColumn('fiscalId', 'string')
                ->addColumn('fiscalName', 'string')
                ->addColumn('address', 'string')
                ->addColumn('city', 'string')
                ->addColumn('postalCode', 'integer')
                ->addColumn('state', 'string')
                ->addColumn('country', 'string')
                ->addColumn('phone', 'string')
                ->addColumn('email', 'string')
                ->addColumn('site', 'string', ['null' => true])
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime', ['null' => true])
                ->addColumn('deleted_at', 'datetime', ['null' => true])
                ->addIndex('fiscalId', ['unique' => true])
                ->create();
    }
}