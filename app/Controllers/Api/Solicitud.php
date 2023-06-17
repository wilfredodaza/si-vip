<?php


namespace App\Controllers\Api;


use App\Models\Applicant;
use App\Models\Applicant_documents;
use App\Models\Company;
use App\Models\Packages;
use App\Models\Sellers;
use App\Models\Subscription;
use App\Models\TypeDocumentIdentifications;
use CodeIgniter\RESTful\ResourceController;

class Solicitud extends ResourceController
{
    protected $format = 'json';

    public function create()
    {
        $json = file_get_contents('php://input');
        $register = json_decode($json);
        $vendedores = new Sellers();
        $packagesV = new Packages();
        $validarVendedores = $vendedores->where(['identification_number' => $register->provider])->countAllResults();
        $typeDocuments = new TypeDocumentIdentifications();
        $validarDocumentC = $typeDocuments->where(['id' => $register->type_document_company])->countAllResults();
        $validarDocument = $typeDocuments->where(['id' => $register->type_document])->countAllResults();
        $validarPackages = $packagesV->where(['id' => $register->packages])->countAllResults();

        if ($validarPackages != 0) {
            $paquete = 'si';
        } else {
            $paquete = 'no';
            $message = 'El Id del paquete no se encuentra en nuestra base de datos.';
        }
        if ($validarVendedores != 0) {
            $provider = 'si';
        } else {
            $message = 'No se encontró proveedor  para autentificar.';
            $provider = 'no';
        }

        if ($validarDocumentC != 0) {
            $typeDocumentCompany = 'si';
        } else {
            $message = 'El Documento de la empresa no se encuentra en nuestra base de datos.';
            $typeDocumentCompany = 'no';
        }


        if ($validarDocument != 0) {
            $typeDocument = 'si';
        } else {
            $message = 'El Id Documento del representante legal no se encuentra en nuestra base de datos.';
            $typeDocument = 'no';
        }
        $cantidadName = strlen($register->company_name);
        if($cantidadName <= 255){
            $companyName = 'si';
        }else{
            $companyName = 'no';
            $message = 'El Nombre de la compañia supera la longitud máxima.';
        }
        if (filter_var($register->email, FILTER_VALIDATE_EMAIL)) {
            $email = 'si';
        }else{
            $email = 'no';
            $message = 'El Correo electronico es invalido.';
        }
        $cantidadAddress = strlen($register->address);
        if($cantidadAddress <= 191){
            $address = 'si';
        }else{
            $address = 'no';
            $message = 'La dirección supera la longitud máxima.';
        }
        $cantidadLegalRepresentative = strlen($register->legal_representative);
        if($cantidadLegalRepresentative <= 150){
            $LegalRepresentative = 'si';
        }else{
            $LegalRepresentative = 'no';
            $message = 'El nombre del Representante legal supera la longitud máxima.';
        }
        $cantidadtransactionNumber = strlen($register->transaction_number);
        if($cantidadtransactionNumber <= 255){
            $transactionNumber = 'si';
        }else{
            $transactionNumber = 'no';
            $message = 'La referencia de pago supera la longitud máxima.';
        }
        $cantidadNit= strlen($register->nit);
        if($cantidadNit <= 191){
            $nit = 'si';
        }else{
            $nit = 'no';
            $message = 'El nit supera la longitud máxima.';
        }



        if ($provider == 'si' && $typeDocumentCompany == 'si' && $typeDocument == 'si' && $paquete == 'si' && $companyName == 'si' && $email == 'si' && $address == 'si' && $LegalRepresentative == 'si' && $transactionNumber == "si" && $nit == 'si') {
            $vendedor = $vendedores->where(['identification_number' => $register->provider])->get()->getResult()[0];
            $data = [
                'application_date' => date('Y-m-d H:i:s'),
                'company_name' => $register->company_name,
                'nit' => $register->nit,
                'phone' => '7777777',
                'adress' => $register->address,
                'legal_representative' => $register->legal_representative,
                'type_document' => $register->type_document,
                'num_documento' => $register->number_document,
                'email' => $register->email,
                'email_confirmation' => '',
                'contract' => '',
                'autorizacion' => '',
                'status' => 1,
                'seller' => $vendedor->id
            ];
            $applicant = new Applicant();
            $idApplicant = $applicant->insert($data);

            //company
            $data = [
                'company' => $register->company_name,
                'identification_number' => $register->nit,
                'dv' => 0,
                'merchant_registration' => 000000,
                'address' => $register->address,
                'email' => $register->email,
                'phone' => 777777,
                'taxes_id' => 1,
                'type_currencies_id' => 35,
                'type_liabilities_id' => 14,
                'type_organizations_id' => 1,
                'type_document_identifications_id' => $register->type_document_company,
                'countries_id' => 46,
                'departments_id' => 5,
                'municipalities_id' => 149,
                'languages_id' => 79,
                'type_operations_id' => 10,
                'type_regimes_id' => 1,
                'type_environments_id' => 2
            ];
            $date = date('Y-m-d');
            $nuevafecha = strtotime('+1 year', strtotime($date));
            $nuevafecha = date('Y-m-d', $nuevafecha);
            $company = new Company();
            $idCompany = $company->insert($data);
            $packages = new Packages();
            $package = $packages->where(['id' => $register->packages])->get()->getResult()[0];
            $data = [
                'companies_id' => $idCompany,
                'applicant_id' => $idApplicant,
                'packages_id' => $register->packages,
                'start_date' => date('Y-m-d H:i:s'),
                'end_date' => $nuevafecha,
                'status' => 'Activo',
                'date_due_certificate' => $nuevafecha,
                'sopport_invoice' => '',
                'ref_epayco' => $register->transaction_number,
                'price' => $package->price,
                'seller' => $vendedor->id,
                'seller_tip' => ''

            ];

            $subscription = new Subscription();
            $subscription->save($data);
            $data1 = [
                'applicant_id' => $idApplicant,
                'documento' => 'Comprobante de pago',
                'archivo' => 'pago proveedor',
                'status' => 'Aprovado'
            ];
            $applicant_documents = new Applicant_documents();
            if ($applicant_documents->save($data1)) {
                return $this->respond([
                    'status' => 201,
                    'message' => 'created.',
                    'data' => $register
                ]);
            } else {
                return $this->respond([
                    'status' => 400,
                    'message' => 'Bad Request'
                ]);
            }
        } else {
            return $this->respond([
                'status' => ($provider == 'si')?400:401,
                'message' => ($provider == 'si')?'Bad Request':'Unauthorized',
                'error' => $message
            ]);
        }
    }

}