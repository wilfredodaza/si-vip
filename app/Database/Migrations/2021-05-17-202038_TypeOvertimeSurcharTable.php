<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TypeOvertimeSurcharTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                             => ['type' => 'INT', 'constraint' => 11, 'unsigned'=> true, 'auto_increment' => true],
            'name'                           => ['type' => 'VARCHAR', 'constraint' => 191],
            'code'                           => ['type' => 'VARCHAR', 'constraint' => 45],
            'percentage'                     => ['type' => 'DOUBLE', 'constraint' => '5,2'],
            'created_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                     => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('type_overtime_surcharges');
	}

	public function down()
	{
        $this->forge->dropTable('type_overtime_surcharges');
	}
}
