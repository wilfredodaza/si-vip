<?php
    namespace Config;

    // Create a new instance of our RouteCollection class.
    $routes = Services::routes();

    // Load the system's routing file first, so that the app and ENVIRONMENT
    // can override as needed.
    if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
        require SYSTEMPATH . 'Config/Routes.php';
    }

    /**
     * --------------------------------------------------------------------
     * Router Setup
     * --------------------------------------------------------------------
     */
    $routes->setDefaultNamespace('App\Controllers');
    $routes->setDefaultController('AuthController');
    $routes->setDefaultMethod('login');
    $routes->setTranslateURIDashes(false);
    $routes->set404Override();
    $routes->setAutoRoute(true);

    /**
     * --------------------------------------------------------------------
     * Route Definitions
     * --------------------------------------------------------------------
     */

    // We get a performance increase by specifying the default
    // route since we don't have to scan directories.

    //Configuraciones
    $routes->get('/', 'Configuration\AuthController::login');
    $routes->get('/register', 'Configuration\AuthController::register');
    $routes->post('/create', 'Configuration\AuthController::create');
    $routes->get('/reset_password', 'Configuration\AuthController::resetPassword');
    $routes->post('/forgot_password', 'Configuration\AuthController::forgotPassword');    
    $routes->post('/validation', 'Configuration\AuthController::validation');
    $routes->get('/logout', 'Configuration\AuthController::logout');
    $routes->get('/home', 'Configuration\HomeController::index');
    $routes->get('/about', 'Configuration\HomeController::about');
    $routes->get('/perfile', 'Configuration\UserController::perfile');
    $routes->post('/update_photo', 'Configuration\UserController::updatePhoto');
    $routes->post('/update_user', 'Configuration\UserController::updateUser');
    $routes->post('/config/(:segment)', 'Configuration\ConfigController::index/$1');
    $routes->get('/config/(:segment)', 'Configuration\ConfigController::index/$1');
    $routes->post('/table/(:segment)', 'Configuration\TableController::index/$1');
    $routes->get('/table/(:segment)', 'Configuration\TableController::index/$1');
    // $routes->get('/table/reference/:(num)', 'Configuration\TableController::reference/$1');
    $routes->get('/home/products', 'Configuration\HomeController::products');
    $routes->get('/home/customers', 'Configuration\HomeController::customers');
    $routes->get('/prueba', 'Configuration\HomeController::prueba');

    $routes->get('module/ubication/(:num)', 'ModuleController::ubication/$1');

    $routes->get('client_secret', 'ReceptionGmailController::client_secret');
    $routes->post('client_secret', 'ReceptionGmailController::upload_client_secret');


    //Api
    $routes->group('api', function ($routes) {
         //Auth
        $routes->post('auth', 'Api\Auth::create');
        $routes->post('me', 'Api\Auth::verifyToken');
        // $routes->post('auth/verify', 'Api\Auth::verifyToken');
        $routes->group('v1', function ($routes) {
            $routes->get('resolutions/(:num)', 'Api\Resolution::resolutions/$1');
            $routes->get('customers/', 'Api\Customer::index');
            $routes->get('company', 'Api\Company::index');
            $routes->get('type_payroll_adjust_notes','Api\V2\TypePayrollAdjustNote::index');
            $routes->group('customer/', function($routes) {
                $routes->post('/',          'Api\Customer::new');
                $routes->get('/(:num)',     'Api\Customer::show/$1');
                $routes->put('/(:num)',     'Api\Customer::update/$1');
                $routes->delete('/(:num)',  'Api\Customer::delete/$1');
            });
            //Resolutions
            $routes->group('resolution', function($routes) {
                $routes->get('invoice/(:num)', 'Api\Resolution::invoice/$1');
                $routes->get('credit_note/(:any)', 'Api\Resolution::creditNote/$1');
                $routes->get('debit_note/(:num)', 'Api\Resolution::debitNote/$1');
                $routes->get('payroll/(:num)', 'Api\Resolution::payroll/$1');
            });



       		$routes->get('email_invoices/', 'ApiController::envioMasivo');
            //Tablas de referencias
            $routes->get('currencies', 'Api\Currency::list');
            $routes->get('payment_methods', 'Api\PaymentMethod::index');
            $routes->get('payment_forms', 'Api\PaymentForm::index');
            $routes->get('delevery_terms', 'Api\DeleveryTerm::index');
            $routes->get('taxes', 'Api\Tax::index');
            $routes->get('debit_note_discrepancy_responses', 'Api\DiscrepancyResponse::debit');
            $routes->get('credit_note_discrepancy_responses', 'Api\DiscrepancyResponse::credit');
      
            // Products
            $routes->get('products', 'Api\Products::list');
            $routes->post('products', 'Api\Products::store');
            $routes->get('products/edit/(:segment)', 'Api\Products::edit/$1');
            $routes->put('products/(:segment)', 'Api\Products::update/$1');
            $routes->delete('products/(:segment)', 'Api\Products::delete/$1');

            // Customers
           // $routes->get('customers', 'Api\Customers::list');
            $routes->post('customers', 'Api\Customers::create');

            //worker
            $routes->get('workers', 'Api\Worker::index');
            $routes->post('worker', 'Api\Worker::new');


        


            //Otros
            $routes->get('type_document_identifications', 'Api\TypeDocumentIdentification::index');
            $routes->get('type_customers', 'Api\TypeCustomer::index');
            $routes->get('type_regimes', 'Api\TypeRegime::index');
            $routes->get('type_organizations', 'Api\TypeOrganization::index');
            $routes->get('municipalities', 'Api\Municipality::index');
            $routes->get('payroll_type_document_identifications', 'Api\PayrollTypeDocumentIdentification::index');
            $routes->get('type_contracts', 'Api\TypeContract::index');
            $routes->get('type_workers', 'Api\TypeWorker::index');
            $routes->get('sub_type_workers', 'Api\SubTypeWorker::index');
            $routes->get('banks', 'Api\Bank::index');
            $routes->get('bank_account_types', 'Api\BankAccountType::index');
            $routes->get('payroll_periods', 'Api\PayrollPeriod::index');
            $routes->get('type_accrueds', 'Api\TypeAccrued::index');
            $routes->get('type_deductions', 'Api\TypeDeduction::index');
            $routes->get('type_law_deductions', 'Api\TypeLawDeduction::index');
            $routes->get('type_disabilities', 'Api\TypeDisability::index');
            $routes->get('type_generation_transmitions', 'Api\TypeGenerationTransmition::index');
   

            //quotation
            $routes->get('quotation/close/(:segment)', 'Api\Quotation::close/$1');

            //Accounting Acount
            $routes->get('entry_credit', 'Api\AccountingAcount::entryCredit');
            $routes->get('entry_debit', 'Api\AccountingAcount::entryDebit');
            $routes->get('tax_pay', 'Api\AccountingAcount::taxPay');
            $routes->get('tax_advance', 'Api\AccountingAcount::taxAdvance');
            $routes->get('account_pay', 'Api\AccountingAcount::accountPay');

	    
            $routes->get('invoices/all', 'Api\Invoices::invoices');
            $routes->post('enviado_dian/(:segment)/(:segment)', 'Api\Invoices::sendDIAN/$1/$2');
            $routes->get('invoices/status_transfer/(:any)/(:any)', 'Api\Invoices::status/$1/$2');


            //configurations
            $routes->get('configuration', 'Api\Config::index');
            

            $routes->get('sellers', 'Api\Seller::index');
            $routes->get('users', 'Api\User::index');
            $routes->get('user/show', 'Api\User::show');

            //informes
            $routes->get('graphic/sales_of_month', 'Api\Graphic::salesOfMonth');
            $routes->get('graphic/sales_of_product_twelve', 'Api\Graphic::salesOfProductTwelve');
            $routes->get('graphic/sales_of_product', 'Api\Graphic::salesOfProduct');
            $routes->get('graphic/sales_of_customer', 'Api\Graphic::salesOfCustomer');
            $routes->get('graphic/sales_of_customer_month', 'Api\Graphic::salesOfCustomerMonth');
            $routes->get('graphic/sales_of_customer_month_previus', 'Api\Graphic::salesOfCustomerMonthPrevius');
            $routes->get('graphic/sales_of_customer_month_accumulated', 'Api\Graphic::salesOfCustomerMonthAccumulated');
            $routes->get('graphic/sales_of_seller', 'Api\Graphic::salesOfSeller');
            $routes->get('graphic/sales_of_seller_month', 'Api\Graphic::salesOfSellerMonth');
            $routes->get('graphic/sales_of_seller_previus', 'Api\Graphic::salesOfSellerPrevius');
            $routes->get('graphic/sales_of_seller_accumulated', 'Api\Graphic::salesOfSellerAccumulated');
            $routes->get('graphic/sales_of_wallet', 'Api\Graphic::salesOfWallet');

            $routes->get('graphic/sales_of_month/(:segment)', 'Api\Graphic::salesOfMonth/$1');
            $routes->get('graphic/sales_of_product/(:segment)', 'Api\Graphic::salesOfProduct/$1');
            $routes->get('graphic/sales_of_product_twelve/(:segment)', 'Api\Graphic::salesOfProductTwelve/$1');
            $routes->get('graphic/sales_of_customer/(:segment)', 'Api\Graphic::salesOfCustomer/$1');
            $routes->get('graphic/sales_of_customer_month/(:segment)', 'Api\Graphic::salesOfCustomerMonth/$1');
            $routes->get('graphic/sales_of_customer_month_previus/(:segment)', 'Api\Graphic::salesOfCustomerMonthPrevius/$1');
            $routes->get('graphic/sales_of_customer_month_accumulated/(:segment)', 'Api\Graphic::salesOfCustomerMonthAccumulated/$1');
            $routes->get('graphic/sales_of_seller/(:segment)', 'Api\Graphic::salesOfSeller/$1');
            $routes->get('graphic/sales_of_seller_month/(:segment)', 'Api\Graphic::salesOfSellerMonth/$1');
            $routes->get('graphic/sales_of_seller_previus/(:segment)', 'Api\Graphic::salesOfSellerPrevius/$1');
            $routes->get('graphic/sales_of_seller_accumulated/(:segment)', 'Api\Graphic::salesOfSellerAccumulated/$1');
            $routes->get('graphic/sales_of_wallet/(:segment)', 'Api\Graphic::salesOfWallet/$1');
            $routes->get('graphic/setting', 'Api\Graphic::index');
            $routes->get('graphic/setting/(:segment)', 'Api\Graphic::index/$1');
            $routes->get('graphic/graphic_type/(:segment)', 'Api\Graphic::graphicType/$1');
            $routes->post('graphic/setting', 'Api\Graphic::create');


            //payment_form
            $routes->get('payment_method/', 'Api\PaymentMethod::index');
            $routes->get('document_support/withholdings/(:segment)',        'Api\DocumentSupport::withholdings/$1');
            $routes->post('document_support/withholding',                   'Api\DocumentSupport::withholdingCreate');
            $routes->put('document_support/withholding/update/(:segment)', 'Api\DocumentSupport::withholdingUpdate/$1');
            $routes->get('document_support/withholding/delete/(:segment)', 'Api\DocumentSupport::withholdingDelete/$1');

            //Document Support
            $routes->post('document_support', 'Api\DocumentSupport::create');
            $routes->get('document_support/(:num)/edit', 'Api\DocumentSupport::edit/$1');
            $routes->put('document_support/(:num)',  'Api\DocumentSupport::update/$1');
            $routes->get('document_support/show/(:segment)', 'Api\DocumentSupport::show/$1');
            $routes->get('document_support/providers', 'Api\DocumentSupport::providers');
            $routes->post('document_support/(:segment)', 'Api\DocumentSupport::update/$1');
            $routes->post('document_support/upload_file/(:segment)', 'Api\DocumentSupport::uploadFile/$1');
            $routes->get('document_support/attachment_documents/(:segment)', 'Api\DocumentSupport::attachmentDocument/$1');
            $routes->get('document_support/attachment_documents/delete/(:segment)', 'Api\DocumentSupport::attachmentDocumentDelete/$1');

            //Documento soporte de ajuste
            $routes->get('document_support_adjust/(:num)/edit', 'Api\DocumentSupportAdjust::edit/$1');
            $routes->post('document_support_adjust/(:num)', 'Api\DocumentSupportAdjust::create/$1');
            $routes->put('document_support_adjust/(:num)', 'Api\DocumentSupportAdjust::update/$1');




            $routes->get('wallet/edit/(:segment)', 'Api\Wallet::edit/$1');
            $routes->post('payroll', 'Api\Payroll::new');
            $routes->get('payroll/edit/(:num)', 'Api\Payroll::edit/$1');
            $routes->put('payroll/(:num)', 'Api\Payroll::update/$1');
            $routes->get('payroll_removable/edit/(:num)', 'Api\PayrollRemovable::edit/$1');
            $routes->put('payroll_removable/(:num)', 'Api\PayrollRemovable::update/$1');


            $routes->post('payroll_adjust', 'Api\PayrollAdjust::store');
            $routes->put('payroll_adjust/(:num)', 'Api\PayrollAdjust::update/$1');
            $routes->get('payroll_adjust/edit/(:num)', 'Api\PayrollAdjust::edit/$1');
            $routes->post('payroll_adjust/update/(:num)', 'Api\PayrollAdjust::update/$1');

            // Shopping Api

            // PurchaseOrder api
            $routes->post('PurchaseOrderCreate', 'Api\PurchaseOrder::create');
            $routes->post('PurchaseOrderUpdate/(:num)', 'Api\PurchaseOrder::update/$1');
            $routes->get('PurchaseOrder/invoice/(:num)', 'Api\PurchaseOrder::invoice/$1');

            // expenses
            $routes->get('expenses/headquarters', 'Api\Expenses::headquarters');
            $routes->post('expenses', 'Api\Expenses::create');
            $routes->get('expenses/edit/(:num)', 'Api\Expenses::edit/$1');
            $routes->post('expenses/update/(:num)', 'Api\Expenses::update/$1');

            // QUOTATION
            $routes->post('quotation/store', 'Api\Quotation::store');
            $routes->get('quotation/invoice/(:segment)', 'Api\Quotation::invoice/$1');
            $routes->post('quotation/update/(:segment)', 'Api\Quotation::update/$1');
        });


        //invoice
        $routes->post('invoices/create', 'Api\Invoices::create');
        $routes->get('invoices/resolution/(:segment)', 'Api\Invoices::resolution/$1');
        $routes->get('invoices/multiple_resolutions', 'Api\Invoices::multipleResolution');
        $routes->post('invoices/update/(:segment)', 'Api\Invoices::update/$1');
        $routes->get('invoices/invoice/$1', 'Api\Invoices::invoice/$1');
        $routes->get('invoices/line_invoice/$1', 'Api\Invoices::line_invoice/$1');
        $routes->get('invoices/currency', 'Api\Currency::index');
        $routes->get('invoices/cufe/(:segment)', 'Api\Invoices::cufe/$1');
    	$routes->get('invoices/all_annexes/(:segment)', 'Api\Invoices::annexe/$1');


        //Autenticacion
        $routes->get('products', 'Api\Products::index');
        $routes->get('customers', 'Api\Customers::index');
        $routes->post('customers/store', 'Api\Customers::store');
        $routes->get('customers/providers', 'Api\Customers::providers');
        $routes->get('municipalities', 'Api\PostController::municipalities');
        $routes->get('typedocumentidentification', 'Api\PostController::TypeDocumentIdentification');
        $routes->get('typecustomer', 'Api\PostController::typeCustomer');
        $routes->get('typeregimes', 'Api\PostController::typeRegimes');
        $routes->get('typeorganizations', 'Api\PostController::typeOrganizations');
        $routes->get('companies', 'Api\PostController::Company');

        // odoo
        $routes->get('invoices/multiple_resolutions_odoo/(:segment)', 'Api\Invoices::multipleResolutionOdoo/$1');
        $routes->get('invoices/resolution_odoo/(:any)/(:any)/(:any)', 'Api\Invoices::resolutionOdoo/$1/$2/$3');
        $routes->get('companiesodoo/(:any)', 'Api\Companies::Companies/$1');

        
        //Note Credit
        $routes->post('note_credit/create', 'Api\NoteCredit::create');
        $routes->get('note_credit/resolution', 'Api\NoteCredit::resolution');
        $routes->get('note_credit/invoice/(:segment)', 'Api\NoteCredit::invoice/$1');
        $routes->get('note_credit/line_invoice/(:segment)', 'Api\NoteCredit::line_invoice/$1');

        //Note Debit
        $routes->post('note_debit/create', 'Api\NoteDebit::create');
        $routes->get('note_debit/resolution', 'Api\NoteDebit::resolution');
        $routes->get('note_debit/invoice/(:segment)', 'Api\NoteDebit::invoice/$1');
        $routes->get('note_debit/line_invoice/(:segment)', 'Api\NoteDebit::line_invoice/$1');

        //solicitud
        $routes->post('Solicitud/create', 'Api\Solicitud::create');


        //Resolution
        $routes->get('resolution/quatation', 'Api\Resolution::quatation');
        $routes->get('resolution/invoice',    'Api\Resolution::invoice');
        $routes->get('resolution/purchaseOrder', 'Api\Resolution::purchaseOrder');


        //Quotation
        $routes->post('quatation/store', 'Api\Quotation::store');
        $routes->get('quotation/invoice/(:segment)', 'Api\Quotation::invoice/$1');
        $routes->get('quotation/line_invoice/(:segment)', 'Api\Quotation::lineInvoice/$1');
        $routes->post('quotation/update/(:segment)', 'Api\Quotation::update/$1');
        $routes->post('quotation/generate_facture/(:segment)', 'Api\Quotation::generateFacture/$1');
    });





    //Invoice
    $routes->get('/invoice', 'InvoiceController::index',['as' => 'invoice.index'] );
    $routes->get('/invoice/create', 'InvoiceController::create');
    $routes->get('/invoice/edit/(:any)', 'InvoiceController::edit/$1');
    $routes->get('/invoice/preview/(:segment)', 'ApiController::preview/$1');

    $routes->get('/invoice/pdf/(:segment)', 'ApiController::pdf/$1');
    $routes->get('/invoice/pdf/(:segment)/(:segment)', 'ApiController::preview2/$1/$2');
    $routes->get('/invoice/email/(:segment)/(:segment)', 'ApiController::email/$1/$2');
    //$routes->get('/invoice/send/(:segment)/(:any)', 'ApiController::send/$1/$2');
    $routes->post('/invoice/send/(:segment)', 'ApiController::send/$1');
    $routes->get('/invoice/receivedMail', 'InvoiceController::receivedMail');
    $routes->get('/invoice/attached_document/(:segment)', 'InvoiceController::attachedDocument/$1');
    $routes->post('invoice/delete', 'InvoiceController::delete', ['as' => 'invoice-delete']);
    $routes->get('invoice/validate_uuid/(:segment)', 'InvoiceController::validationUUID/$1', ['as' => 'invoice-validation']);
    $routes->post('invoice/send_multiple', 'InvoiceController::sendMultiple', ['as' => 'invoice.sendMultiple']);
   

    $routes->get('roles', 'RoleController::index');
    $routes->post('roles', 'RoleController::store');
    $routes->get('roles/(:num)', 'RoleController::edit/$1');
    $routes->post('roles/(:num)', 'RoleController::update/$1');
    $routes->get('permissions/(:num)', 'RoleController::permissions/$1');
    $routes->post('permissions/(:num)', 'RoleController::storePermissions/$1');
    
   

   
    //Documents
    $routes->get('/document/csv/(:segment)', 'DocumentController::csv/$1');
    $routes->get('/document/worldOffice/(:segment)', 'DocumentController::worldOffice/$1');

    //Nota Credito
    $routes->get('/noteCredit/(:segment)', 'NoteCreditController::index/$1');

    //Nota Debito
    $routes->get('/noteDebit/(:segment)', 'NoteDebitController::index/$1');


    //solicitudes de registro
    $routes->get('/solicitudes', 'SolicitudesController::index');
    $routes->post('/solicitud/archivos/(:any)', 'SolicitudesController::cargarArchivos/$1');
    $routes->get('/solicitudes/info/(:any)', 'SolicitudesController::info/$1');
    $routes->post('/solicitud/edit/(:any)', 'SolicitudesController::edit/$1');
    $routes->post('/solicitud/documento/estado', 'SolicitudesController::estadoDocumento');
    $routes->post('/solicitud/validacion/(:any)', 'SolicitudesController::validacion/$1');
    $routes->post('/solicitud/documento/edit/(:any)/(:any)', 'SolicitudesController::editarArchivos/$1/$2');
    $routes->post('/solicitud/pruebaycredenciales/(:any)', 'SolicitudesController::pruebaCredenciles/$1');
    $routes->get('/solicitud/informacion/(:any)', 'SolicitudesController::informacionCliente/$1');
    $routes->post('/solicitud/guardarDocumentos/(:any)', 'SolicitudesController::guardarDocumentos/$1');
    $routes->get('/solicitud/reenvio/(:any)', 'SolicitudesController::Reenvio/$1');


    //metodos temporales
    $routes->post('/solicitud/guardarsolicitante', 'ApplicantController::create');
    $routes->post('/solicitud/pagosolicitante', 'ApplicantController::subscription');
    $routes->get('/solicitudes/registro', 'SolicitudesController::registro');
    $routes->post('/solicitudes/epayco', 'SolicitudesController::Epayco');
    $routes->post('/solicitudes/seguimiento/(:segment)', 'SolicitudesController::Seguimiento/$1');
    //registro actualicese
    $routes->get('/actualicese', 'ActualiceseApi::index');
    $routes->get('/solicitudes/datos/(:segment)', 'ActualiceseApi::edit/$1');
    $routes->post('/solicitudes/actualicese/(:segment)', 'ActualiceseApi::store/$1');

    //solicitudes incompletas
    $routes->get('/solicitudes/incompletas', 'SolicitudesIncomController::index');
    $routes->get('/solicitudes/incompletas/info/(:any)', 'SolicitudesIncomController::info/$1');
    $routes->post('/solicitud/incompletas/edit/(:any)', 'SolicitudesIncomController::edit/$1');
    $routes->post('/solicitudes/incompletas/seguimiento/(:segment)', 'SolicitudesIncomController::Seguimiento/$1');
    $routes->get('/solicitudes/incompletas/email', 'SolicitudesIncomController::solicitudesIncomEmail');

    //Wallet
    $routes->get('/wallet', 'WalletController::index');
    $routes->get('/wallet/show/(:segment)', 'WalletController::show/$1');
    $routes->post('/wallet/update/(:segment)/(:segment)', 'WalletController::update/$1/$2');
    $routes->post('/wallet/store/(:segment)', 'WalletController::store/$1');
    $routes->delete('/wallet/(:num)', 'WalletController::delete/$1');
    $routes->get('/wallet/download/(:segment)', 'WalletController::download/$1');

    $routes->get('report', 'Reports\ReportController::index');
    $routes->post('report', 'Reports\ReportController::index');
    $routes->get('report/reset', 'Reports\ReportController::reset');

    $routes->get('report/sale/(:segment)', 'Reports\ReportController::reportSale/$1');
    $routes->get('report/tax/(:segment)', 'Reports\ReportController::reportTax/$1');
    $routes->get('report/csv', 'Reports/ReportController::csv');
    $routes->get('report/csvExportReportTax', 'Reports\ReportController::csvExportReportTax');
    $routes->get('report/csvExportReportSale', 'Reports\ReportController::csvExportReportSale');
    $routes->get('report/csvExportHelisa', 'Reports\ReportController::csvExportHelisa');
    $routes->get('report/helisa/invoice', 'Reports\ReportHelisaController::index');
    $routes->post('report/helisa/invoice', 'Reports\ReportHelisaController::index');
    $routes->get('report/helisa/download', 'Reports\ReportHelisaController::reportHelisaInvoice');


    $routes->get('report/helisa/customer', 'Reports\ReportHelisaCustomerController::index');
    $routes->post('report/helisa/customer', 'Reports\ReportHelisaCustomerController::index');
    $routes->get('report/helisa/customers/download', 'Reports\ReportHelisaCustomerController::reportHelisaInvoice');

    $routes->get('report_general', 'Reports\ReportGeneralController::index');
    $routes->post('report_general', 'Reports\ReportGeneralController::index');
    $routes->get('report_general/reset', 'Reports\ReportGeneralController::reset');
    $routes->get('report_general/excel', 'Reports\ReportGeneralController::excel');


    $routes->get('report_detail', 'Reports\ReportDetailController::index');
    $routes->post('report_detail', 'Reports\ReportDetailController::index');
    $routes->get('report_detail/reset', 'Reports\ReportDetailController::reset');
    $routes->get('report_detail/excel', 'Reports\ReportDetailController::excel');


    $routes->get('report_taxes', 'Reports\ReportTaxesController::index');
    $routes->post('report_taxes', 'Reports\ReportTaxesController::index');
    $routes->get('report_taxes/reset', 'Reports\ReportTaxesController::reset');
    $routes->get('report_taxes/excel', 'Reports\ReportTaxesController::excel');


    $routes->get('report_retention', 'Reports\ReportRetentionController::index');
    $routes->post('report_retention', 'Reports\ReportRetentionController::index');
    $routes->get('report_retention/reset', 'Reports\ReportRetentionController::reset');
    $routes->get('report_retention/excel', 'Reports\ReportRetentionController::excel');


    $routes->get('report_wallet', 'Reports\ReportWalletController::index');
    $routes->post('report_wallet', 'Reports\ReportWalletController::index');
    $routes->get('report_wallet/reset', 'Reports\ReportWalletController::reset');
    $routes->get('report_wallet/excel', 'Reports\ReportWalletController::excel');


    $routes->get('report_quotation', 'Reports\ReportQuotationController::index');
    $routes->post('report_quotation', 'Reports\ReportQuotationController::index');
    $routes->get('report_quotation/reset', 'Reports\ReportQuotationController::reset');
    $routes->get('report_quotation/excel', 'Reports\ReportQuotationController::excel');


    $routes->get('report/reportInvoiceDetail', 'Reports\ReportController::reportInvoiceDetail');
    $routes->get('report/reportTaxes', 'Reports\ReportController::reportTaxes');
    $routes->get('report/reportRetention', 'Reports\ReportController::reportRetention');
    $routes->get('report/reportWallet', 'Reports\ReportController::reportWallet');


    $routes->get('report_payroll', 'Reports\ReportPayrollController::index');
    $routes->post('report_payroll', 'Reports\ReportPayrollController::index');
    $routes->get('report_payroll/reset', 'Reports\ReportPayrollController::reset');
    $routes->get('report_payroll/excel', 'Reports\ReportPayrollController::excel');

    //Eventos
    $routes->get('events/accept', 'EventController::accept/$1/$2');
    $routes->get('events/rejected', 'EventController::rejected/$1/$2');


    //Post
    $routes->get('/post/create', 'PostController::create');
    $routes->get('post/cierre', 'PostController::indexCierre');

    //Notification
    $routes->get('notification/index', 'NotificationController::index');
    $routes->get('notification/view/(:segment)', 'NotificationController::view/$1');

    //Quotation
    $routes->get('quotation', 'QuotationController::index');
    $routes->get('quotation/create', 'QuotationController::create');
    $routes->get('quotation/edit/(:segment)', 'QuotationController::edit/$1');
    $routes->get('quotation/email/(:segment)', 'QuotationController::email/$1');
    $routes->get('quotation/close/(:num)', 'QuotationController::close/$1');



    $routes->get('tracking/quotation/(:segment)', 'TrackingController::quotation/$1');
    $routes->post('tracking/create/(:segment)/(:segment)', 'TrackingController::create/$1/$2');
    $routes->post('tracking/update/(:segment)/(:segment)/(:segment)', 'TrackingController::update/$1/$2/$3');
    $routes->get('tracking/edit/(:segment)', 'TrackingController::edit/$1');





    //informes
    $routes->get('graphic/', 'GraphicController::setting');
    $routes->get('graphic/sales_of_month', 'GraphicController::salesOfMonth');
    $routes->get('graphic/sales_of_product', 'GraphicController::salesOfProduct');
    $routes->get('graphic/sales_of_customer', 'GraphicController::salesOfCustomer');
    $routes->get('graphic/sales_of_seller', 'GraphicController::salesOfSeller');
    $routes->get('graphic/sales_of_wallet', 'GraphicController::salesOfWallet');


    //imports
    $routes->get('import','ImportController::index');
    $routes->post('import/upload', 'ImportController::upload');


    //Document External y RADIAN
    $routes->group('documents', function ($routes){
        $routes->get('', 'Documents\DocumentReceptionController::index', ['as' => 'document-index']);
        $routes->post('', 'Documents\DocumentReceptionController::create', ['as' => 'document-create']);
        $routes->get('(:num)', 'Documents\DocumentReceptionController::show/$1', ['as' => 'document-show']);
        $routes->delete('documents/(:num)', 'Documents\DocumentReceptionController::delete/$1', ['as' => 'document-delete']);
        $routes->get('payment/(:num)', 'Documents\DocumentReceptionController::payment/$1', ['as' => 'document-payment']);
        $routes->get('associate_product/(:segment)', 'Documents\DocumentReceptionController::associateProduct/$1', ['as' => 'document-associate-product']);
        $routes->get('event/(:num)/(:num)/(:segment)/(:segment)', 'EventController::event/$1/$2/$3/$4', ['as' => 'documents-event']);
        $routes->get('download/(:segment)', 'Documents\DocumentReceptionController::download/$1', ['as' => 'documents-download']);
    });

    $routes->post('documents/payment_upload/(:num)', 'Documents\DocumentReceptionController::paymentUpload/$1');

    $routes->get('documents/download_file/(:segment)', 'Documents\DocumentReceptionController::downloadFile/$1');
    $routes->get('documents/delete_file/(:num)/(:num)/(:num)', 'Documents\DocumentReceptionController::deleteFile/$1/$2/$3');

    $routes->get('documents/validations/(:segment)/(:segment)', 'Documents\DocumentReceptionController::validations/$1/$2');
    $routes->get('documents/products/(:segment)', 'Documents\DocumentReceptionController::products/$1');
    $routes->post('documents/product_created/(:segment)/(:segment)/(:segment)', 'Documents\DocumentReceptionController::productCreated/$1/$2/$3');
    $routes->get('document_support/sending_invitation/(:segment)', 'DocumentSupportController::sendingInvitation/$1');
    $routes->get('document_support/sending_invitation_provider/(:segment)', 'DocumentSupportController::sendingInvitationProvider/$1');
    $routes->get('accept_invitation/(:segment)', 'DocumentSupportController::acceptInvitation/$1');





    //Customer
    $routes->post('customer/(:segment)', 'CustomerController::update/$1');
    $routes->post('customer/bank_certificate/(:segment)', 'CustomerController::bankCertificate/$1');
    $routes->post('customer/rut/(:segment)', 'CustomerController::rut/$1');
    $routes->post('customer/firm/(:segment)', 'CustomerController::firm/$1');
    $routes->post('customer/attached_document/(:segment)', 'CustomerController::attachedDocument/$1');
    $routes->get('customer/attached_document/delete/(:segment)/(:segment)', 'DocumentSupportController::attachedDocumentDelete/$1/$2');



    // Documento Soporte
    $routes->get('document_support/sending_invitation/(:segment)', 'DocumentSupportController::sendingInvitation/$1');
    $routes->get('document_support/sending_invitation_provider/(:segment)', 'DocumentSupportController::sendingInvitationProvider/$1');
    $routes->get('accept_invitation/(:segment)', 'DocumentSupportController::acceptInvitation/$1');
    $routes->post('document_support/upload_file_excel', 'Imports\DocumentSupportImportController::create');

    $routes->group('document_support', function ($routes){
        $routes->get('', 'DocumentSupportController::index', ['as' => 'document_support.index']);
        $routes->get('create', 'DocumentSupportController::create', ['as' => 'document_support.create']);
        $routes->get('edit/(:num)', 'DocumentSupportController::edit/$1', ['as' => 'document_support.edit']);
        $routes->post('send/(:num)', 'DocumentSupportController::send/$1', ['as' => 'document_support.send']);
        $routes->get('send/(:num)/(:num)', 'DocumentSupportController::send/$1/$2');
        $routes->get('email/(:num)', 'DocumentSupportController::email/$1', ['as' => 'document_support.email']);
        $routes->delete('delete/(:num)', 'DocumentSupportController::delete/$1', ['as' => 'document_support.delete']);
        $routes->get('pdf/(:num)', 'DocumentSupportController::pdf/$1', ['as' => 'document_support.pdf']);
        $routes->get('previsualization/(:num)', 'DocumentSupportController::previsualization/$1', ['as' => 'document_support.previsualization']);
        $routes->get('file_info/(:num)', 'DocumentSupportController::fileInfo/$1', ['as' => 'document_support.upload_file']);
    });

    $routes->get('document_support/firm/(:segment)', 'DocumentSupportController::firm/$1');
    $routes->get('document_support/create_pdf/(:segment)', 'DocumentSupportController::createPdf/$1');
    $routes->get('document_support/agree/(:segment)', 'DocumentSupportController::agree/$1');
    $routes->post('document_support/cancel/(:segment)', 'DocumentSupportController::cancel/$1');
    $routes->get('document_support/firm_document/(:any)', 'DocumentSupportController::firmDocument/$1');

    $routes->get('document_support/delete/(:segment)/(:segment)', 'DocumentSupportController::deleteDocument/$1/$2');
    $routes->post('document_support/payroll_document_support/(:num)', 'DocumentSupportController::payrollDocumentSupport/$1');
    $routes->get('tracking/delete/(:num)/(:num)', 'DocumentSupportController::deleteTracking/$1/$2');
   

    $routes->post('document_support/update_provider/(:segment)/(:segment)', 'DocumentSupportController::updateProvider/$1/$2');
    $routes->get('graphic/email', 'GraphicController::emailSales');

    $routes->get('document_support_adjust/create/(:num)', 'DocumentSupportAdjustController::create/$1');
    $routes->get('document_support_adjust/(:num)/edit', 'DocumentSupportAdjustController::edit/$1', ['as' => 'document_support_adjust_edit']);


    //payrolls
    $routes->get('payrolls', 'PayrollController::index', ['as' => 'payroll-Index']);
    $routes->get('payrolls/create', 'PayrollController::create');
    $routes->get('payroll/edit/(:num)', 'PayrollController::edit/$1');
    $routes->post('payrolls/send/(:num)', 'PayrollController::send/$1');
    $routes->post('payroll/send_multiple/(:num)', 'PayrollController::sendMultiple/$1');
    $routes->get('payrolls/download/(:num)', 'PayrollController::download/$1');
    $routes->get('payrolls/download_previsualization/(:num)', 'PayrollController::downloadPrevisualization/$1');
    $routes->get('payrolls/xml/(:num)', 'PayrollController::xml/$1');
    $routes->post('payroll/add/(:num)', 'PeriodController::addWorker/$1');
    $routes->post('payrolls/payment/(:num)/(:any)', 'PayrollController::createClosePayroll/$1/$2', ['payroll-payment']);

    
   

    $routes->get('workers', 'WorkerController::index');
    $routes->get('workers/create', 'WorkerController::create');
    $routes->post('workers', 'WorkerController::new');
    $routes->get('workers/edit/(:num)', 'WorkerController::edit/$1');
    $routes->put('workers/(:num)', 'WorkerController::update/$1');
    $routes->get('workers/(:num)', 'WorkerController::show/$1');
    $routes->get('workers/delete/(:num)', 'WorkerController::delete/$1');
    $routes->get('workers/change_status/(:num)', 'WorkerController::changeStatus/$1');

    $routes->get('workers/export', 'WorkerController::export');
    $routes->post('workers/import', 'WorkerController::import');

    $routes->get('periods/cune/(:segment)', 'PayrollController::cune/$1');
    $routes->get('periods/status_zip/(:num)', 'PayrollController::statusZIP/$1');

    $routes->resource('periods',  ['controller' =>'PeriodController']);
    $routes->post('periods/delete/(:segment)', 'PeriodController::delete/$1');

    $routes->resource('period_adjusts',  ['controller' =>'PeriodAdjustController']);
    $routes->post('period_adjusts/delete/(:segment)', 'PeriodAdjustController::delete/$1');



    $routes->get('import/payroll', 'ImportPayrollController::index');
    $routes->post('import/load', 'ImportPayrollController::load');
    $routes->post('import/tyc/load', 'ImportPayrollTycController::load');
    $routes->Post('validation/(:any)', 'ImportPayrollController::uploadPayroll/$1');
    $routes->Post('delete/(:any)', 'ImportPayrollController::delete_payroll/$1');
    $routes->post('monthvalidation','ImportPayrollController::monthvalidation');

    //other_concepts
    $routes->get('other_concepts', 'OtherConceptsController::index');
    $routes->post('other_concepts/create', 'OtherConceptsController::create');
    $routes->post('other_concepts/edit/(:num)', 'OtherConceptsController::edit/$1');
    //other_banks
    $routes->get('other_banks', 'OtherBanksController::index');
    $routes->post('other_banks/create', 'OtherBanksController::create');
    $routes->post('other_banks/edit/(:num)', 'OtherBanksController::edit/$1');

    $routes->get('validation_subscription/', 'ValidationSubcriptionController::emails');
    $routes->get('manager/notification', 'Managers/Manager::emailNotification');


    $routes->get('search_product', 'ProductsController::search');
    $routes->get('inventory', 'InventoryController::index');
    $routes->get('inventory/table', 'InventoryController::tableIndex');
    $routes->get('inventory/report', 'InventoryController::reports');
    $routes->post('inventory/report_result', 'InventoryController::result');
    $routes->get('inventory/details/(:num)', 'InventoryController::details/$1');
    $routes->get('inventory/availability', 'InventoryController::availability' , ['as' => 'inventory-availability']);
    $routes->get('inventory/kardex/(:num)', 'InventoryController::kardex/$1');
    $routes->get('inventory/kardexTable/(:num)', 'InventoryController::kardexTable/$1');
    $routes->get('inventory/create', 'InventoryController::create');
    $routes->get('inventory/edit/(:num)', 'InventoryController::edit/$1');
    $routes->get('inventory/create/out', 'InventoryController::out_create');
    $routes->get('inventory/out_transfer', 'InventoryController::out_transfer', ['as' => 'inventory-OutTransfer']);
    $routes->get('inventory/edit_out_transfer/(:num)', 'InventoryController::edit_out_transfer/$1', ['as' => 'inventory-EditOutTransfer']);

    $routes->get('products', 'ProductsController::index', ['as' => 'products-index']);
    $routes->get('products_create', 'ProductsController::create', ['as' => 'products-create']);
    $routes->post('products_save', 'ProductsController::store', ['as' => 'products-save']);
    $routes->get('products_edit/(:num)', 'ProductsController::edit/$1', ['as' => 'products-edit']);
    $routes->post('products_update/(:num)', 'ProductsController::update/$1', ['as' => 'products-update']);
    $routes->get('products_show/(:num)', 'ProductsController::show/$1', ['as' => 'products-show']);
    $routes->post('products_detail_create/(:num)', 'ProductsController::createDetails/$1', ['as' => 'productsDetails-create']);
    $routes->post('products_details_status', 'ProductsController::changeStatusDetail', ['as' => 'productsDetails-changeStatus']);
    $routes->post('products_jsoncode', 'ProductsController::jsonCode', ['as' => 'products-jsonCode']);
    $routes->post('products/subgroup', 'ProductsController::subGroups', ['as' => 'products-subGroups']);

    $routes->get('purchaseOrder', 'PurchaseOrderController::index', ['as' => 'purchaseOrder-index']);
    $routes->get('purchaseOrder/budget', 'PurchaseOrderController::indexBudget', ['as' => 'purchaseOrder-indexBudget']);
    $routes->get('purchaseOrder/create', 'PurchaseOrderController::create', ['as' => 'purchaseOrder-create']);
    $routes->get('purchaseOrder/edit/(:num)', 'PurchaseOrderController::edit/$1', ['as' => 'purchaseOrder-edit']);
    $routes->get('purchaseOrder/tracking/(:segment)', 'PurchaseOrderController::tracking/$1', ['as' => 'purchaseOrder-tracking']);
    $routes->get('purchaseOrder/email/(:segment)', 'PurchaseOrderController::email/$1',['as' => 'purchaseOrder-email']);
    $routes->post('purchaseOrder/create/budget', 'PurchaseOrderController::createBudget', ['as' => 'purchaseOrder-createBudget']);
    $routes->get('purchaseOrder/expiration', 'PurchaseOrderController::validateExpiration', ['as' => 'purchaseOrder-expiration']);
    $routes->get('purchaseOrder/view/(:any)', 'PurchaseOrderController::view/$1');
    $routes->get('purchaseOrder/edit/remision/(:num)', 'PurchaseOrderController::editRemision/$1');

    $routes->get('payroll_adjust/(:num)', 'PayrollAdjustController::index/$1');
    $routes->get('payroll_adjust/edit/(:num)', 'PayrollAdjustController::edit/$1');
    $routes->get('expired_subscription', 'SolicitudesController::expiredSubscription');


    $routes->group('api',  function ($routes) {
        $routes->group('v2', function ($routes) {
            $routes->resource('products',       ['controller' =>'Api\V2\Product']);
            $routes->resource('invoices',       ['controller' =>'Api\V2\Invoice']);
            $routes->resource('inventories',    ['controller' =>'Api\V2\Inventory']);
            $routes->resource('customers',    ['controller' =>'Api\Customer']);
            $routes->resource('quotation',      ['controller' =>'Api\Quotation']);
        });
    });

    $routes->group('payroll_removable', function ($routes) {
        $routes->get('/', 'PayrollRemovableController::index');
        $routes->get('worker', 'PayrollRemovableController::worker');
        $routes->get('(:num)', 'PayrollRemovableController::show/$1');
        $routes->get('(:num)/edit', 'PayrollRemovableController::edit/$1');
        $routes->get('previsualization/(:num)', 'PayrollRemovableController::previsualization/$1');
        $routes->post('consolidate', 'PayrollRemovableController::consolidate');
    });

    $routes->get('consolidate_reverse/(:num)', 'PayrollRemovableController::consolidateReverse/$1');

    //integrations
    $routes->group('integrations', function ($routes) {
        $routes->get('/', 'IntegrationsController::index');

        $routes->group('shopify', function ($routes) {

            $routes->get('/', 'Integrations\ShopifyController::index');
            $routes->post('auth', 'Integrations\ShopifyController::auth');
            $routes->get('token_access', 'Integrations\ShopifyController::token_access');
            $routes->post('save_name', 'Integrations\ShopifyController::save_name');
            $routes->post('update_register', 'Integrations\ShopifyController::updateRegister');
            $routes->get('orders', 'Integrations\ShopifyController::orders');
            $routes->get('active_job', 'Integrations\ShopifyController::activationCompanies');
            $routes->get('control_orders/(:num)', 'Integrations\ShopifyController::controlOrders/$1');
            $routes->post('credit_note', 'Integrations\ShopifyController::noteCredit');
            $routes->post('credit_note_by_product', 'Integrations\ShopifyController::noteCreditByProduct');
            $routes->post('product_for_note_credit', 'Integrations\ShopifyController::productsForNoteCredit');
            $routes->post('regenerate_order', 'Integrations\ShopifyController::regenerateOrder');
            $routes->get('report_conciliation','Integrations\ShopifyReportsController::conciliation');
            $routes->post('upload_consolidation','Integrations\ShopifyReportsController::uploadConsolidation');
            $routes->get('see_consolidation/(:num)/(:num)','Integrations\ShopifyReportsController::seeConsolidation/$1/$2');
        });
    });


    
    $routes->get('reception_email/(:num)', 'ReceptionEmailController::index/$1');
    $routes->get('/work_certificate/pdf/(:num)', 'WorkCertificateController::pdf/$1');
    $routes->get('/work_certificate', 'WorkCertificateController::index');

    $routes->put('/new_password/(:num)', 'Configuration\NewPasswordController::update/$1');
    $routes->get('new_password', 'Configuration\NewPasswordController::index');
    $routes->get('password', 'Configuration\NewPasswordController::newPassword');
    


    $routes->get('import/invoice', 'Imports\InvoiceImportController::index');
    $routes->post('import/invoice', 'Imports\InvoiceImportController::import');



    $routes->get('expired_subscription_export', 'SolicitudesController::expiredSubscriptionExtport');

    $routes->get('consolidate/invoice', 'InvoiceController::consolidation');


    $routes->get('income_withholding', 'IncomeWithholdingController::index');
    $routes->post('income_withholding/import', 'IncomeWithholdingController::importExcel');
    $routes->get('income_withholding/(:num)', 'IncomeWithholdingController::pdf/$1');

    $routes->post('import_health', 'ImportController::uploadSalud');
    $routes->get('import_health', 'ImportController::importSalud');
    $routes->get('plantillaProductos', 'ImportController::plantillaProductos',['as' => 'import.plantillaProduct']);

    // Recepcion

    $routes->get('reception_email', 'ReceptionEmailController::index');
    $routes->get('reception_email/export', 'ReceptionEmailController::export');


    // Shopping

    $routes->group('shopping', function ($routes) {
        $routes->get('/', 'Shopping\ShoppingController::index');
        $routes->get('table/(:segment)', 'Shopping\ShoppingController::tables/$1');
        $routes->get('history/(:segment)/(:segment)', 'Shopping\ShoppingController::history/$1/$2');
        $routes->post('update', 'Shopping\ShoppingController::update');
        $routes->post('file', 'Shopping\ShoppingController::file');
        $routes->get('table/history/(:segment)', 'Shopping\ShoppingController::table_history/$1');
        $routes->post('assign', 'Shopping\ShoppingController::assign');

        $routes->get('download/(:segment)/(:segment)', 'Shopping\ShoppingController::download/$1/$2');
        $routes->get('table/product/(:segment)/(:segment)', 'Shopping\ShoppingController::product/$1/$2');
    });

    $routes->group('providers', function($routes){
        $routes->get('/', 'Shopping\ProvidersController::index');
        $routes->get('table/(:segment)', 'Shopping\ProvidersController::tables/$1');
        $routes->get('table_taxes/(:segment)', 'Shopping\ProvidersController::table_taxes/$1');
        $routes->get('providers/show', 'Shopping\ProvidersController::show');

        $routes->get('contabilidad', 'ReceptionEmailController::contabilidad');
        $routes->get('finansiera', 'ReceptionEmailController::finansiera');
    });

    $routes->group('expenses', function($routes){
        $routes->get('/', 'ExpensesController::index');
        $routes->get('create', 'ExpensesController::create',['as' => 'expenses.create']);
        $routes->get('editExpenses/(:segment)', 'ExpensesController::edit/$1',['as' => 'expenses.edit']);
    });

    $routes->group('customers', function($routes){
        $routes->get('profile/(:num)', 'CustomerController::profile/$1',['as' => 'customer.profile']);
        $routes->get('employee/(:num)', 'CustomerController::employee/$1',['as' => 'customer.employee']);
        $routes->post('updatePayment/(:num)', 'CustomerController::updatePayment/$1',['as' => 'customer.updatePayment']);
        $routes->post('updateData/(:num)', 'CustomerController::updateData/$1',['as' => 'customer.updateData']);
        $routes->get('shopping/(:num)', 'CustomerController::shopping/$1');
        $routes->get('products/(:num)', 'CustomerController::products/$1');
        $routes->get('updateTypeClient', 'CustomerController::updateTypeClient');
        $routes->get('frequency', 'CustomerController::frequency');
    });

    $routes->group('reports', function($routes){
        $routes->get('customerAges', 'ReportsController::customerAges',['as' => 'reports.customerAges']);
        $routes->get('customersAges/kardex/(:num)', 'ReportsController::kardex/$1');
        $routes->get('incomeAndExpenses', 'ReportsController::incomeAndExpenses',['as' => 'reports.incomeAndExpenses']);
        $routes->get('incomeExpensesAges', 'ReportsController::ageIncomeExpenses');
        $routes->get('incomeExpensesAges/data/(:any)', 'ReportsController::dataIeA/$1');
        $routes->get('view/(:any)', 'ReportsController::view/$1');
        $routes->get('sell', 'ReportsController::sell');
        $routes->get('providersAges', 'ReportsController::providersAges',['as' => 'reports.providersAges']);
        $routes->get('providersAges/kardex/(:num)', 'ReportsController::kardexP/$1');
    });
    $routes->group('discharge', function($routes){
        $routes->get('/', 'DischargeController::index', ['as' => 'discharge.index']);
        $routes->get('show/(:segment)', 'DischargeController::show/$1');
        $routes->post('update/(:segment)/(:segment)', 'DischargeController::update/$1/$2');
        $routes->post('store/(:segment)', 'DischargeController::store/$1');
        $routes->delete('(:num)', 'DischargeController::delete/$1');
        $routes->get('download/(:segment)', 'DischargeController::download/$1');
    });
    $routes->group('providers', function($routes){
        $routes->get('profile/(:num)', 'ProvidersController::profile/$1',['as' => 'providers.profile']);
        $routes->post('updatePayment/(:num)', 'ProvidersController::updatePayment/$1',['as' => 'providers.updatePayment']);
        $routes->get('shopping/(:num)', 'ProvidersController::shopping/$1');
        $routes->get('products/(:num)', 'ProvidersController::products/$1');
    });





    /**
     * --------------------------------------------------------------------
     * Additional Routing
     * --------------------------------------------------------------------
     *
     * There will often be times that you need additional routing and you
     * need to it be able to override any defaults in this file. Environment
     * based routes is one such time. require() additional route files here
     * to make that happen.
     *
     * You will have access to the $routes object within that file without
     * needing to reload it.
     */
    if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
        require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
    }
