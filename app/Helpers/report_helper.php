<?php


use App\Controllers\Api\Auth;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\Wallet;

function userName($id){
        $user = new \App\Models\User();
        $users = $user->where(['id' => $id])->asObject()->get()->getResult();

        if(count($users) > 0) {
            return $users[0]->name;
        }else {
            return '';
        }
    }

    function taxes($id, $typeDocumentId, $line = null) {
    
    if($line == null) {
    
        $lineInvoice = new LineInvoice();
        $lineInvoices = $lineInvoice->select('line_invoice_taxs.*, invoices.resolution_credit, line_invoices.products_id, line_invoices.line_extension_amount, products.free_of_charge_indicator, invoices.resolution')
            ->join('line_invoice_taxs', 'line_invoice_taxs.line_invoices_id = line_invoices.id')
            ->join('products', 'products.id = line_invoices.products_id')
            ->join('invoices', 'line_invoices.invoices_id = invoices.id')
            ->where(['line_invoices.invoices_id' => $id])
            ->asObject()
            ->get()
            ->getResult();
	    }else {
	     $lineInvoice = new LineInvoice();
        $lineInvoices = $lineInvoice->select('line_invoice_taxs.*, invoices.resolution_credit, line_invoices.products_id, line_invoices.line_extension_amount, products.free_of_charge_indicator, invoices.resolution')
            ->join('line_invoice_taxs', 'line_invoice_taxs.line_invoices_id = line_invoices.id')
            ->join('products', 'products.id = line_invoices.products_id')
            ->join('invoices', 'line_invoices.invoices_id = invoices.id')
            ->where(['line_invoices.id' => $id])
            ->asObject()
            ->get()
            ->getResult();
	    }
	    

        $reteFuente = 0;
        $reteIVA = 0;
        $reteICA = 0;
        $iva = 0;
        $free = 0;
        $product = 0;
        $percentIva = 0;
        $percentReteFuente = 0;
        $percentReteICA = 0;
        $percentReteIVA = 0;




        if ($typeDocumentId != 4 ) {
            foreach ($lineInvoices as $lineInvoice) {
                switch ($lineInvoice->taxes_id) {
                    case 1:
                        $percentIva = $lineInvoice->percent;
                        $iva += $lineInvoice->tax_amount;
                        break;
                    case 5:
                        $percentReteIVA = $lineInvoice->percent;
                        $reteIVA += $lineInvoice->tax_amount;
                        break;
                    case 7:
                        $percentReteICA = $lineInvoice->percent;
                        $reteICA += $lineInvoice->tax_amount;
                        break;
                    case 6:
                        $percentReteFuente = $lineInvoice->percent;
                        $reteFuente += $lineInvoice->tax_amount;
                        break;
                }


                if($product != $lineInvoice->products_id )  {
                    if($lineInvoice->free_of_charge_indicator == 'true') {
                        $free +=  $lineInvoice->line_extension_amount;
                    }
                    $product = $lineInvoice->products_id;
                }
            }
        } else {
            foreach ($lineInvoices as $lineInvoice) {
                $lineInvoicesCredits = new LineInvoice();
                $lineInvoicesCredit = $lineInvoicesCredits->select('line_invoice_taxs.*, invoices.resolution_credit, line_invoices.price_amount')
                    ->join('line_invoice_taxs', 'line_invoice_taxs.line_invoices_id = line_invoices.id')
                    ->join('invoices', 'line_invoices.invoices_id = invoices.id')
                    ->where(['invoices.resolution' => $lineInvoice->resolution_credit, 'invoices.companies_id' => Auth::querys()->companies_id, 'line_invoices.products_id' => $lineInvoice->products_id])
                    ->asObject()
                    ->get()
                    ->getResult();

                foreach ($lineInvoicesCredit as $optionsCredit) {
                    switch ($optionsCredit->taxes_id) {
                        case 1:
                            $percentIva = $lineInvoice->percent;
                            $iva += $optionsCredit->percent * $lineInvoice->line_extension_amount / 100;
                            break;
                        case 5:
                            $percentReteIVA = $lineInvoice->percent;
                            $reteIVA += $optionsCredit->percent * $lineInvoice->line_extension_amount / 100;
                            break;
                        case 7:
                            $percentReteICA = $lineInvoice->percent;
                            $reteICA += $optionsCredit->percent * $lineInvoice->line_extension_amount / 100;
                            break;
                        case 6:
                            $percentReteFuente = $lineInvoice->percent;
                            $reteFuente += $optionsCredit->percent * $lineInvoice->line_extension_amount / 100;
                            break;
                    }
                    if($product != $lineInvoice->products_id )  {
                        if($lineInvoice->free_of_charge_indicator == 'true') {
                            $free +=  $lineInvoice->line_extension_amount;
                        }
                        $product = $lineInvoice->products_id;
                    }
                }


            }

        }

        $credit = 0;
        $quotation = 0;
        if(count($lineInvoices) > 0) {
            $invoiceCredits = new Invoice();
            $invoiceCredit = $invoiceCredits->select('invoices.*')
                ->where(['invoices.resolution_credit' => $lineInvoices[0]->resolution, 'invoices.companies_id' => Auth::querys()->companies_id])
                ->asObject()
                ->get()
                ->getResult();

            foreach ($invoiceCredit as $itemCredit) {
                $credit += $itemCredit->payable_amount - ($reteICA + $reteIVA + $reteFuente);
            }


            $quotations = new Invoice();
            $quotation = $quotations->select('count(invoices.id) as quantity')->where(['resolution_credit' => $id])->get()->getResult();
        }





        return [
            'iva'               => $iva,
            'reteIVA'           => $reteIVA,
            'reteICA'           => $reteICA,
            'reteFuente'        => $reteFuente,
            'free'              => $free,
            'percentIva'        => $percentIva,
            'percentReteFuente' => $percentReteFuente,
            'percentReteICA'    => $percentReteICA,
            'percentReteIVA'    => $percentReteIVA,
            'credit'            => $credit,
            'quotation'         => $quotation
        ];
    }

    function wallets($id) {
        $wallet = new Wallet();
        $wallets = $wallet->select('sum(value) as value')
            ->where(['invoices_id' => $id])
            ->groupBy(['invoices_id'])
            ->get()
            ->getResult();
        return $wallets;
    }


    function timeDays($paymenDue)
    {
        $date1 = new \DateTime($paymenDue);
        $date2 = new \DateTime(date('Y-m-d'));
        $interval 		= $date1->diff($date2);
        return  str_replace ('+',  '', $interval->format('%R%a'));
    }