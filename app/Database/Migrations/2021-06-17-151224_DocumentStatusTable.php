<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DocumentStatusTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 45],
            'description'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'color'         => ['type' => 'VARCHAR', 'constraint' => 20],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => TRUE],
            'updated_at'    => ['type' => 'TIMESTAMP',  'null' => TRUE],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('document_status');
    }

    public function down()
    {
        $this->forge->dropTable('document_status');
    }
}
