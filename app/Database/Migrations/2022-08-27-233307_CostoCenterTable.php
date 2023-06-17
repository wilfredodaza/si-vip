<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CostoCenterTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'company_id'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'name'                       => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'code'                       => ['type' => 'VARCHAR', 'constraint' => 45 ],
            'status'                     => ['type' => 'ENUM("Activa", "Inactivo")'],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cost_center');
    }

    public function down()
    {
        $this->forge->dropForeignKey('cost_center','cost_center_company_id_foreign');
        $this->forge->dropTable('cost_center');
    }
}
