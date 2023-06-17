<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class WithholdingInvoicesTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'invoice_id'                    => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'accounting_account_id'         => ['type' => 'VARCHAR', 'constraint' => 45],
            'percent'                       => ['type' => 'DECIMAL', 'constraint' => '20,3'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('invoice_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('withholding_invoices');
	}

	public function down()
	{
        $this->forge->dropForeignKey('withholding_invoices','withholding_invoices_invoice_id_foreign');
        $this->forge->dropTable('withholding_invoices');
	}
}
