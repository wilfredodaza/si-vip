<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AccountingFileTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                             => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'company_id'                     => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE],
            'title'                          => ['type' => 'VARCHAR', 'constraint' => 45 ],
            'filename'                       => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'description'                    => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'type'                           => ['type' => "ENUM('Extracto', 'Caja Menor', 'Otros')", 'default' => NULL],
            'status'                         => ['type' => "ENUM('Pendiente', 'Contabilizado')", 'default' => NULL],
            'observation'                    => ['type' => 'VARCHAR', 'constraint' => 200 ],
            'created_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('accounting_files');
	}

	public function down()
	{
        $this->forge->dropTable('accounting_files');
	}
}
