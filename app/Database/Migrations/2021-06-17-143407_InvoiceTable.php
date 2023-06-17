<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InvoiceTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned'=> true, 'auto_increment' => true],
            'payment_forms_id'              => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'payment_methods_id'            => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'type_documents_id'             => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'idcurrency'                    => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'invoice_status_id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'customers_id'                  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'companies_id'                  => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'delevery_term_id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'discrepancy_response_id'       => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true],
            'user_id'                       => ['type' => 'BIGINT', 'constraint' => 20, 'null' => true],
            'seller_id'                     => ['type' => 'BIGINT', 'constraint' => 20, 'null' => true],
            'resolution'                    => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'prefix'                        => ['type' => 'VARCHAR', 'constraint' => 4, 'null' => true],
            'resolution_id'                 => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'resolution_credit'             => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'payment_due_date'              => ['type' => 'DATE', 'null' => true],
            'duration_measure'              => ['type' => 'INT', 'constraint' => 10, 'null' => true],
            'line_extesion_amount'          => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'tax_exclusive_amount'          => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'tax_inclusive_amount'          => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'allowance_total_amount'        => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'charge_total_amount'           => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'pre_paid_amount'               => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'payable_amount'                => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'calculationrate'               => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'calculationratedate'           => ['type' => 'DATE', 'null' => true],
            'issue_date'                    => ['type' => 'DATE', 'null' => true],
            'uuid'                          => ['type' => 'VARCHAR', 'constraint' => 255,'null' => true],
            'zipkey'                        => ['type' => 'VARCHAR', 'constraint' => 255,'null' => true],
            'notes'                         => ['type' => 'TEXT',   'null' => true],
            'status_wallet'                 => ['type' => 'ENUM("Pendiente","Paga")', 'default' => 'Pendiente',  'null' => true],
            'send'                          => ['type' => 'ENUM("True","False")', 'default' => 'True',  'null' => true],
            'errors'                        => ['type' => 'TEXT', 'null' => true],
            'created_at'                    => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                    => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                    => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('payment_forms_id', 'payment_forms', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('payment_methods_id', 'payment_methods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_documents_id', 'type_documents', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('idcurrency', 'type_currencies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('invoice_status_id', 'invoice_status', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('customers_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('companies_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('delevery_term_id', 'delevery_terms', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('discrepancy_response_id', 'discrepancy_responses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('invoices');
	}

	public function down()
	{
        $this->forge->dropForeignKey('invoices','invoices_companies_id_foreign');
        $this->forge->dropForeignKey('invoices','invoices_customers_id_foreign');
        $this->forge->dropForeignKey('invoices','invoices_delevery_term_id_foreign');
        $this->forge->dropForeignKey('invoices','invoices_idcurrency_foreign');
        $this->forge->dropForeignKey('invoices','invoices_payment_forms_id_foreign');
        $this->forge->dropForeignKey('invoices','invoices_payment_methods_id_foreign');
        $this->forge->dropForeignKey('invoices','invoices_type_documents_id_foreign');
        $this->forge->dropForeignKey('invoices','invoices_discrepancy_response_id_foreign');
        $this->forge->dropTable('invoices');
	}
}
