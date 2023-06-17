<?php

namespace App\Controllers\Imports;

use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\Product;
use App\Traits\ExcelValidationTrait;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DocumentSupportImportController extends  BaseController
{
    use ExcelValidationTrait;

    /**
     * @return \CodeIgniter\HTTP\RedirectResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \ReflectionException
     */

    public function create()
    {
        if(!$this->request->getFile('file')) {
            return redirect()->back()->with('errors', 'Por favor ingresa un documento');
        }

        $excel                  = IOFactory::load($this->request->getFile('file'));
        $documents              = [];
        $sheet                  = $excel->getSheet(0);
        $largestRowNumber       = $sheet->getHighestRow();


        for ($rowIndex = 2; $rowIndex <= $largestRowNumber; $rowIndex++) {
            $this->required($sheet->getCell('A'.$rowIndex)->getValue(), 'Consecutivo', 'A'.$rowIndex);
            $this->required($sheet->getCell('B'.$rowIndex)->getValue(), 'Tipo de documento electrónico', 'B'.$rowIndex);
            $this->required($sheet->getCell('C'.$rowIndex)->getValue(), 'Número de identificación', 'C'.$rowIndex);
            $this->required($sheet->getCell('D'.$rowIndex)->getValue(), 'Cod. producto', 'D'.$rowIndex);
            $this->required($sheet->getCell('E'.$rowIndex)->getValue(), 'Descripcion', 'E'.$rowIndex);
            $this->required($sheet->getCell('F'.$rowIndex)->getValue(), 'F. Entrega', 'F'.$rowIndex);
            $this->required($sheet->getCell('G'.$rowIndex)->getValue(), 'Valor del producto', 'G'.$rowIndex);
            $this->required($sheet->getCell('H'.$rowIndex)->getValue(), 'Cantidad', 'H'.$rowIndex);
            $this->required($sheet->getCell('I'.$rowIndex)->getValue(), 'Descuento', 'I'.$rowIndex);
            $this->required($sheet->getCell('J'.$rowIndex)->getValue(), 'IVA %', 'J'.$rowIndex);
            $this->required($sheet->getCell('K'.$rowIndex)->getValue(), 'RETEICA %', 'K'.$rowIndex);
            $this->required($sheet->getCell('L'.$rowIndex)->getValue(), 'RETEFUENTE %', 'L'.$rowIndex);
            $this->required($sheet->getCell('N'.(string)$rowIndex)->getValue(), 'Forma de pago', 'N'.$rowIndex);
            $this->required($sheet->getCell('O'.(string)$rowIndex)->getValue(), 'Método de pago', 'O'.$rowIndex);
            $this->required($sheet->getCell('P'.(string)$rowIndex)->getValue(), 'Tipo de generación', 'P'.$rowIndex);
            $typeDocument   = $this->validExistDB($sheet->getCell('B'.$rowIndex)->getValue(), 'Tipo de documento electrónico', 'B'.$rowIndex, 'type_documents', 'name', false);
            $customer       = $this->validExistDB($sheet->getCell('C'.$rowIndex)->getValue(), 'Cliente', 'C'.$rowIndex, 'customers', 'identification_number', true);
            $product        = $this->validExistDB($sheet->getCell('D'.$rowIndex)->getValue(), 'Producto', 'D'.$rowIndex, 'products', 'code', true);
            $paymentForm    = $this->validExistDB($sheet->getCell('N'.$rowIndex)->getValue(), 'Forma de pago', 'N'.$rowIndex, 'payment_forms', 'name', false);
            $paymentMethod  = $this->validExistDB($sheet->getCell('O'.$rowIndex)->getValue(), 'Método de pago', 'O'.$rowIndex, 'payment_methods', 'name', false);
            $typeGeneration = $this->validExistDB($sheet->getCell('P'.$rowIndex)->getValue(), 'Tipo de generación', 'P'.$rowIndex, 'type_generation_transmitions', 'name', false);
        }

        if(count($this->getErrors()) > 0) {
            return redirect()->back()->with('errors', implode('<br>', $this->getErrors()));
        }

        for ($rowIndex = 2; $rowIndex <= $largestRowNumber; $rowIndex++) {
            if(key_exists($sheet->getCell('A'.$rowIndex)->getValue(), $documents)) {
                $position = count($documents[$sheet->getCell('A'.$rowIndex)->getValue()]);
            } else {
                $position = 0;
            }

            $documents[$sheet->getCell('A'.$rowIndex)->getValue()][(int) $position] = [
                'type_document_id'                   => $this->validExistDB($sheet->getCell('B'.$rowIndex)->getValue(), 'Tipo de documento electrónico', 'B'.$rowIndex, 'type_documents', 'name', false)->id,
                'identification_number'              => $sheet->getCell('C'.$rowIndex)->getValue(),
                'cod_producto'                       => $sheet->getCell('D'.$rowIndex)->getValue(),
                'description'                        => $sheet->getCell('E'.$rowIndex)->getValue(),
                'start_date'                         => $this->tranformDate($sheet->getCell('F'.$rowIndex)->getValue()),
                'value'                              => $sheet->getCell('G'.$rowIndex)->getValue(),
                'quantity'                           => $sheet->getCell('H'.$rowIndex)->getValue(),
                'discount'                           => $sheet->getCell('I'.$rowIndex)->getValue(),
                'iva'                                => $sheet->getCell('J'.$rowIndex)->getValue(),
                'retefuente'                         => $sheet->getCell('K'.$rowIndex)->getValue(),
                'reteICA'                            => $sheet->getCell('L'.$rowIndex)->getValue(),
                'notes'                              => $sheet->getCell('M'.$rowIndex)->getValue(),
                'payment_form_id'                    => $this->validExistDB($sheet->getCell('N'.$rowIndex)->getValue(), 'Forma de pago', 'N'.$rowIndex, 'payment_forms', 'name', false)->id,
                'payment_method_id'                  => $this->validExistDB($sheet->getCell('O'.$rowIndex)->getValue(), 'Método de pago', 'O'.$rowIndex, 'payment_methods', 'name', false)->id,
                'type_generation_transmition_id'     => $this->validExistDB($sheet->getCell('P'.$rowIndex)->getValue(), 'Método de generación', 'P'.$rowIndex, 'type_generation_transmitions', 'name', false)->id,
            ];
        }


        $l = 0 ;
        foreach($documents as $document) {

            $model  =  new Customer();
            $customerId  = $model->select(['id'])
                ->where([
                    'identification_number' => $document[0]['identification_number'],
                    'companies_id'          => Auth::querys()->companies_id,
                    'type_customer_id'      => 2
                ])
                ->asObject()
                ->first();


            $model = new Invoice();
            $dataInvoice = [
                'payment_forms_id'          => $document[0]['payment_form_id'],
                'payment_methods_id'        => $document[0]['payment_method_id'],
                'type_documents_id'         => $document[0]['type_document_id'],
                'idcurrency'                => 35,
                'invoice_status_id'         => 8,
                'customers_id'              => $customerId->id,
                'companies_id'              => Auth::querys()->companies_id,
                'user_id'                   => Auth::querys()->id,
                'resolution'                => null,
                'resolution_id'             => null,
                'payment_due_date'          => date('Y-m-d'),
                'duration_measure'          => 0,
                'line_extesion_amount'      => 0,
                'tax_exclusive_amount'      => 0,
                'tax_inclusive_amount'      => 0,
                'allowance_total_amount'    => 0,
                'charge_paid_amount'        => 0,
                'payable_amount'            => 0,
                'calculationrate'           => 0,
                'issue_date'                => date('Y-m-d'),
                'notes'                     => $document[0]['notes'],
                'send'                      => 'False'
            ];

            $invoiceId = $model->insert($dataInvoice);

            $lineExtesionAmount = 0;
            $taxExclusiveAmount = 0;
            $taxInclusiveAmount = 0;
            $payableAmount      = 0;
            $tax                = 0;

            foreach($document  as   $line) {
                $line = (Object) $line;

                $model      =  new Product();
                $productId  = $model->select(['id'])
                    ->where([
                        'code'                  => $line->cod_producto,
                        'kind_product_id'       => 2,
                        'companies_id'          => Auth::querys()->companies_id
                    ])
                    ->asObject()
                    ->first();


                $dataLine = [
                    'invoices_id'                       => $invoiceId,
                    'discounts_id'                      => 1,
                    'products_id'                       => $productId->id,
                    'discount_amount'                   => ($line->discount),
                    'quantity'                          => $line->quantity,
                    'price_amount'                      => $line->value,
                    'start_date'                        => $line->start_date,
                    'line_extension_amount'             => ($line->value  * $line->quantity) - ($line->discount),
                    'description'                       => $line->description,
                    'type_generation_transmition_id'    => $line->type_generation_transmition_id
                ];
                $lineExtesionAmount +=  $dataLine['line_extension_amount'];

                $modelLine = new LineInvoice();
                $lineInvoiceId = $modelLine->insert($dataLine);

                $modelTax = new LineInvoiceTax();

                $dataTax =  [
                    'line_invoices_id' => $lineInvoiceId,
                    'taxes_id'         => 1,
                    'tax_amount'       => ($line->iva == 0 ? 0 : $dataLine['line_extension_amount'] * $line->iva / 100),
                    'taxable_amount'   => $dataLine['line_extension_amount'],
                    'percent'          => $line->iva
                ];

                $modelTax->insert($dataTax);

                $tax +=  $dataTax['tax_amount'];

                $dataTax =  [
                    'line_invoices_id' => $lineInvoiceId,
                    'taxes_id'         => 5,
                    'tax_amount'       => 0,
                    'taxable_amount'   => $dataLine['line_extension_amount'],
                    'percent'          => 0
                ];
                $modelTax->insert($dataTax);

                $dataTax =  [
                    'line_invoices_id' => $lineInvoiceId,
                    'taxes_id'         => 6,
                    'tax_amount'       => ($line->retefuente == 0 ? 0 : $dataLine['line_extension_amount'] * $line->retefuente / 100),
                    'taxable_amount'   => $dataLine['line_extension_amount'],
                    'percent'          => $line->retefuente
                ];

                $modelTax->insert($dataTax);

                $dataTax =  [
                    'line_invoices_id' => $lineInvoiceId,
                    'taxes_id'         => 7,
                    'tax_amount'       => ($line->reteICA == 0 ? 0 : $dataLine['line_extension_amount'] * $line->reteICA / 100),
                    'taxable_amount'   => $dataLine['line_extension_amount'],
                    'percent'          => $line->reteICA
                ];
                $modelTax->insert($dataTax);
            }
            $taxExclusiveAmount = $lineExtesionAmount;
            $payableAmount      = $lineExtesionAmount + $tax;
            $taxInclusiveAmount = $lineExtesionAmount + $tax;


            $model = new Invoice();
            $model->where(['id' => $invoiceId])
                ->set('line_extesion_amount', $lineExtesionAmount)
                ->set('tax_exclusive_amount', $taxExclusiveAmount)
                ->set('tax_inclusive_amount', $taxInclusiveAmount)
                ->set('payable_amount', $payableAmount)
                ->update();
            $l++;

        }

        return redirect()->back()->with('success','El documento excel fue cargado correctamente.');
    }




}