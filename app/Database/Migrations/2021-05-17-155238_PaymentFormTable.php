<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PaymentFormTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'                          => ['type' => 'VARCHAR', 'constraint' => 191],
            'code'                          => ['type' => 'CHAR', 'constraint' => 191],
            'created_at'                    => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                    => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('payment_forms');
    }

    public function down()
    {
        $this->forge->dropTable('payment_forms');
    }
}
