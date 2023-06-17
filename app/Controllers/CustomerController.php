<?php

namespace App\Controllers;

use App\Models\Customer;
use App\Models\CustomerWorker;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\Notification;
use App\Models\PaymentPolicies;
use App\Models\TrackingCustomer;
use App\Models\User;
use App\Models\InvoiceDocumentUpload;
use DateInterval;
use DatePeriod;
use DateTime;


class CustomerController extends BaseController
{
    public $tableCustomers;
    public $tableInvoices;
    public $tablePaymentPolicies;
    public $tableLineInvoices;
    public $controllerWallet;
    public $tableCustomerWorker;
    public $controllerTracking;

    public function __construct()
    {
        $this->tableCustomers = new Customer();
        $this->tableCustomerWorker = new CustomerWorker();
        $this->tableInvoices = new Invoice();
        $this->tableLineInvoices = new LineInvoice();
        $this->tablePaymentPolicies = new PaymentPolicies();
        $this->controllerWallet = new WalletController();
        $this->controllerTracking = new TrackingController();
    }

    public function profile($id)
    {
        //echo json_encode($id);die();
        $customer = $this->tableCustomers
            ->select([
                'customers.id',
                'customers.name',
                'customers.identification_number as identification',
                'type_document_identifications.name as type_identification',
                'customers.phone',
                'customers.email',
                'customers.address',
                'customers.quota',
                'customers.payment_policies',
                'customer_worker.surname',
                'customers.type_client_status',
                'customers.frequency'
            ])
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
            ->join('customer_worker', 'customers.id = customer_worker.customer_id', 'left')
            ->where('customers.id', $id)->asObject()->first();
        $invoices = new Invoice();
        $querys = [];
        $querysc = [];
        $lastShoppingTotal = 0;
        $lastProductsShoppingTotal = 0;
        if ($this->request->getGet('option') == 'c') {
            if ($this->request->getGet('start_date')) {
                $querys = array_merge($querys, ['invoices.created_at >=' => $this->request->getGet('start_date')]);
            }
            if ($this->request->getGet('end_date')) {
                $querys = array_merge($querys, ['invoices.created_at <=' => $this->request->getGet('end_date')]);
            }
        }
        if ($this->request->getGet('option') == 'p') {
            if ($this->request->getGet('start_date')) {
                $querysc = array_merge($querysc, ['invoices.created_at >=' => $this->request->getGet('start_date')]);
            }
            if ($this->request->getGet('end_date')) {
                $querysc = array_merge($querysc, ['invoices.created_at <=' => $this->request->getGet('end_date')]);
            }
        }
        $lastShopping = $invoices
            ->select([
                'invoices.payable_amount as total',
            ])
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id')
            ->where(['invoices.customers_id' => $id])
            ->where($querys)
            ->whereIn('invoices.type_documents_id', [1, 2, 108])
            //->whereIn('invoices.invoice_status_id', [2, 3])
            ->orderBy('invoices.created_at', 'DESC')
            ->get()->getResult();
        foreach ($lastShopping as $item) {
            $lastShoppingTotal += $item->total;
        }
        $lineInvoices = new LineInvoice();
        $productsShopping = $lineInvoices
            ->select([
                'products.code as code',
                'products.name as nameProduct',
                'products.tax_iva as reference',
                'SUM(line_invoices.quantity) as tQuantity',
                'SUM(line_invoices.line_extension_amount) as total'
            ])
            ->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', ' products.id = line_invoices.products_id')
            ->where(['invoices.customers_id' => $id])
            ->whereIn('invoices.type_documents_id', [1, 2, 108])
            ->groupBy('line_invoices.products_id')
            ->orderBy('tQuantity', 'DESC')
            ->asObject()->get(10)->getResult();
        $lineInvoices = new LineInvoice();
        $lastProductsShopping = $lineInvoices
            ->select([
                'line_invoices.line_extension_amount as total'
            ])
            ->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', ' products.id = line_invoices.products_id')
            ->where(['invoices.customers_id' => $id])
            ->where($querysc)
            ->whereIn('invoices.type_documents_id', [1, 2, 108])
            ->orderBy('line_invoices.id', 'DESC')
            ->get()->getResult();
        foreach ($lastProductsShopping as $item) {
            $lastProductsShoppingTotal += $item->total;
        }
        return view('customers/profile', [
            'customer' => $customer,
            'paymentPolicies' => $this->tablePaymentPolicies->get()->getResult(),
            'lastShopping' => $lastShoppingTotal,
            'productsShopping' => $productsShopping,
            'lastProductsShopping' => $lastProductsShoppingTotal,
            'debt' => $this->controllerWallet->totalPerson($id)
        ]);

    }

    public function products($id)
    {
        $querys = [];

        if ($this->request->getGet('option') == 'p') {
            if ($this->request->getGet('start_date')) {
                $querys = array_merge($querys, ['invoices.created_at >=' => $this->request->getGet('start_date')]);
            }
            if ($this->request->getGet('end_date')) {
                $querys = array_merge($querys, ['invoices.created_at <=' => $this->request->getGet('end_date')]);
            }
        }

        $lastProductsShopping = $this->tableLineInvoices
            ->select([
                'invoices.created_at as date',
                'products.name as nameProduct',
                'products.tax_iva as reference',
                'line_invoices.quantity as tQuantity',
                'line_invoices.line_extension_amount as total'
            ])
            ->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', ' products.id = line_invoices.products_id')
            ->where(['invoices.customers_id' => $id])
            ->where($querys)
            ->whereIn('invoices.type_documents_id', [1, 2, 108])
            ->orderBy('line_invoices.id', 'DESC')
            ->get()->getResult();
        foreach ($lastProductsShopping as $item) {
            $item->name = $item->nameProduct . '-' . $item->reference;
            $item->total = '$ ' . number_format($item->total, '2', ',', '.');
        }
        return json_encode($lastProductsShopping);
    }

    public function shopping($id)
    {
        $querys = [];
        if ($this->request->getGet('option') == 'c') {
            if ($this->request->getGet('start_date')) {
                $querys = array_merge($querys, ['invoices.created_at >=' => $this->request->getGet('start_date')]);
            }
            if ($this->request->getGet('end_date')) {
                $querys = array_merge($querys, ['invoices.created_at <=' => $this->request->getGet('end_date')]);
            }
        }
        $lastShopping = $this->tableInvoices
            ->select([
                'invoices.id as id',
                'invoices.created_at as date',
                'invoices.payable_amount as total',
                'type_documents.name as document',
            ])
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->where(['invoices.customers_id' => $id])
            ->where($querys)
            ->whereIn('invoices.type_documents_id', [1, 2, 108])
            //->whereIn('invoices.invoice_status_id', [2, 3])
            ->orderBy('invoices.created_at', 'DESC')
            ->get()->getResult();
        foreach ($lastShopping as $item) {
            $item->total = '$ ' . number_format($item->total, '2', ',', '.');
            $item->action = '<div class="btn-group" role="group">
                                                        <a href="' . base_url() . '/invoice/pdf/' . $item->id . '"
                                                           class="btn btn-small  yellow darken-1  tooltipped" data-position="top" data-tooltip="ver detalle">
                                                            <i class="material-icons">insert_drive_file</i>
                                                        </a>
                                                    </div>';
        }

        return json_encode($lastShopping);
    }

    public function updatePayment($id)
    {
        try {
            if ($this->tableCustomers->update($id,
                ['quota' => $this->request->getPost('quota'),
                    'payment_policies' => $this->request->getPost('payment_policies'),
                    'type_client_status' => $this->request->getPost('type_client_status'),
                    'name' => $this->request->getPost('name'),
                    'identification' => $this->request->getPost('identification'),
                    'address' => $this->request->getPost('address'),
                    'email' => $this->request->getPost('email'),
                    'phone' => $this->request->getPost('phone'),
                    'frequency' => $this->request->getPost('frequency'),
                ]
            )) {
                return redirect()->to(base_url('/customers/profile/' . $id))->with('success', 'Datos actualizados correctamente');
            } else {
                throw  new \Exception('Los datos no se actualizaron con exíto');
            }
        } catch (\Exception $e) {
            return redirect()->to(base_url('/customers/profile/' . $id))->with('error', $e->getMessage());
        }
    }

    public function update($id = null)
    {

        $validation = \Config\Services::validation();

        $validation->setRules([
            'name' => 'required',
            'type_document_identifications_id' => 'required',
            'identification_number' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'email' => 'required|valid_email|is_unique[customers.email,id,' . $id . ']',
            'type_regime_id' => 'required',
            'type_organization_id' => 'required',
            'municipality_id' => 'required',
        ],
            [   // Errors
                'name' => [
                    'required' => 'El campo nombre es obligatorio.',
                ],
                'type_document_identification' => [
                    'required' => 'El campo tipo de documento es obligatorio.'
                ],
                'identification_number' => [
                    'required' => 'El campo número de documento es obligatorio.'
                ],
                'phone' => [
                    'required' => 'El campo teléfono es obligatorio.'
                ],
                'address' => [
                    'required' => 'El campo dirección es obligatorio.'
                ],
                'email' => [
                    'required' => 'El campo correo electrónico es obligatorio.',
                    'valid_email' => 'El correo electrónico no es válido.',
                    'is_unique' => 'El correo electrónico ya existe.'
                ],
                'type_regime_id' => [
                    'required' => 'El campo tipo de régimen es obligatorio.'
                ],
                'type_organization_id' => [
                    'required' => 'El campo tipo de organización es obligatorio.'
                ],
                'municipality_id' => [
                    'required' => 'El campo ciudad es obligatorio.'
                ],
            ]
        );


        if (!$validation->withRequest($this->request)->run()) {
            $errors = '';
            foreach ($validation->getErrors() as $item) {
                $errors .= $item . '<br>';
            }
            return redirect()->to(base_url('/home'))->with('errors', $errors);
        }


        $customer = new Customer();
        $data = [
            'name' => $this->request->getPost('name'),
            'type_document_identification' => $this->request->getPost('type_document_identification'),
            'identification_number' => $this->request->getPost('identification_number'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'email' => $this->request->getPost('email'),
            'type_regime_id' => $this->request->getPost('type_regime_id'),
            'type_organization_id' => $this->request->getPost('type_organization_id'),
            'municipality_id' => $this->request->getPost('municipality_id'),
            'status' => 'Activo'
        ];


        $customer->update($id, $data);

        $user = new User();
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'username' => $this->request->getPost('email'),
        ];

        session('user')->email = $this->request->getPost('email');
        session('user')->username = $this->request->getPost('email');

        $user->update(session('user')->id, $data);

        return redirect()->to(base_url('/home'))->with('update', 'Datos actualizados correctamente');

    }

    public function bankCertificate($id = null)
    {
        if ($imagefile = $this->request->getFiles()) {
            $customer = new Customer();
            $data = $customer->where(['bank_certificate !=' => null, 'id' => $id])->asObject()->first($id);
            if ($data) {
                unlink(str_replace('app\\', '', APPPATH) . 'public/upload/bank_certificate/' . $data->bank_certificate);
            }
            $img = $imagefile['file'];
            $newName = $img->getRandomName();
            $img->move('upload/bank_certificate/', $newName);
            $customer = new Customer();
            $customer = $customer->update($id, ['bank_certificate' => $newName]);
        }


    }

    public function rut($id = null)
    {
        if ($imagefile = $this->request->getFiles()) {
            $customer = new Customer();
            $data = $customer->where(['rut !=' => null, 'id' => $id])->asObject()->first($id);
            if ($data) {
                unlink(str_replace('app\\', '', APPPATH) . 'public/upload/rut/' . $data->rut);
            }
            $img = $imagefile['file'];
            $newName = $img->getRandomName();
            $img->move('upload/rut', $newName);
            $customer = new Customer();
            $customer->update($id, ['rut' => $newName]);
        }
    }

    public function firm($id = null)
    {
        if ($imagefile = $this->request->getFiles()) {
            $customer = new Customer();
            $data = $customer->where(['firm !=' => null, 'id' => $id])->asObject()->first($id);
            $img = $imagefile['file'];
            $newName = $img->getRandomName();
            $img->move('upload/firm', $newName);
            $customer = new Customer();
            $customer = $customer->update($id, ['firm' => $newName]);
        }
    }

    public function attachedDocument($id = null)
    {
        if ($imagefile = $this->request->getFiles()) {
            foreach ($imagefile['file'] as $img) {
                $name = $img->getName();
                $newName = $img->getRandomName();
                $img->move('upload/attached_document', $newName);

                $invoiceDocumentUpload = new InvoiceDocumentUpload();
                $invoiceDocumentUpload->save([
                    'title' => $name,
                    'file' => $newName,
                    'invoice_id' => $id
                ]);
            }
        }
    }

    public function employee($id)
    {
        $employee = $this->tableCustomers
            ->select([
                'customers.id',
                'customers.name',
                'customers.identification_number as identification',
                'type_document_identifications.name as type_identification',
                'customers.phone',
                'customers.email',
                'customers.address',
                'customer_worker.salary',
                'customer_worker.work',
                'customer_worker.surname',
                'customer_worker.admision_date',
                'customer_worker.retirement_date',
                'customer_worker.birthday',
                'customer_worker.number_people',
                'customers.neighborhood',
                'customer_worker.withdrawal_reason'
            ])
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
            ->join('customer_worker', 'customers.id = customer_worker.customer_id', 'left')
            ->where('customers.id', $id)->asObject()->first();
        // echo json_encode($employee);die();
        return view('customers/employee', [
            'employee' => $employee
        ]);
    }

    public function updateData($id): \CodeIgniter\HTTP\RedirectResponse
    {
        try {
            // echo json_encode($this->request->getPost('retirement_date'));die();
            $customer = [
                'neighborhood' => ($this->request->getPost('neighborhood') !== null) ? $this->request->getPost('neighborhood') : null,
                'address' => ($this->request->getPost('address') !== null) ? $this->request->getPost('address') : null,
                'phone' => ($this->request->getPost('phone') !== null) ? $this->request->getPost('phone') : null,
                'email' => ($this->request->getPost('email') !== null) ? $this->request->getPost('email') : null,
            ];
            $customerWorker = [
                'birthday' => ($this->request->getPost('birthday') !== null) ? $this->request->getPost('birthday') : null,
                'number_people' => ($this->request->getPost('number_people') !== null) ? $this->request->getPost('number_people') : null,
                'retirement_date' => ($this->request->getPost('retirement_date') !== null) ? $this->request->getPost('retirement_date') : null,
                'admision_date' => ($this->request->getPost('admision_date') !== null) ? $this->request->getPost('admision_date') : null,
                'salary' => ($this->request->getPost('salary') !== null) ? $this->request->getPost('salary') : null,
                'work' => ($this->request->getPost('work') !== null) ? $this->request->getPost('work') : null
            ];
            if (!is_null($this->request->getPost('retirement_date'))) {
                $customerWorker['withdrawal_reason'] = ($this->request->getPost('withdrawal_reason') !== null) ? $this->request->getPost('withdrawal_reason') : 'Sin Motivo de retiro';
                $customer['status'] = 'Inactivo';
            }
            if ($this->tableCustomers->update($id, $customer)) {
                if ($this->tableCustomerWorker->set($customerWorker)->where('customer_id', $id)->update()) {
                    return redirect()->to(base_url('/customers/employee/' . $id))->with('success', 'Datos actualizados correctamente');
                } else {
                    throw  new \Exception('Los datos no se actualizaron con exíto en detalles del empleado');
                }
            } else {
                throw  new \Exception('Los datos no se actualizaron con exíto');
            }
        } catch (\Exception $e) {
            return redirect()->to(base_url('/customers/employee/' . $id))->with('error', $e->getMessage());
        }
    }

    public function updateTypeClient()
    {
        $currentDay = date('Y/m/d', strtotime(date('Y/m/d') . "- 0 month"));
        $finalDate = date('Y/m/d', strtotime(date('Y/m/d') . "- 1 month"));
        $customers = $this->tableCustomers->whereIn('type_customer_id', [1, 2])->asObject()->get()->getResult();
        foreach ($customers as $customer) {
            $query = ['invoices.customers_id' => $customer->id, 'invoices.created_at >=' => $finalDate . ' 00:00:00', 'invoices.created_at <=' => $currentDay . ' 23:59:59'];
            $total = $this->tableInvoices
                ->select([
                    'SUM(invoices.payable_amount) as total',
                ])
                ->whereIn('invoices.type_documents_id ', [1, 2, 108])
                ->where($query)
                ->asObject()
                ->get()->getResult()[0];
            if ($total->total >= 10000000) {
                $typeClient = '01';
            } elseif ($total->total >= 5000000) {
                $typeClient = '02';
            } else {
                $typeClient = '03';
            }
            //echo json_encode($total->total.' '.$typeClient);die();
            $this->tableCustomers->update($customer->id, ['type_client_status' => $typeClient]);
        }
    }

    public function frequency()
    {
        $frequency = 'Sin frecuencia';
        $finalDate = date('Y-m-d', strtotime(date('Y-m-d') . "- 1 month"));
        $mes = date('m', strtotime($finalDate));
        $ano = date('Y', strtotime($finalDate)); // Año correspondiente al mes
        $fecha_inicio = new DateTime("$ano-$mes-01"); // Fecha de inicio del mes
        $fecha_fin = new DateTime("$ano-$mes-01 +1 month"); // Fecha de fin del mes
        $customers = $this->tableCustomers->whereIn('type_customer_id', [1, 2])->asObject()->get()->getResult();
        foreach ($customers as $customer) {
            $dates = $this->buysMonth($customer->id, $fecha_inicio->format('Y-m-d'), $fecha_fin->format('Y-m-d'));
            $semanas = $this->weeks($fecha_inicio, $fecha_fin);
            if (count($dates) > 0) {
                $i = 0;
                foreach ($semanas as $item) { // 5
                    $l = 0;
                    foreach ($item as $day) { // 7
                        foreach ($dates as $date) { // fecha de compra por cliente
                            if ($day['date'] == date('d-m-Y', strtotime($date->date))) {
                                $semanas[$i][$l]['sell'] = 1;
                            }
                        }
                        $l++;
                    }
                    $i++;
                }
                $compras = [];
                $quincenas1 = 0;
                $quincenas2 = 0;
                foreach ($semanas as $item) {
                    $cps = 0;
                    foreach ($item as $days) {
                        $cps = $cps + $days['sell'];
                        if (date('d', strtotime($days['date'])) <= 15) {
                            $quincenas1 = $quincenas1 + $days['sell'];
                        } else {
                            $quincenas2 = $quincenas2 + $days['sell'];
                        }
                    }
                    array_push($compras, ['cd' => count($item), 'cc' => $cps]);
                }
                echo json_encode($compras);
                $validateWeek = true;
                $validateDays = true;
                foreach ($compras as $compra) {
                    if ($compra['cd'] != $compra['cc']) {
                        if ($compra['cc'] == 0) {
                            $validateWeek = false;
                        }
                        $validateDays = false;
                    }
                }
                if ($validateDays && $validateWeek) {
                    $frequency = 'Diario';
                } elseif ($validateWeek && !$validateDays) {
                    $frequency = 'Semanal';
                } elseif ($quincenas1 > 0 && $quincenas2 > 0 && !$validateDays && !$validateWeek) {
                    $frequency = 'Quincenal';
                } else {
                    $frequency = 'Mensual';
                }
                if ($customer->frequency != $frequency) {
                    $this->notification(['id' => $customer->id, 'name' => $customer->name, 'fa' => $customer->frequency, 'fn' => $frequency]);
                    $this->tableCustomers->update($customer->id, ['frequency' => $frequency]);
                }
            }
        }

    }

    private function buysMonth($idCustomers, $start, $end): array
    {
        $query = ['invoices.customers_id' => $idCustomers, 'invoices.created_at >=' => $start . ' 00:00:00', 'invoices.created_at <=' => $end . ' 23:59:59'];
        return $this->tableInvoices
            ->select([
                'invoices.created_at as date',
            ])
            ->whereIn('invoices.type_documents_id ', [1, 2, 108])
            ->where($query)
            ->asObject()
            ->get()->getResult();
    }

    private function weeks($fecha_inicio, $fecha_fin): array
    {
        $fecha_actual = clone $fecha_inicio; // Copia de la fecha de inicio para iterar sobre ella

        $dias = array(); // Array vacío para almacenar los días

        while ($fecha_actual <= $fecha_fin) {
            $dia_semana = $fecha_actual->format('N'); // Número del día de la semana (1=lunes, 7=domingo)
            $dia_mes = $fecha_actual->format('j'); // Número del día del mes
            $fecha_formato = $fecha_actual->format('d-m-Y'); // Fecha en formato "d/m/Y"

            // Agregar la fecha al array correspondiente a la semana actual
            $semana_actual = count($dias) - 1;
            $dias[$semana_actual][$dia_semana] = $fecha_formato;

            // Si el día actual es domingo, agregar un nuevo array para la siguiente semana
            if ($dia_semana == 7) {
                $dias[] = array();
            }

            // Avanzar un día en la fecha actual
            $fecha_actual->add(new DateInterval('P1D'));
        }

        // Eliminar la última semana si está vacía
        if (empty($dias[count($dias) - 1])) {
            array_pop($dias);
        }
        $semana = 1;
        $semanas = [];
        $organizarSemana = [];
        // echo json_encode($dias);die();
        foreach ($dias as $dia) {
            $data = '';
            if (isset($dia[1])) {
                $data = $dia[1];
            } elseif (isset($dia[2])) {
                $data = $dia[2];
            } elseif (isset($dia[3])) {
                $data = $dia[3];
            } elseif (isset($dia[4])) {
                $data = $dia[4];
            } elseif (isset($dia[5])) {
                $data = $dia[5];
            } elseif (isset($dia[6])) {
                $data = $dia[6];
            } elseif (isset($dia[7])) {
                $data = $dia[7];
            }
            if (empty($dia)) {
                $semana++;
                array_push($semanas, $organizarSemana);
                $organizarSemana = [];
                continue;
            }
            array_push($organizarSemana, ['date' => $data, 'sell' => 0]);
        }
        array_push($semanas, $organizarSemana);
        return $semanas;
    }

    private function notification($customer)
    {
        $typeTrackingData = $this->controllerTracking->_typeTracking('customer', $customer);
        $data = [
            'message' => $typeTrackingData['body'],
            'username' => session('user')->username,
            'created_at' => date('Y-m-d H:i:s'),
            'table_id' => $customer['id'],
            'companies_id' => session('user')->companies_id,
            'type_tracking_id' => $typeTrackingData['id'],
        ];

        $tracking = new TrackingCustomer();
        $tracking->save($data);

        $notificacion = new Notification();
        $data = [
            'title' => 'Seguimiento ' . $typeTrackingData['title'] . ' cliente N° ' . $customer['id'],
            'body' => $typeTrackingData['body'],
            'icon' => 'receipt',
            'color' => 'cyan',
            'companies_id' => session('user')->companies_id,
            'status' => 'Active',
            'created_at' => date('Y-m-d'),
            'view' => 'false',
            'type_document_id' => null,
            'url' => $typeTrackingData['url']
        ];

        $notificacion->save($data);

    }

    public function organization($customers){
        $ids = [];
        $idsValidate = [];
        foreach ($customers as $key => $customer) {
            if(!is_null($customer->headquarters_id)) {
                if (in_array($customer->headquarters_id, $idsValidate)) {
                    array_push($ids, $key);
                }else{
                    array_push($idsValidate, $customer->headquarters_id);
                }
            }
        }
        //echo json_encode();die();
        foreach($ids as $key => $id){
            unset($customers[$id]);
        }
        return array_values($customers);
    }
}