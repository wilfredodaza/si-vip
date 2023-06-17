<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InvoiceStatusTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => TRUE ],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 45],
            'description'   => ['type' => 'TEXT' ],
            'block'         => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => TRUE],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => TRUE],
            'updated_at'    => ['type' => 'TIMESTAMP',  'null' => TRUE],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('invoice_status');
    }

    public function down()
    {
        $this->forge->dropTable('invoice_status');
    }
}
