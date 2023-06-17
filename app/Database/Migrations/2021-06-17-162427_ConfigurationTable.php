<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConfigurationTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                            => ['type' => 'INT', 'constraint' => 11, 'unsigned'=> true, 'auto_increment' => true],
            'name_app'                      => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'icon_app'                      => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'email'                         => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'logo_menu'                     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'intro'                         => ['type' => 'TEXT', 'null' => true],
            'footer'                        => ['type' => 'TEXT', 'null' => true],
            'alert_title'                   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'alert_body'                    => ['type' => 'TEXT', 'null' => true],
            'status_alert'                  => ['type' => 'ENUM("active", "inactive")', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('configurations');
	}

	public function down()
	{
        $this->forge->dropTable('configurations');
	}
}
