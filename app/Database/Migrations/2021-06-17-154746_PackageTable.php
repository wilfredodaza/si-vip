<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PackageTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'constraint' => 11, 'unsigned'=> true,  'auto_increment' => TRUE ],
            'name'                  => ['type' => 'VARCHAR', 'constraint' => 255],
            'description'           => ['type' => 'TEXT', 'null' => TRUE],
            'quantity_document'     => ['type' => 'INT', 'constraint' => 11],
            'price'                 => ['type' => 'DECIMAL', 'constraint' => '10,0', 'null' => TRUE]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('packages');
    }

    public function down()
    {
        $this->forge->dropTable('packages');
    }
}
