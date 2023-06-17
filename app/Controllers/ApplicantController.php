<?php

namespace App\Controllers;

use App\Models\Company;
use App\Models\Applicant;
use App\Models\Packages;
use App\Models\Sellers;
use App\Models\Subscription;
use App\Models\Applicant_documents;
use App\Controllers\Configuration\EmailController;

class ApplicantController extends BaseController
{
    public function create()
    {
        $company = new Company();
        $validation = $company->where(['identification_number' => $this->request->getPost('nit')])->countAllResults();
        $this->request->getPost();
        if ($img = $this->request->getFile('dato')) {
            $archivo = $img->getRandomName();
            $ext2 = $img->getExtension();
            if($ext2 != 'pdf' && $ext2 != 'png' && $ext2 != 'jpg' && $ext2 != 'jpeg' ){
                return redirect()->to(base_url() . '/solicitudes/registro')->with('errors', 'El Archivo no se pudo cargar ya que la extensión no es permitida.');
            }
            $img->move('upload/documentos/'. $_POST['nit'], $archivo);
            rename("upload/documentos/" .$_POST['nit']."/" . $archivo, "upload/documentos/" . $_POST['nit']. "/comprobantePago_" . $_POST['nit'] . "." . $ext2);
            $pago = "comprobantePago_".$_POST['nit'] .".". $ext2;
        }
        $vendedores = new Sellers();
        $vendedor = $vendedores->where(['identification_number' => $_POST['vendedor']])->get()->getResult()[0];

        $data=[
            'application_date' => date('Y-m-d H:i:s'),
            'company_name' => $this->request->getPost('nempresa'),
            'nit' => $this->request->getPost('nit'),
            'phone' => '7777777',
            'adress' => $this->request->getPost('direccion'),
            'legal_representative' => $this->request->getPost('rl'),
            'type_document' => $this->request->getPost('tdocumento'),
            'num_documento' => $this->request->getPost('documento'),
            'email' => $this->request->getPost('email'),
            'email_confirmation' => (isset($_POST['emailem']))?$this->request->getPost('emailem'):'',
            'contract' => '',
            'autorizacion' => '',
            'status' => 1,
            'seller' => $vendedor->id,
            'process' => $this->request->getPost('process')
        ];
        $applicant = new Applicant();
        $id=$applicant->insert($data);
        if ($validation < 1) {
        //company
        $data=[
            'company' => $this->request->getPost('nempresa'),
            'identification_number' => $this->request->getPost('nit'),
            'dv' => 0,
            'merchant_registration' => 000000,
            'address' => $this->request->getPost('direccion'),
            'email' => $this->request->getPost('email'),
            'phone' => 777777,
            'taxes_id' => 1,
            'type_currencies_id' => 35,
            'type_liabilities_id' => 14,
            'type_organizations_id' => 1,
            'type_document_identifications_id' => $this->request->getPost('tdocumentoc'),
            'countries_id' => 46,
            'departments_id' => 5,
            'municipalities_id' => 149,
            'languages_id' => 79,
            'type_operations_id' => 10,
            'type_regimes_id' => 1,
            'type_environments_id' => 2
        ];
            $idc = $company->insert($data);
        }else{
            $ultimo = $company->where(['identification_number' => $this->request->getPost('nit')])->orderBy('id', "DESC")->get()->getResult();
            $idc = $ultimo[0]->id;
        }
        $date= date('Y-m-d');
        $nuevafecha = strtotime ( '+1 year' , strtotime ( $date) ) ;
        $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
        $packages = new Packages();
        $package = $packages->where(['id'=>$_POST['plan']])->get()->getResult()[0];
        if($_POST['vendedor'] == '222222222' || $this->request->getPost('process') != 'renovacion'){
            $valor = $package->price;
        }else{
            $valor = $package->price - 30000;
        }
        $data=[
            'companies_id' => $idc,
            'applicant_id' => $id,
            'packages_id'  => $_POST['plan'],
            'start_date'   => date('Y-m-d H:i:s'),
            'end_date'     => $nuevafecha,
            'status'       => 'Activo',
            'date_due_certificate' => $nuevafecha,
            'sopport_invoice' => '',
            'ref_epayco' => '',
            'price' => $valor,
            'seller' => $vendedor->id,
            'seller_tip' => ''

        ];

        $subscription = new Subscription();
        $subscription->save($data);
        $data1 = [
            'applicant_id' => $id,
            'documento' => 'Comprobante de pago',
            'archivo' => $pago,
            'status' => 'Pendiente'
        ];
        $applicant_documents = new Applicant_documents();
        $applicant_documents->save($data1);

        //correo
        $Body    = 'Nombre empresa:'.$this->request->getPost('nempresa').'<br> nit empresa:'.$this->request->getPost('nit').'<br> Tipo Documento:'.$this->request->getPost('tdocumento').'<br> Número Documento:'.$this->request->getPost('documento').'<br> Representante legal:'.$this->request->getPost('rl').'<br> Correo:'.$this->request->getPost('email').'<br> Direccion:'.$this->request->getPost('direccion').'<br> Observaciones:'.$this->request->getPost('observaciones');

        $email = new EmailController();
        $email->send('soporte@mifacturalegal.com', 'prueba', 'soporte@mifacturalegal.com' , 'Registro de Empresa', $Body);

        header('Location: https://mifacturalegal.com/?success=true');
        exit;
    }

    public function subscription()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Allow: GET, POST, OPTIONS, PUT, DELETE");

        $dta=json_decode(file_get_contents('php://input'));
echo json_encode($dta);

        $packages = new Packages();
        $package = $packages->where(['name' => $this->eliminar_tildes(utf8_decode($dta->plan))])->get()->getResult()[0];
        $applicant= new Applicant();
        $company = new Company();
        $subscription = new Subscription();
        $this->request->getPost();
        if($dta->registrado == 'si'){

            $solicitud = $applicant->select('*')
                ->where(['applicant.nit' => $dta->nit])->orderBy('id', "desc")->get()
                ->getResult()[0];

            $compania = $company->select('*')->where(['identification_number' => $dta->nit])->orderBy('id', "desc")
                ->get()->getResult();
            if($dta->estado == 'Aceptada'){
                if(count($compania) > 0){

                    $nuevafecha = strtotime ( '+1 year' , strtotime ( $dta->fecha) ) ;
                    $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
                    // guardar subscripcion
                    $data=[
                        'companies_id' => $compania[0]->id,
                        'applicant_id' => $solicitud->id,
                        'packages_id'  => $package->id,
                        'start_date'   => date('Y-m-d H:i:s'),
                        'end_date'     => $nuevafecha,
                        'status'       => 'Activo',
                        'date_due_certificate' => $nuevafecha,
                        'sopport_invoice' => '',
                        'ref_epayco' => $dta->ref_epayco,
                        'price' => $dta->precio,
                        'seller' => $solicitud->seller,
                        'seller_tip' => ''

                    ];

                    $subscription->save($data);

                    //documento de pago
                    $data=[
                        'applicant_id' => $solicitud->id,
                        'documento' => 'Comprobante de pago',
                        'archivo' => '',
                        'status' => 'Pendiente'
                    ];
                    $pago= new Applicant_documents();
                    $pago->save($data);

                    //correo
                    $asunto = 'Compra de plan  '.$dta->plan;
                    $Body = 'Nombre:  '.$dta->nombre.'<br> Nit:  '.$dta->nit. '<br> Telefono:  '.$dta->telefono.'<br> Correo:  '.$dta->correo.'<br> Plan:  '.$dta->plan.'<br> Fecha transacción:  '.$dta->fecha.'<br> Precio:  '.$dta->precio.'<br> Estado transaccion:  '.$dta->estado.'<br> Ref_epayco:  '.$dta->ref_epayco.'<br> Referencia:  '.$dta->referencia;

                    $email = new EmailController();
                    $email->send('soporte@mifacturalegal.com', 'prueba', 'soporte@mifacturalegal.com' , $asunto, $Body);

                    //header('Location: https://planeta-internet.com/mifacturalegal/public/solicitud/informacion/'.$this->request->getPost('nit'));
                    //exit;
                }
                http_response_code(201);
            }else{
                $solicitud = $applicant->delete($solicitud->id);

                $compania = $company->delete($compania[0]->id);
            }
        }
    }
        public function eliminar_tildes($cadena)
    {
        //Reemplazamos la A y a
        $cadena = str_replace(
            array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
            array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
            $cadena
        );
        //Reemplazamos la E y e
        $cadena = str_replace(
            array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
            array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
            $cadena);
        //Reemplazamos la I y i
        $cadena = str_replace(
            array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
            array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
            $cadena);
        //Reemplazamos la O y o
        $cadena = str_replace(
            array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
            array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
            $cadena);
        //Reemplazamos la U y u
        $cadena = str_replace(
            array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
            array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
            $cadena);
        //Reemplazamos la N, n, C y c
        $cadena = str_replace(
            array('Ñ', 'ñ', 'Ç', 'ç'),
            array('N', 'n', 'C', 'c'),
            $cadena
        );
        return $cadena;
    }
}