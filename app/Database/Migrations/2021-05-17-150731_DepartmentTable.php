<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DepartmentTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => TRUE ],
            'country_id'    => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 191],
            'code'          => ['type' => 'CHAR', 'constraint' => 191],
            'created_at'    => ['type' => 'TIMESTAMP', 'null' => TRUE],
            'updated_at'    => ['type' => 'TIMESTAMP',  'null' => TRUE]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('country_id', 'countries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('departments');

    }

    public function down()
    {
        $this->forge->dropForeignKey('departments','departments_country_id_foreign');
        $this->forge->dropTable('departments');
    }
}
