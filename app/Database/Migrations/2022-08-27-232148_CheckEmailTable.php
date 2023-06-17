<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CheckEmailTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'company_id'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'folder'                     => ['type' => 'VARCHAR', 'constraint' => 45 ],
            'date'                       => ['type' => 'DATE', 'null' => FALSE ],
            'email_id'                   => ['type' => 'VARCHAR',  'constraint' => 45, 'null' => FALSE],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('check_emails');
    }

    public function down()
    {
        $this->forge->dropForeignKey('check_emails','check_emails_company_id_foreign');
        $this->forge->dropTable('check_emails');
    }
}
