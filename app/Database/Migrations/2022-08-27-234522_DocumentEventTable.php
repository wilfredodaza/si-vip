<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DocumentEventTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'document_id'                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'event_id'                   => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'type_rejection_id'          => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'uuid'                       => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('document_id', 'documents', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('document_event');
    }

    public function down()
    {
        $this->forge->dropForeignKey('document_event','document_event_document_id_foreign');
        $this->forge->dropTable('document_event');
    }
}
