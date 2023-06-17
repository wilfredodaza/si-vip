<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MenuTable extends Migration
{
    public function up()
    {
        $this->db->disableForeignKeyChecks();
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'option'        => ['type' => 'VARCHAR', 'constraint' => 40],
            'url'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
            'icon'          => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => TRUE],
            'position'      => ['type' => 'INT', 'constraint' => 3],
            'type'          => ['type' => 'ENUM("primario", "secundario")','default' => 'primario'],
            'references'    => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
            'status'        => ['type' => 'ENUM("active", "inactive")', 'default' => 'active'],
            'component'     => ['type' => 'ENUM("table", "controller")', 'default' => 'table'],
            'title'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE ],
            'description'   => ['type' => 'TEXT', 'null' => TRUE],
            'table'         => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => TRUE],
            'to_list'       => ['type' => 'ENUM("Si", "No")', 'default' => 'No'],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('menus');
    }

    public function down()
    {
        $this->forge->dropTable('menus');
    }
}
