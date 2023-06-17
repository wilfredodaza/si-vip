<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProductTransferTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'auto_increment' => TRUE ],
            'companies_id'               => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'product_id'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'destination_product_id'     => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'quantity'                   => ['type' => 'INT', 'constraint' => 11 ],
            'destination_headquarters'   => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'user_id'                    => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE],
            'type_document_id'           => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('destination_headquarters', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('destination_product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_document_id', 'type_documents', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('product_transfer');
    }

    public function down()
    {
        $this->forge->dropTable('product_transfer');
    }
}
