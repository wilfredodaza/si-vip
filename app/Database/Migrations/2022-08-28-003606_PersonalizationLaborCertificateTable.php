<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PersonalizationLaborCertificateTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'company_id'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'municipality_id'            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'telephone'                  => ['type' => 'VARCHAR', 'constraint' => 45 ],
            'email'                      => ['type' => 'VARCHAR', 'constraint' => 191],
            'web_page'                   => ['type' => 'VARCHAR', 'constraint' => 191],
            'address'                    => ['type' => 'VARCHAR', 'constraint' => 191],
            'payroll_manager'            => ['type' => 'VARCHAR', 'constraint' => 191],
            'payroll_work_manager'       => ['type' => 'VARCHAR', 'constraint' => 191],
            'firm'                       => ['type' => 'VARCHAR', 'constraint' => 255],
            'stamp'                      => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('municipality_id', 'municipalities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('personalization_labor_certificates');
    }

    public function down()
    {
        $this->forge->dropForeignKey('personalization_labor_certificates','personalization_labor_certificates_company_id_foreign');
        $this->forge->dropForeignKey('personalization_labor_certificates','personalization_labor_certificates_municipality_id_foreign');
        $this->forge->dropTable('personalization_labor_certificates');
    }
}
