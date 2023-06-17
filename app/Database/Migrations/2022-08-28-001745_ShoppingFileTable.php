<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ShoppingFileTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'shopping_email_id'         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'name'                       => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('shopping_email_id', 'shopping_emails', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('shopping_files');
    }

    public function down()
    {
        $this->forge->dropForeignKey('shopping_files','shopping_files_shopping_email_id_foreign');
        $this->forge->dropTable('shopping_files');
    }
}
