<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ShopifyExceptionTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'companies_id'               => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'shopify_app_id'             => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'shop'                       => ['type' => 'VARCHAR', 'constraint' => 300 ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('shopify_app_id', 'shopify_apps', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('shopify_exceptions');
    }

    public function down()
    {
        $this->forge->dropForeignKey('shopify_exceptions','shopify_exceptions_companies_id_foreign');
        $this->forge->dropForeignKey('shopify_exceptions','shopify_exceptions_shopify_app_id_foreign');
        $this->forge->dropTable('shopify_exceptions');
    }
}
