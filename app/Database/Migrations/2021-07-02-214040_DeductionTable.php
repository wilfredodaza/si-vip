<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DeductionTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                                        => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'payroll_id'                                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'type_deduction_id'                         => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE ],
            'type_law_deduction_id'                     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE,  'null' => true],
            'payment'                                   => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'percentage'                                => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'description'                               => ['type' => 'TEXT', 'null' => true],
            'created_at'                                => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                                => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                                => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('payroll_id', 'payrolls', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_deduction_id', 'type_deductions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_law_deduction_id', 'type_law_deductions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('deductions');

	}

	public function down()
	{
        $this->forge->dropForeignKey('deductions','deductions_payroll_id_foreign');
        $this->forge->dropForeignKey('deductions','deductions_type_deduction_id_foreign');
        $this->forge->dropForeignKey('deductions','deductions_type_law_deduction_id_foreign');
        $this->forge->dropTable('deductions');
	}
}
