<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TypeAccountingAccountTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                             => ['type' => 'INT', 'constraint' => 10, 'unsigned'=> true, 'auto_increment' => true],
            'name'                           => ['type' => 'VARCHAR', 'constraint' => 45],
            'created_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                     => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('type_accounting_account');
	}

	public function down()
	{
        $this->forge->dropTable('type_accounting_account');
	}
}
