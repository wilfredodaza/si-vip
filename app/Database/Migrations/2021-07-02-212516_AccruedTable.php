<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AccruedTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                                        => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'payroll_id'                                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'type_accrued_id'                           => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE ],
            'type_overtime_surcharge_id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE,  'null' => true],
            'type_disability_id'                        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'null' => true ],
            'payment'                                   => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'start_time'                                => ['type' => 'DATETIME', 'null' => true],
            'end_time'                                  => ['type' => 'DATETIME', 'null' => true],
            'quantity'                                  =>['type' => 'INT', 'constraint' => '11', 'null' => true],
            'percentage'                                => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'description'                               => ['type' => 'TEXT', 'null' => true],
            'other_payments'                            => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'created_at'                                => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                                => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                                => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('payroll_id', 'payrolls', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_accrued_id', 'type_accrueds', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_overtime_surcharge_id', 'type_overtime_surcharges', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_disability_id', 'type_disabilities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('accrueds');
	}

	public function down()
	{
        $this->forge->dropForeignKey('accrueds','accrueds_payroll_id_foreign');
        $this->forge->dropForeignKey('accrueds','accrueds_type_accrued_id_foreign');
        $this->forge->dropForeignKey('accrueds','accrueds_type_disability_id_foreign');
        $this->forge->dropForeignKey('accrueds','accrueds_type_overtime_surcharge_id_foreign');
        $this->forge->dropTable('accrueds');
	}
}
