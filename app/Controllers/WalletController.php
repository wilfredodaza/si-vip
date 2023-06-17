<?php

/**
 * @author wilson andres bachiller Oritz <wilson@mawii.com.co>
 * Clase encarga de gestionar los pagos de la factura.
 */

namespace App\Controllers;


use App\Controllers\Api\Auth;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\PaymentMethodCompany;
use App\Models\Wallet;
use App\Models\Resolution;
use App\Models\AccountingAcount;

class WalletController extends BaseController
{
    public $controllerHeadquarters;
    public $manager;
    public $idsCompanies;

    public function __construct()
    {
        $this->controllerHeadquarters = new HeadquartersController();
    }

    /**
     * Método encargado de mostrar el listado de facturas
     * pendientes y pagas
     * @return string
     */
    public function index()
    {
        $this->activeUser();
        $whereInType = session('module') == 29 ? [108] : [1, 2, 5];
        $whereInTypeStatus = session('module') == 29 ? [1, 2] : [2, 3, 4];
        $indicadores = [];
        $paymentMethodCompanys = new AccountingAcount();
        $paymentMethod = $paymentMethodCompanys
            ->whereIn('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
            ->where(['type_accounting_account_id' => 5])
            ->asObject()
            ->get()
            ->getResult();

        $model = new Resolution();
        $resolutions = $model
            ->whereIn('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
            ->where(['type_documents_id' => 1])
            ->get()
            ->getResult();
        $customer = new Customer();

            $customers = $customer
                ->whereIn('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
                ->where(['type_customer_id' => 1])
                ->orderBy('name', 'asc')
                ->get()
                ->getResult();

        //sedes
        $companies = new Company();
        $headquarters = $companies->whereIn('id',$this->controllerHeadquarters->idsCompaniesHeadquarters())->where(['id !=' => 1])->asObject()->get()->getResult();


        $wallet = new Invoice();
        $total = $wallet->select([
            'SUM(invoices.payable_amount) as payable_amount',
            '(SELECT  IFNULL(SUM(value), 0) FROM wallet WHERE wallet.invoices_id = invoices.id  GROUP  BY wallet.invoices_id) as balance',
            '(SELECT IFNULL(SUM(tax_amount), 0) FROM line_invoices INNER JOIN line_invoice_taxs ON line_invoice_taxs.line_invoices_id  =  line_invoices.id WHERE line_invoices.invoices_id = invoices.id AND line_invoice_taxs.taxes_id IN (5,6,7) GROUP BY line_invoices.invoices_id) AS withholdings'
        ]);
        if ($this->manager) {
            $total->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $total->where('invoices.companies_id', Auth::querys()->companies_id);
        }

        $total->whereIn('invoices.type_documents_id', $whereInType)
            ->whereIn('invoices.invoice_status_id', $whereInTypeStatus)
            ->where('invoices.deleted_at', null);

        $this->extracted($total);

        $total->orderBy('invoices.id', 'DESC')
            ->groupBy('invoices.id')
            ->asObject();

        //indicadores
        array_push($indicadores, (object)[
            'id' => 'adeudado',
            'color' => 'red',
            'icon' => 'trending_down',
            'name' => 'Adeudado',
            'total' => 0
        ]);
        array_push($indicadores, (object)[
            'id' => 'recaudado',
            'color' => 'green',
            'icon' => 'verified_user',
            'name' => 'Recaudado',
            'total' => 0
        ]);

        $model = new Invoice();
        $select = $this->dataSearch($this->manager, $this->idsCompanies);
        array_push($select, 'companies.company as nameCompany');
        $data = $model->select($select)
            ->join('customers', 'customers.id = invoices.customers_id', 'left')
            ->join('companies', 'companies.id = invoices.companies_id', 'left')
            ->whereIn('invoices.type_documents_id', $whereInType)
            ->whereIn('invoices.invoice_status_id', $whereInTypeStatus);
        if ($this->manager) {
            $data->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $data->where('invoices.companies_id', Auth::querys()->companies_id);
        }
        $data->where('invoices.deleted_at', null)
            ->orderBy('invoices.created_at', 'DESC');


        $this->extracted($data);
        //echo json_encode($data->get()->getResult());die();
        return view('wallet/index', [
            'resolutions' => $resolutions,
            'wallets' => $data->asObject()->paginate(10),
            'pager' => $data->pager,
            'total' => $total->get()->getResult(),
            'paymentMethod' => $paymentMethod,
            'customers' => $customers,
            'headquarters' => $headquarters,
            'indicadores' => $indicadores
        ]);
    }


    /**
     * Método encargado de mostrar la vista de los
     * pagos realizados en la factura.
     * @param int $id id de la factura o ducmento electronico
     * @return string
     */
    public function show($id)
    {
        $wallet = new  Invoice();
        $this->activeUser();
        $invoice = $wallet->select($this->dataSearch($this->manager, $this->idsCompanies))
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('wallet', 'wallet.invoices_id = invoices.id', 'left')
            ->where([
                'invoices.id' => $id,
                'invoices.deleted_at' => null
            ])
            ->asObject()
            ->first();


        $paymentMethodCompanys = new AccountingAcount();
        $paymentMethod = $paymentMethodCompanys->asObject()
            ->whereIn('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
            ->where([
                'type_accounting_account_id' => 5
            ])
            ->get()
            ->getResult();

        $model = new Wallet();
        $payments = $model->where([
            'invoices_id' => $invoice->id,
            'wallet.deleted_at' => null
        ])
            ->get()
            ->getResult();

        $model = new Invoice();
        $creditNotes = $model->select([
            'invoices.id',
            'invoices.resolution',
            'invoices.prefix',
            'invoices.created_at',
            'invoices.payable_amount',
            '(SELECT IFNULL(SUM(line_invoice_taxs.tax_amount), 0) FROM line_invoices 
            INNER JOIN line_invoice_taxs 
            ON line_invoice_taxs.line_invoices_id =  line_invoices.id
            WHERE line_invoices.invoices_id = invoices.id AND line_invoice_taxs.taxes_id IN (5,6,7) AND invoices.deleted_at IS NOT NULL) as credit_note_withholdings',
        ])
            ->where([
                'invoices.resolution_credit' => $invoice->resolution,
                'invoices.type_documents_id' => 4,
                'invoices.deleted_at !=' => null
            ])
            ->get()
            ->getResult();

        // echo json_encode($creditNotes);die();

        return view('wallet/show', [
            'invoice' => $invoice,
            'paymentMethod' => $paymentMethod,
            'payments' => $payments,
            'creditNotes' => $creditNotes
        ]);
    }

    /**
     * Método encargado de realizar el registro de los pagos
     * de cartera
     * @param $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     * @throws \ReflectionException
     */
    public function store($id)
    {

        $model = new Company();
        $company = $model->where(['id' => Auth::querys()->companies_id])->asObject()->first();
        $data = [
            'value' => $this->request->getPost('value'),
            'description' => $this->request->getPost('description'),
            'payment_method_id' => $this->request->getPost('payment_method_id'),
            'invoices_id' => $this->request->uri->getSegment(3),
            'created_at' => date("Y-m-d H:i:s"),
            'user_id' => Auth::querys()->id
        ];
        if (!empty($this->request->getPost('nameFile'))) {

            if (!is_dir(WRITEPATH . 'upload/wallets/' . $company->identification_number)) {
                mkdir(WRITEPATH . 'upload/wallets/' . $company->identification_number, 0777, true);
                chmod(WRITEPATH . 'upload/wallets/', 0777);
            }
            /* $img = $this->request->getFile('soport');
             $newName = $img->getRandomName();
             $img->move('upload/wallet', $newName);
             $data['soport'] = $newName;*/
            $file = upload('wallets/' . $company->identification_number, $this->request->getFile('soport'));
            $data['soport'] = $file['new_name'];
        }

        $wallet = new Invoice();
        $values = $wallet->join('wallet', 'wallet.invoices_id = invoices.id', 'left')
            ->where(['invoices.id' => $data['invoices_id']])
            ->get()
            ->getResult();

        $wallet = new Wallet();
        $info = $wallet->save($data);

        if ($info) {
            return redirect()->back()->with('success', 'Datos actualizados correcamente.');
        } else {
            return redirect()->back()->with('errors', 'Los datos no pudieron ser guardados');
        }
    }

    /**
     * Método encargado de actualizar los pagos en
     * cartera
     * @param $id id del pago en cartera
     * @param $id_invoice id de la factura a quien se le realizo el pago
     * @return \CodeIgniter\HTTP\RedirectResponse
     * @throws \ReflectionException
     */

    public function update($id, $id_invoice)
    {
        $wallet = new Wallet();
        $data = [
            'value' => $this->request->getPost('value'),
            'description' => $this->request->getPost('description'),
            'payment_method_id' => $this->request->getPost('payment_method_id'),
            'created_at' => date("Y-m-d H:i:s")
        ];

        $model = new Company();
        $company = $model->where(['id' => Auth::querys()->companies_id])
            ->asObject()
            ->first();

        if (!empty($this->request->getPost('nameFile'))) {
            if (!is_dir(WRITEPATH . 'upload/wallets/' . $company->identification_number)) {
                mkdir(WRITEPATH . 'upload/wallets/' . $company->identification_number, 0777, true);
                chmod(WRITEPATH . 'upload/wallets/' . $company->identification_number, 0777);
            }
            $file = $wallet->select(['soport'])->where(['id' => $id])->asObject()->first();

            /* $img = $this->request->getFile('soport');
             $newName = $img->getRandomName();
             $img->move('upload/wallet', $newName);
             $data['soport'] = $newName;*/
            deleteFile('wallets/' . $company->identification_number, $file->soport);
            $file = upload('wallets/' . $company->identification_number, $this->request->getFile('soport'));
            $data['soport'] = $file['new_name'];
        }


        $info = $wallet->set($data)
            ->where(['id' => $id])
            ->update();

        if ($info) {
            return redirect()->to('/wallet/show/' . $id_invoice)->with('success', 'Datos actualizados correcamente.');
        } else {
            return redirect()->to('/wallet/show/' . $id_invoice)->with('errors', 'Los datos no pudieron ser guardados');
        }

    }

    /**
     * Método encargado de descargar documento de pago
     * @param $name nombre del archivo a descargar
     * @return false|string
     * @throws \Exception
     */

    public function download($name)
    {
        $model = new Company();
        $company = $model->where(['id' => Auth::querys()->companies_id])->asObject()->first();
        return download('wallets/' . $company->identification_number, $name);
    }


    public function delete($id)
    {
        $wallet = new Wallet();
        $validation = $wallet
            ->join('invoices', 'invoices.id = wallet.invoices_id')
            ->join('companies', 'companies.id = invoices.companies_id')
            ->where(['wallet.id' => $id, 'invoices.companies_id' => Auth::querys()->companies_id])
            ->asObject()
            ->get()
            ->getResult();

        if (count($validation) > 0) {
            if (!is_null($validation[0]->soport)) {
                deleteFile('wallets/' . $validation[0]->identification_number, $validation[0]->soport);
            }
            $wallet = $wallet->where(['id' => $id])
                ->delete();
            echo json_encode(['status' => 200, 'data' => $wallet]);
            die();
        }
        echo json_encode(['status' => 404, 'message' => 'El pago no puede ser eliminado.']);
        die();
    }

    /**
     * @param Invoice $data
     */
    protected function extracted(Invoice $data): void
    {
        $this->request->getGet('start_date') ? $data->where('invoices.created_at >=', $this->request->getGet('start_date') . ' 00:00:00') : '';
        $this->request->getGet('end_date') ? $data->where('invoices.created_at <=', $this->request->getGet('end_date') . ' 00:00:00') : '';
        $this->request->getGet('resolution') ? $data->where('invoices.resolution', $this->request->getGet('resolution')) : '';
        $this->request->getGet('customers_id') ? $data->where('invoices.customers_id', $this->request->getGet('customers_id')) : '';
        $this->request->getGet('status') && $this->request->getGet('status') != 'Todos' ? $data->where('invoices.status_wallet', $this->request->getGet('status')) : ($this->request->getGet('status') == 'Todos' ? '' : $data->where('invoices.status_wallet', 'Pendiente'));
        $this->request->getGet('headquarters') ? $data->where('invoices.companies_id', $this->request->getGet('headquarters')) : '';
    }

    /**
     * @param $manager
     * @param $idsCompanies
     * @return array
     */
    public function dataSearch($manager, $idsCompanies): array
    {
        $dataSearch = [
            'invoices.companies_id',
            'invoices.payment_methods_id',
            'invoices.resolution',
            'invoices.created_at',
            'invoices.payable_amount',
            'invoices.status_wallet',
            'invoices.id',
            'customers.name',
            'invoices.type_documents_id',
            '(SELECT  IFNULL(SUM(value), 0) FROM wallet WHERE wallet.invoices_id = invoices.id AND wallet.deleted_at IS  NULL   GROUP  BY wallet.invoices_id) as balance',
            '(SELECT IFNULL(SUM(tax_amount), 0) FROM line_invoices INNER JOIN line_invoice_taxs ON line_invoice_taxs.line_invoices_id  =  line_invoices.id WHERE line_invoices.invoices_id = invoices.id AND line_invoice_taxs.taxes_id IN (5,6,7)  AND invoices.deleted_at IS  NULL GROUP BY line_invoices.invoices_id) AS withholdings'
        ];
        if ($manager) {
            $dataNew = [
                '(SELECT IFNULL(SUM(line_invoice_taxs.tax_amount), 0) FROM invoices as inv2 
                INNER JOIN line_invoices ON line_invoices.invoices_id = inv2.id 
                INNER JOIN line_invoice_taxs ON line_invoice_taxs.line_invoices_id =  line_invoices.id
                WHERE inv2.type_documents_id = 4 
                AND inv2.companies_id IN (' . $idsCompanies . ')
                AND  inv2.resolution_credit =  invoices.resolution
                AND line_invoice_taxs.taxes_id IN (5,6,7) ) as credit_note_withholdings',
                '(SELECT IFNULL(SUM(inv2.payable_amount), 0) FROM invoices as inv2 WHERE inv2.companies_id IN(' . $idsCompanies . ') AND inv2.type_documents_id = 4 AND  inv2.resolution_credit =  invoices.resolution AND inv2.deleted_at IS  NULL LIMIT 1 ) as credit_note',

            ];
        } else {
            $dataNew = ['(SELECT IFNULL(SUM(line_invoice_taxs.tax_amount), 0) FROM invoices as inv2 
                INNER JOIN line_invoices ON line_invoices.invoices_id = inv2.id 
                INNER JOIN line_invoice_taxs ON line_invoice_taxs.line_invoices_id =  line_invoices.id
                WHERE inv2.type_documents_id = 4 
                AND  inv2.resolution_credit =  invoices.resolution
                AND inv2.companies_id = ' . $idsCompanies . ' 
                AND line_invoice_taxs.taxes_id IN (5,6,7) ) as credit_note_withholdings',
                '(SELECT IFNULL(SUM(inv2.payable_amount), 0) FROM invoices as inv2 WHERE inv2.type_documents_id = 4 AND  inv2.resolution_credit =  invoices.resolution and inv2.companies_id = ' . $idsCompanies . ' AND inv2.deleted_at IS  NULL ) as credit_note',
            ];
        }

        return array_merge($dataSearch, $dataNew);
    }

    protected function activeUser()
    {
        $this->manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
        $this->idsCompanies = $this->controllerHeadquarters->idsCompaniesText();
        if (!$this->manager) {
            $this->idsCompanies = Auth::querys()->companies_id;
        }
    }

    public function totalPerson($id): int
    {
        $wallet = new Invoice();
        $balance = 0;
        $total = $wallet->select([
            'SUM(invoices.payable_amount) as payable_amount',
            '(SELECT  IFNULL(SUM(value), 0) FROM wallet WHERE wallet.invoices_id = invoices.id  GROUP  BY wallet.invoices_id) as balance',
            '(SELECT IFNULL(SUM(tax_amount), 0) FROM line_invoices INNER JOIN line_invoice_taxs ON line_invoice_taxs.line_invoices_id  =  line_invoices.id WHERE line_invoices.invoices_id = invoices.id AND line_invoice_taxs.taxes_id IN (5,6,7) GROUP BY line_invoices.invoices_id) AS withholdings'
        ])->whereIn('invoices.type_documents_id', [1, 2, 5])
            ->whereIn('invoices.invoice_status_id', [2, 3, 4])
            ->where(['invoices.deleted_at' => null, 'invoices.customers_id ' => $id, 'invoices.payment_forms_id' => 2])
            ->orderBy('invoices.id', 'DESC')
            ->groupBy('invoices.id')
            ->asObject()->get()->getResult();
        foreach($total as $item){
            $balance += $item->payable_amount - ($item->withholdings + $item->balance);
        }
        return $balance;
    }

}