<?php

namespace App\Controllers\Api;


use App\Models\GraphicComponentType;
use App\Models\Company;
use App\Models\GraphicEmail;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\Wallet;
use CodeIgniter\RESTful\ResourceController;

class Graphic extends ResourceController
{

    protected $format = 'json';


    public function salesOfMonth($id = null)
    {
        $invoice = new Invoice();
        $invoices = $invoice->select([
            'MONTH(invoices.created_at) as mes',
            'sum(invoices.line_extesion_amount) as totals'
        ])
            ->where([
                'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >=' => date('Y') . '-01-01',
                'invoices.created_at <=' => date('Y') . '-12-31',
                'invoices.invoice_status_id >' => 1
            ])
            ->groupBy(['mes'])
            ->asObject()
            ->get()
            ->getResult();

        $values = [];
        $months2 = [];
        $months = [
            'Ene-' . date('y'),
            'Feb-' . date('y'),
            'Mar-' . date('y'),
            'Abr-' . date('y'),
            'May-' . date('y'),
            'Jun-' . date('y'),
            'Jul-' . date('y'),
            'Ago-' . date('y'),
            'Sep-' . date('y'),
            'Oct-' . date('y'),
            'Nov-' . date('y'),
            'Dic-' . date('y')
        ];
        foreach ($invoices as $item) {
            array_push($months2, $item->mes);
        }

        for ($i = 0; $i < 12; $i++) {
            if (in_array($i + 1, $months2)) {
                array_push($values, (int)$invoices[array_search($i + 1, $months2)]->totals);
            } else {
                array_push($values, 0);
            }
        }

        return $this->respond([
            'status' => 200,
            'data' => ['values' => $values, 'labels' => $months]
        ]);
    }

    public function salesOfProduct($id = null)
    {
        $invoice = new Invoice();
        $invoices = $invoice->select([
            'products.name',
            'count(line_invoices.products_id) as count_product',
        ])
            ->join('line_invoices', 'line_invoices.invoices_id = invoices.id')
            ->join('products', 'products.id = line_invoices.products_id')
            ->where([
                'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >=' => '2020-01-01', //date('Y') . '-01-01',
                'invoices.created_at <=' => date('Y') . '-12-31'
            ])
            ->groupBy(['line_invoices.products_id'])
            ->limit(5)
            ->asObject()
            ->get()
            ->getResult();


        $total = 0;
        foreach ($invoices as $item) {
            $total += $item->count_product;
        }

        $labels = [];
        $values = [];
        foreach ($invoices as $item) {
            array_push($values, round($item->count_product * 100 / $total));
            array_push($labels, $item->name);
        }

        return $this->respond(['status' => 200, 'data' => [
            'labels' => $labels,
            'values' => $values
        ]]);
    }

    public function salesOfProductTwelve($id = null)
    {

        $invoice = new Invoice();
        $invoices = $invoice->select([
            'products.name',
            'sum(line_invoices.line_extension_amount) as total',
        ])
            ->join('line_invoices', 'line_invoices.invoices_id = invoices.id')
            ->join('products', 'products.id = line_invoices.products_id')
            ->where([
                'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >=' => date('Y') . '-01-01',
                'invoices.created_at <=' => date('Y') . '-12-31'
            ])
            ->groupBy(['line_invoices.products_id'])
            ->asObject()
            ->get()
            ->getResult();

        $labels = [];
        $values = [];
        foreach ($invoices as $invoice) {
            array_push($labels, $invoice->name);
            array_push($values, (int)$invoice->total);
        }

        return $this->respond(['status' => 200, 'data' => ['labels' => $labels, 'values' => $values]]);
    }

    public function salesOfCustomer($id = null)
    {
        $invoice = new Invoice();
        $invoices = $invoice->select('MONTH(invoices.created_at) as mes, sum(invoices.line_extesion_amount) as totals, customers.name, customers.id ')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->where([
                'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >=' => '2020-01-01', //date('Y') . '-01-01',
                'invoices.created_at <=' => date('Y') . '-12-31',
                'invoices.invoice_status_id >' => 1
            ])
            ->groupBy(['month(invoices.created_at), invoices.customers_id'])
            ->asObject()
            ->get()
            ->getResult();


        $data = [];
        $customers = [];
        $i = 0;
        foreach ($invoices as $item) {
            if (!in_array($item->id, $customers)) {
                array_push($customers, $item->id);
                $productsData[$item->id] = ['name' => $item->name];
            }
            $i++;
        }
        for ($i = 0; $i < 12; $i++) {
            $data[$i] = [];
            foreach ($customers as $customer) {
                $items = [];
                foreach ($invoices as $item) {


                    if ($item->mes == $i + 1 && $item->id == $customer) {

                        $items = [
                            '_id' => $item->id,
                            'name' => $item->name,
                            'totals' => $item->totals,
                            'mes' => $item->mes
                        ];
                        break;


                    } else {

                        if ($item->id == $customer) {
                            $items = [
                                '_id' => $customer,
                                'name' => $productsData[$customer]['name'],
                                'totals' => 0,
                                'mes' => $i + 1
                            ];
                        }
                    }
                }
                array_push($data[$i], $items);


            }
        }


        return $this->respond(['status' => 200, 'data' => $data]);
    }

    public function salesOfCustomerMonth($id = null)
    {


        $invoice = new Invoice();
        $invoices = $invoice->select('sum(invoices.line_extesion_amount) as totals, customers.name ')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->where([
                'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >=' => date('Y-m') . '-01',
                'invoices.created_at <=' => date('Y-m') . '-31',
                'invoices.invoice_status_id >' => 1
            ])
            ->groupBy(['invoices.customers_id'])
            ->asObject()
            ->get()
            ->getResult();

        $labels = [];
        $values = [];
        $total =0;
        foreach ($invoices as $invoice) {
            $total += (int)$invoice->totals;
            array_push($labels, $invoice->name);

        }


        foreach ($invoices as $invoice) {
            array_push($values, round((int)$invoice->totals * 100 / $total));
        }

        return $this->respond([
            'status' => 200,
            'data' => [
                'labels' => $labels,
                'values' => $values
            ]]);
    }


    public function salesOfCustomerMonthPrevius($id = null)
    {
        $currentDate = date("Y-m-d");


        $invoice = new Invoice();
        $invoices = $invoice->select('sum(invoices.line_extesion_amount) as totals, customers.name ')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->where([
                'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >=' => date("Y-m-01", strtotime($currentDate . "- 1 month")),
                'invoices.created_at <=' => date("Y-m-31", strtotime($currentDate . "- 1 month")),
                'invoices.invoice_status_id >' => 1
            ])
            ->groupBy(['invoices.customers_id'])
            ->asObject()
            ->get()
            ->getResult();

        $labels = [];
        $values = [];
        $total = 0;
        foreach ($invoices as $invoice) {
            array_push($labels, $invoice->name);
            $total += (int)$invoice->totals;
        }



        foreach ($invoices as $invoice) {
            array_push($values, round((int)$invoice->totals * 100 / $total));
        }


        return $this->respond([
            'status' => 200,
            'data' => [
                'labels' => $labels,
                'values' => $values
            ]]);
    }


    public function salesOfCustomerMonthAccumulated($id = null)
    {
        $invoice = new Invoice();
        $invoices = $invoice->select('sum(invoices.line_extesion_amount) as totals, customers.name ')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->where([
                'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >=' => date("Y-01-01"),
                'invoices.created_at <=' => date("Y-12-31"),
                'invoices.invoice_status_id >' => 1
            ])
            ->groupBy(['invoices.customers_id'])
            ->asObject()
            ->get()
            ->getResult();

        $labels = [];
        $values = [];
        $total = 0;
        foreach ($invoices as $invoice) {
            array_push($labels, $invoice->name);
            $total += (int)$invoice->totals;
        }


        foreach ($invoices as $invoice) {
            array_push($values, round((int)$invoice->totals * 100 / $total));
        }


        return $this->respond([
            'status' => 200,
            'data' => [
                'labels' => $labels,
                'values' => $values
            ]]);
    }


    public function salesOfSeller($id = null)
    {
        $invoice = new Invoice();
        $invoices = $invoice->select('MONTH(invoices.created_at) as mes, sum(invoices.line_extesion_amount) as totals, users.name, users.id ')
            ->join('users', 'users.id = invoices.seller_id')
            ->where([
                'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >=' => '2020-01-01', //date('Y') . '-01-01',
                'invoices.created_at <=' => date('Y') . '-12-31',
                'invoices.seller_id !=' => NULL
            ])
            ->groupBy(['month(invoices.created_at), invoices.seller_id'])
            ->asObject()
            ->get()
            ->getResult();


        $data = [];
        $customers = [];
        $i = 0;
        foreach ($invoices as $item) {
            if (!in_array($item->id, $customers)) {
                array_push($customers, $item->id);
                $productsData[$item->id] = ['name' => $item->name];
            }
            $i++;
        }
        for ($i = 0; $i < 12; $i++) {
            $data[$i] = [];
            foreach ($customers as $customer) {
                $items = [];
                foreach ($invoices as $item) {


                    if ($item->mes == $i + 1 && $item->id == $customer) {

                        $items = [
                            '_id' => $item->id,
                            'name' => $item->name,
                            'totals' => $item->totals,
                            'mes' => $item->mes
                        ];
                        break;


                    } else {

                        if ($item->id == $customer) {
                            $items = [
                                '_id' => $customer,
                                'name' => $productsData[$customer]['name'],
                                'totals' => 0,
                                'mes' => $i + 1
                            ];
                        }
                    }
                }
                array_push($data[$i], $items);


            }
        }
        return $this->respond(['status' => 200, 'data' => $data]);
    }


    public function salesOfSellerMonth($id = null)
    {


        $invoice = new Invoice();
        $invoices = $invoice->select(' sum(invoices.line_extesion_amount) as totals, users.name, users.id ')
            ->join('users', 'users.id = invoices.seller_id')
            ->where([
                'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >=' => date('Y-m') . '-01',
                'invoices.created_at <=' => date('Y-m') . '-31',
                'invoices.seller_id !=' => NULL
            ])
            ->groupBy(['invoices.seller_id'])
            ->asObject()
            ->get()
            ->getResult();

        $labels = [];
        $values = [];
        $total = 0;
        foreach ($invoices as $invoice) {
            array_push($labels, $invoice->name);
            $total += (int)$invoice->totals;
        }

        foreach ($invoices as $invoice) {
            array_push($values, round((int)$invoice->totals * 100 / $total));
        }


        return $this->respond(['status' => 200, 'data' => ['labels' => $labels, 'values' => $values]]);
    }


    public function salesOfSellerPrevius($id = null)
    {
        $currentDate = date("Y-m-d");
        $invoice = new Invoice();
        $invoices = $invoice->select(' sum(invoices.line_extesion_amount) as totals, users.name, users.id ')
            ->join('users', 'users.id = invoices.seller_id')
            ->where([
                'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >=' => date("Y-m-01", strtotime($currentDate . "- 1 month")),
                'invoices.created_at <=' => date("Y-m-31", strtotime($currentDate . "- 1 month")),
                'invoices.seller_id !=' => NULL
            ])
            ->groupBy(['invoices.seller_id'])
            ->asObject()
            ->get()
            ->getResult();

        $labels = [];
        $values = [];
        $total = 0;
        foreach ($invoices as $invoice) {
            array_push($labels, $invoice->name);
            $total += (int)$invoice->totals;
        }


        foreach ($invoices as $invoice) {
            array_push($values, round((int)$invoice->totals * 100 / $total));
        }


        return $this->respond(['status' => 200, 'data' => ['labels' => $labels, 'values' => $values]]);
    }


    public function salesOfSellerAccumulated($id = null)
    {

        $invoice = new Invoice();
        $invoices = $invoice->select(' sum(invoices.line_extesion_amount) as totals, users.name, users.id ')
            ->join('users', 'users.id = invoices.seller_id')
            ->where([
                'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >=' => date("Y-01-01"),
                'invoices.created_at <=' => date("Y-12-31"),
                'invoices.seller_id !=' => NULL
            ])
            ->groupBy(['invoices.seller_id'])
            ->asObject()
            ->get()
            ->getResult();

        $labels = [];
        $values = [];
        $total = 0;
        foreach ($invoices as $invoice) {
            array_push($labels, $invoice->name);

            $total += (int)$invoice->totals;
        }


        foreach ($invoices as $invoice) {
            array_push($values, round((int)$invoice->totals * 100 / $total));
        }


        return $this->respond(['status' => 200, 'data' => ['labels' => $labels, 'values' => $values]]);
    }


    public function salesOfWallet($id = null)
    {
        $data = [];
        $invoice = new Invoice();
        $invoices = $invoice->select([
            'invoices.resolution',
            'type_documents.name as type_documents',
            'customers.name as customer_name',
            'type_document_identifications.name as type_document_identification',
            'customers.identification_number',
            'invoices.status_wallet',
            'invoices.payable_amount',
            'invoices.created_at',
            'invoices.line_extesion_amount',
            'invoices.tax_inclusive_amount',
            'invoices.id',
            'invoices.payment_due_date',
            'invoices.resolution_credit'
        ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
            ->where([
                'invoices.companies_id' => Auth::querys()->role_id == 1 ? 4 : Auth::querys()->companies_id,
                'invoices.invoice_status_id >' => 1,
            ])
            ->groupBy(['invoices.id'])
            ->asObject()
            ->get()
            ->getResult();


        foreach ($invoices as $invoice) {

            $invoiceCredits = new Invoice();
            $invoiceCredit = $invoiceCredits->select('invoices.*')
                ->where(['invoices.resolution_credit' => $invoice->resolution, 'invoices.companies_id' => Auth::querys()->companies_id])
                ->asObject()
                ->get()
                ->getResult();

            $lineInvoice = new LineInvoice();
            $lineInvoices = $lineInvoice->select('line_invoice_taxs.*, line_invoices.products_id, line_invoices.line_extension_amount, products.free_of_charge_indicator')
                ->join('line_invoice_taxs', 'line_invoice_taxs.line_invoices_id = line_invoices.id')
                ->join('products', 'products.id = line_invoices.products_id')
                ->where(['line_invoices.invoices_id' => $invoice->id])
                ->asObject()
                ->get()
                ->getResult();

            $reteFuente = 0;
            $reteIVA = 0;
            $reteICA = 0;
            $product = 0;
            $free = 0;

            foreach ($lineInvoices as $lineInvoice) {
                switch ($lineInvoice->taxes_id) {
                    case 5:
                        $reteIVA += $lineInvoice->tax_amount;
                        break;
                    case 7:
                        $reteICA += $lineInvoice->tax_amount;
                        break;
                    case 6:
                        $reteFuente += $lineInvoice->tax_amount;
                        break;
                }

                if ($product != $lineInvoice->products_id) {
                    if ($lineInvoice->free_of_charge_indicator == 'true') {
                        $free += $lineInvoice->line_extension_amount;
                    }
                    $product = $lineInvoice->products_id;
                }
            }

            $credit = 0;
            foreach ($invoiceCredit as $itemCredit) {
                $credit += $itemCredit->payable_amount - ($reteICA + $reteIVA + $reteFuente);
            }


            $wallet = new Wallet();
            $wallets = $wallet->select('sum(value) as value')
                ->where(['invoices_id' => $invoice->id])
                ->groupBy(['invoices_id'])
                ->get()
                ->getResult();


            $date1 = new \DateTime($invoice->payment_due_date);
            $date2 = new \DateTime(date('Y-m-d'));
            $interval = $date1->diff($date2);
            $daysDiff = str_replace('+', '', $interval->format('%R%a'));


            if ($daysDiff >= 0){
                if (count($wallets) > 0) {
                    array_push($data,
                        ['nombre' => $invoice->customer_name,
                            'dias' => $daysDiff,
                            'valor' => ($invoice->payable_amount - ($wallets[0]->value + $credit + $reteFuente + $reteIVA + $reteICA + $free))]);
                } else {
                    array_push($data, [
                        'nombre' => $invoice->customer_name,
                        'dias' => $daysDiff,
                        'valor' => ($invoice->payable_amount - ($credit + $reteFuente + $reteIVA + $reteICA + $free))
                    ]);
                }
            }else if($daysDiff > 30 && $daysDiff <= 60) {
                if (count($wallets) > 0) {
                    array_push($data,
                        [
                            'nombre' => $invoice->customer_name,
                            'dias' => $daysDiff,
                            'valor' => ($invoice->payable_amount - ($wallets[0]->value + $credit + $reteFuente + $reteIVA + $reteICA + $free))
                        ]);
                } else {
                    array_push($data, [
                        'nombre' => $invoice->customer_name,
                        'dias' => $daysDiff,
                        'valor' => ($invoice->payable_amount - ($credit + $reteFuente + $reteIVA + $reteICA + $free))
                    ]);
                }
            }else if($daysDiff > 60 && $daysDiff <= 90) {
                if (count($wallets) > 0) {
                    array_push($data,
                        [
                            'nombre' => $invoice->customer_name,
                            'dias' => $daysDiff,
                            'valor' => ($invoice->payable_amount - ($wallets[0]->value + $credit + $reteFuente + $reteIVA + $reteICA + $free))
                        ]);
                } else {
                    array_push($data, [
                        'nombre' => $invoice->customer_name,
                        'dias' => $daysDiff,
                        'valor' => ($invoice->payable_amount - ($credit + $reteFuente + $reteIVA + $reteICA + $free))
                    ]);
                }
            }else if($daysDiff > 90 && $daysDiff <= 120) {
                echo $daysDiff;
                if (count($wallets) > 0) {
                    array_push($data,
                        [
                            'nombre' => $invoice->customer_name,
                            'dias' => $daysDiff,
                            'valor' => ($invoice->payable_amount - ($wallets[0]->value + $credit + $reteFuente + $reteIVA + $reteICA + $free))
                        ]);
                } else {
                    array_push($data, [
                        'nombre' => $invoice->customer_name,
                        'dias' => $daysDiff,
                        'valor' => ($invoice->payable_amount - ($credit + $reteFuente + $reteIVA + $reteICA + $free))
                    ]);
                }
            }else if($daysDiff > 120 && $daysDiff <= 180) {
                if (count($wallets) > 0) {
                    array_push($data,
                        [
                            'nombre' => $invoice->customer_name,
                            'dias' => $daysDiff,
                            'valor' => ($invoice->payable_amount - ($wallets[0]->value + $credit + $reteFuente + $reteIVA + $reteICA + $free))
                        ]);
                } else {
                    array_push($data, [
                        'nombre' => $invoice->customer_name,
                        'dias' => $daysDiff,
                        'valor' => ($invoice->payable_amount - ($credit + $reteFuente + $reteIVA + $reteICA + $free))
                    ]);
                }
            }else if($daysDiff > 180 && $daysDiff <= 365) {
                echo $daysDiff;
                if (count($wallets) > 0) {
                    array_push($data,
                        [
                            'nombre' => $invoice->customer_name,
                            'dias' => $daysDiff,
                            'valor' => ($invoice->payable_amount - ($wallets[0]->value + $credit + $reteFuente + $reteIVA + $reteICA + $free))
                        ]);
                } else {
                    array_push($data, [
                        'nombre' => $invoice->customer_name,
                        'dias' => $daysDiff,
                        'valor' => ($invoice->payable_amount - ($credit + $reteFuente + $reteIVA + $reteICA + $free))
                    ]);
                }
            }else if($daysDiff > 365) {
                echo $daysDiff;
                if (count($wallets) > 0) {
                    array_push($data,
                        [
                            'nombre' => $invoice->customer_name,
                            'dias' => $daysDiff,
                            'valor' => ($invoice->payable_amount - ($wallets[0]->value + $credit + $reteFuente + $reteIVA + $reteICA + $free))
                        ]);
                } else {
                    array_push($data, [
                        'nombre' => $invoice->customer_name,
                        'dias' => $daysDiff,
                        'valor' => ($invoice->payable_amount - ($credit + $reteFuente + $reteIVA + $reteICA + $free))
                    ]);
                }
            }

}


        $walletsData = [];
        foreach ($data as $item){
           if(array_key_exists($item['nombre'], $walletsData)) {
               if ($item['dias'] == 0){
                   $walletsData[$item['nombre']]['corrientes']  =   $item['valor'];
                   $walletsData[$item['nombre']]['cantidad'] += 1;
               }else if ($item['dias']> 0 && $item['dias'] <= 30){
                   $walletsData[$item['nombre']]['cantidad'] += 1;
                   $walletsData[$item['nombre']]['30'] =  isset($walletsData[$item['nombre']]['30']) ? $walletsData[$item['nombre']]['30'] + $item['valor'] : $item['valor'];
               }else if($item['dias'] > 30 && $item['dias'] <= 60){
                   $walletsData[$item['nombre']]['cantidad'] += 1;
                   $walletsData[$item['nombre']]['60'] =  isset($walletsData[$item['nombre']]['60']) ? $walletsData[$item['nombre']]['60'] + $item['valor'] : $item['valor'];
               }else if($item['dias'] > 60 && $item['dias'] <= 90) {
                   $walletsData[$item['nombre']]['cantidad'] += 1;
                   $walletsData[$item['nombre']]['90'] =  isset($walletsData[$item['nombre']]['90']) ? $walletsData[$item['nombre']]['90'] + $item['valor'] : $item['valor'];
               }else if($item['dias'] > 90) {
                   $walletsData[$item['nombre']]['cantidad'] += 1;
                   $walletsData[$item['nombre']]['120'] = isset($walletsData[$item['nombre']]['120']) ? $walletsData[$item['nombre']]['120'] + $item['valor'] : $item['valor'];
               }
           } else {
                $walletsData[$item['nombre']] = [];
                $walletsData[$item['nombre']]['cantidad'] = 1;

               if ($item['dias'] == 0){
                   $walletsData[$item['nombre']]['corrientes']  =   $item['valor'];
               }else if ($item['dias'] > 0 && $item['dias'] <= 30){
                   $walletsData[$item['nombre']]['30']  =   $item['valor'];
               }else if($item['dias'] > 30 && $item['dias'] <= 60){
                   $walletsData[$item['nombre']]['60'] = $item['valor'];
               }else if($item['dias'] > 60 && $item['dias'] <= 90) {
                   $walletsData[$item['nombre']]['90'] = $item['valor'];
               }else if($item['dias']> 90) {
                   $walletsData[$item['nombre']]['120'] = $item['valor'];
               }
           }
        }



$invoice = new Invoice();
$invoices = $invoice->select(' sum(invoices.payable_amount) as total, customers.name as customer, invoices.customers_id, count(invoices.customers_id) as invoices, sum(wallet.value) as pago
        ')
    ->join('wallet', 'invoices.id = wallet.invoices_id', 'left')
    ->join('customers', 'invoices.customers_id = customers.id')
    ->where([
        'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
        'invoices.status_wallet' => 'Pendiente',
        'invoices.invoice_status_id >' => 1
    ])
    ->whereIn('invoices.type_documents_id', [1, 2])
    ->groupBy(['invoices.customers_id'])
    ->asObject()
    ->get()
    ->getResult();

$invoicesCredit = new Invoice();
$invoicesCredits = $invoicesCredit->select(' sum(invoices.payable_amount) as total, customers.name as customer, invoices.customers_id, count(invoices.customers_id) as invoices, sum(wallet.value) as pago
        ')
    ->join('wallet', 'invoices.id = wallet.invoices_id', 'left')
    ->join('customers', 'invoices.customers_id = customers.id')
    ->where([
        'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
        'invoices.invoice_status_id >' => 1,
    ])
    ->whereIn('invoices.type_documents_id', [4])
    ->groupBy(['invoices.customers_id'])
    ->asObject()
    ->get()
    ->getResult();


$data = [];
$i = 0;
foreach ($invoices as $invoice) {
    $totalCredit = 0;
    foreach ($invoicesCredits as $invoicesCredit) {
        if ($invoice->customers_id == $invoicesCredit->customers_id) {
            $totalCredit = $invoicesCredit->total;

        }
    }
    $data[$i] = [
        'customer' => $invoice->customer,
        'invoices' => $invoice->invoices,
        'total' => ($invoice->total - ($invoice->pago + $totalCredit))];
    $i++;
}

return $this->respond(['status' => 200, 'data' => $data ,'wallet_table' => (array) $walletsData], 200);
}

public function index($id = null)
{
    $companies = new Company();
    $company = $companies->select('graphic_email.setting_json')
        ->join('graphic_email', 'companies.id = graphic_email.companies_id')
        ->where(['companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,])
        ->asObject()
        ->get()
        ->getResult();

    if (count($company) > 0) {
        return $this->respond([
            'status' => 200,
            'data' => json_decode($company[0]->setting_json)
        ]);

    } else {
        return $this->respond([
            'status' => 200,
            'data' => [
                'day' => 'monday',
                'day_number' => '',
                'hours' => '12:00',
                'sale_of_customer' => true,
                'sale_of_month' => true,
                'sale_of_product' => true,
                'sale_of_seller' => true,
                'sale_of_wallet' => true,
                'time' => '2'
            ]]);


    }
    die();
}

public function create()
{

    $graphicEmails = new GraphicEmail();
    $graphicEmail = $graphicEmails->where(['companies_id' => Auth::querys()->companies_id])->get()->getResult();

    $json = $this->request->getJSON();



    $data = [
        'setting_json' => json_encode($json),
        'companies_id' => Auth::querys()->companies_id
    ];


    if (count($graphicEmail) > 0) {
        $graphicEmails = new GraphicEmail();
        $graphicEmails->where(['companies_id' => Auth::querys()->companies_id])->set($data)->update( );
    } else {
        $graphicEmails = new GraphicEmail();
        $graphicEmails->save($data);
    }
    die();
}

public function graphicType($id = null)
{
    $graphics = new GraphicComponentType();
    $graphic = $graphics
        ->select([
            'graphic_component_types.graphic_type_id',
            'graphic_component_types.graphic_component_id',
            'graphic_type.name as graphic_type_name'
        ])
        ->join('graphic_type', 'graphic_component_types.graphic_type_id = graphic_type.id')
        ->where(['graphic_component_id' => $id])
        ->asObject()
        ->get()
        ->getResult();

    return $this->respond(['status' => 200, 'data' => $graphic]);
}

    public function graphicSetting()
    {

    }
}