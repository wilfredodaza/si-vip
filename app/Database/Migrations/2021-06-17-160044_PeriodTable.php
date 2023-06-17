<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PeriodTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'month'         => ['type' => 'VARCHAR', 'constraint' => 45],
            'year'          => ['type' => 'VARCHAR', 'constraint' => 45],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => TRUE],
            'updated_at'    => ['type' => 'TIMESTAMP',  'null' => TRUE],
            'deleted_at'    => ['type' => 'TIMESTAMP',  'null' => TRUE]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('periods');
    }

    public function down()
    {
        $this->forge->dropTable('periods');
    }
}
