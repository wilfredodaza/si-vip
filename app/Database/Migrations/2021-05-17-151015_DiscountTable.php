<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DiscountTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 191],
            'code'          => ['type' => 'CHAR', 'constraint' => 191],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => TRUE],
            'updated_at'    => ['type' => 'TIMESTAMP',  'null' => TRUE],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('discounts');
    }

    public function down()
    {
        $this->forge->dropTable('discounts');
    }
}
