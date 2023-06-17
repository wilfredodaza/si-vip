<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AssociateDocumentTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'documents_id'                  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'null' => true],
            'name'                          => ['type' => 'VARCHAR', 'constraint' => 255],
            'extension'                     => ['type' => 'VARCHAR', 'constraint' => 45],
            'new_name'                      => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at'                    => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                    => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('documents_id', 'documents', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('associate_document');
	}

	public function down()
	{
        $this->forge->dropForeignKey('associate_document', 'associate_document_documents_id_foreign');
        $this->forge->dropTable('associate_document');
	}
}
