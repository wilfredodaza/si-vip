<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HeadquartersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'auto_increment' => TRUE ],
            'name'                       => ['type' => 'VARCHAR', 'constraint' => 150]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('headquarters');
    }

    public function down()
    {
        $this->forge->dropTable('headquarters');
    }
}
