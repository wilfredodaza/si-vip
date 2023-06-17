<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TypePayrollAdjustNoteTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                                        => ['type' => 'INT',        'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'                                      => ['type' => 'VARCHAR',    'constraint' => 45],
            'code'                                      => ['type' => 'VARCHAR',    'constraint' => 45],
            'created_at'                                => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                                => ['type' => 'TIMESTAMP', 'null' => true]
        ]);

        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('type_payroll_adjust_notes');
	}

	public function down()
	{
        $this->forge->dropTable('type_payroll_adjust_notes');
	}
}
