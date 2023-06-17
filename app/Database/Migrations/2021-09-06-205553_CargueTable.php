<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CargueTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE ],
            'date'                          => ['type' => 'DATETIME', 'default' => NULL],
            'month_payroll'                 => ['type' => 'INT', 'constraint' => 11],
            'payroll_period'                => ['type' => 'INT', 'constraint' => 11],
            'load_number'                   => ['type' => 'INT', 'constraint' => 11],
            'nit'                           => ['type' => 'INT', 'constraint' => 11],
            'data'                          => ['type' => 'TEXT'],
            'period_id'                     => ['type' => 'INT', 'constraint' => 11],
            'payment_dates'                 => ['type' => 'TEXT'],
            'status'                        => ['type' => 'ENUM("Inactive","Active")', 'default' => 'Inactive']
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('cargue');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('cargue');
    }
}
