<?php


namespace App\Controllers;

use App\Models\Invoice;
use App\Models\LineInvoiceTax;
use App\Models\Resolution;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class DocumentController extends BaseController
{

    public function csv($id)
    {
        $invoice = new Invoice();
        $invoice = $invoice->select('*, 
        payment_forms.name as payment_form, 
        payment_methods.name as payment_method,
        type_documents.name as type_document,
        customers.name as customer,
        invoices.id as id_invoice,    
        products.name as product,
        line_invoices.id as id_line_invoice
        ')
        ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id')
        ->join('type_documents', 'type_documents.id = invoices.type_documents_id')
        ->join('payment_forms', 'payment_forms.id = invoices.payment_forms_id')
        ->join('payment_methods', 'payment_methods.id = invoices.payment_methods_id')
        ->join('customers', 'customers.id = invoices.customers_id')
        ->join('line_invoices', 'invoices.id = line_invoices.invoices_id')
        ->join('products', 'products.id = line_invoices.products_id')
        ->where('invoices.id', $id)
        ->get()
        ->getResult();


        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Tipo de documento')
            ->setCellValue('B1', 'Numero factura')
            ->setCellValue('C1', 'Fecha de creacion')
            ->setCellValue('D1', 'Cliente')
            ->setCellValue('E1', 'Codigo')
            ->setCellValue('F1', 'Producto')
            ->setCellValue('G1', 'Cantidad')
            ->setCellValue('H1', 'Nota')
            ->setCellValue('I1', 'Valor Unitario')
            ->setCellValue('J1', 'IVA')
            ->setCellValue('K1', 'Descuento')
            ->setCellValue('L1', 'Forma de Pago')
            ->setCellValue('M1', 'Metodo de Pago')
            ->setCellValue('N1', 'Vencimiento de la factura')
            ->setCellValue('O1', 'Uuid');

        $i = 2;


        foreach ($invoice as $item) {
            $lineInvoiceTax = new  LineInvoiceTax();
            $taxes = $lineInvoiceTax->where(['line_invoices_id' => $item->id_line_invoice])
                ->get()
                ->getResult();

            foreach ($taxes as $tax) {
                if ($tax->taxes_id == 1) {
                    $Tax = $tax->percent;
                }
            }

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $item->type_document)
                ->setCellValue('B' . $i, $item->resolution)
                ->setCellValue('C' . $i, $item->created_at)
                ->setCellValue('D' . $i, $item->customer)
                ->setCellValue('E' . $i, $item->code)
                ->setCellValue('F' . $i, $item->product)
                ->setCellValue('G' . $i, $item->quantity)
                ->setCellValue('H' . $i, strip_tags($item->description))
                ->setCellValue('I' . $i, $item->price_amount)
                ->setCellValue('J' . $i, $Tax)
                ->setCellValue('K' . $i, $item->discount_amount)
                ->setCellValue('L' . $i, $item->payment_form)
                ->setCellValue('M' . $i, $item->payment_method)
                ->setCellValue('N' . $i, $item->issue_date)
                ->setCellValue('O' . $i, $item->uuid);

            $i++;
        }




        $spreadsheet->getActiveSheet()->setTitle('Simple');
        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$invoice[0]->resolution.'.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');
        exit;
    }

    public function worldOffice($id)
    {


        $invoice = new Invoice();
        $invoice = $invoice->select('*, 
        payment_forms.name as payment_form, 
        payment_methods.name as payment_method,
        type_documents.name as type_document,
        customers.name as customer,
        invoices.id as id_invoice,    
        products.name as product,
        line_invoices.id as id_line_invoice,
        companies.id as companies_id
        ')
            ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id')
            ->join('payment_forms', 'payment_forms.id = invoices.payment_forms_id')
            ->join('payment_methods', 'payment_methods.id = invoices.payment_methods_id')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('line_invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', 'products.id = line_invoices.products_id')
            ->join('companies', 'companies.id =  invoices.companies_id')
            ->where('invoices.id', $id)
            ->get()
            ->getResult();






        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Encab: Empresa')
            ->setCellValue('B1', 'Encab: Tipo Documento')
            ->setCellValue('C1', 'Encab: Prefijo')
            ->setCellValue('D1', 'Encab: Documento Número')
            ->setCellValue('E1', 'Encab: Fecha')
            ->setCellValue('F1', 'Encab: Tercero Interno')
            ->setCellValue('G1', 'Encab: Tercero Externo')
            ->setCellValue('H1', 'Encab: Nota')
            ->setCellValue('I1', 'Encab: FormaPago')
            ->setCellValue('J1', 'Encab: Fecha Entrega')
            ->setCellValue('K1', 'Encab: Prefijo Documento Externo')
            ->setCellValue('L1', 'Encab: Número_Documento_Externo')
            ->setCellValue('M1', 'Encab: Verificado')
            ->setCellValue('N1', 'Encab: Anulado')
            ->setCellValue('O1', 'Encab: Personalizado 1')
            ->setCellValue('P1', 'Encab: Personalizado 2')
            ->setCellValue('Q1', 'Encab: Personalizado 3')
            ->setCellValue('R1', 'Encab: Personalizado 4')
            ->setCellValue('S1', 'Encab: Personalizado 5')
            ->setCellValue('T1', 'Encab: Personalizado 6')
            ->setCellValue('U1', 'Encab: Personalizado 7')
            ->setCellValue('V1', 'Encab: Personalizado 8')
            ->setCellValue('W1', 'Encab: Personalizado 9')
            ->setCellValue('X1', 'Encab: Personalizado 10')
            ->setCellValue('Y1', 'Encab: Personalizado 11')
            ->setCellValue('Z1', 'Encab: Personalizado 12')
            ->setCellValue('AA1', 'Encab: Personalizado 13')
            ->setCellValue('AB1', 'Encab: Personalizado 14')
            ->setCellValue('AC1', 'Encab: Personalizado 15')
            ->setCellValue('AD1', 'Encab: Sucursal')
            ->setCellValue('AE1', 'Encab: Clasificación')
            ->setCellValue('AF1', 'Detalle: Producto')
            ->setCellValue('AG1', 'Detalle: Bodega')
            ->setCellValue('AH1', 'Detalle: UnidadDeMedida')
            ->setCellValue('AI1', 'Detalle: Cantidad')
            ->setCellValue('AJ1', 'Detalle: IVA')
            ->setCellValue('AK1', 'Detalle: Valor Unitario')
            ->setCellValue('AL1', 'Detalle: Descuento')
            ->setCellValue('AM1', 'Detalle: Vencimiento')
            ->setCellValue('AN1', 'Detalle: Nota')
            ->setCellValue('AO1', 'Detalle: Centro costos')
            ->setCellValue('AP1', 'Detalle: Personalizado1')
            ->setCellValue('AQ1', 'Detalle: Personalizado2')
            ->setCellValue('AR1', 'Detalle: Personalizado3')
            ->setCellValue('AS1', 'Detalle: Personalizado4')
            ->setCellValue('AT1', 'Detalle: Personalizado5')
            ->setCellValue('AU1', 'Detalle: Personalizado6')
            ->setCellValue('AV1', 'Detalle: Personalizado7')
            ->setCellValue('AW1', 'Detalle: Personalizado8')
            ->setCellValue('AX1', 'Detalle: Personalizado9')
            ->setCellValue('AY1', 'Detalle: Personalizado10')
            ->setCellValue('AZ1', 'Detalle: Personalizado11')
            ->setCellValue('BA1', 'Detalle: Personalizado12')
            ->setCellValue('BB1', 'Detalle: Personalizado13')
            ->setCellValue('BC1', 'Detalle: Personalizado14')
            ->setCellValue('BD1', 'Detalle: Personalizado15')
            ->setCellValue('BE1', 'Detalle: Código Centro Costos');


        $i = 2;




        foreach ($invoice as $item) {
            $resolution = new Resolution();
            $resolution = $resolution->where(['type_documents_id' => $item->type_documents_id, 'companies_id' => $item->companies_id])
                ->get()->getResult();

            $lineInvoiceTax = new  LineInvoiceTax();
            $taxes = $lineInvoiceTax->where(['line_invoices_id' => $item->id_line_invoice])
                ->get()
                ->getResult();

            foreach ($taxes as $tax) {
                if ($tax->taxes_id == 1) {
                    $Tax = $tax->percent;
                }
            }

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $item->company)
                ->setCellValue('B' . $i, $item->prefix)
                ->setCellValue('C' . $i, $resolution[0]->prefix)
                ->setCellValue('D' . $i, $item->resolution)
                ->setCellValue('E' . $i, date('d-m-Y',strtotime($item->created_at)))
                ->setCellValue('F' . $i, $item->identification_number)
                ->setCellValue('G' . $i, $item->identification_number)
                ->setCellValue('H' . $i, strip_tags($item->description))
                ->setCellValue('I' . $i, $item->payment_form)
                ->setCellValue('J' . $i, date('d-m-Y',strtotime($item->created_at)))
                ->setCellValue('K' . $i, '')
                ->setCellValue('L' . $i, '')
                ->setCellValue('M' . $i, '-1')
                ->setCellValue('N' . $i, '0')
                ->setCellValue('O' . $i, '')
                ->setCellValue('P' . $i, '')
                ->setCellValue('Q' . $i, '')
                ->setCellValue('R' . $i, '')
                ->setCellValue('S' . $i, '')
                ->setCellValue('T' . $i, '')
                ->setCellValue('U' . $i, '')
                ->setCellValue('V' . $i, '')
                ->setCellValue('W' . $i, '')
                ->setCellValue('X' . $i, '')
                ->setCellValue('Y' . $i, '')
                ->setCellValue('Z' . $i, '')
                ->setCellValue('AA' . $i, '')
                ->setCellValue('AB' . $i, '')
                ->setCellValue('AC' . $i, '')
                ->setCellValue('AD' . $i, '')
                ->setCellValue('AE' . $i, '')
                ->setCellValue('AF' . $i, $item->code)
                ->setCellValue('AG' . $i, 'Principal')
                ->setCellValue('AH' . $i,  'Und.')
                ->setCellValue('AI' . $i, $item->quantity)
                ->setCellValue('AJ' . $i, $Tax)
                ->setCellValue('AK' . $i, ($item->line_extension_amount + ($item->discount_amount/$item->quantity )) )
                ->setCellValue('AL' . $i, $item->discount_amount)
                ->setCellValue('AM' . $i, $item->issue_date)
                ->setCellValue('AN' . $i, strip_tags($item->description))
                ->setCellValue('AO' . $i, '')
                ->setCellValue('AP' . $i, '')
                ->setCellValue('AR' . $i, '')
                ->setCellValue('AS' . $i, '')
                ->setCellValue('AT' . $i, '')
                ->setCellValue('AU' . $i, '')
                ->setCellValue('AV' . $i, '')
                ->setCellValue('AW' . $i, '')
                ->setCellValue('AX' . $i, '')
                ->setCellValue('AY' . $i, '')
                ->setCellValue('AZ' . $i, '')
                ->setCellValue('BA' . $i, '')
                ->setCellValue('BB' . $i, '')
                ->setCellValue('BC' . $i, '')
                ->setCellValue('BD' . $i, '')
                ->setCellValue('BE' . $i, '');



            $i++;
        }



        $spreadsheet->getActiveSheet()->setTitle('Simple');
        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$invoice[0]->resolution.'.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');
        exit;
    }

}