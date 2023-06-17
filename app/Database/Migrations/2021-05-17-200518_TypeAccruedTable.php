<?php

namespace App\Database\Migrations;

use Cassandra\Exception\TruncateException;
use CodeIgniter\Database\Migration;

class TypeAccruedTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                             => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'                           => ['type' => 'VARCHAR', 'constraint' => 190],
            'component'                      => ['type' => 'VARCHAR', 'constraint' => 45],
            'domain'                         => ['type' => 'VARCHAR', 'constraint' => 45],
            'group'                          => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => TRUE],
            'element'                        => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => TRUE],
            'created_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                     => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('type_accrueds');
    }
    public function down()
    {
        $this->forge->dropTable('type_accrueds');
    }
}
