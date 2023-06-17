<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InvoicesTypeFileTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'                       => ['type' => 'VARCHAR', 'constraint' => 100 ],
            'description'                => ['type' => 'TEXT'],
            'block'                      => ['type' => 'VARCHAR', 'constraint' => 45 ],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('invoices_type_files');
    }

    public function down()
    {
        $this->forge->dropTable('invoices_type_files');
    }
}
