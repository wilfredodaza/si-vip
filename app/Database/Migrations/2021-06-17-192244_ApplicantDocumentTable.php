<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ApplicantDocumentTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                             => ['type' => 'INT', 'constraint' => 11, 'unsigned'=> true, 'auto_increment' => true],
            'applicant_id'                   => ['type' => 'INT', 'constraint' => 11, 'unsigned'=> true],
            'documento'                      => ['type' => "ENUM('Rut', 'Camara de comercio', 'Cedula representante', 'Contrato firma', 'Autorizacion firma', 'Resolucion Dian', 'Comprobante de pago', 'certificado')", 'null' => true],
            'archivo'                        => ['type' => 'VARCHAR','constraint' => 300, 'null' => true],
            'status'                         => ['type' => 'ENUM("Aprobado","Desaprobado","Pendiente")', 'null' => true],
            'created_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                     => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('applicant_id', 'applicant', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('applicant_documents');


	}

	public function down()
	{
        $this->forge->dropForeignKey('applicant_documents','applicant_documents_applicant_id_foreign');
        $this->forge->dropTable('applicant_documents');
	}
}
