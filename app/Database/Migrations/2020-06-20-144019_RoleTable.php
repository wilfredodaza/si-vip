<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RoleTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
		    'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'companies_id'      => ['type' => 'BIGINT', 'constraint'  => 20, 'null' => TRUE, 'unsigned' => TRUE],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 40],
            'description'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'type'              => ['type' => 'ENUM("Por Defecto", "Personalizado")',  'default' => 'Por Defecto'],
            'created_at'        => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'        => ['type' => 'TIMESTAMP', 'null' => true],
            'status'            => ['type' => 'ENUM("Activo", "Inactivo")',  'default' => 'Activo'],
        ]);
		$this->forge->addKey('id', TRUE);
      //  $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
		$this->forge->createTable('roles');
	}

	public function down()
	{
		$this->forge->dropTable('roles');
	}
}
