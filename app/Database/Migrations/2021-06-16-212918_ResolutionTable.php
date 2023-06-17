<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ResolutionTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'companies_id'      => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'type_documents_id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'prefix'            => ['type' => 'CHAR', 'constraint' => 4, 'null' => true],
            'resolution'        => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'resolution_date'   => ['type' => 'DATE'],
            'technical_key'     => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'from'              => ['type' => 'BIGINT', 'constraint' => 20],
            'to'                => ['type' => 'BIGINT', 'constraint' => 20],
            'date_from'         => ['type' => 'DATE'],
            'date_to'           => ['type' => 'DATE'],
            'priority'          => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'status'            => ['type' => 'ENUM("Activo", "Inactivo")', 'null' => true],
            'created_at'        => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'        => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_documents_id', 'type_documents', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('resolutions');
	}

	public function down()
	{
        $this->forge->dropForeignKey('resolutions','resolutions_companies_id_foreign');
        $this->forge->dropForeignKey('resolutions','resolutions_type_documents_id_foreign');
        $this->forge->dropTable('resolutions');
	}
}
