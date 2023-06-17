<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned'=> true, 'auto_increment' => true],
            'role_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'companies_id'  => [ 'type' => 'BIGINT', 'constraint' => 20,  'unsigned' => true, 'null' => true],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 45],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 191],
            'username'      => ['type' => 'VARCHAR', 'constraint' => 45],
            'password'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'photo'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status'        => ['type' => 'ENUM("active","inactive")', 'default' => 'active', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('users');
    }

	public function down()
	{

        $this->forge->dropForeignKey('users','users_companies_id_foreign');
        $this->forge->dropForeignKey('users','users_role_id_foreign');
        $this->forge->dropTable('users');
	}
}
