<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ShopifyProductsVatExemptTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'company_id'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'integration_shopify_id'     => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'product_name'               => ['type' => 'VARCHAR', 'constraint' => 300 ],
            'id_product_shopify'         => ['type' => 'VARCHAR', 'constraint' => 300 ],
            'sku_shopify'                => ['type' => 'VARCHAR', 'constraint' => 300 ],
            'status'                     => ['type' => "ENUM('active', 'inactive')", 'default' =>'inactive'],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('shopify_products_vat_exempt');
    }

    public function down()
    {
        $this->forge->dropTable('shopify_products_vat_exempt');
    }
}
