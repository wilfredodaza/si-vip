<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProductTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                                        => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'companies_id'                              => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'category_id'                               => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'unit_measures_id'                          => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'reference_prices_id'                       => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'type_item_identifications_id'              => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'kind_product_id'                           => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'type_generation_transmition_id'            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'code'                                      => ['type' => 'VARCHAR', 'constraint' => 45],
            'name'                                      => ['type' => 'VARCHAR', 'constraint' => 255],
            'valor'                                     => ['type' => 'VARCHAR', 'constraint' => 45],
            'cost'                                      => ['type' => 'VARCHAR', 'constraint' => 45, 'default' => 0],
            'description'                               => ['type' => 'TEXT', 'null' => true ],
            'free_of_charge_indicator'                  => ['type' => 'VARCHAR', 'constraint' => 45],
            'entry_credit'                              => ['type' => 'VARCHAR', 'constraint' => 11, 'null' => true],
            'entry_debit'                               => ['type' => 'VARCHAR', 'constraint' => 11, 'null' => true],
            'iva'                                       => ['type' => 'VARCHAR', 'constraint' => 11, 'null' => true],
            'retefuente'                                => ['type' => 'VARCHAR', 'constraint' => 11, 'null' => true],
            'reteica'                                   => ['type' => 'VARCHAR', 'constraint' => 11, 'null' => true],
            'reteiva'                                   => ['type' => 'VARCHAR', 'constraint' => 11, 'null' => true],
            'account_pay'                               => ['type' => 'VARCHAR', 'constraint' => 11, 'null' => true],
            'brandname'                                 => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'modelname'                                 => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'foto'                                      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'produc_valu_in'                            => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'produc_descu'                              => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'created_at'                                => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                                => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('unit_measures_id', 'unit_measures', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('reference_prices_id', 'reference_prices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_item_identifications_id', 'type_item_identifications', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'category', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kind_product_id', 'kind_products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_generation_transmition_id', 'type_generation_transmitions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('products');
	}

	public function down()
	{
        $this->forge->dropForeignKey('products','products_category_id_foreign');
        $this->forge->dropForeignKey('products','products_companies_id_foreign');
        $this->forge->dropForeignKey('products','products_kind_product_id_foreign');
        $this->forge->dropForeignKey('products','products_reference_prices_id_foreign');
        $this->forge->dropForeignKey('products','products_unit_measures_id_foreign');
        $this->forge->dropForeignKey('products','products_type_generation_transmition_id_foreign');
        $this->forge->dropTable('products');
	}
}
