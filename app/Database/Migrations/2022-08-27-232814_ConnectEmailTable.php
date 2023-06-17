<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConnectEmailTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'company_id'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'server'                     => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'email'                      => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'password'                   => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'port'                       => ['type' => 'INT', 'constraint' => 11 ],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('connect_emails');
    }

    public function down()
    {
        $this->forge->dropForeignKey('connect_emails','connect_emails_company_id_foreign');
        $this->forge->dropTable('connect_emails');
    }
}
