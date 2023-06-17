<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TypeDocumentIdentificationTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 191],
            'code'          => ['type' => 'CHAR', 'constraint' => 191],
            'block'         => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => TRUE],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'    => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('type_document_identifications');
    }

    public function down()
    {
        $this->forge->dropTable('type_document_identifications');
    }
}
