<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DiscrepancyResponseTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'                       => ['type' => 'VARCHAR', 'constraint' => 191 ],
            'code'                       => ['type' => 'VARCHAR', 'constraint' => 45 ],
            'block'                      => ['type' => 'VARCHAR', 'constraint' => 4 ],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('discrepancy_responses');
    }

    public function down()
    {
        $this->forge->dropTable('discrepancy_responses');
    }
}
