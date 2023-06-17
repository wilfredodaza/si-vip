<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TypeTrackingTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                             => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'name'                           => ['type' => 'VARCHAR', 'constraint' => 45],
            'created_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                     => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('type_tracking');
	}

	public function down()
	{
        $this->forge->dropTable('type_tracking');
	}
}
