<?php


namespace App\Controllers;

use App\Controllers\Configuration\EmailController;
use App\Models\Applicant;
use App\Models\Applicant_documents;
use App\Models\Company;
use App\Models\Packages;
use App\Models\Subscription;
use App\Models\TypeDocumentIdentifications;
use mysql_xdevapi\Exception;

class ActualiceseApi extends BaseController
{
    public $api = 'https://actualicese.com/convenios/facturacion/?';
    private $token = '4d9c3d068d3695f04dbeea0133b3c558';

    public function index()
    {

        $fecha = date('Y-m-d');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->api . 'convenio=' . $this->token . '&desde=2021-02-08&hasta=' . $fecha . '&json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: __cfduid=d246a383219ffb81ed1fcfdefbeb188371612275637'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $datos = json_decode($response);
        $errores = [];


        if (count($datos) > 0) {
            foreach ($datos as $cliente) {
                $applicants = new Applicant();
                try {
                    $applicantNew = $applicants->where(['nit' => $cliente->documento])->countAllResults();
                    if ($applicantNew > 0) {
                        throw new \Exception('El nit o número de documento a registrar ya se encuentra en base de datos');
                    }
                } catch (\Exception $e) {
                    array_push($errores, 'Excepción: ' . $e->getMessage());
                    //correo
                    $Body = '<p>Buen día.</p><p>Empresa ya registrada en el sistema.</p>';
                    $email = new EmailController();
                    $email->send('soporte@mifacturalegal.com', 'Soporte', 'pruebasapp@iplanetcolombia.com', 'MFL - Excepciones', $Body);
                    continue;
                }
                if (stristr($cliente->Concepto, 'Básico')) {
                    $pack = 5;
                } elseif (stristr($cliente->Concepto, 'Emprendedor')) {
                    $pack = 1;
                } elseif (stristr($cliente->Concepto, 'Empresarial')) {
                    $pack = 2;
                } elseif (stristr($cliente->Concepto, 'Premium')) {
                    $pack = 3;
                } elseif (stristr($cliente->Concepto, 'Gold')) {
                    $pack = 6;
                }
                try {
                    if (!isset($pack) || $pack == '') {
                        throw new \Exception('No se encuentra el Paquete que desea utilizar ');
                    }
                } catch (\Exception $e) {
                    array_push($errores, 'Excepción: ' . $e->getMessage());
                    //correo
                    $Body = '<p>Buen día.</p><p>Empresa ya registrada en el sistema.</p>';
                    $email = new EmailController();
                    $email->send('soporte@mifacturalegal.com', 'Soporte', 'pruebasapp@iplanetcolombia.com', 'MFL - Excepciones', $Body);
                    continue;
                }

                $date = date('Y-m-d');
                $nuevafecha = strtotime('+1 year', strtotime($date));
                $nuevafecha = date('Y-m-d', $nuevafecha);

                $packages = new Packages();
                $package = $packages->where(['id' => $pack])->get()->getResult()[0];

                $data = [
                    'application_date' => date('Y-m-d H:i:s'),
                    'company_name' => $cliente->nombre,
                    'nit' => $cliente->documento,
                    'phone' => $cliente->telefono,
                    'adress' => $cliente->direccion . ',' . $cliente->ciudad,
                    'legal_representative' => (isset($cliente->apellido)) ? $cliente->nombre : $cliente->nombre . ' ' . $cliente->apellido,
                    'type_document' => 'cc',
                    'num_documento' => $cliente->documento,
                    'email' => $cliente->email,
                    'email_confirmation' => '',
                    'contract' => '',
                    'autorizacion' => '',
                    'status' => 1,
                    'seller' => 10
                ];

                $applicant = $applicants->insert($data);
                $data = [
                    'companies_id' => '',
                    'applicant_id' => $applicant,
                    'packages_id' => $pack,
                    'start_date' => date('Y-m-d H:i:s'),
                    'end_date' => $nuevafecha,
                    'status' => 'Activo',
                    'date_due_certificate' => $nuevafecha,
                    'sopport_invoice' => '',
                    'ref_epayco' => '',
                    'price' => $cliente->Recaudo,
                    'seller' => 10,
                    'seller_tip' => ''

                ];

                $subscription = new Subscription();
                $subscription->save($data);
                $data1 = [
                    'applicant_id' => $applicant,
                    'documento' => 'Comprobante de pago',
                    'archivo' => 'pago actualicese',
                    'status' => 'Aprobado'
                ];
                $applicant_documents = new Applicant_documents();
                $applicant_documents->save($data1);

                //correo
                $Body = '<p>Buen día ' . $cliente->nombre . '.<br /><br />En primer lugar, queremos agradecerle por escogernos como su aliado de facturación electrónica con validación previa de la DIAN. Software adquirido a través de la plataforma de Actualicese.com.</p>';
                $Body .= '<p>A continuación, encontrara el detalle del proceso completo:</p>';
                $Body .= '<p><strong>Paso 1</strong>: Registrar la empresa. Ingrese los datos en el siguiente formulario ( <span><a href="' . base_url() . '/solicitudes/datos/' . $applicant . '">clic al enlace</a></span> ).</p>';
                $Body .= '<p><p><strong>Paso 2</strong>: Cargar documentos. Le llegará; un correo después de registrar la empresa solicitándole la siguiente documentación:</p></p>';
                $Body .= '<ol>
                            <li>RUT original.</li>
                            <li>Fotocopia de documento de identidad del representante legal.</li>
                            <li>Cámara de Comercio con vigencia no mayor a 30 días (No aplica para personas naturales).</li>
                            <li>Autorización Solicitud Certificado de Componente y Contrato de Suscripción para Emisión de Certificados Digital.</li>
                            <li>Resolución de facturación electrónica emitida por la Dian.<br /></li>
                           </ol>';
                $Body .= '<p><strong>Paso 3</strong>: Estar pendiente del correo electrónico con los accesos al software de facturación.
                          <br><strong>Recuerde que:</strong></p>';
                $Body .= '<ul>
                            <li>Puede programar una capacitación de uso del software mientras se genera la activación. Para solicitarla, por favor escribir al correo: <span><a href="mailto:soporte@mifacturalegal.com">soporte@mifacturalegal.com</a></span> o al Whatsapp 301 3207088.</li>
                          </ul>';
                $Body .= '<p><strong>Información Adicional</strong></p>';
                $Body .= '<p>En el momento que usted prefiera, puede ingresar a nuestro sistema de pruebas, o si nos permite le agendamos una cita para que pueda realizar una prueba guiada.</p>';
                $Body .= '<ul>
                            <li><u><a href="https://planeta-internet.com/mifacturalegal/public/">Acceso a la plataforma de pruebas</a></u></li>
                            <li>Usuario: <strong>pruebas</strong></li>
                            <li>Clave: <strong>123456789</strong></li>
                          </ul>';
                $Body .= '<p>Quedamos atentos a su pronta respuesta, si tiene alguna inquietud por favor enviarla al correo: soporte@mifacturalegal.com o escribir a Whatsapp a los números 301 3207088 | 314 2957896 estaremos pendientes.<br /><br />Equipo de Soporte<br />MiFacturaLegal.com</p>';
                $email = new EmailController();
                $email->send('soporte@mifacturalegal.com', $cliente->nombre, 'pruebasapp@iplanetcolombia.com', 'Bienvenid@ a MiFacturaLegal.com – Su software de facturación electrónica.', $Body);

            }

        } else {
            //correo
            $Body = '<p>Buen día.</p><p>No se encuentran datos para registrar.</p>';
            $email = new EmailController();
            $email->send('soporte@mifacturalegal.com', 'Soporte', 'pruebasapp@iplanetcolombia.com', 'No se encuentran datos para registrar', $Body);
        }


    }

    public function edit($solicitud)
    {
        $applicants = new Applicant();
        $applicant = $applicants->join('subscriptions', 'applicant.id = subscriptions.applicant_id')->where(['applicant.id' => $solicitud])->get()->getResult();
        $paquetes = new Packages();
        $paquete = $paquetes->where(['id !=' => 4])->orderBy('price', 'ASC')->get()->getResult();
        $tdocumentos = new TypeDocumentIdentifications();
        $Tdocumento = $tdocumentos->get()->getResult();
        $data = [
            'paquetes' => $paquete,
            'tdocumentos' => $Tdocumento,
            'solicitante' => $applicant
        ];
        echo view('solicitudes/registro', $data);
    }

    public function store($solicitud)
    {
        $applicant = new Applicant();
        // actualizar solicitante
        $dataApplicant = [
            'company_name' => $this->request->getPost('nempresa'),
            'nit' => $this->request->getPost('nit'),
            'adress' => $this->request->getPost('direccion'),
            'legal_representative' => $this->request->getPost('rl'),
            'type_document' => $this->request->getPost('tdocumento'),
            'num_documento' => $this->request->getPost('documento'),
            'email' => $this->request->getPost('email'),
            'email_confirmation' => (isset($_POST['emailem'])) ? $this->request->getPost('emailem') : '',
            'status' => 1
        ];
        $applicant->set($dataApplicant)->where(['id' => $solicitud])->update();

        //Agregar a compáñia
        $data = [
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
        $company = new Company();
        $idc = $company->insert($data);

        // actualizar subscipcion

        $dataSubscription = [
            'companies_id' => $idc,
        ];

        $subscription = new Subscription();
        $subscription->set($dataSubscription)->where(['applicant_id' => $solicitud])->update();

    }
}