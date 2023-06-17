<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InvoiceDocumentUploadTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'invoice_id'                    => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'title'                         => ['type' => 'VARCHAR', 'constraint' => 45],
            'file'                          => ['type' => 'VARCHAR', 'constraint' => 45],
            'created_at'                    => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                    => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('invoice_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('invoice_document_upload');
	}

	public function down()
	{
        $this->forge->dropForeignKey('invoice_document_upload','invoice_document_upload_invoice_id_foreign');
        $this->forge->dropTable('invoice_document_upload');
	}
}
