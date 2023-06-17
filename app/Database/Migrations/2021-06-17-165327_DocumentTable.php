<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DocumentTable extends Migration
{
	public function up()
    {
        $this->forge->addField([
            'id'                            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'companies_id'                  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true],
            'invoice_id'                    => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'null' => true],
            'document_status_id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned'=> true, 'null' => true],
            'name'                          => ['type' => 'VARCHAR', 'constraint' => 255],
            'extension'                     => ['type' => 'VARCHAR', 'constraint' => 45],
            'new_name'                      => ['type' => 'VARCHAR', 'constraint' => 255],
            'provider'                      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true] ,
            'zip'                           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true] ,
            'uuid'                          => ['type' => 'ENUM("true", "false")'],
            'created_at'                    => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                    => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('invoice_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('document_status_id', 'document_status', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('documents');
    }

    public function down()
    {
        $this->forge->dropForeignKey('documents','documents_companies_id_foreign');
        $this->forge->dropForeignKey('documents','documents_document_status_id_foreign');
        $this->forge->dropForeignKey('documents','documents_invoice_id_foreign');
        $this->forge->dropTable('documents');
    }
}
