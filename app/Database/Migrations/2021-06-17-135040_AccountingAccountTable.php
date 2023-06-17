<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AccountingAccountTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                                        => ['type' => 'INT', 'constraint' => 10, 'unsigned'=> true, 'auto_increment' => true],
            'companies_id'                              => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'type_accounting_account_id'                => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'code'                                      => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'name'                                      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'percent'                                   => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true ],
            'nature'                                    => ['type' => 'ENUM("Crédito", "Débito")', 'default' => 'Crédito', 'null' => true],
            'status'                                    => ['type' => 'ENUM("Activa", "Inactivo")', 'default' => 'Activa', 'null' => true],
            'created_at'                                => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                                => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_accounting_account_id', 'type_accounting_account', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('accounting_account');
	}

	public function down()
	{
        $this->forge->dropForeignKey('accounting_account','accounting_account_companies_id_foreign');
        $this->forge->dropForeignKey('accounting_account', 'accounting_account_type_accounting_account_id_foreign');
        $this->forge->dropTable('accounting_account');
	}
}
