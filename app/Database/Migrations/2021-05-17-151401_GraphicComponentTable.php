<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GraphicComponentTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE , 'unsigned'=> true],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 45],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => TRUE],
            'updated_at'    => ['type' => 'TIMESTAMP',  'null' => TRUE],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('graphic_component');
    }

    public function down()
    {
        $this->forge->dropTable('graphic_component');
    }
}
