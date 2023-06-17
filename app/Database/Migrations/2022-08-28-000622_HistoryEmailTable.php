<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HistoryEmailTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'shopping_emails_id'         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'users_id'                   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE ],
            'file'                       => ['type' => 'ENUM("true", "false")', 'default' => 'false'],
            'observation'                => ['type' => 'TEXT', 'constraint' => 255 ],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('shopping_emails_id', 'shopping_emails', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('users_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('history_emails');
    }

    public function down()
    {
        $this->forge->dropForeignKey('history_emails','history_emails_shopping_emails_id_foreign');
        $this->forge->dropForeignKey('history_emails','history_emails_users_id_foreign');
        $this->forge->dropTable('history_emails');
    }
}
