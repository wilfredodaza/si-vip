<?php

namespace App\Controllers\Imports;

use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\Customer;
use App\Traits\ExcelValidationTrait;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CustomerImportController extends BaseController
{
    use ExcelValidationTrait;

    public function create()
    {

        if(empty($_FILES['file']['name'])) {
            return redirect()->back()->with('errors', 'Por favor ingresa un documento');
        }

        $excel                  = IOFactory::load($_FILES['file']['tmp_name']);
        $documents              = [];
        $sheet                  = $excel->getSheet(0);
        $largestRowNumber       = $sheet->getHighestRow();


        for ($rowIndex = 2; $rowIndex <= $largestRowNumber; $rowIndex++) {
            $this->required($sheet->getCell('A'.$rowIndex)->getValue(), 'Nombre', 'A'.$rowIndex);
            $this->required($sheet->getCell('B'.$rowIndex)->getValue(), 'Tipo de documento identificación', 'B'.$rowIndex);
            $this->required($sheet->getCell('C'.$rowIndex)->getValue(), 'Número de identificación', 'C'.$rowIndex);
            $this->required($sheet->getCell('D'.$rowIndex)->getValue(), 'Telefono', 'D'.$rowIndex);
            $this->required($sheet->getCell('E'.$rowIndex)->getValue(), 'Dirección', 'E'.$rowIndex);
            $this->required($sheet->getCell('F'.$rowIndex)->getValue(), 'Correo electrónico', 'F'.$rowIndex);
            $this->required($sheet->getCell('I'.$rowIndex)->getValue(), 'Regimen', 'I'.$rowIndex);
            $this->required($sheet->getCell('J'.$rowIndex)->getValue(), 'Municipio', 'J'.$rowIndex);
            $this->required($sheet->getCell('K'.$rowIndex)->getValue(), 'Tipo Organización', 'K'.$rowIndex);
            $this->required($sheet->getCell('L'.$rowIndex)->getValue(), 'Código Postal', 'L'.$rowIndex);
            $this->validExistDB($sheet->getCell('B'.$rowIndex)->getValue(), 'Tipo de documento identficación', 'B'.$rowIndex, 'type_document_identifications', 'name', false);
            $this->validExistDB($sheet->getCell('I'.$rowIndex)->getValue(), 'Regimen', 'I'.$rowIndex, 'type_regimes', 'name', false);
            $this->validExistDB($sheet->getCell('J'.$rowIndex)->getValue(), 'Municipio', 'J'.$rowIndex, 'municipalities', 'name', false);
            $this->validExistDB($sheet->getCell('K'.$rowIndex)->getValue(), 'Tipo Organización', 'K'.$rowIndex, 'type_organizations', 'name', false);
        }

        if(count($this->getErrors()) > 0) {
            return redirect()->back()->with('errors', implode('<br>', $this->getErrors()));
        }

        for ($rowIndex = 2; $rowIndex <= $largestRowNumber; $rowIndex++) {
            $model = new Customer();
            $customerId = $model->where([
                'companies_id'              =>  Auth::querys()->companies_id,
                'type_customer_id'          => 2,
                'identification_number'     =>  $sheet->getCell('C'.$rowIndex)->getValue()
            ])
                ->get()
                ->getResult();

            if(count($customerId) == 0) {
                $customers = [
                    'name'                                              => $sheet->getCell('A'.$rowIndex)->getValue(),
                    'type_document_identifications_id'                  => $this->validExistDB($sheet->getCell('B'.$rowIndex)->getValue(), 'Tipo de documento identficación', 'B'.$rowIndex, 'type_document_identifications', 'name', false)->id,
                    'identification_number'                             => $sheet->getCell('C'.$rowIndex)->getValue(),
                    'phone'                                             => $sheet->getCell('D'.$rowIndex)->getValue(),
                    'address'                                           => $sheet->getCell('E'.$rowIndex)->getValue(),
                    'email'                                             => $sheet->getCell('F'.$rowIndex)->getValue(),
                    'type_regime_id'                                    => $this->validExistDB($sheet->getCell('I'.$rowIndex)->getValue(), 'Regimen', 'I'.$rowIndex, 'type_regimes', 'name', false)->id,
                    'municipality_id'                                   => $this->validExistDB($sheet->getCell('J'.$rowIndex)->getValue(), 'Municipio', 'J'.$rowIndex, 'municipalities', 'name', false)->id,
                    'type_organization_id'                              => $this->validExistDB($sheet->getCell('K'.$rowIndex)->getValue(), 'Tipo Organización', 'K'.$rowIndex, 'type_organizations', 'name', false)->id,
                    'postal_code'                                       => $sheet->getCell('L'.$rowIndex)->getValue(),
                    'companies_id'                                      => Auth::querys()->companies_id,
                    'type_customer_id'                                  => 2,
                    'merchant_registration'                             => '000000',
                    'dv'                                                => $this->calcularDV($sheet->getCell('C'.$rowIndex)->getValue())
                ];

                $model->insert($customers);
            }

        }

        return redirect()->back()->with('success','El documento excel fue cargado correctamente.');
    }


    private function calcularDV($nit) {
        if (! is_numeric($nit)) {
            return false;
        }

        $arr = array(1 => 3, 4 => 17, 7 => 29, 10 => 43, 13 => 59, 2 => 7, 5 => 19,
            8 => 37, 11 => 47, 14 => 67, 3 => 13, 6 => 23, 9 => 41, 12 => 53, 15 => 71);
        $x = 0;
        $y = 0;
        $z = strlen($nit);
        $dv = '';

        for ($i=0; $i<$z; $i++) {
            $y = substr($nit, $i, 1);
            $x += ($y*$arr[$z-$i]);
        }

        $y = $x%11;

        if ($y > 1) {
            $dv = 11 - $y;
            return $dv;
        } else {
            $dv = $y;
            return $dv;
        }

    }
}