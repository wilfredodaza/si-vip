<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ShopifyAppTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'                       => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'name_app'                   => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'client_id'                  => ['type' => 'VARCHAR', 'constraint' => 300 ],
            'secret_id'                  => ['type' => 'VARCHAR', 'constraint' => 300 ],
            'redirect_url'               => ['type' => 'VARCHAR', 'constraint' => 300 ],
            'status'                     => ['type' => "ENUM('Active', 'Inactive')"],
            'type_app'                   => ['type' => "ENUM('private', 'public')"],

        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('shopify_apps');
    }

    public function down()
    {
        $this->forge->dropTable('shopify_apps');
    }
}
