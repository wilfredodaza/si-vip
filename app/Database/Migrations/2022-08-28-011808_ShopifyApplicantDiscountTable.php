<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ShopifyApplicantDiscountTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'companies_id'                  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'order_number_shopify'          => ['type' => 'VARCHAR', 'constraint' => 300 ],
            'percentage'                    => ['type' => 'INT', 'constraint' => 11 ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('shopify_applicant_discount');
    }

    public function down()
    {
        $this->forge->dropForeignKey('shopify_applicant_discount','shopify_applicant_discount_companies_id_foreign');
        $this->forge->dropTable('shopify_applicant_discount');
    }
}
