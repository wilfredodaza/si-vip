<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CertificateTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'companies_id'      => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => false],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 191],
            'password'          => ['type' => 'VARCHAR', 'constraint' => 191],
            'created_at'        => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'        => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('certificates');
	}

	public function down()
	{
        $this->forge->dropForeignKey('certificates','certificates_companies_id_foreign');
        $this->forge->dropTable('certificates');
	}
}
