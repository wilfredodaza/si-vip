<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TypeCustomerTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => TRUE ],
            'name'                           => ['type' => 'VARCHAR', 'constraint' => 45],
            'created_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                     => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('type_customer');
    }

    public function down()
    {
        $this->forge->dropTable('type_customer');
    }
}
