<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SectionShoppingTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'                       => ['type' => 'VARCHAR', 'constraint' => 255 ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('section_shopping');
    }

    public function down()
    {
        $this->forge->dropTable('section_shopping');
    }
}
