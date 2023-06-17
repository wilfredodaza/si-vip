<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GraphicComponentTypeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                    => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => TRUE ],
            'graphic_type_id'       => ['type' => 'int', 'constraint' => 11, 'unsigned'=> true],
            'graphic_component_id'  => ['type' => 'int', 'constraint' => 11, 'unsigned'=> true]

        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('graphic_type_id', 'graphic_type', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('graphic_component_id', 'graphic_component', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('graphic_component_types');
    }

    public function down()
    {
        $this->forge->dropForeignKey('graphic_component_types','graphic_component_types_graphic_component_id_foreign');
        $this->forge->dropForeignKey('graphic_component_types','graphic_component_types_graphic_type_id_foreign');
        $this->forge->dropTable('graphic_component_types');
    }
}
