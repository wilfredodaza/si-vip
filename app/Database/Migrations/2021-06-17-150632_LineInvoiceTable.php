<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LineInvoiceTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'invoices_id'                   => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true ],
            'discounts_id'                  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'cost_center_id'                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'type_generation_transmition_id'=> ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'products_id'                   => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'provider_id'                   => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'code'                          => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'discount_amount'               => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'quantity'                      => ['type' => 'VARCHAR', 'constraint' => 11],
            'line_extension_amount'         => ['type' => 'DECIMAL', 'constraint' => '20,2'],
            'price_amount'                  => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'cost_amount'                   => ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0],
            'description'                   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'upload'                        => ['type' => 'ENUM("En Espera", "Cargado", "Sin Referencia")', 'default' => 'En Espera', 'null' => true],
            'start_date'                    => ['type' => 'DATE',  'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('invoices_id', 'invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('discounts_id', 'discounts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('products_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('provider_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cost_center_id', 'cost_center', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_generation_transmition_id', 'type_generation_transmitions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('line_invoices');
	}

	public function down()
	{
        $this->forge->dropForeignKey('line_invoices','line_invoices_discounts_id_foreign');
        $this->forge->dropForeignKey('line_invoices','line_invoices_invoices_id_foreign');
        $this->forge->dropForeignKey('line_invoices','line_invoices_products_id_foreign');
        $this->forge->dropForeignKey('line_invoices','line_invoices_provider_id_foreign');
        $this->forge->dropForeignKey('line_invoices','line_invoices_type_generation_transmition_id_foreign');
        $this->forge->dropForeignKey('line_invoices','line_invoices_cost_center_id_foreign');
        $this->forge->dropTable('line_invoices');
	}
}
