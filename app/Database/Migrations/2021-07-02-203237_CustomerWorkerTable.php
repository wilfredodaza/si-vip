<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CustomerWorkerTable extends Migration
{
	public function up()
	{
        $this->forge->addField([
            'id'                        => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'customer_id'               => ['type' => 'BIGINT', 'constraint'  => 20, 'null' => TRUE, 'unsigned' => TRUE],
            'type_worker_id'            => ['type' => 'INT', 'constraint'  => 11, 'null' => TRUE, 'unsigned' => TRUE],
            'sub_type_worker_id'        => ['type' => 'INT', 'constraint'  => 11, 'null' => TRUE, 'unsigned' => TRUE],
            'bank_id'                   => ['type' => 'INT', 'constraint'  => 11, 'null' => TRUE, 'unsigned' => TRUE],
            'bank_account_type_id'      => ['type' => 'INT', 'constraint'  => 11, 'null' => TRUE, 'unsigned' => TRUE],
            'type_contract_id'          => ['type' => 'INT', 'constraint'  => 11, 'null' => TRUE, 'unsigned' => TRUE],
            'payment_method_id'         => ['type' => 'BIGINT', 'constraint'  => 20, 'null' => TRUE, 'unsigned' => TRUE],
            'payroll_period_id'         => ['type' => 'BIGINT', 'constraint'  => 20, 'null' => TRUE, 'unsigned' => TRUE],
            'account_number'            => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => TRUE],
            'worker_code'               => ['type' => 'VARCHAR', 'constraint' => 45],
            'second_name'               => ['type' => 'VARCHAR', 'constraint' => 60],
            'surname'                   => ['type' => 'VARCHAR', 'constraint' => 60],
            'second_surname'            => ['type' => 'VARCHAR', 'constraint' => 60],
            'high_risk_pension'         => ['type' => 'ENUM("true", "false")',  'default' => 'false'],
            'integral_salary'           => ['type' => 'ENUM("true", "false")',  'default' => 'false'],
            'salary'                    => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'admision_date'             => ['type' => 'DATE'],
            'retirement_date'           => ['type' => 'DATE', 'null' => true],
            'work'                      => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'transportation_assistance' => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'non_salary_payment'        => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'other_payments'            => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'holidays'                  => ['type' => 'DECIMAL', 'constraint' => '10,1', 'null' => true],
            'court_date'                => ['type' => 'DATE', 'null' => true],
            'created_at'                => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_worker_id', 'type_workers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('sub_type_worker_id', 'sub_type_workers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('bank_id', 'banks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('bank_account_type_id', 'bank_account_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('type_contract_id', 'type_contracts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('payment_method_id', 'payment_methods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('payroll_period_id', 'payroll_periods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('customer_worker');
	}

	public function down()
	{
        $this->forge->dropForeignKey('customer_worker','customer_worker_bank_account_type_id_foreign');
        $this->forge->dropForeignKey('customer_worker','customer_worker_bank_id_foreign');
        $this->forge->dropForeignKey('customer_worker','customer_worker_customer_id_foreign');
        $this->forge->dropForeignKey('customer_worker','customer_worker_payment_method_id_foreign');
        $this->forge->dropForeignKey('customer_worker','customer_worker_payroll_period_id_foreign');
        $this->forge->dropForeignKey('customer_worker','customer_worker_sub_type_worker_id_foreign');
        $this->forge->dropForeignKey('customer_worker','customer_worker_type_contract_id_foreign');
        $this->forge->dropForeignKey('customer_worker','customer_worker_type_worker_id_foreign');
        $this->forge->dropTable('customer_worker');
	}
}
