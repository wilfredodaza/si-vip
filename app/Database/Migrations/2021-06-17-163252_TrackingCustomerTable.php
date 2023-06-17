<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TrackingCustomerTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'companies_id'                  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'type_tracking_id'              => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'table_id'                      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'message'                       => ['type' => 'TEXT'],
            'username'                      => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'file'                          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'                    => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                    => ['type' => 'TIMESTAMP', 'null' => true],

        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_tracking_id', 'type_tracking', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tracking_customer');
	}

	public function down()
	{
        $this->forge->dropForeignKey('tracking_customer','tracking_customer_companies_id_foreign');
        $this->forge->dropForeignKey('tracking_customer','tracking_customer_type_tracking_id_foreign');
        $this->forge->dropTable('tracking_customer');
	}
}
