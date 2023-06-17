<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TypeDeductionTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'                           => ['type' => 'VARCHAR', 'constraint' => 45],
            'component'                      => ['type' => 'VARCHAR', 'constraint' => 10],
            'domain'                         => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => TRUE],
            'group'                          => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => TRUE],
            'element'                        => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => TRUE],
            'created_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                     => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('type_deductions');
    }

    public function down()
    {
        $this->forge->dropTable('type_deductions');
    }
}
