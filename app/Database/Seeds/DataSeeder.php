<?php


namespace App\Database\Seeds;


use CodeIgniter\Database\Seeder;
use Config\Database;

class DataSeeder extends Seeder
{
    public $prefix = 'csv';

    public $tables = [
        'roles' => [
            'columns' => 'id, name, description, type, @created_at, @updated_at',
        ],

        'applicant_status' => [
            'columns' => 'id, status, @created_at, @updated_at',
        ],
        'discounts' => [
            'columns' => 'id, name, code, @created_at, @updated_at'
        ],
        'document_status' => [
            'columns' => 'id, name, description, color, @created_at, @updated_at'
        ],

        'graphic_component' => [
            'columns' => 'id, name, @created_at, @updated_at'
        ],
        'invoice_status' => [
            'columns' => 'id, name, description, block, @created_at, @updated_at'
        ],
        'countries' => [
            'columns' => 'id,  name, code, @created_at, @updated_at',
        ],
        'departments' => [
            'columns' => 'id, country_id, name, code, @created_at, @updated_at',
        ],
        'municipalities' => [
            'columns' => 'id, department_id, name, code,  codefacturador, @created_at, @updated_at'
        ],
        'type_document_identifications' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'type_organizations' => [
            'columns' => 'id, name, code, created_at, updated_at',
        ],
        'taxes' => [
            'columns' => 'id, name, description, code, @created_at, @updated_at',
        ],
        'type_regimes' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'type_liabilities' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'payment_forms' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'payment_methods' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'type_currencies' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'unit_measures' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'type_documents' => [
            'columns' => 'id, name, code, cufe_algorithm, prefix, @created_at, @updated_at',
        ],
        'type_item_identifications' => [
            'columns' => 'id, name, code, code_agency, @created_at, @updated_at',
        ],
        'type_operations' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'type_environments' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'languages' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'type_contracts' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'template_pdf' => [
            'columns' => 'id, name, @created_at, @updated_at',
        ],
        'type_customer' => [
            'columns' => 'id, name, @created_at, @updated_at',
        ],
        'type_tracking' => [
            'columns' => 'id, name, @created_at, @updated_at',
        ],
        'type_accounting_account' => [
            'columns' => 'id, name, @created_at, @updated_at',
        ],
        'reference_prices' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'type_workers' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'type_disabilities' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'type_overtime_surcharges' => [
            'columns' => 'id, name, code, percentage, @created_at, @updated_at',
        ],
        'type_law_deductions' => [
            'columns' => 'id, name, code, percentage, @created_at, @updated_at',
        ],
        'sub_type_workers' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'payroll_periods' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'type_payroll_adjust_notes' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'banks' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'bank_account_types' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'delevery_terms' => [
            'columns' => 'id, name, code, @created_at, @updated_at',
        ],
        'type_accrueds' => [
            'columns' => 'id, name, component, domain, `group`, element, @created_at, @updated_at',
        ],
        'type_deductions' => [
            'columns' => 'id, name, component, domain, `group`, element, @created_at, @updated_at',
        ],
        'kind_product' => [
            'columns' => 'id, name, block, @created_at, @updated_at',
        ],
        'invoices_type_files' => [
            'columns' => 'id, name, description, block, @created_at, @updated_at',
        ],
        'permissions' => [
            'columns' => 'id, role_id, menu_id, @created_at, @updated_at',
        ]
    ];

    public function  run()
    {
        helper('filesystem');

        $this->call('MenuSeeder');
        echo 'menus'. PHP_EOL;
        $mysqli  =  new \mysqli(getenv('database.default.hostname'), getenv('database.default.username'),getenv('database.default.password'),getenv('database.default.database'));
        mysqli_options($mysqli, MYSQLI_OPT_LOCAL_INFILE, true);
        /* check connection */
        foreach ($this->tables as $key => $table) {
            echo $key. PHP_EOL;
            $routeFile = realpath('./').'/'.$this->prefix.'/'.$key.'.'.$this->prefix;
            $routeFile = str_replace('\\', '/', $routeFile);
            $sql = "LOAD DATA LOCAL INFILE '".$routeFile."' INTO TABLE $key({$table['columns']}) SET created_at = NOW(), updated_at = NOW()";
            $mysqli->query($sql);
        }
        $mysqli->close();
        $this->call('ConfigurationSeeder');
        echo 'configurations'. PHP_EOL;
        $this->call('CategorySeeder');
        echo 'category'. PHP_EOL;
        $this->call('CompanySeeder');
        echo 'companies'. PHP_EOL;
        $this->call('SoftwareSeeder');
        echo 'software'. PHP_EOL;
        $this->call('CertificateSeeder');
        echo 'certificates'. PHP_EOL;
        $this->call('ResolutionSeeder');
        echo 'resolutiones'. PHP_EOL;
        $this->call('UserSeeder');
        echo 'users'. PHP_EOL;
    }
}