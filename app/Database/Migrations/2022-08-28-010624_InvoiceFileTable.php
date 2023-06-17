<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InvoiceFileTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'invoices_id'                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'invoices_type_files_id'     => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'users_id'                   => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE ],
            'name'                       => ['type' => 'VARCHAR', 'constraint' => 100 ],
            'number'                     => ['type' => 'VARCHAR', 'constraint' => 100 ],
            'observation'                => ['type' => 'TEXT'],
            'status'                     => ['type' => "ENUM('Aceptado', 'Rechazado', 'Pendiente')"],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('invoices_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('invoices_type_files_id', 'invoices_type_files', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('users_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('invoices_files');
    }

    public function down()
    {
        $this->forge->dropForeignKey('invoices_files','invoices_files_invoices_id_foreign');
        $this->forge->dropForeignKey('invoices_files','invoices_files_users_id_foreign');
        $this->forge->dropForeignKey('invoices_files','invoices_files_invoices_type_files_id_foreign');
        $this->forge->dropTable('invoices_files');
    }
}
