<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class WalletTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                            => ['type' => 'BIGINT', 'constraint'    => 20, 'unsigned'=> true, 'auto_increment' => true],
            'invoices_id'                   => ['type' => 'BIGINT', 'constraint'    => 20, 'unsigned' => true],
            'payment_method_id'             => ['type' => 'INT', 'constraint'       => 11, 'unsigned' => true],
            'user_id'                       => ['type' => 'INT', 'constraint'       => 11, 'unsigned' => true, 'null' => true],
            'description'                   => ['type' => 'TEXT'],
            'value'                         => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'soport'                        => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'created_at'                    => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                    => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('invoices_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('wallet');
	}

	public function down()
	{
        $this->forge->dropForeignKey('wallet','wallet_invoices_id_foreign');
        $this->forge->dropTable('wallet');
	}
}
