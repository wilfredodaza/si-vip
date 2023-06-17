<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TypeCompanyTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                             => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'                           => ['type' => 'VARCHAR', 'constraint' => 45 ],
            'description'                    => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'created_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                     => ['type' => 'TIMESTAMP', 'null' => true],

        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('type_companies');
    }

    public function down()
    {
        $this->forge->dropTable('type_companies');
    }
}
