<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AplicantStatusTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE ],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => TRUE],
            'updated_at'    => ['type' => 'TIMESTAMP',  'null' => TRUE],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('applicant_status');
    }

    public function down()
    {
        $this->forge->dropTable('applicant_status');
    }
}
