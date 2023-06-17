<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StatusShoppingTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'section_shopping_id'        => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'name'                       => ['type' => 'VARCHAR', 'constraint' => 255 ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('section_shopping_id', 'section_shopping', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('status_shopping');
    }

    public function down()
    {
        $this->forge->dropForeignKey('status_shopping','status_shopping_section_shopping_id_foreign');
        $this->forge->dropTable('status_shopping');
    }
}
