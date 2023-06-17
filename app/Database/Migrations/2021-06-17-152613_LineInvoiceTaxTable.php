<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LineInvoiceTaxTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'line_invoices_id'              => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'taxes_id'                      => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'tax_amount'                    => ['type' => 'DECIMAL', 'constraint' => '20,2'],
            'taxable_amount'                => ['type' => 'DECIMAL', 'constraint' => '20,2'],
            'percent'                       => ['type' => 'DECIMAL', 'constraint' => '20,3']
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('line_invoices_id', 'line_invoices', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('line_invoice_taxs');
    }

    public function down()
    {
        $this->forge->dropForeignKey('line_invoice_taxs','line_invoice_taxs_line_invoices_id_foreign');
        $this->forge->dropTable('line_invoice_taxs');
    }
}
