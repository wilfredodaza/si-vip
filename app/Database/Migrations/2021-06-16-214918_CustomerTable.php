<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CustomerTable extends Migration
{
	public function up()
	{
	    $this->forge->addField([
            'id'                                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'type_customer_id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'type_regime_id'                    => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'type_document_identifications_id'  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'municipality_id'                   => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'companies_id'                      => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'type_organization_id'              => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'user_id'                           => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'type_liability_id'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'name'                              => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'identification_number'             => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'dv'                                => ['type' => 'INT', 'constraint' => 1, 'null' => true ],
            'phone'                             => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'address'                           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'email'                             => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'email2'                            => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'email3'                            => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'merchant_registration'             => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'status'                            => ['type' => 'ENUM("Activo", "Inactivo")', 'default' => 'Activo', 'null' => true],
            'firm'                              => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'rut'                               => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'bank_certificate'                  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'postal_code'                       => ['type' => 'VARCHAR', 'constraint' => 6, 'null' => true],
            'created_at'                        => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                        => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                        => ['type' => 'TIMESTAMP', 'null' => true]
    ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_document_identifications_id', 'type_document_identifications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_customer_id', 'type_customer', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_regime_id', 'type_regimes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('municipality_id', 'municipalities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_organization_id', 'type_organizations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('customers');
	}

	public function down()
	{
        $this->forge->dropForeignKey('customers','customers_companies_id_foreign');
        $this->forge->dropForeignKey('customers','customers_municipality_id_foreign');
        $this->forge->dropForeignKey('customers','customers_type_customer_id_foreign');
        $this->forge->dropForeignKey('customers','customers_type_document_identifications_id_foreign');
        $this->forge->dropForeignKey('customers','customers_type_organization_id_foreign');
        $this->forge->dropForeignKey('customers','customers_type_regime_id_foreign');
        $this->forge->dropTable('customers');
	}
}
