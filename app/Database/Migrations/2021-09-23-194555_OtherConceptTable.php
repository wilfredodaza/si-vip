<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OtherConceptTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                            => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE ],
            'companies_id'                  => ['type' => 'BIGINT', 'constraint' => 20,'unsigned'=> true ],
            'name'                          => ['type' => 'VARCHAR', 'constraint' => 100],
            'type_concept'                  => ['type' => 'ENUM("Devengado","Deduccion")'],
            'concept_dian'                  => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'type_other'                    => ['type' => 'ENUM("Comun","Profesional","Laboral")', 'null' => true],
            'status'                        => ['type' => 'ENUM("Active","Inactive")', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('other_concepts');
	}

	public function down()
	{
        $this->forge->dropForeignKey('other_concepts','other_concepts_companies_id_foreign');
        $this->forge->dropTable('other_concepts');
	}
}
