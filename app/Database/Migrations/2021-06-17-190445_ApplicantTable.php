<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ApplicantTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                             => ['type' => 'INT', 'constraint' => 11, 'unsigned'=> true, 'auto_increment' => true],
            'application_date'               => ['type' => 'DATETIME',  'null' => true],
            'company_name'                   => ['type' => 'VARCHAR','constraint' => 150, 'null' => true],
            'nit'                            => ['type' => 'VARCHAR','constraint' => 100, 'null' => true],
            'phone'                          => ['type' => 'VARCHAR','constraint' => 45, 'null' => true],
            'adress'                         => ['type' => 'VARCHAR','constraint' => 150, 'null' => true],
            'legal_representative'           => ['type' => 'VARCHAR','constraint' => 150, 'null' => true],
            'type_document'                  => ['type' => 'ENUM("cc", "nit", "otro")', 'null' => true],
            'num_documento'                  => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'email'                          => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'email_confirmation'             => ['type' => 'VARCHAR','constraint' => 300, 'null' => true],
            'contract'                       => ['type' => 'VARCHAR','constraint' => 250, 'null' => true],
            'autorizacion'                   => ['type' => 'VARCHAR','constraint' => 250, 'null' => true],
            'seller'                         => ['type' => 'BIGINT', 'constraint' => 20, 'null' => true],
            'process'                        => ['type' => 'ENUM("enuevo", "renovacion")', 'null' => true, 'default' => 'enuevo'],
            'status'                         => ['type' => 'INT','constraint' => 11, 'null' => true],
            'created_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                     => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('applicant');
	}

	public function down()
	{
        $this->forge->dropTable('applicant');
	}
}
