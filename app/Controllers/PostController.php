<?php


namespace App\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;

class PostController extends BaseController
{
    public function create(){
        echo view('post/index');
    }
    public function IndexCierre(){
        $datos = [];
        $productos = [];
        $total = 0;
        $totalIva = 0;
        $totalRetenciones = 0;
        $subtotal = 0;

        if(isset($_GET['value'])){
            $fecha = date("Y-m-d", strtotime($_GET['value']));
        }else{
            $fecha = date("Y-m-d");
        }
        $companies = new Company();
        $company = $companies->select('*')->where(['id'=>session('user')->companies_id])->get()->getResult()[0];
        $users = new User();
        $user = $users->select('*')->where(['id'=>session('user')->id])->get()->getResult()[0];
        $invoices = new Invoice();
        $invoice = $invoices->select('*')
        ->where(['companies_id'=>session('user')->companies_id])
        ->like('created_at ',$fecha)
        ->get()->getResult();
        
        // datos de invoices 
        foreach ($invoice as $key) {
            $line_invoices = new LineInvoice();
            $line_invoices_taxs = new LineInvoiceTax();
            $line_invoice = $line_invoices->select('*')
            ->where(['invoices_id'=>$key->id])
            ->get()->getResult();
            foreach ($line_invoice as $value) {
                $line_invoices_tax = $line_invoices_taxs->select('*')
                ->where(['line_invoices_id'=>$value->id])
                ->get()->getResult();
                array_push($datos,$value);
                foreach ($line_invoices_tax as $tax) {
                    if($tax->taxes_id == 1){
                        $totalIva += $tax->tax_amount;
                    }else{
                        $totalRetenciones += $tax->tax_amount;
                    }
                }
                $subtotal = $subtotal+($value->price_amount * $value->quantity);
                $value->line_invoices_tax = $line_invoices_tax;
            }
            $key->line_invoices = $line_invoice;
        }
        
        $total = $subtotal + $totalIva - $totalRetenciones;
        // envio de datos por roles
        if($user->role_id == 2){
            $usuarios = $users->select('*')->where(['companies_id'=>session('user')->companies_id])->get()->getResult();
        }elseif($user->role_id == 3){
            $usuarios = '';
        }
        //productos
        $productos = $invoices->select('line_invoices.products_id, products.name, sum(line_invoices.line_extension_amount) as valor, sum(line_invoices.quantity) as cantidad')
        ->join('line_invoices','invoices.id = line_invoices.invoices_id')
        ->join('products','products.id = line_invoices.products_id', 'left')
        ->where(['invoices.companies_id'=>session('user')->companies_id ])
        ->like('created_at ',$fecha)
        ->groupBy('line_invoices.products_id')
        ->get()->getResult();
        // inpuestos
        $impuesto = $invoices->select('line_invoice_taxs.taxes_id as impuesto, line_invoice_taxs.percent as porcentaje, sum(line_invoice_taxs.taxable_amount) as valorProductos, sum(line_invoice_taxs.tax_amount) as totalImpuesto')
        ->join('line_invoices','invoices.id = line_invoices.invoices_id')
        ->join('line_invoice_taxs','line_invoices.id = line_invoice_taxs.line_invoices_id')
        ->join('products','products.id = line_invoices.products_id', 'left')
        ->where(['invoices.companies_id'=>session('user')->companies_id ])
        ->like('created_at ',$fecha)
        ->groupBy('line_invoice_taxs.taxes_id, line_invoice_taxs.percent')
        ->get()->getResult();
        //facturas ventas nacional
        $ventasNacionales = $invoices->select('max(resolution) as maxima,MIN(resolution) as minimo')
        ->where(['companies_id'=>session('user')->companies_id,'type_documents_id'=>1])
        ->like('created_at ',$fecha)
        ->get()->getResult();
        //facturas nota Credito
        $notaC = $invoices->select('max(resolution) as maxima,MIN(resolution) as minimo')
        ->where(['companies_id'=>session('user')->companies_id,'type_documents_id'=>4])
        ->like('created_at ',$fecha)
        ->get()->getResult();
        //facturas nota debito
        $notaD = $invoices->select('max(resolution) as maxima,MIN(resolution) as minimo')
        ->where(['companies_id'=>session('user')->companies_id,'type_documents_id'=>5])
        ->like('created_at ',$fecha)
        ->get()->getResult();
        $data = [
            'usuarios'    => $usuarios,
            'caja'        => $user,
            'company'     => $company,
            'subtotal'    => $subtotal,
            'iva'         => $totalIva,
            'retenciones' => $totalRetenciones,
            'total'       => $total,
            'productos'   => $productos,
            'impuestos'   => $impuesto,
            'fecha'       => $fecha,
            'ventasN'     => $ventasNacionales,
            'notaC'       => $notaC,
            'notaD'       => $notaD
        ];
        echo view('post/view/cierre',$data);
    }
}