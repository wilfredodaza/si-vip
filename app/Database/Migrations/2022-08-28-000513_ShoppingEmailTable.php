<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ShoppingEmailTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'companies_id'               => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'invoices_id'                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'subject'                    => ['type' => 'TEXT'],
            'body'                       => ['type' => 'TEXT' ],
            'name'                       => ['type' => 'VARCHAR', 'constraint' => 100 ],
            'from_address'               => ['type' => 'VARCHAR', 'constraint' => 100 ],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('invoices_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('shopping_emails');
    }

    public function down()
    {
        $this->forge->dropForeignKey('shopping_emails','shopping_emails_companies_id_foreign');
        $this->forge->dropForeignKey('shopping_emails','shopping_emails_invoices_id_foreign');
        $this->forge->dropTable('shopping_emails');
    }
}
