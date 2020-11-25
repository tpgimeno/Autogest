<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBuyInvoicesTable extends AbstractMigration
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
        $table = $this->table('buyInvoices');
        $table->addColumn('invoiceNumber', 'string')
                ->addColumn('date', 'date')
                ->addColumn('providorId', 'integer')
                ->addColumn('articles', 'string')
                ->addColumn('base', 'float')
                ->addColumn('tva', 'float')
                ->addColumn('total', 'float')
                ->addColumn('observations', 'string')
                ->addColumn('text', 'string')
                ->addColumn('created_at', 'datetime')
                ->addColumn('updated_at', 'datetime')
                ->addColumn('deleted_at', 'datetime')
                ->addIndex('invoiceNumber', ['unique' => true])
                ->create();
    }
}
