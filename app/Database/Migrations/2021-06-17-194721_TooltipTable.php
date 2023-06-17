<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TooltipTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                             => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'documento'                      => ['type' => 'ENUM("rut", "camara de comercio", "cedula de representante", "contrato firma digital", "autorizacion firma digital", "resolucion dian", "comprobante de pago")'],
            'ayuda'                          => ['type' => 'TEXT']
            ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tooltips');
	}

	public function down()
	{
        $this->forge->dropTable('tooltips');
	}
}
