<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NotificationTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'companies_id'                  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'title'                         => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'body'                          => ['type' => 'TEXT'],
            'icon'                          => ['type' => 'VARCHAR', 'constraint' => 45 ],
            'color'                         => ['type' => 'ENUM("","cyan","amber","orange","purple","red darken-1")', 'default' => 'cyan', 'null' => true],
            'status'                        => ['type' => 'ENUM("Active", "Inactive")'],
            'view'                          =>  ['type' => 'ENUM("true", "false")'],
            'created_at'                    => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', true);

        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notifications');
	}

	public function down()
	{
        $this->forge->dropTable('notifications');
	}
}
