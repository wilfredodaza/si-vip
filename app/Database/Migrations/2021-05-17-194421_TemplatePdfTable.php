<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TemplatePdfTable extends Migration
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
        $this->forge->createTable('template_pdf');
	}

	public function down()
	{
        $this->forge->dropTable('template_pdf');
	}
}
