<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CompaniesTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                                    => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'taxes_id'                              => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'type_currencies_id'                    => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'type_liabilities_id'                   => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'type_organizations_id'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'type_document_identifications_id'      => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'countries_id'                          => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'departments_id'                        => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'municipalities_id'                     => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'languages_id'                          => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'type_operations_id'                    => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'type_regimes_id'                       => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'type_environments_id'                  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'type_environment_payroll_id'           => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'template_pdf_id'                       => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'type_company_id'                       => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'company'                               => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'identification_number'                 => ['type' => 'VARCHAR', 'constraint' => 191],
            'dv'                                    => ['type' => 'CHAR', 'constraint' => 1 ],
            'merchant_registration'                 => ['type' => 'VARCHAR', 'constraint' => 10],
            'address'                               => ['type' => 'VARCHAR', 'constraint' => 191],
            'email'                                 => ['type' => 'VARCHAR', 'constraint' => 100],
            'token'                                 => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'phone'                                 => ['type' => 'VARCHAR', 'constraint' => 30],
            'testId'                                => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'                            => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                            => ['type' => 'TIMESTAMP', 'null' => true],
            'headquarters_id'                       => ['type' => 'BIGINT', 'constraint' => 20,  'default' => 1]
            ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('taxes_id', 'taxes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_currencies_id', 'type_currencies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_liabilities_id', 'type_liabilities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_organizations_id', 'type_organizations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_document_identifications_id', 'type_document_identifications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('countries_id', 'countries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('departments_id', 'departments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('municipalities_id', 'municipalities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('languages_id', 'languages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_operations_id', 'type_operations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_regimes_id', 'type_regimes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_environments_id', 'type_environments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('template_pdf_id', 'template_pdf', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_company_id', 'type_companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('headquarters_id', 'headquarters', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('companies');
	}

	public function down()
	{
        $this->forge->dropForeignKey('companies','companies_taxes_id_foreign');
        $this->forge->dropForeignKey('companies','companies_type_currencies_id_foreign');
        $this->forge->dropForeignKey('companies','companies_type_liabilities_id_foreign');
        $this->forge->dropForeignKey('companies','companies_type_organizations_id_foreign');
        $this->forge->dropForeignKey('companies','companies_type_document_identifications_id_foreign');
        $this->forge->dropForeignKey('companies','companies_countries_id_foreign');
        $this->forge->dropForeignKey('companies','companies_departments_id_foreign');
        $this->forge->dropForeignKey('companies','companies_municipalities_id_foreign');
        $this->forge->dropForeignKey('companies','companies_languages_id_foreign');
        $this->forge->dropForeignKey('companies','companies_type_operations_id_foreign');
        $this->forge->dropForeignKey('companies','companies_type_regimes_id_foreign');
        $this->forge->dropForeignKey('companies','companies_type_environments_id_foreign');
        $this->forge->dropForeignKey('companies','companies_template_pdf_id_foreign');
        $this->forge->dropForeignKey('companies','companies_type_company_id_foreign');
        $this->forge->dropForeignKey('companies','companies_headquarters_id_foreign');
        $this->forge->dropTable('companies');
	}
}
