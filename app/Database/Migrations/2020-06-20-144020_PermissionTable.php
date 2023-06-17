<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PermissionTable extends Migration
{
	public function up()
	{

		$this->forge->addField([
		    'id'                => ['type' => 'INT','constraint' => 11, 'auto_increment' => TRUE, 'unsigned' => TRUE],
            'role_id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'menu_id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'created_at'        => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'        => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'        => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
		$this->forge->addKey('id', TRUE);
		$this->forge->addForeignKey('role_id', 'roles', 'id');
		$this->forge->addForeignKey('menu_id', 'menus', 'id');
		$this->forge->createTable('permissions');
	}



	public function down()
	{
        $this->forge->dropForeignKey('permissions','permissions_menu_id_foreign');
        $this->forge->dropForeignKey('permissions','permissions_role_id_foreign');
        $this->forge->dropTable('permissions');
	}
}
