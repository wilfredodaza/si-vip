<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GraphicCompaniesTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'companies_id'                  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'graphic_component_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'setting_json'                  => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('graphic_component_id', 'graphic_component', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('graphic_companies');
	}

	public function down()
	{
        $this->forge->dropTable('graphic_companies');
	}
}
