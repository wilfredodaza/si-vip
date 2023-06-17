<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PayrollDateTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                                        => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'invoice_id'                                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'payroll_date'                              => ['type' => 'DATE', 'null' => true],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('invoice_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payroll_dates');
	}

	public function down()
	{

        $this->forge->dropTable('payroll_dates');
	}
}
