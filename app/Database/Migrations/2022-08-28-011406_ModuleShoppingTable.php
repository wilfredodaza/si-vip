<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModuleShoppingTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'section_shopping_id'        => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'status_shopping_id'         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'invoices_id'                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'name'                       => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'code'                       => ['type' => 'VARCHAR', 'constraint' => 45 ],
            'status'                     => ['type' => 'ENUM("Activa", "Inactivo")'],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('section_shopping_id', 'section_shopping', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('status_shopping_id', 'status_shopping', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('invoices_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('module_shopping');
    }

    public function down()
    {
        $this->forge->dropForeignKey('module_shopping','module_shopping_section_shopping_id_foreign');
        $this->forge->dropForeignKey('module_shopping','module_shopping_status_shopping_id_foreign');
        $this->forge->dropForeignKey('module_shopping','module_shopping_invoices_id_foreign');
        $this->forge->dropTable('module_shopping');
    }
}
