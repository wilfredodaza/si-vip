<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PayrollTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'invoice_id'                    => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'period_id'                     => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'sub_period_id'                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'null' => TRUE],
            'type_payroll_adjust_note_id'   => ['type' => 'INT',    'constraint' => 11, 'unsigned' => TRUE, 'null' => TRUE ],
            'settlement_start_date'         => ['type' => 'DATE'],
            'settlement_end_date'           => ['type' => 'DATE'],
            'worked_time'                   => ['type' => 'INT', 'constraint' => 11]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('invoice_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('period_id', 'periods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('sub_period_id', 'sub_periods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_payroll_adjust_note_id', 'type_payroll_adjust_notes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payrolls');
	}

	public function down()
	{
        $this->forge->dropForeignKey('payrolls','payrolls_invoice_id_foreign');
        $this->forge->dropForeignKey('payrolls','payrolls_period_id_foreign');
        $this->forge->dropForeignKey('payrolls','payrolls_sub_period_id_foreign');
        $this->forge->dropForeignKey('payrolls','payrolls_type_payroll_adjust_note_id_foreign');
        $this->forge->dropTable('payrolls');
	}
}
