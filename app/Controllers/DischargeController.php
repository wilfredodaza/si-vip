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
use App\Models\Notification;
use App\Models\PaymentMethodCompany;
use App\Models\Wallet;
use App\Models\Resolution;
use App\Models\AccountingAcount;

class DischargeController extends WalletController
{

    /**
     * Método encargado de mostrar el listado de facturas
     * pendientes y pagas
     * @return string
     */
    public function index()
    {
        $this->activeUser();
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
            ->whereIn('type_documents_id', [11, 105, 106])
            ->get()
            ->getResult();
        $customer = new Customer();
        $idsValidate = [];
        $ids = [];
        $customers = $customer
            ->where(['type_customer_id' => 2])
            ->whereIn('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
            ->whereNotIn('name', ['gerente', 'Gerente'])
            ->orderBy('name', 'asc')
            ->get()
            ->getResult();
        $customerController = new CustomerController();
        $customers = $customerController->organization($customers);

        //sedes
        $companies = new Company();
        $headquarters = $companies->whereIn('id', $this->controllerHeadquarters->idsCompaniesHeadquarters())->where(['id !=' => 1])->asObject()->get()->getResult();
        //
        $invoicesPay = new Invoice();
        $paysInvoices = $this->getPaysInvoices($invoicesPay);

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

        $total->whereIn('invoices.type_documents_id', [11, 105, 106, 107, 118])
            ->where('invoices.deleted_at', null);

        $this->extracted($total);

        $total->orderBy('invoices.created_at', 'DESC')
            ->groupBy('invoices.id')
            ->asObject();

        //echo json_encode($total->get()->getResult());die();

        $model = new Invoice();
        $select = $this->dataSearch($this->manager, $this->idsCompanies);
        array_push($select, 'companies.company as nameCompany');
        $data = $model->select($select)
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('companies', 'companies.id = invoices.companies_id')
            ->whereIn('invoices.type_documents_id', [11, 105, 106, 107, 118]);
        if ($this->manager) {
            $data->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $data->where('invoices.companies_id', Auth::querys()->companies_id);
        }
        $data->where('invoices.deleted_at', null)
            ->orderBy('invoices.created_at', 'DESC');


        $this->extracted($data);
        array_push($indicadores, (object)[
            'id' => 'aprobado',
            'color' => 'green',
            'icon' => 'trending_down',
            'name' => 'Aprobado',
            'total' => 0
        ]);
        array_push($indicadores, (object)[
            'id' => 'pagado',
            'color' => 'red',
            'icon' => 'trending_down',
            'name' => 'Pagado',
            'total' => 0
        ]);
        array_push($indicadores, (object)[
            'id' => 'saldo',
            'color' => 'blue',
            'icon' => 'attach_money',
            'name' => 'saldo',
            'total' => 0
        ]);
        return view('discharge/index', [
            'resolutions' => $resolutions,
            'wallets' => $data->asObject()->paginate(10),
            'pager' => $data->pager,
            'total' => $total->get()->getResult(),
            'paymentMethod' => $paymentMethod,
            'customers' => $customers,
            'indicadores' => $indicadores,
            'headquarters' => $headquarters,
            'paysInvoices' => $paysInvoices
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
        $discharge = new  Invoice();
        $this->activeUser();
        $invoice = $discharge->select($this->dataSearch($this->manager, $this->idsCompanies))
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

        foreach ($payments as $payment) {
            if (!is_null($payment->invoices_pay)) {
                $discharge = new  Invoice();
                $iPay = $discharge->select(['resolution'])->where(['id' => $payment->invoices_pay])->asObject()->first();
                $payment->methodPay = "Pagada con Factura # {$iPay->resolution}";
            } else {
                foreach ($paymentMethod as $item) {
                    if ($item->id == $payment->payment_method_id) {
                        $payment->methodPay = $item->name;
                    }
                }
            }
        }


        // echo json_encode($creditNotes);die();
        $invoicesPay = new Invoice();
        $paysInvoices = $this->getPaysInvoices($invoicesPay);

        return view('discharge/show', [
            'invoice' => $invoice,
            'paymentMethod' => $paymentMethod,
            'payments' => $payments,
            'paysInvoices' => $paysInvoices
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
        $payment = $this->request->getPost('payment_method_id');
        $company = $model->where(['id' => Auth::querys()->companies_id])->asObject()->first();
        $validate = explode('-', $this->request->getPost('payment_method_id'));
        if (isset($validate[1])) {
            $payment = $validate[1];
        }
        $data = [
            'value' => $this->request->getPost('value'),
            'description' => $this->request->getPost('description'),
            'payment_method_id' => $payment,
            'invoices_id' => $this->request->uri->getSegment(3),
            'created_at' => date("Y-m-d H:i:s"),
            'user_id' => Auth::querys()->id
        ];
        if (isset($validate[1])) {
            $data['invoices_pay'] = $payment;
        }
        if (!empty($this->request->getPost('nameFile'))) {

            if (!is_dir(WRITEPATH . 'upload/discharge/' . $company->identification_number)) {
                mkdir(WRITEPATH . 'upload/discharge/' . $company->identification_number, 0777, true);
                chmod(WRITEPATH . 'upload/discharge/', 0777);
            }
            /* $img = $this->request->getFile('soport');
             $newName = $img->getRandomName();
             $img->move('upload/wallet', $newName);
             $data['soport'] = $newName;*/
            $file = upload('discharge/' . $company->identification_number, $this->request->getFile('soport'));
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
            if (!is_dir(WRITEPATH . 'upload/discharge/' . $company->identification_number)) {
                mkdir(WRITEPATH . 'upload/discharge/' . $company->identification_number, 0777, true);
                chmod(WRITEPATH . 'upload/discharge/' . $company->identification_number, 0777);
            }
            $file = $wallet->select(['soport'])->where(['id' => $id])->asObject()->first();

            /* $img = $this->request->getFile('soport');
             $newName = $img->getRandomName();
             $img->move('upload/wallet', $newName);
             $data['soport'] = $newName;*/
            deleteFile('discharge/' . $company->identification_number, $file->soport);
            $file = upload('discharge/' . $company->identification_number, $this->request->getFile('soport'));
            $data['soport'] = $file['new_name'];
        }


        $info = $wallet->set($data)
            ->where(['id' => $id])
            ->update();

        if ($info) {
            return redirect()->to('/discharge/show/' . $id_invoice)->with('success', 'Datos actualizados correcamente.');
        } else {
            return redirect()->to('/discharge/show/' . $id_invoice)->with('errors', 'Los datos no pudieron ser guardados');
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
        return download('discharge/' . $company->identification_number, $name);
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
                deleteFile('discharge/' . $validation[0]->identification_number, $validation[0]->soport);
            }
            $wallet = $wallet->where(['id' => $id])
                ->delete();
            echo json_encode(['status' => 200, 'data' => $wallet]);
            die();
        }
        echo json_encode(['status' => 404, 'message' => 'El pago no puede ser eliminado.']);
        die();
    }

    private function valueDischarge($id)
    {
        $wallet = new Wallet();
        $invoices = $wallet->select(['SUM(value) AS value'])->where(['invoices_pay' => $id])->get()->getResult();
        return $invoices[0]->value;

    }

    /**
     * @param Invoice $invoicesPay
     * @return array
     */
    private function getPaysInvoices(Invoice $invoicesPay): array
    {
        $invoicesPays = $invoicesPay->select(['invoices.id', 'invoices.payable_amount', 'invoices.resolution'])
            ->join('companies', 'companies.id = invoices.companies_id')
            ->whereIn('invoices.type_documents_id', [1, 108])
            ->where(['invoices.status_wallet' => 'Paga', 'payment_forms_id' => 1])
            ->whereIn('invoices.payment_methods_id', [10, 41, 45, 46, 47]);
        //->whereIn('invoices.invoice_status_id', [2, 3, 4]);
        if ($this->manager) {
            $invoicesPays->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $invoicesPays->where('invoices.companies_id', Auth::querys()->companies_id);
        }
        $invoicesPays->where('invoices.deleted_at', null);

        $paysInvoices = $invoicesPays->get()->getResult();

        foreach ($paysInvoices as $paysInvoice) {
            $valueDischarge = $this->valueDischarge($paysInvoice->id);
            $paysInvoice->payable_amount = $paysInvoice->payable_amount - $valueDischarge;
        }
        return $paysInvoices;
    }

    /**
     * Metodo encargao de validar los pagos a proveedores que ya estan vencidos
     * @throws \ReflectionException
     */
    public function validateExpiration()
    {
        $invoice = new Invoice();
        $fechaActual = date('Y-m-d');
        $datetime2 = date_create($fechaActual);
        $invoices = $invoice
            ->select('
            invoices.id,
            invoices.created_at,
            invoices.duration_measure,
            invoices.resolution
            ')
            ->where(['invoices.type_documents_id' => 107, 'status_wallet !=' => 'Paga'])->asObject()->get()->getResult();
        foreach ($invoices as $order) {
            $datetime1 = date_create(date('Y-m-d', strtotime($order->created_at)));
            $contador = date_diff($datetime1, $datetime2);
            $differenceFormat = '%a';
            $diferencia = $contador->format($differenceFormat);
            if ($order->duration_measure != 0 && $diferencia > $order->duration_measure) {
                $notificacion = new Notification();
                $data = [
                    'title' => "Pago a proveedor con # {$order->resolution} Id N° {$order->id}",
                    'body' => "No se a registrado el pago completo de a proveedor de la entrada # {$order->resolution} con id N° {$order->id}",
                    'icon' => 'receipt',
                    'color' => 'cyan',
                    'companies_id' => session('user')->companies_id,
                    'status' => 'Active',
                    'created_at' => date('Y-m-d'),
                    'view' => 'false',
                    'type_document_id' => 107,
                    'url' => "discharge?resolution{$order->resolution}"
                ];
                $notificacion->save($data);
            }
        }
    }


}