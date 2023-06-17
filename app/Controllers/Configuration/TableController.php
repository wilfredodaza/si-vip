<?php


namespace App\Controllers\Configuration;


use App\Controllers\Api\Auth;
use App\Controllers\CustomerController;
use App\Controllers\HeadquartersController;
use App\Controllers\RoleController;
use App\Controllers\Tables\AccountingFileTable;
use App\Controllers\Tables\CertificateTable;
use App\Controllers\Tables\CompanyTable;
use App\Controllers\Tables\PartnershipTable;
use App\Controllers\Tables\ProductExpensesTable;
use App\Controllers\Tables\ProductTable;
use App\Controllers\Tables\ProductProviderTable;
use App\Controllers\Tables\ProviderTable;
use App\Controllers\Tables\ResolutionTable;
use App\Controllers\Tables\SoftwareTable;
use App\Controllers\Tables\UserTable;
use App\Controllers\Tables\PersonalizationLaborCertificateTable;
use App\Models\BudgetPurchaseOrder;
use App\Models\Category;
use App\Models\Gender;
use App\Models\Groups;
use App\Models\Materials;
use App\Models\Menu;
use App\Models\Prices;
use App\Models\Providers;
use App\Models\SubGroup;
use App\Models\YearsPurchaseOrder;
use Config\Services;
use App\Models\Config;
use App\Models\AccountingAcount;
use App\Models\Company;
use App\Traits\Grocery;
use App\Models\User;
use App\Models\Role;
use App\Models\Customer;
use App\Models\Customize_mail;
use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class TableController extends BaseController
{
    use Grocery;

    private $crud;
    private $headquartersController;
    private $customerController;

    public function __construct()
    {
        $this->crud = $this->_getGroceryCrudEnterprise();
        $this->crud->setSkin('bootstrap-v3');
        $this->crud->setLanguage('Spanish');
        $this->headquartersController = new HeadquartersController();
        $this->customerController = new CustomerController();
    }

    public function index($data)
    {

        $menu = new Menu();
        $component = $menu->where(['table' => $data, 'component' => 'table'])->get()->getResult();

        if ($component) {
            $this->crud->setTable($component[0]->table);
            switch ($component[0]->table) {
                case 'users':
                    $table = new UserTable();
                    $table->init('users');
                    break;
                case 'companies':
                    $table = new CompanyTable();
                    $table->init('companies');
                    break;
                case 'software':
                    $table = new SoftwareTable();
                    $table->init('software');
                    break;
                case 'resolutions':
                    $table = new ResolutionTable();
                    $table->init('resolutions');
                    break;
                case 'certificates':
                    $table = new CertificateTable();
                    $table->init('certificates');
                    break;
                case 'partnerships':
                    $table = new PartnershipTable();
                    $table->init('partnerships');
                    break;
                case 'personalization_labor_certificates':
                    $table = new  PersonalizationLaborCertificateTable();
                    $table->init('personalization_labor_certificates');
                    break;
                case 'products':
                    $table = new  ProductTable();
                    $table->init('products');
                    break;
                case 'product_provider':
                    $table = new  ProductProviderTable();
                    $table->init('products');
                    break;
                case 'config':
                    $this->crud->fieldType('responsable_iva', 'dropdown', ['Simplificado', 'Persona Juridica Regimen Comun']);
                    $this->crud->setFieldUpload('logo', 'assets/upload/imgs', base_url('/assets/upload/imgs'));
                    $this->crud->setRelation('companies_id', 'companies', 'company');
                    $this->crud->setTexteditor(['default_notes']);
                    $this->crud->setRule('quantity_decimal', 'max', '2');
                    $this->crud->setRule('quantity_decimal', 'min', '0');
                    $this->crud->setRule('quantity_decimal', 'lengthBetween', ['0', '1']);
                    $this->crud->displayAs(lang('config.config'));
                    if (session('user')->role_id == 2 || session('user')->role_id >= 3) {
                        $this->crud->where(['companies_id' => session('user')->companies_id]);
                        $this->crud->unsetColumns(['companies_id']);
                        $this->crud->fieldType('companies_id', 'hidden');

                        $config = new Config();
                        $count = $config->where(['companies_id' => session('user')->companies_id])->countAllResults();
                        if ($count > 0) {
                            $this->crud->unsetAdd();
                            $this->crud->unsetDelete();
                        }
                        $this->crud->callbackAddForm(function ($data) {
                            $data['companies_id'] = session('user')->companies_id;
                            return $data;
                        });
                    }
                    $this->crud->callbackAfterInsert(function ($data) {
                        if (!empty($data->data['logo'])) {
                            $this->logo($data);
                        }
                        return $data;
                    });
                    $this->crud->callbackAfterUpdate(function ($data) {
                        if (!empty($data->data['logo'])) {
                            $this->logo($data);
                        }
                        return $data;
                    });
                    break;
                case 'customers':
                    $this->customerController->updateTypeClient();
                    $this->crud->setRule('name', 'required');
                    $this->crud->setRule('identification_number', 'required');
                    //$this->crud->fieldType('identification_number', 'int');
                    $this->crud->setRule('phone', 'required');
                    $this->crud->setRule('phone', 'lengthBetween', ['7', '10']);
                    $this->crud->fieldType('phone', 'int');
                    $this->crud->fieldType('dv', 'hidden');
                    $this->crud->setRule('address', 'required');
                    // $this->crud->setRule('dv', 'lengthBetween', ['0', '1']);
                    $this->crud->setRule('email', 'required');
                    $this->crud->setRule('merchant_registration', 'required');
                    $this->crud->setRelation('companies_id', 'companies', 'company');
                    $this->crud->setRelation('type_document_identifications_id', 'type_document_identifications', 'name');
                    $this->crud->setRelation('type_customer_id', 'type_customer', 'name');
                    $this->crud->setRelation('type_regime_id', 'type_regimes', 'name');
                    $this->crud->setRelation('municipality_id', 'municipalities', 'name');
                    $this->crud->setRelation('type_organization_id', 'type_organizations', 'name');
                    $this->crud->setRelation('type_liability_id', 'type_liabilities', '[{code}] {name}');
                    $this->crud->setRelation('type_customer_id', 'type_customer', 'name');
                    $this->crud->fieldType('firm', 'hidden');
                    $this->crud->fieldType('rut', 'hidden');
                    $this->crud->fieldType('bank_certificate', 'hidden');
                    $this->crud->fieldType('user_id', 'hidden');
                    $this->crud->fieldType('created_at', 'hidden');
                    $this->crud->fieldType('updated_at', 'hidden');
                    $this->crud->fieldType('deleted_at', 'hidden');
                    $this->crud->fieldType('quota', 'hidden');
                    $this->crud->fieldType('payment_policies', 'hidden');
                    $this->crud->fieldType('type_client_status', 'hidden');
                    $this->crud->fieldType('frequency', 'hidden');
                    $this->crud->fieldType('status', 'dropdown_search', ['Activo' => 'Activo', 'Inactivo' => 'Inactivo']);
                    $this->crud->fieldType('headquarters_id', 'hidden');
                    $rol = $this->headquartersController->permissionManager(session('user')->role_id);
                    if(!$rol){
                        $this->crud->unsetExport();
                        $this->crud->unsetPrint();
                    }else{
                        $this->crud->setActionButton('', 'fa fa-user', function ($row) {
                            return base_url('customers/profile') . '/' . $row->id;
                        }, false);
                    }
                    $this->crud->columns([
                        'name',
                        'type_document_identifications_id',
                        'identification_number',
                        'dv',
                        'phone',
                        'email',
                        'type_customer_id',
                        'type_client_status',
                        'frequency'
                    ]);

                    $this->crud->unsetDelete();
                    $this->crud->unsetEdit();
                    $this->crud->callbackBeforeInsert(function ($data) {
                        $data->data['identification_number'] = $this->_clearNumber($data->data['identification_number']);
                        $data->data['phone'] = $this->_clearNumber($data->data['phone']);
                        $data->data['dv'] = $this->calcularDV($data->data['identification_number']);
                        $data->data['status'] = 'Activo';
                        return $data;
                    });
                    $this->crud->callbackBeforeUpdate(function ($data) {
                        $data->data['identification_number'] = $this->_clearNumber($data->data['identification_number']);
                        $data->data['phone'] = $this->_clearNumber($data->data['phone']);
                        $data->data['dv'] = $this->calcularDV($data->data['identification_number']);
                        return $data;
                    });
                    $this->crud->displayAs(lang('customers.customers'));
                    //$this->crud->where('type_customer_id IN(1,2)');
                    $rol = $this->headquartersController->permissionManager(session('user')->role_id);
                    if (session('user')->role_id == 2 || session('user')->role_id >= 3) {
                        $this->crud->unsetColumns(['companies_id']);
                        if ($rol) {
                            $idCompanies = $this->headquartersController->idsCompaniesHeadquarters();
                            $this->crud->where('companies_id IN(' . implode(",", $idCompanies) . ')');
                        } else {
                            $this->crud->where(['companies_id' => session('user')->companies_id]);
                        }
                        $this->crud->where('type_customer_id IN(1)');
                        $this->crud->fieldType('companies_id', 'hidden');
                        $this->crud->callbackAddForm(function ($data) {
                            $data['companies_id'] = session('user')->companies_id;
                            $data['dv'] = $this->calcularDV($data['identification_number']);
                            $data['merchant_registration'] = '00000';
                            return $data;
                        });
                    } else {
                        if ($rol) {
                            $idCompanies = $this->headquartersController->idsCompaniesHeadquarters();
                            $this->crud->where('companies_id IN(' . implode(",", $idCompanies) . ')');
                            $this->crud->where('type_customer_id IN(1)');
                        }
                        $this->crud->callbackAddForm(function ($data) {
                            $data['merchant_registration'] = '00000';
                            $data['dv'] = $this->calcularDV($data['identification_number']);
                            return $data;
                        });
                    }

                    break;
                case 'subscriptions':
                    $this->crud->setRelation('companies_id', 'companies', 'company');
                    $this->crud->setRelation('packages_id', 'packages', 'name');
                    $this->crud->setFieldUpload('sopport_invoice', 'assets/upload/documents', '/assets/upload/documents');
                    $this->crud->displayAs(lang('subscriptions.subscriptions'));
                    $this->crud->setRelation('applicant_id', 'applicant', 'company_name');
                    break;
                case 'packages':
                    $this->crud->displayAs(lang('packages.packages'));
                    break;
                case 'customize_mail':
                    $this->crud->setTexteditor(['body']);
                    $cmail = new Customize_mail();
                    $count = $cmail->countAllResults();
                    if ($count >= 3) {
                        $this->crud->unsetAdd();
                        $this->crud->unsetDelete();
                    }
                    break;
                case 'accounting_account':
                    $this->crud->displayAs(lang('accounting_account.columns'));
                    $this->crud->setRelation('companies_id', 'companies', 'company');
                    $this->crud->setRelation('type_accounting_account_id', 'type_accounting_account', 'name');
                    $this->crud->fieldType('updated_at', 'hidden');
                    if (session('user')->role_id == 2 || session('user')->role_id >= 3) {
                        $this->crud->fieldType('companies_id', 'hidden');
                        $this->crud->fieldType('created_at', 'hidden');
                        $this->crud->unsetColumns(['companies_id']);
                        $this->crud->where(['companies_id' => session('user')->companies_id]);
                        $this->crud->callbackAddForm(function ($data) {
                            $data['companies_id'] = session('user')->companies_id;
                            $data['created_at'] = date('Y-m-d H:m:i');
                            return $data;
                        });
                    }

                    break;
                case 'type_accounting_account':
                    $this->crud->displayAs(lang('type_accounting_account.columns'));
                    break;
                case 'payment_method_company':
                    $this->crud->displayAs(lang('payment_method_company.columns'));
                    $this->crud->setRelation('accounting_account_id', 'accounting_account', 'name', ['type_accounting_account_id' => 5]);
                    if (session('user')->role_id == 2 || session('user')->role_id >= 3) {
                        $this->crud->where(['payment_method_company.companies_id' => session('user')->companies_id]);
                        $this->crud->unsetColumns(['companies_id']);
                        $this->crud->fieldType('companies_id', 'hidden');
                        $this->crud->callbackAddForm(function ($data) {
                            $data['companies_id'] = session('user')->companies_id;
                            return $data;
                        });
                    } else {
                        $this->crud->setRelation('companies_id', 'companies', 'company');

                    }
                    break;
                case 'sellers':
                    $this->crud->displayAs(lang('sellers.columns'));
                    $this->crud->fieldType('link', 'hidden');
                    $this->crud->callbackBeforeInsert(function ($data) {
                        $data->data['link'] = base_url() . '/solicitudes/registro?v=' . $data->data['identification_number'];
                        return $data;
                    });
                    $this->crud->setActionButton('Link', 'fa fa-user', function ($row) {
                        return 'https://api.whatsapp.com/send?text=https://facturadorv2.mifacturalegal.com/solicitudes/registro?v=' . $row->identification_number;
                    }, true);
                    $this->crud->unsetDeleteMultiple();
                    break;
                case 'seller':
                    $this->crud->displayAs(lang('users.users'));
                    $this->crud->setTable('users');
                    $this->crud->fieldType('password', 'password');
                    $this->crud->unsetColumns(['role_id', 'companies_id']);
                    $this->crud->setFieldUpload('photo', 'assets/upload/images', '/assets/upload/images');
                    if (Auth::querys()->role_id >= 3) {
                        $this->crud->fieldType('companies_id', 'hidden');
                        $this->crud->fieldType('role_id', 'hidden');
                        $this->crud->unsetColumns(['role_id', 'companies_id']);
                        $this->crud->where(['companies_id' => Auth::querys()->companies_id, 'role_id' => 6]);
                        $this->crud->callbackAddForm(function ($data) {
                            $data['companies_id'] = Auth::querys()->companies_id;
                            $data['role_id'] = 6;
                            return $data;
                        });
                    } else if (Auth::querys()->role_id == 2) {
                        $this->crud->where(['companies_id' => Auth::querys()->companies_id, 'role_id' => 6]);
                        $this->crud->fieldType('companies_id', 'hidden');
                        $this->crud->fieldType('role_id', 'hidden');
                        $this->crud->unsetColumns(['role_id', 'companies_id']);
                        $this->crud->callbackAddForm(function ($data) {
                            $data['companies_id'] = Auth::querys()->companies_id;
                            $data['role_id'] = 6;
                            return $data;
                        });
                    } else if (Auth::querys()->role_id == 1) {
                        $this->crud->where(['role_id' => 6]);
                        $this->crud->setRelation('companies_id', 'companies', 'company');
                        $this->crud->setRelation('role_id', 'roles', 'name');
                    }

                    $this->crud->callbackBeforeInsert(function ($stateParameters) {
                        $stateParameters->data['password'] = password_hash($stateParameters->data['password'], PASSWORD_DEFAULT);
                        return $stateParameters;
                    });
                    $this->crud->callbackBeforeUpdate(function ($stateParameters) {
                        if (strlen($stateParameters->data['password']) < 20) {
                            $stateParameters->data['password'] = password_hash($stateParameters->data['password'], PASSWORD_DEFAULT);
                        }
                        return $stateParameters;
                    });

                    break;
                case 'products_inven':
                    $this->crud->setTable('products');
                    $this->crud->fieldType('entry_credit', 'dropdown_search', $this->_getAccountingAccount(1, 'Crédito'));
                    $this->crud->fieldType('entry_debit', 'dropdown_search', $this->_getAccountingAccount(1, 'Débito'));
                    $this->crud->fieldType('iva', 'dropdown_search', $this->_getAccountingAccount(2));
                    $this->crud->fieldType('retefuente', 'dropdown_search', $this->_getAccountingAccount(3));
                    $this->crud->fieldType('reteica', 'dropdown_search', $this->_getAccountingAccount(3));
                    $this->crud->fieldType('reteiva', 'dropdown_search', $this->_getAccountingAccount(3));
                    $this->crud->fieldType('account_pay', 'dropdown_search', $this->_getAccountingAccount(4));
                    $this->crud->fieldType('reference_prices_id', 'hidden');
                    $this->crud->fieldType('type_item_identifications_id', 'hidden');
                    $this->crud->setRelation('category_id', 'category', 'name');
                    $this->crud->setFieldUpload('foto', 'assets/upload/products', '/assets/upload/products');
                    //$this->crud->fieldType('foto', 'hidden');
                    //$this->crud->fieldType('category_id', 'hidden');
                    //$this->crud->fieldType('produc_valu_in', 'hidden');
                    //$this->crud->fieldType('produc_descu', 'hidden');
                    $this->crud->setRule('code', 'required');
                    $this->crud->setRule('name', 'required');
                    $this->crud->setRule('valor', 'required');
                    $this->crud->setRule('entry_credit', 'required');
                    $this->crud->setRule('entry_debit', 'required');
                    $this->crud->setRule('iva', 'required');
                    $this->crud->setRule('retefuente', 'required');
                    $this->crud->setRule('reteica', 'required');
                    $this->crud->setRule('reteiva', 'required');
                    $this->crud->setRule('account_pay', 'required');
                    $this->crud->setRule('produc_valu_in', 'required');

                    $this->crud->fieldType('unit_measures_id', 'hidden');
                    $this->crud->fieldType('deleted_at', 'hidden');
                    $this->crud->fieldType('free_of_charge_indicator', 'dropdown_search', ['false' => 'No', 'true' => 'Si']);
                    $this->crud->fieldType('valor', 'int');
                    $this->crud->displayAs(lang('products.products'));
                    $this->crud->setRelation('companies_id', 'companies', 'company');
                    if (session('user')->role_id == 2 || session('user')->role_id == 3) {
                        $this->crud->where(['companies_id' => session('user')->companies_id]);
                        $this->crud->fieldType('companies_id', 'hidden');
                        $this->crud->columns(['code', 'name', 'category_id', 'valor', 'foto']);
                        $this->crud->callbackAddForm(function ($data) {
                            $data['reference_prices_id'] = 1;
                            $data['type_item_identifications_id'] = 4;
                            $data['unit_measures_id'] = 70;
                            $data['companies_id'] = session('user')->companies_id;
                            $data['brandname'] = 'No Aplica';
                            $data['modelname'] = 'No Aplica';
                            $data['category_id'] = 1;
                            $data['entry_credit'] = array_keys($this->_getAccountingAccount(1, 'Crédito'))[0];
                            $data['entry_debit'] = array_keys($this->_getAccountingAccount(1, 'Débito'))[0];
                            $data['iva'] = array_keys($this->_getAccountingAccount(2))[0];
                            $data['retefuente'] = array_keys($this->_getAccountingAccount(3))[0];
                            $data['reteica'] = array_keys($this->_getAccountingAccount(3))[0];
                            $data['reteiva'] = array_keys($this->_getAccountingAccount(3))[0];
                            $data['account_pay'] = array_keys($this->_getAccountingAccount(4))[0];
                            return $data;
                        });
                    } else {
                        $this->crud->columns(['code', 'name', 'valor', 'description', 'companies_id']);
                        $this->crud->callbackAddForm(function ($data) {
                            $data['reference_prices_id'] = 1;
                            $data['type_item_identifications_id'] = 1;
                            $data['unit_measures_id'] = 70;
                            return $data;
                        });
                    }

                    $this->crud->callbackBeforeInsert(function ($data) {
                        $data->data['valor'] = $this->_clearNumber($data->data['valor']);

                        return $data;
                    });
                    $this->crud->callbackBeforeUpdate(function ($data) {
                        $data->data['valor'] = $this->_clearNumber($data->data['valor']);
                        return $data;
                    });
                    break;
                case 'category':
                    $this->crud->setTable('category');
                    $this->crud->where(['expenses' => 'no']);
                    $this->crud->fieldType('expenses', 'hidden');
                    $this->crud->fieldType('payroll', 'hidden');
                    $this->crud->columns([
                        'name',
                    ]);
                    $this->crud->callbackBeforeInsert(function ($data) {
                        $data->data['expenses'] = 'no';
                        $data->data['payroll'] = 'no';
                        return $data;
                    });
                    $this->crud->displayAs(lang('category.columns'));
                    break;
                case 'accounting_files':
                    $table = new AccountingFileTable();
                    $table->init('accounting_files', 'accouting_files.accouting_files');
                    break;
                case 'expense_type':
                    $table = new  ProductExpensesTable();
                    $table->init('products');
                    break;
                case 'employees':
                    $rol = $this->headquartersController->permissionManager(session('user')->role_id);
                    $this->crud->setTable('customers');
                    $this->crud->displayAs(lang('customers.customers'));
                    $this->crud->setRule('identification_number', 'required');
                    $this->crud->setRule('phone', 'required');
                    $this->crud->setRule('name', 'required');
                    $this->crud->setRule('phone', 'lengthBetween', ['7', '10']);
                    $this->crud->fieldType('phone', 'int');
                    $this->crud->fieldType('dv', 'hidden');
                    $this->crud->setRule('address', 'required');
                    $this->crud->setRelation('municipality_id', 'municipalities', 'name');
                    $this->crud->setRule('email', 'required');
                    $this->crud->fieldType('status', 'dropdown_search', ['Activo' => 'Activo', 'Inactivo' => 'Inactivo']);
                    $this->crud->setRule('neighborhood', 'required');
                    // relations
                    $this->crud->setRelation('companies_id', 'companies', 'company');
                    $this->crud->setRelation('type_document_identifications_id', 'type_document_identifications', 'name');
                    // $this->crud->setRelation('type_customer_id', 'type_customer', 'name');
                    // $this->crud->setRelation('type_regime_id', 'type_regimes', 'name');
                    // $this->crud->setRelation('type_organization_id', 'type_organizations', 'name');
                    // $this->crud->setRelation('type_liability_id', 'type_liabilities', '[{code}] {name}');
                    // $this->crud->setRelation('type_customer_id', 'type_customer', 'name');
                    //hidden
                    $this->crud->fieldType('type_customer_id', 'hidden');
                    $this->crud->fieldType('type_regime_id', 'hidden');
                    $this->crud->fieldType('type_organization_id', 'hidden');
                    $this->crud->fieldType('type_liability_id', 'hidden');
                    $this->crud->fieldType('type_customer_id', 'hidden');
                    $this->crud->fieldType('firm', 'hidden');
                    $this->crud->fieldType('merchant_registration', 'hidden');
                    $this->crud->fieldType('rut', 'hidden');
                    $this->crud->fieldType('quota', 'hidden');
                    $this->crud->fieldType('bank_certificate', 'hidden');
                    $this->crud->fieldType('user_id', 'hidden');
                    $this->crud->fieldType('created_at', 'hidden');
                    $this->crud->fieldType('headquarters_id', 'hidden');
                    $this->crud->fieldType('updated_at', 'hidden');
                    $this->crud->fieldType('deleted_at', 'hidden');
                    $this->crud->fieldType('payment_policies', 'hidden');
                    $this->crud->fieldType('type_client_status', 'hidden');
                    $this->crud->fieldType('frequency', 'hidden');

                    $this->crud->columns([
                        'name',
                        'type_document_identifications_id',
                        'identification_number',
                        'phone',
                        'email',
                        'companies_id',
                        'status'
                    ]);
                    $this->crud->unsetDelete();
                    $this->crud->setActionButton('Perfil', 'fa fa-user', function ($row) {
                        return base_url('customers/employee') . '/' . $row->id;
                    }, false);


                    $this->crud->callbackBeforeInsert(function ($data) {
                        $data->data['phone'] = $this->_clearNumber($data->data['phone']);
                        $data->data['identification_number'] = $this->_clearNumber($data->data['identification_number']);
                        $data->data['dv'] = $this->calcularDV($data->data['identification_number']);
                        $data->data['status'] = 'Activo';
                        $data->data['type_customer_id'] = 3;
                        return $data;
                    });
                    $this->crud->callbackBeforeUpdate(function ($data) {
                        $data->data['phone'] = $this->_clearNumber($data->data['phone']);
                        $data->data['identification_number'] = $this->_clearNumber($data->data['identification_number']);
                        $data->data['dv'] = $this->calcularDV($data->data['identification_number']);
                        return $data;
                    });

                    //$this->crud->where('type_customer_id IN(1,2)');
                    if (session('user')->role_id == 2 || session('user')->role_id >= 3) {
                        //$this->crud->unsetColumns(['companies_id']);
                        if ($rol) {
                            $idCompanies = $this->headquartersController->idsCompaniesHeadquarters();
                            $this->crud->where('companies_id IN(' . implode(",", $idCompanies) . ')');
                            $this->crud->where(['type_customer_id' => 3]);
                        } else {
                            $this->crud->where(['companies_id' => session('user')->companies_id, 'type_customer_id' => 3]);
                        }
                        // $this->crud->fieldType('companies_id', 'hidden');
                        $this->crud->callbackAddForm(function ($data) {
                            $data['companies_id'] = session('user')->companies_id;
                            $data['dv'] = $this->calcularDV($data['identification_number']);
                            $data['merchant_registration'] = '00000';
                            return $data;
                        });
                    } else {
                        if ($rol) {
                            $idCompanies = $this->headquartersController->idsCompaniesHeadquarters();
                            $this->crud->where('companies_id IN(' . implode(",", $idCompanies) . ')');
                            $this->crud->where(['type_customer_id' => 3]);
                        }
                        $this->crud->callbackAddForm(function ($data) {
                            $data['merchant_registration'] = '00000';
                            $data['dv'] = $this->calcularDV($data['identification_number']);
                            return $data;
                        });
                    }

                    break;
                case 'categoryExpenses':
                    $this->crud->setTable('category');
                    $this->crud->where(['expenses' => 'si']);
                    $this->crud->fieldType('expenses', 'hidden');
                    $this->crud->fieldType('payroll', 'dropdown_search', ['no' => 'no', 'si' => 'si']);
                    $this->crud->columns([
                        'name',
                        'payroll'
                    ]);
                    $this->crud->callbackBeforeInsert(function ($data) {
                        $data->data['expenses'] = 'si';
                        return $data;
                    });
                    $this->crud->displayAs(lang('category.columns'));
                    break;
                case 'payment_methods':
                    $this->crud->setTable('payment_methods');
                    $this->crud->unsetAdd();
                    $this->crud->fieldType('base', 'float');
                    $this->crud->fieldType('code', 'hidden');
                    $this->crud->fieldType('created_at', 'hidden');
                    $this->crud->fieldType('updated_at', 'hidden');
                    $this->crud->fieldType('payroll', 'dropdown_search', ['Activo' => 'Activo', 'Inactivo' => 'Inactivo']);
                    $this->crud->callbackEditField('base', function ($fieldValue, $primaryKeyValue, $rowData) {
                        // You have access now at the extra custom variable $username
                        return '<input class="form-control" name="base" disabled value="' . $fieldValue . '"  />';
                    });
                    $this->crud->columns([
                        'base',
                        'name',
                        'status'
                    ]);
                    $this->crud->displayAs(lang('paymentMethod.columns'));
                    break;
                case 'providers':
                    $this->crud->setTable('providers');
                    $this->crud->fieldType('code', 'hidden');
                    $this->crud->displayAs(lang('providers.columns'));
                    $this->crud->unsetDelete();
                    // $this->_codeAddForm('providers');
                    $this->_codeSaveTable('providers');
                    break;
                case 'gender':
                    $this->crud->setTable('gender');
                    $this->crud->fieldType('code', 'hidden');
                    $this->crud->displayAs(lang('gender.columns'));
                    $this->crud->unsetDelete();
                    // $this->_codeAddForm('gender');
                    $this->_codeSaveTable('gender');
                    break;
                case 'prices':
                    $this->crud->setTable('prices');
                    $this->crud->fieldType('code', 'hidden');
                    $this->crud->displayAs(lang('prices.columns'));
                    $this->crud->unsetDelete();
                    // $this->_codeAddForm('prices');
                    $this->_codeSaveTable('prices');
                    break;
                case 'materials':
                    $this->crud->setTable('materials');
                    $this->crud->fieldType('code', 'hidden');
                    $this->crud->displayAs(lang('materials.columns'));
                    $this->crud->unsetDelete();
                    // $this->_codeAddForm('materials');
                    $this->_codeSaveTable('materials');
                    break;
                case 'sub_group':
                    $this->crud->setTable('sub_group');
                    $this->crud->fieldType('code', 'hidden');
                    $this->crud->setRelation('group_id', 'groups', 'name');
                    $this->crud->displayAs(lang('subCategory.columns'));
                    $this->crud->defaultOrdering('sub_group.group_id', 'ASC');
                    // $this->crud->unsetDelete();
                    // $this->_codeAddForm('sub_group');
                    //$this->_codeSaveTable('sub_group');
                    $this->crud->callbackBeforeInsert(function ($stateParameters) {
                        $data = new SubGroup();
                        $disponible = [];
                        for ($i = 0; $i <= 99; $i++) {
                            $number = (strlen($i) == 1) ? "0{$i}" : "{$i}";
                            array_push($disponible, ['id' => $number]);
                        }
                        $codesItems = $data->where(['group_id' => $stateParameters->data['group_id']])->orderBy('id', 'Desc')->asObject()->get()->getResult();
                        foreach ($codesItems as $codesItem) {
                            unset($disponible[(int)$codesItem->code]);
                        }
                        $disponible = array_values($disponible);
                        $stateParameters->data['code'] = $disponible[0]['id'];
                        return $stateParameters;
                    });
                    break;
                case 'groups':
                    $this->crud->setTable('groups');
                    $this->crud->fieldType('code', 'hidden');
                    $this->crud->displayAs(lang('groups.columns'));
                    $this->crud->unsetDelete();
                    // $this->_codeAddForm('groups');
                    $this->_codeSaveTable('groups');
                    break;
                case 'providersC':
                    $this->crud->setTable('customers');
                    $this->crud->setRule('name', 'required');
                    $this->crud->setRule('identification_number', 'required');
                    //$this->crud->fieldType('identification_number', 'int');
                    $this->crud->setRule('phone', 'required');
                    $this->crud->setRule('phone', 'lengthBetween', ['7', '10']);
                    $this->crud->fieldType('phone', 'int');
                    $this->crud->fieldType('dv', 'hidden');
                    $this->crud->setRule('address', 'required');
                    // $this->crud->setRule('dv', 'lengthBetween', ['0', '1']);
                    $this->crud->setRule('email', 'required');
                    $this->crud->setRule('merchant_registration', 'required');
                    $this->crud->setRelation('companies_id', 'companies', 'company');
                    $this->crud->setRelation('type_document_identifications_id', 'type_document_identifications', 'name');
                    $this->crud->setRelation('type_regime_id', 'type_regimes', 'name');
                    $this->crud->setRelation('municipality_id', 'municipalities', 'name');
                    $this->crud->setRelation('type_organization_id', 'type_organizations', 'name');
                    $this->crud->setRelation('type_liability_id', 'type_liabilities', '[{code}] {name}');
                    $this->crud->setRelation('type_customer_id', 'type_customer', 'name');
                    $this->crud->fieldType('firm', 'hidden');
                    $this->crud->fieldType('rut', 'hidden');
                    $this->crud->fieldType('bank_certificate', 'hidden');
                    $this->crud->fieldType('user_id', 'hidden');
                    $this->crud->fieldType('created_at', 'hidden');
                    $this->crud->fieldType('updated_at', 'hidden');
                    $this->crud->fieldType('deleted_at', 'hidden');
                    $this->crud->fieldType('quota', 'hidden');
                    $this->crud->fieldType('payment_policies', 'hidden');
                    $this->crud->fieldType('type_client_status', 'hidden');
                    $this->crud->fieldType('frequency', 'hidden');
                    $this->crud->fieldType('status', 'dropdown_search', ['Activo' => 'Activo', 'Inactivo' => 'Inactivo']);
                    $this->crud->fieldType('headquarters_id', 'hidden');
                    $this->crud->fieldType('type_customer_id', 'hidden');

                    $this->crud->columns([
                        'name',
                        'type_document_identifications_id',
                        'identification_number',
                        'dv',
                        'phone',
                        'email',
                    ]);

                    $this->crud->setActionButton('', 'fa fa-user', function ($row) {
                        return base_url('providers/profile') . '/' . $row->id;
                    }, false);

                    $this->crud->unsetDelete();
                    $this->crud->unsetEdit();
                    $this->crud->callbackBeforeInsert(function ($data) {
                        $data->data['identification_number'] = $this->_clearNumber($data->data['identification_number']);
                        $data->data['phone'] = $this->_clearNumber($data->data['phone']);
                        $data->data['dv'] = $this->calcularDV($data->data['identification_number']);
                        $data->data['status'] = 'Activo';
                        $data->data['type_customer_id'] = 2;
                        return $data;
                    });
                    $this->crud->callbackBeforeUpdate(function ($data) {
                        $data->data['identification_number'] = $this->_clearNumber($data->data['identification_number']);
                        $data->data['phone'] = $this->_clearNumber($data->data['phone']);
                        $data->data['dv'] = $this->calcularDV($data->data['identification_number']);
                        return $data;
                    });
                    $this->crud->displayAs(lang('customers.customers'));
                    //$this->crud->where('type_customer_id IN(1,2)');
                    $rol = $this->headquartersController->permissionManager(session('user')->role_id);
                    if (session('user')->role_id == 2 || session('user')->role_id >= 3) {
                        $this->crud->unsetColumns(['companies_id']);
                        if ($rol) {
                            $idCompanies = $this->headquartersController->idsCompaniesHeadquarters();
                            $this->crud->where('companies_id IN(' . implode(",", $idCompanies) . ')');
                        } else {
                            $this->crud->where(['companies_id' => session('user')->companies_id]);
                        }
                        $this->crud->where(['type_customer_id' => 2, 'headquarters_id' => null]);
                        $this->crud->fieldType('companies_id', 'hidden');
                        $this->crud->callbackAddForm(function ($data) {
                            $data['companies_id'] = session('user')->companies_id;
                            $data['dv'] = $this->calcularDV($data['identification_number']);
                            $data['merchant_registration'] = '00000';
                            return $data;
                        });
                    } else {
                        if ($rol) {
                            $idCompanies = $this->headquartersController->idsCompaniesHeadquarters();
                            $this->crud->where('companies_id IN(' . implode(",", $idCompanies) . ')');
                            $this->crud->where(['type_customer_id' => 2, 'headquarters_id' => null]);
                        }
                        $this->crud->callbackAddForm(function ($data) {
                            $data['merchant_registration'] = '00000';
                            $data['dv'] = $this->calcularDV($data['identification_number']);
                            return $data;
                        });
                    }

                    break;
                case 'years_purchase_order':
                    $this->crud->setTable('years_purchase_order');
                    $this->crud->displayAs('year', 'Año');
                    $this->crud->callbackBeforeInsert(function ($stateParameters) {
                        $yearsTable = new YearsPurchaseOrder();
                        $year = $yearsTable->where(['year' => $stateParameters->data['year']])->asObject()->first();
                        if (!is_null($year)) {
                            $errorMessage = new \GroceryCrud\Core\Error\ErrorMessage();
                            return $errorMessage->setMessage("Este Año ya se encuentra agregado");
                        }
                        return $stateParameters;
                    });
                    break;
                case 'budgetpurchaseorder':
                    $years = [];
                    $this->crud->setTable('budgetpurchaseorder');
                    $this->crud->fieldType('month', 'dropdown_search',
                        [1 => 'Enero',
                            2 => 'Febrero',
                            3 => 'Marzo',
                            4 => 'Abril',
                            5 => 'Mayo',
                            6 => 'Junio',
                            7 => 'Julio',
                            8 => 'Agosto',
                            9 => 'Septiembre',
                            10 => 'Octubre',
                            11 => 'Noviembre',
                            12 => 'Diciembre']
                    );
                    $yearsTable = new YearsPurchaseOrder();
                    $yearsActive = $yearsTable->select(['year'])->asObject()->get()->getResult();
                    foreach ($yearsActive as $year) {
                        $years[$year->year] = $year->year;
                    }
                    $this->crud->fieldType('year', 'dropdown_search', $years);
                    $this->crud->callbackBeforeInsert(function ($stateParameters) {
                        $budgetOcs = new BudgetPurchaseOrder();
                        $budget = $budgetOcs->where(['year' => $stateParameters->data['year'], 'month' => $stateParameters->data['month']])->asObject()->first();
                        if (!is_null($budget)) {
                            $errorMessage = new \GroceryCrud\Core\Error\ErrorMessage();
                            return $errorMessage->setMessage("Este mes ya se encuentra con presupuesto asignado");
                        }
                        return $stateParameters;
                    });
                    $this->crud->columns([
                        'year',
                        'month',
                        'value',
                    ]);
                    $this->crud->displayAs(lang('budget.columns'));
                    break;
            }

            $output = $this->crud->render();
            if (isset($output->isJSONResponse) && $output->isJSONResponse) {
                header('Content-Type: application/json; charset=utf-8');
                echo $output->output;
                exit;
            }

            $this->viewTable($output, $component[0]->title, $component[0]->description);
        } else {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    private function _codeAddForm($table)
    {
        $this->crud->callbackAddField('code', function ($fieldType, $fieldName) use ($table) {
            return '<input class="form-control" disabled name="' . $fieldName . '" type="text" value="' . $this->_code($table) . '">';
        });
    }

    private function _codeSaveTable($table)
    {
        $this->crud->callbackBeforeInsert(function ($data) use ($table) {
            $data->data['code'] = $this->_code($table);
            return $data;
        });
        $this->crud->callbackEditField('code', function ($fieldValue, $primaryKeyValue, $rowData) {
            // You have access now at the extra custom variable $username
            return '<input class="form-control" name="code" disabled value="' . $fieldValue . '"  />';
        });
    }

    public function _code($table)
    {
        $data = $this->_tableCode($table);
        $quantity = $data->orderBy('id', 'Desc')->asObject()->get()->getResult();
        if (count($quantity) > 0) {
            $lastCode = (int)$quantity[0]->code;
            switch ($table) {
                case 'prices':
                    $consecutive = $lastCode + 10;
                    if (strlen($consecutive) >= 3) {
                        $code = "{$consecutive}";
                    } else {
                        $code = "0{$consecutive}";
                    }
                    break;
                case 'gender':
                case 'materials':
                    $consecutive = $lastCode + 1;
                    $code = "{$consecutive}";
                    break;
                case 'groups':
                case 'providers':
                    $consecutive = $lastCode + 1;
                    if (strlen($consecutive) >= 2) {
                        $code = "{$consecutive}";
                    } else {
                        $code = "0{$consecutive}";
                    }
                    break;
            }
        } else {
            switch ($table) {
                case 'prices':
                    $code = '010';
                    break;
                case 'gender':
                case 'materials':
                    $code = '0';
                    break;
                case 'sub_group':
                    $code = '01';
                    break;
                case 'providers':
                case 'groups':
                    $code = '00';
                    break;
            }
        }
        return $code;
    }

    /**
     * @param $table
     */
    private function _tableCode($table)
    {
        switch ($table) {
            case 'prices':
                $data = new Prices();
                break;
            case 'materials':
                $data = new Materials();
                break;
            case 'gender':
                $data = new Gender();
                break;
            case 'sub_group':
                $data = new SubGroup();
                break;
            case 'groups':
                $data = new Groups();
                break;
            case 'providers':
                $data = new Providers();
                break;
        }
        return $data;
    }

    public function logo($data)
    {

        $image = \Config\Services::image()
            ->withFile('assets/upload/imgs/' . $data->data['logo'])
            ->resize(200, 200, true)
            ->save('assets/upload/imgs/' . $data->data['logo']);

        $client = Services::curlrequest();
        $companies = new Company();

        $token = $companies->find($data->data['companies_id']);
        $client->setHeader('Content-Type', 'application/json');
        $client->setHeader('Accept', 'application/json');
        $client->setHeader('Authorization', "Bearer " . $token['token']);
        if (!empty($data->data['logo'])) {
            $res = $client->put(
                getenv('API') . '/ubl2.1/config/logo', [
                    'form_params' => [
                        'logo' => base64_encode(file_get_contents(base_url() . '/assets/upload/imgs/' . $data->data['logo'])),
                    ],
                ]
            );
            $json = json_decode($res->getBody());
            if (isset($json->errors)) {
                echo json_encode($json);
                die();
            }
        }
    }

    private function _clearNumber($data)
    {
        $data = str_replace('-', '', $data);
        return $data;
    }

    private function _getAccountingAccount($id, $nature = '')
    {
        $account = new AccountingAcount();
        if ($id == 1) {
            $data = $account->where(['type_accounting_account_id' => $id, 'nature' => $nature, 'companies_id' => session('user')->companies_id])->get()->getResult();
        } else {
            $data = $account->where(['type_accounting_account_id' => $id, 'companies_id' => session('user')->companies_id])->get()->getResult();
        }

        $info = [];
        foreach ($data as $item) {
            if ($id != 1 && $id != 4) {
                $info = array_merge($info, array($item->name . ' (' . $item->percent . '%' . ') ' => $item->id));
            } else {
                $info = array_merge($info, array($item->name => $item->id));
            }
        }
        $info = array_flip($info);
        return $info;
    }

    private function calcularDV($nit)
    {
        if (!is_numeric($nit)) {
            return false;
        }

        $arr = array(1 => 3, 4 => 17, 7 => 29, 10 => 43, 13 => 59, 2 => 7, 5 => 19,
            8 => 37, 11 => 47, 14 => 67, 3 => 13, 6 => 23, 9 => 41, 12 => 53, 15 => 71);
        $x = 0;
        $y = 0;
        $z = strlen($nit);
        $dv = '';

        for ($i = 0; $i < $z; $i++) {
            $y = substr($nit, $i, 1);
            $x += ($y * $arr[$z - $i]);
        }

        $y = $x % 11;

        if ($y > 1) {
            $dv = 11 - $y;
            return $dv;
        } else {
            $dv = $y;
            return $dv;
        }

    }


    protected function relations()
    {
        // TODO: Implement relations() method.
    }

    protected function rules()
    {
        // TODO: Implement rules() method.
    }

    protected function fieldType()
    {
        // TODO: Implement fieldType() method.
    }

    protected function callback()
    {
        // TODO: Implement callback() method.
    }
}
