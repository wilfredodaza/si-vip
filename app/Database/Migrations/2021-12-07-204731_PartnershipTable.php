<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PartnershipTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                             => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'company_id'                     => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'type_document_identification_id'=> ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'type_liability_id'              => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'type_regime_id'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'tax_id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'name'                            => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'identification_number'          => ['type' => 'VARCHAR', 'constraint' => 45 ],
            'dv'                             => ['type' => 'CHAR', 'constraint' => 1],
            'participation_percentage'       => ['type' => 'DECIMAL', 'constraint' => '20,2'],
            'created_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                     => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('partnerships');
    }

    public function down()
    {
        $this->forge->dropForeignKey('partnerships','partnerships_company_id_foreign');
        $this->forge->dropTable('partnerships');
    }
}
