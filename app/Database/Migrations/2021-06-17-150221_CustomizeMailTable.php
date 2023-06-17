<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CustomizeMailTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE],
            'type_email'        => ['type' => 'ENUM("Solicitud de documentos","Validacion de datos","Evidencias y credenciales", "Renovacion")'],
            'subjetc'           => ['type' => 'VARCHAR', 'constraint' => 300, 'null' => TRUE],
            'body'              => ['type' => 'TEXT', 'null' => TRUE]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('customize_mail');
    }

    public function down()
    {
        $this->forge->dropTable('customize_mail');
    }
}
