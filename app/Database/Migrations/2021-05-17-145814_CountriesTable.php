<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CountriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'constraint' => 20, 'auto_increment' => TRUE, 'unsigned' => TRUE],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 191],
            'code'          => ['type' => 'CHAR', 'constraint' => 191],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => TRUE],
            'updated_at'    => ['type' => 'TIMESTAMP',  'null' => TRUE],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('countries');
    }

    public function down()
    {
        $this->forge->dropTable('countries');
    }
}
