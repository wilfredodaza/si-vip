<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OtherBankTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE ],
            'bank_id'                       => ['type' => 'INT', 'constraint' => 10,'unsigned'=> true],
            'companies_id'                  => ['type' => 'BIGINT', 'constraint' => 20,'unsigned'=> true ],
            'name'                          => ['type' => 'VARCHAR', 'constraint' => 100],
            'status'                        => ['type' => 'ENUM("Active","Inactive")', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('bank_id', 'banks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('other_bank');
    }

    public function down()
    {
        $this->forge->dropForeignKey('other_bank','other_bank_bank_id_foreign');
        $this->forge->dropForeignKey('other_bank','other_bank_companies_id_foreign');
        $this->forge->dropTable('other_bank');
    }
}
