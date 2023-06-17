<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MunicipalityTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 191],
            'code'          => ['type' => 'CHAR', 'constraint' => 191],
            'department_id' => ['type' => 'BIGINT', 'constraint' => 20,  'unsigned' => true, 'null' => TRUE],
            'created_at'    => ['type' => 'TIMESTAMP',  'null' => TRUE],
            'updated_at'    => ['type' => 'TIMESTAMP',  'null' => TRUE],
            'codefacturador'=> ['type' => 'VARCHAR', 'constraint' => 30, 'null' => TRUE],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('municipalities');

    }

    public function down()
    {
        $this->forge->dropForeignKey('municipalities','municipalities_department_id_foreign');
        $this->forge->dropTable('municipalities');
    }
}
