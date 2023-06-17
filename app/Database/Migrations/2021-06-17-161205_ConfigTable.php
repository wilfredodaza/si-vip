<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConfigTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'companies_id'                  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'economic_activity'             => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
            'name_type_doc_id'              => ['type' => 'TEXT', 'null' => true],
            'responsable_iva'               => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'logo'                          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'default_notes'                 => ['type' => 'TEXT', 'null' => true],
            'quantity_decimal'              => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'days_notification'              => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('config');
	}

	public function down()
	{
        $this->forge->dropForeignKey('config','config_companies_id_foreign');
        $this->forge->dropTable('config');
	}
}
