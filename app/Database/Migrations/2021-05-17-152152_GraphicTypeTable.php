<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GraphicTypeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE, 'unsigned'=> true ],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 45],
            'component'     => ['type' => 'VARCHAR', 'constraint' => 45]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('graphic_type');
    }

    public function down()
    {
        $this->forge->dropTable('graphic_type');
    }
}
