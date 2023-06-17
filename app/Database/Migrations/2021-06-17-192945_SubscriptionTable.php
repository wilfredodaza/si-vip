<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SubscriptionTable extends Migration
{
	public function up()
    {
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'companies_id'          => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true,],
            'packages_id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true,],
            'applicant_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'start_date'            => ['type' => 'DATE'],
            'end_date'              => ['type' => 'DATE'],
            'date_due_certificate'  => ['type' => 'DATE'],
            'sopport_invoice'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'ref_epayco'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'price'                 => ['type' => 'DECIMAL', 'constraint' => '10,0'],
            'seller'                => ['type' => 'TEXT', 'null' => true],
            'seller_tip'            => ['type' => 'TEXT', 'null' => true],
            'status'                => ['type' => "ENUM('Inactivo', 'Activo', 'Suspendido')", 'null' => true],
            'type'                  => ['type' => "ENUM('Nuevo', 'Renovacion', 'Actualización')", 'null' => true],
            'observation'           => ['type' => 'TEXT', 'null' => true],
            'date'                  => ['type' => 'DATE', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('packages_id', 'packages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('applicant_id', 'applicant', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('subscriptions');


    }

	public function down()
	{
        $this->forge->dropForeignKey('subscriptions','subscriptions_applicant_id_foreign');
        $this->forge->dropForeignKey('subscriptions','subscriptions_companies_id_foreign');
        $this->forge->dropForeignKey('subscriptions','subscriptions_packages_id_foreign');
        $this->forge->dropTable('subscriptions');
	}
}
