<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SellerTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                             => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE ],
            'name'                           => ['type' => 'VARCHAR', 'constraint' => 150],
            'identification_number'          => ['type' => 'INT', 'constraint' => 11],
            'phone'                          => ['type' => 'VARCHAR', 'constraint' => 30],
            'status'                         => ['type' => "ENUM('Activo', 'Inactivo')", 'null' => true],
            'link'                           => ['type' => 'VARCHAR', 'constraint' => 250],

        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('sellers');
	}

	public function down()
	{
        $this->forge->dropTable('sellers');
	}
}
