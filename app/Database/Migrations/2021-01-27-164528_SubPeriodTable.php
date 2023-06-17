<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SubPeriodTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'companies_id'               => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment'],
            'name'                       => ['type' => 'VARCHAR', 'constraint' => 255 ]

        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('sub_periods');
    }

    public function down()
    {
        $this->forge->dropTable('sub_periods');
    }
}
