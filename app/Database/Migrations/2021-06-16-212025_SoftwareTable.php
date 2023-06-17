<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SoftwareTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'companies_id'      => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'identifier'        => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'pin'               => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'identifier_payroll'=> ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'pin_payroll'       => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'created_at'        => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'        => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('software');
	}

	public function down()
	{
        $this->forge->dropForeignKey('software','software_companies_id_foreign');
        $this->forge->dropTable('software');
	}
}
