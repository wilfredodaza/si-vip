<?php


namespace App\Controllers;

use App\Models\Packages;
use App\Models\Sellers;
use App\Models\Subscription;
use App\Models\Tracing;
use App\Models\TypeDocumentIdentifications;
use Config\Services;
use App\Models\Config;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Tooltips;
use App\Models\Applicant;
use App\Models\Resolution;
use App\Models\LineInvoice;
use App\Models\Customize_mail;
use App\Models\LineInvoiceTax;
use App\Controllers\BaseController;
use App\Models\Applicant_documents;
use App\Models\Applicant_subscription;
use App\Controllers\Configuration\EmailController;
use App\Traits\SubscriptionTrait;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class SolicitudesController extends BaseController
{
    use  SubscriptionTrait;
    //vista de todas las solicitudes realizadas
    public function index()
    {

        $applicant = new Applicant();
        $solicitudes = $applicant->select('*, applicant_status.status as estado, applicant.status as vestado, applicant.id as idsolicitud, sellers.id as idsellers, sellers.name as nameseller, subscriptions.id as idsubscription, packages.name as paquete')
            ->like($this->search(), isset($_GET['value']) ? $this->request->getGet('value') : '', 'both')
            ->join('applicant_status', 'applicant.status = applicant_status.id')
            ->join('sellers', 'applicant.seller = sellers.id')
            ->join('subscriptions', 'applicant.id = subscriptions.applicant_id')
            ->join('packages', 'packages.id = subscriptions.packages_id')
            ->orderBy('applicant.id', 'desc');
        $applicant = new Applicant();
        $totals = $applicant->select('*, applicant_status.status as estado, applicant.status as vestado, applicant.id as idsolicitud, sellers.id as idsellers, sellers.name as nameseller, subscriptions.id as idsubscription, packages.name as paquete')
            ->like($this->search(), isset($_GET['value']) ? $this->request->getGet('value') : '', 'both')
            ->join('applicant_status', 'applicant.status = applicant_status.id')
            ->join('sellers', 'applicant.seller = sellers.id')
            ->join('subscriptions', 'applicant.id = subscriptions.applicant_id')
            ->join('packages', 'packages.id = subscriptions.packages_id')
            ->countAllResults();


        $data = [
            'solicitudes' => $solicitudes->paginate(10),
            'pager' => $solicitudes->pager,
            'count' => $totals
        ];
        echo view('solicitudes/solicitudes', $data);
    }

    //funcion para buscar la solicitud correspondiente
    public function search()
    {
        if (isset($_GET['campo'])) {
            switch ($_GET['campo']) {
                case 'solicitud':
                    $campo = 'applicant.id';
                    break;
                case 'fecha':
                    $campo = 'applicant.application_date';
                    break;
                case 'empresa':
                    $campo = 'applicant.company_name';
                    break;
                case 'plan':
                    $campo = 'packages.name';
                    break;
                case 'vendedor':
                    $campo = 'sellers.name';
                    break;
                case 'estado':
                    $campo = 'applicant_status.status';
                    break;
                case 'fechae':
                    $campo = 'applicant.updated_at';
                    break;
                case 'process':
                    $campo = 'applicant.process';
                    break;
            }

        } else {
            $campo = 'applicant.company_name';
        }
        return $campo;
    }

    //idicion de solicitud
    public function edit($id)
    {

        $request = Services::request();
        $applicant = new Applicant();

        if ($applicant->set(['company_name' => $request->getPost('nempresa'),
            'nit' => $request->getPost('nit'),
            'adress' => $request->getPost('direccion'),
            'email' => $request->getPost('correo'),
            'email_confirmation' => $request->getPost('correoem'),
            'legal_representative' => $request->getPost('rl'),
            'type_document' => $request->getPost('tdocumento'),
            'num_documento' => $request->getPost('documento')
        ])
            ->where(['id' => $id])
            ->update()) {
            return redirect()->to(base_url() . '/solicitudes/info/' . $id)->with('success', 'Datos editados correctamente');
        } else {
            return redirect()->to(base_url() . '/solicitudes/info/' . $id)->with('error', 'No se editaron los datos');
        }

    }

    //funcion que nos permite ver la informacion del solicitante deacuerdo al estado en el que se encuentre ---------------------
    public function info($id)
    {
        $documentos = '';
        $img = '';
        $pago = '';
        $applicant = new Applicant();
        $solicitud = $applicant->select('*,applicant_status.status as estado,applicant.status as vestado,applicant.id as idsolicitud, sellers.id as idsellers, sellers.name as nameseller,')
            ->join('applicant_status', 'applicant.status = applicant_status.id')
            ->join('sellers', 'applicant.seller = sellers.id')
            ->where(['applicant.id' => $id])
            ->get()
            ->getResult();

        //if ($solicitud[0]->estado != 'Solicitada' && $solicitud[0]->estado != 'Solicitud de Documentos') {

            $applicant_documents = new Applicant_documents();
            $documentos = $applicant_documents->select('*')->where(['applicant_id' => $id, 'documento <>' => 'prueba1'])->where(['documento <>' => 'prueba2'])->get()->getResult();
            $img = base_url() . '/upload/documentos/' . $solicitud[0]->nit . '/';

            $pagos = new Applicant_subscription();
            $cantidadPago = $pagos->select('*')->join('packages', 'packages.id = subscriptions.packages_id')->where(['subscriptions.applicant_id' => $id])->countAllResults();
            if ($cantidadPago > 0) {
                $pago = $pagos->select('*')->join('packages', 'packages.id = subscriptions.packages_id')->where(['subscriptions.applicant_id' => $id])->orderBy('subscriptions.id', 'Desc')->get()->getResult()[0];
            }


        //}



        $tracings= new Tracing();
        $tracing = $tracings->join('users', 'tracing.user_id = users.id')
            ->where(['tracing.applicant_id' => $id])
            ->orderBy('tracing.id','DESC')
            ->get()->getResult();

        return view('solicitudes/editar_solicitud', ['solicitud' => $solicitud, 'documentos' => $documentos, 'ruta_img' => $img, 'pago' => $pago, 'tracings' => $tracing]);
    }

    //cargue de documento de autorizacion y contrato---------------------------------
    public function cargarArchivos($id)
    {

        $applicant = new Applicant();
        //se traen los datos de la solictud
        $solicitud = $applicant->select('*,applicant_status.status as estado,applicant.status as vestado,applicant.id as idsolicitud')
            ->join('applicant_status', 'applicant.status = applicant_status.id')
            ->where(['applicant.id' => $id])->get()
            ->getResult();

        //se cragan las dos archivos
        if ($img1 = $this->request->getFile('autorizacion')) {
            $autorizacion = $img1->getRandomName();
            $ext1 = $img1->getExtension();
            $img1->move('upload/conyauto/'.$solicitud[0]->nit, $autorizacion);
            rename("upload/conyauto/" . $solicitud[0]->nit . "/" . $autorizacion, "upload/conyauto/" . $solicitud[0]->nit . "/autorizacion_" . $solicitud[0]->nit . "." . $ext1);
        }

        if ($img2 = $this->request->getFile('contrato')) {
            $contrato = $img2->getRandomName();
            $ext2 = $img2->getExtension();
            $img2->move('upload/conyauto/' . $solicitud[0]->nit, $contrato);

            rename("upload/conyauto/" . $solicitud[0]->nit . "/" . $contrato, "upload/conyauto/" . $solicitud[0]->nit . "/contrato_" . $solicitud[0]->nit . "." . $ext2);
	
        }

        //se realiza la actualizacion de contrato y de autorizacion en la base de datos

        if ($applicant->set(['contract' => "contrato_" . $solicitud[0]->nit . "." . $ext1, 'autorizacion' => "autorizacion_" . $solicitud[0]->nit . "." . $ext2])
            ->where(['id' => $id])
            ->update()) {
            $customizeMail = new Customize_mail();

            //se cargan los archivos correspondiente para ser enviado a las empresa
            $archivos = [
                'autorizacion' => base_url() . '/upload/conyauto/' . $solicitud[0]->nit . '/autorizacion_' . $solicitud[0]->nit . '.' . $ext1,
                'Contrato' => base_url() . '/upload/conyauto/' . $solicitud[0]->nit . '/contrato_' . $solicitud[0]->nit . '.' . $ext2
            ];


            //se trean los datos de la plantilla
            $datosCorreo = $customizeMail->select('*')
                ->where(['type_email' => 'Solicitud de documentos'])->get()
                ->getResult();
            $email = new EmailController();

            //se reemplazan la variables de entrorno en la plantilla
            $url = base_url() . '/solicitud/informacion/' . $id;
            $asunto = str_replace('${NAME}', $solicitud[0]->company_name, $datosCorreo[0]->subjetc);
            $nombre = str_replace('${NAME}', $solicitud[0]->legal_representative, $datosCorreo[0]->body);
            $empresa = str_replace('${COMPANY}', $solicitud[0]->company_name, $nombre);
            $cuerpo = str_replace('${LINK}', $url, $empresa);
		


            //se realiza el envio del correo electronico
            $email->send('no-responder@mifacturalegal.com', 'prueba', $solicitud[0]->email, $asunto, $cuerpo, $archivos);

            //cambiamos el estado
            $applicant->set(['status' => 2])
                ->where(['id' => $id])
                ->update();

            return redirect()->to(base_url() . '/solicitudes/info/' . $id)->with('success', 'Archivos Guardados con exíto y se envio un correo con los pasos correspondientes a la empresa.');
        } else {
            return redirect()->to(base_url() . '/solicitudes/info/' . $id)->with('error', 'No se lograron subir los archivos');
        }

    }

    //cambio de estado documento validado
    public function estadoDocumento()
    {
        $id = $_POST['id'];
        $estado = $_POST['estado'];


        $applicant_documents = new Applicant_documents();
        if ($applicant_documents->set(['status' => $estado])
            ->where(['id' => $id])
            ->update()) {
            return json_encode('Se cambio estado con exito');
        }
    }

    public function validacion($id)
    {
        $applicant = new Applicant();
        $solicitud = $applicant->select('*,applicant_status.status as estado,applicant.status as vestado,applicant.id as idsolicitud')
            ->join('applicant_status', 'applicant.status = applicant_status.id')
            ->where(['applicant.id' => $id])->get()
            ->getResult();


        $customizeMail = new Customize_mail();
        $datosCorreo = $customizeMail->select('*')
            ->where(['type_email' => 'Validacion de datos'])->get()
            ->getResult();
        $asunto = str_replace('${NAME}', $solicitud[0]->company_name, $datosCorreo[0]->subjetc);
        $email = new EmailController();
        if ($applicant->set(['status' => 4])->where(['id' => $id])->update()) {
            $email->send('no-responder@mifacturalegal.com', 'prueba', $solicitud[0]->email, $asunto, $datosCorreo[0]->body);
            return redirect()->to(base_url() . '/solicitudes/info/' . $id)->with('success', 'Archivos Validados, se envió un correo con los pasos correspondientes a la empresa.');
        } else {
            return redirect()->to(base_url() . '/solicitudes/info/' . $id)->with('error', 'No se logro guardar correctamente la validacion');
        }

    }

    public function editarArchivos($id, $iddocumento)
    {

        $applicant = new Applicant();
        $applicant_documents = new Applicant_documents();
        //se traen los datos de la solictud
        $solicitud = $applicant->select('*,applicant_status.status as estado,applicant.status as vestado,applicant.id as idsolicitud')
            ->join('applicant_status', 'applicant.status = applicant_status.id')
            ->where(['applicant.id' => $id])->get()
            ->getResult();



        $request = Services::request();
        $documento = $request->getPost('documento');

        if ($img = $this->request->getFile('edicion')) {
            $archivo = $img->getRandomName();
            $ext = $img->getExtension();
            $img->move('upload/documentos/' . $solicitud[0]->nit, $archivo);
            rename("upload/documentos/" . $solicitud[0]->nit . "/" . $archivo, "upload/documentos/" . $solicitud[0]->nit . "/" . $documento . "_" . $solicitud[0]->nit . "." . $ext);

        }

        if ($applicant_documents->set(['documento' => $documento, 'archivo' => $documento . "_" . $solicitud[0]->nit . "." . $ext])
            ->where(['id' => $iddocumento])
            ->update()) {
            return redirect()->to(base_url() . '/solicitudes/info/' . $id)->with('success', 'Archivo editado con exíto.');
        } else {
            return redirect()->to(base_url() . '/solicitudes/info/' . $id)->with('error', 'No se logró editar el archivo.');
        }
    }

    //envio de credenciales y pruebas
    public function pruebaCredenciles($id)
    {

        $applicant = new Applicant();
        $request = Services::request();
        $usuario = $request->getPost('usuario');
        $clave = $request->getPost('clave');
        $applicant_documents = new Applicant_documents();
        $solicitud = $applicant->select('*,applicant_status.status as estado,applicant.status as vestado,applicant.id as idsolicitud')
            ->join('applicant_status', 'applicant.status = applicant_status.id')
            ->where(['applicant.id' => $id])->get()
            ->getResult();
        if ($img = $this->request->getFile('prueba')) {
            if ($img->isValid()) {
                $archivo = $img->getRandomName();
                $ext1 = $img->getExtension();
                $img->move('upload/documentos/' . $solicitud[0]->nit, $archivo);
                rename("upload/documentos/" . $solicitud[0]->nit . "/" . $archivo, "upload/documentos/" . $solicitud[0]->nit . "/prueba1_" . $solicitud[0]->nit . "." . $ext1);
                $prueba1 = "prueba1_" . $solicitud[0]->nit . "." . $ext1;
                $data1 = [
                    'applicant_id' => (int)$solicitud[0]->idsolicitud,
                    'documento' => 'prueba1',
                    'archivo' => $prueba1,
                    'status' => 'Aprobado'
                ];
            }
        }
        if ($img = $this->request->getFile('pruebas')) {
            if ($img->isValid()) {
                $archivo = $img->getRandomName();
                $ext2 = $img->getExtension();
                $img->move('upload/documentos/' . $solicitud[0]->nit, $archivo);
                rename("upload/documentos/" . $solicitud[0]->nit . "/" . $archivo, "upload/documentos/" . $solicitud[0]->nit . "/prueba2_" . $solicitud[0]->nit . "." . $ext2);
                $prueba2 = "prueba2_" . $solicitud[0]->nit . "." . $ext2;
                $data2 = [
                    'applicant_id' => (int)$solicitud[0]->idsolicitud,
                    'documento' => 'prueba2',
                    'archivo' => $prueba2,
                    'status' => 'Aprobado'
                ];
            }
        }

        if ($img = $this->request->getFile('certificado')) {
            if ($img->isValid()) {
                $archivo = $img->getRandomName();
                $ext = $img->getExtension();
                $img->move('upload/documentos/' . $solicitud[0]->nit, $archivo);
                rename("upload/documentos/" . $solicitud[0]->nit . "/" . $archivo, "upload/documentos/" . $solicitud[0]->nit . "/certificadothomas_" . $solicitud[0]->nit . "." . $ext);
                $certificado = "certificadothomas_" . $solicitud[0]->nit . "." . $ext;
                $data3 = [
                    'applicant_id' => (int)$solicitud[0]->idsolicitud,
                    'documento' => 'certificado',
                    'archivo' => $certificado,
                    'status' => 'Aprobado'
                ];
            }
        }
        // datos correspondientes del cliente
        $datos = $applicant->select('*,applicant_status.status as estado,applicant.status as vestado,applicant.id as idsolicitud, packages.name as paquete')
            ->join('applicant_status', 'applicant.status = applicant_status.id')
            ->join('subscriptions', 'applicant.id = subscriptions.applicant_id')
            ->join('packages', 'packages.id = subscriptions.packages_id')
            ->where(['applicant.id' => $id])->get()
            ->getResult()[0];


        $customizeMail = new Customize_mail();
        $email = new EmailController();
        if($solicitud[0]->process != 'renovacion'){
            //fotos para correo
            $foto1 = 'https://facturadorv2.mifacturalegal.com/upload/documentos/' . $solicitud[0]->nit . '/prueba1_' . $solicitud[0]->nit . '.' . $ext1;
            $foto2 = 'https://facturadorv2.mifacturalegal.com/upload/documentos/' . $solicitud[0]->nit . '/prueba2_' . $solicitud[0]->nit . '.' . $ext2;
            $datosCorreo = $customizeMail->select('*')
                ->where(['type_email' => 'Evidencias y credenciales'])->get()
                ->getResult();
            //se reemplazan la variables de entrorno en la plantilla
            $asunto = str_replace('${NAME}', $solicitud[0]->company_name, $datosCorreo[0]->subjetc);

            $prueba1 = str_replace('${PRUEBA1}', '<img src="' . $foto1 . '">', $datosCorreo[0]->body);
            $prueba2 = str_replace('${PRUEBA2}', '<img src="' . $foto2 . '">', $prueba1);
            $plan = str_replace('${PLAN}', $datos->paquete, $prueba2);
            $cantidad = str_replace('${CANTIDAD}', $datos->quantity_document, $plan);
            $vigencia = str_replace('${VIGENCIA}', $datos->end_date, $cantidad);
            $user = str_replace('${USUARIO}', $usuario, $vigencia);
            $pass = str_replace('${CLAVE}', $clave, $user);

            //adjuntos
            $archivos = [
                'Manual' => base_url() . '/upload/manual/Manual.pdf',
                'Certificado' => base_url() . '/upload/documentos/' . $solicitud[0]->nit . '/' . $certificado
            ];
            if ($applicant_documents->save($data1) && $applicant_documents->save($data2) && $applicant_documents->save($data3)) {
                $email->send('no-responder@mifacturalegal.com', 'prueba', $solicitud[0]->email, $asunto, $pass, $archivos);
                $applicant->set(['status' => 6])
                    ->where(['id' => $id])
                    ->update();
                return redirect()->to(base_url() . '/solicitudes/info/' . $id)->with('success', 'Evidencias y Credenciales enviadas con Exito');
            } else {
                return redirect()->to(base_url() . '/solicitudes/info/' . $id)->with('error', 'No se logró enviar evidencias y credenciales con exito.');
            }
        }else{

            $datosCorreo = $customizeMail->select('*')
                ->where(['type_email' => 'Renovacion'])->get()
                ->getResult();
            //se reemplazan la variables de entrorno en la plantilla
            $asunto = str_replace('${NAME}', $solicitud[0]->company_name, $datosCorreo[0]->subjetc);
            $plan = str_replace('${PLAN}', $datos->name, $datosCorreo[0]->body);
            $cantidad = str_replace('${CANTIDAD}', $datos->quantity_document, $plan);
            $vigencia = str_replace('${VIGENCIA}', $datos->end_date, $cantidad);
            //adjuntos
            $archivos = [
                'Manual' => base_url() . '/upload/manual/Manual.pdf',
                'Certificado' => base_url() . '/upload/documentos/' . $solicitud[0]->nit . '/' . $certificado
            ];
            if ($applicant_documents->save($data3)) {
                $email->send('no-responder@mifacturalegal.com', 'soporte@mifacturalegal.com', $solicitud[0]->email, $asunto, $vigencia, $archivos);
                $applicant->set(['status' => 6])
                    ->where(['id' => $id])
                    ->update();
                return redirect()->to(base_url() . '/solicitudes/info/' . $id)->with('success', 'Renovación enviada con Exito');
            } else {
                return redirect()->to(base_url() . '/solicitudes/info/' . $id)->with('error', 'No se logró enviar la renovación con exito.');
            }
        }

    }

    public function informacionCliente($id)
    {
        //se traen los datos de la solictud
        $applicant = new Applicant();
        $solicitud = $applicant->select('*,applicant_status.status as estado,applicant.status as vestado,applicant.id as idsolicitud, packages.name as paquete')
            ->join('applicant_status', 'applicant.status = applicant_status.id')
            ->join('subscriptions', 'applicant.id = subscriptions.applicant_id')
            ->join('packages', 'packages.id = subscriptions.packages_id')
            ->where(['applicant.id' => $id])->get()
            ->getResult()[0];
        //informacion de datos enviados por la empresa
        $applicant_documents = new Applicant_documents();
        $documentos = $applicant_documents->select('*')->where(['applicant_id' => $solicitud->idsolicitud, 'documento <>' => 'prueba1'])->where(['documento <>' => 'prueba2'])->countAllResults();
        $cantidad = $documentos;
        if ($documentos <= 0) {
            $documento = 'no';
        } else {
            $documento = $applicant_documents->select('*')->where(['applicant_id' => $solicitud->idsolicitud, 'documento <>' => 'prueba1'])->where(['documento <>' => 'prueba2'])->get()->getResult();
        }
        //informacion de pago
        $pagos = new Applicant_subscription();
        $cantidadPago = $pagos->select('*')->join('packages', 'packages.id = subscriptions.packages_id')->where(['subscriptions.applicant_id' => $solicitud->idsolicitud])->countAllResults();
        if ($cantidadPago > 0) {
            $pago = $pagos->select('*')->join('packages', 'packages.id = subscriptions.packages_id')->where(['subscriptions.applicant_id' => $solicitud->idsolicitud])->orderBy('subscriptions.id', 'Desc')->get()->getResult()[0];
        } else {
            $pago = 'no';
        }
        $paquetes = new Packages();
        $paquete = $paquetes->orderBy('price', 'ASC')->get()->getResult();

        //informacion de ayuda
        $ayuda = new Tooltips();
        $ayudas = $ayuda->select('*')->get()->getResult();
        $data = [
            'datos' => $solicitud,
            'ayudas' => $ayudas,
            'id' => $solicitud->idsolicitud,
            'documentos' => $documento,
            'cantidad' => $cantidad,
            'pago' => $pago,
            'paquetes' => $paquete
        ];
        echo view('solicitudes/informacion', $data);
    }

    //documentos enviados por el cliente
    public function guardarDocumentos($id)
    {
        $request = Services::request();
        $correo = $request->getPost('correo');
        $applicant = new Applicant();
        $applicant_documents = new Applicant_documents();
        $solicitud = $applicant->select('*,applicant_status.status as estado,applicant.status as vestado,applicant.id as idsolicitud')
            ->join('applicant_status', 'applicant.status = applicant_status.id')
            ->where(['applicant.id' => $id])->get()
            ->getResult()[0];

        //valor de pago
        $pagos = new Applicant_subscription();
        $cantidadPago = $pagos->select('*')->join('packages', 'packages.id = subscriptions.packages_id')->where(['subscriptions.applicant_id' => $solicitud->idsolicitud])->countAllResults();
        if ($cantidadPago > 0) {
            $pago = $pagos->select('*')->join('packages', 'packages.id = subscriptions.packages_id')->where(['subscriptions.applicant_id' => $solicitud->idsolicitud])->orderBy('subscriptions.id', 'Desc')->get()->getResult()[0];
        } else {
            $pago = 'no';
        }

        //$pago pendiente
        $ad= $applicant_documents->where(['applicant_id'=> $solicitud->idsolicitud, 'documento'=>'Comprobante de pago'])->get()->getResult()[0];

        //rut
        if ($img = $this->request->getFile('rut')) {
            $rut = $img->getRandomName();
            $ext = $img->getExtension();
            $img->move('upload/documentos/' . $solicitud->nit, $rut);
            rename("upload/documentos/" . $solicitud->nit . "/" . $rut, "upload/documentos/" . $solicitud->nit . "/rut_" . $solicitud->nit . "." . $ext);
            $a_rut = "rut_" . $solicitud->nit . "." . $ext;
            $data_rut = [
                'applicant_id' => $solicitud->idsolicitud,
                'documento' => 'Rut',
                'archivo' => $a_rut,
                'status' => 'Pendiente'
            ];
            $applicant_documents->save($data_rut);
        }
        //camara de comercio
        if ($img1 = $this->request->getFile('cc')) {
            if ($img1->isValid()){
                $camaraComercio = $img1->getRandomName();
                $ext1 = $img1->getExtension();
                $img1->move('upload/documentos/' . $solicitud->nit, $camaraComercio);
                rename("upload/documentos/" . $solicitud->nit . "/" . $camaraComercio, "upload/documentos/" . $solicitud->nit . "/camaraComercio_" . $solicitud->nit . "." . $ext1);
                $a_camaraComercio = "camaraComercio_" . $solicitud->nit . "." . $ext1;
                $data_camaraComercio = [
                    'applicant_id' => (int)$solicitud->idsolicitud,
                    'documento' => 'Camara de comercio',
                    'archivo' => $a_camaraComercio,
                    'status' => 'Pendiente'
                ];
                $applicant_documents->save($data_camaraComercio);
            }
        }
        //Cedula representante
        if ($img2 = $this->request->getFile('cr')) {
            $cedulaRepresentante = $img2->getRandomName();
            $ext2 = $img2->getExtension();
            $img2->move('upload/documentos/' . $solicitud->nit, $cedulaRepresentante);
            rename("upload/documentos/" . $solicitud->nit . "/" . $cedulaRepresentante, "upload/documentos/" . $solicitud->nit . "/cedulaRepresentante_" . $solicitud->nit . "." . $ext2);
            $a_cedulaRepresentante = "cedulaRepresentante_" . $solicitud->nit . "." . $ext2;
            $data_cedulaRepresentante = [
                'applicant_id' => (int)$solicitud->idsolicitud,
                'documento' => 'Cedula representante',
                'archivo' => $a_cedulaRepresentante,
                'status' => 'Pendiente'
            ];
            $applicant_documents->save($data_cedulaRepresentante);
        }
        //contrato firma
        if ($img3 = $this->request->getFile('cfirmad')) {
            $contratoFirma = $img3->getRandomName();
            $ext3 = $img3->getExtension();
            $img3->move('upload/documentos/' . $solicitud->nit, $contratoFirma);
            rename("upload/documentos/" . $solicitud->nit . "/" . $contratoFirma, "upload/documentos/" . $solicitud->nit . "/contratoFirma_" . $solicitud->nit . "." . $ext3);
            $a_contratoFirma = "contratoFirma_" . $solicitud->nit . "." . $ext3;
            $data_contratoFirma = [
                'applicant_id' => (int)$solicitud->idsolicitud,
                'documento' => 'Contrato firma',
                'archivo' => $a_contratoFirma,
                'status' => 'Pendiente'
            ];
            $applicant_documents->save($data_contratoFirma);
        }
        //autorizacion firma
        if ($img4 = $this->request->getFile('afirmad')) {
            $autorizacionFirma = $img4->getRandomName();
            $ext4 = $img4->getExtension();
            $img4->move('upload/documentos/' . $solicitud->nit, $autorizacionFirma);
            rename("upload/documentos/" . $solicitud->nit . "/" . $autorizacionFirma, "upload/documentos/" . $solicitud->nit . "/autorizacionFirma_" . $solicitud->nit . "." . $ext4);
            $a_autorizacionFirma = "autorizacionFirma_" . $solicitud->nit . "." . $ext4;
            $data_autorizacionFirma = [
                'applicant_id' => (int)$solicitud->idsolicitud,
                'documento' => 'Autorizacion firma',
                'archivo' => $a_autorizacionFirma,
                'status' => 'Pendiente'
            ];
            $applicant_documents->save($data_autorizacionFirma);
        }
        //Resolucion Dian
        if ($img5 = $this->request->getFile('rd')) {
            if ($img5->isValid()){
                $resolucionDian = $img5->getRandomName();
                $ext5 = $img5->getExtension();
                $img5->move('upload/documentos/' . $solicitud->nit, $resolucionDian);
                rename("upload/documentos/" . $solicitud->nit . "/" . $resolucionDian, "upload/documentos/" . $solicitud->nit . "/resolucionDian_" . $solicitud->nit . "." . $ext5);
                $a_resolucionDian = "resolucionDian_" . $solicitud->nit . "." . $ext5;
                $data_resolucionDian = [
                    'applicant_id' => (int)$solicitud->idsolicitud,
                    'documento' => 'Resolucion Dian',
                    'archivo' => $a_resolucionDian,
                    'status' => 'Pendiente'
                ];
                $applicant_documents->save($data_resolucionDian);
            }
        }
        //Comprobante de Pago
        if($pago == 'no' || $ad->status == 'Desaprobado'){
            if($img6 = $this->request->getFile('cp')){
                $comprobantePago = $img6->getRandomName();
                $ext6 = $img6->getExtension();
                $img6->move('upload/documentos/'. $solicitud->nit, $comprobantePago);
                rename("upload/documentos/". $solicitud->nit."/".$comprobantePago, "upload/documentos/". $solicitud->nit."/comprobantePago_". $solicitud->nit.".".$ext6);
                $a_comprobantePago="comprobantePago_". $solicitud->nit.".".$ext6;
                $data_comprobantePago=[
                    'applicant_id' => (int)$solicitud->idsolicitud,
                    'documento' => 'Comprobante de pago',
                    'archivo' => $a_comprobantePago,
                    'status' => 'Desaprobado'
                ];
                $applicant_documents->save($data_comprobantePago);
            }
        }

        if ($applicant->set(['status' => 3])
            ->where(['id' => $id])
            ->update()) {
            return redirect()->to(base_url() . '/solicitud/informacion/' . $id)->with('success', 'Documentos enviados con éxito, serán validados por nosotros y enviados para la creación de la firma digital. Por favor estar pendiente de su correo electrónico.');
        } else {
            return redirect()->to(base_url() . '/solicitud/informacion/' . $id)->with('error', 'No se lograron subir los documentos correctamente.');
        }

    }

    public function Reenvio($id)
    {
        $applicant = new Applicant();
        $solicitud = $applicant->select('*,applicant_status.status as estado,applicant.status as vestado,applicant.id as idsolicitud')
            ->join('applicant_status', 'applicant.status = applicant_status.id')
            ->where(['applicant.id' => $id])->get()
            ->getResult()[0];
        $estado = $solicitud->vestado - 1;
        if ($applicant->set(['status' => $estado])
            ->where(['id' => $id])
            ->update()) {
            return redirect()->to(base_url() . '/solicitudes/info/' . $solicitud->idsolicitud);
        } else {
            return redirect()->to(base_url() . '/solicitudes/info/' . $solicitud->idsolicitud);
        }

    }

    public function Registro()
    {
        $paquetes = new Packages();
        $paquete = $paquetes->where(['id !=' => 4])->orderBy('price', 'ASC')->get()->getResult();
        $tdocumentos = new TypeDocumentIdentifications();
        $Tdocumento = $tdocumentos->get()->getResult();
        $data = [
            'paquetes' => $paquete,
            'tdocumentos' => $Tdocumento
        ];
        if(isset($_GET['renv'])){
            $applicants = new Applicant();
            $applicant = $applicants->where(['id' => $_GET['renv']])->get()->getResultObject();
            $data['applicant'] = $applicant;
        }
        //echo json_encode($data);die();
        echo view('solicitudes/registro', $data);
    }

    public function expiredSubscription()
    {
        $data = ['sub.status'  => 'Activo'];
        if(!is_null($this->request->getGet('company_id')) && !empty($this->request->getGet('company_id'))) {
            $data['companies.id'] = $this->request->getGet('company_id');
        }

        if(!is_null($this->request->getGet('start_date')) && !empty($this->request->getGet('start_date'))) {
            $data['sub.date_due_certificate >='] = $this->request->getGet('start_date');
        }

        if(!is_null($this->request->getGet('end_date')) && !empty($this->request->getGet('end_date'))) {
            $data['sub.date_due_certificate <='] = $this->request->getGet('end_date');
        }
 


        $model = new Company();
        $subscriptions = $model->select([
            'companies.company', 
            'sub.date_due_certificate',
            'sub.end_date', 
            'sub.start_date',
            'packages.name as package_name',
            'packages.quantity_document',
           '(SELECT count(*) FROM invoices WHERE invoices.type_documents_id IN (1, 2, 3 , 4, 5, 9, 10, 101, 102, 103, 104) AND
            invoices.invoice_status_id IN (2, 3, 4, 14) AND invoices.companies_id = companies.id AND invoices.created_at BETWEEN sub.start_date AND  sub.end_date) as count_invoices',
            '(SELECT count(*) FROM invoices INNER JOIN wallet ON invoices.id = wallet.invoices_id 
            WHERE wallet.soport IS NOT NULL AND invoices.companies_id = companies.id) as wallet'
        ])
        ->join('subscriptions as sub' , 'companies.id = sub.companies_id')
        ->join('packages' , 'packages.id = sub.packages_id')
        ->where($data)
        ->orderBy('sub.date_due_certificate', 'ASC')
        ->asObject();

        
          
  

        $model = new Company();
        $companies = $model->select(['id', 'company'])
        ->orderBy('company', 'asc')
        ->asObject()
        ->get()
        ->getResult();  

        return view('solicitudes/expired_subscription', ['subscriptions' => $subscriptions->paginate(10), 'pager' => $subscriptions->pager, 'companies' => $companies]);
    }


    public function expiredSubscriptionExtport()
    {
        $data = ['sub.status'  => 'Activo'];
        if(!is_null($this->request->getGet('company_id')) && !empty($this->request->getGet('company_id'))) {
            $data['companies.id'] = $this->request->getGet('company_id');
        }

        if(!is_null($this->request->getGet('start_date')) && !empty($this->request->getGet('start_date'))) {
            $data['sub.date_due_certificate >='] = $this->request->getGet('start_date');
        }

        if(!is_null($this->request->getGet('end_date')) && !empty($this->request->getGet('end_date'))) {
            $data['sub.date_due_certificate <='] = $this->request->getGet('end_date');
        }
 
        $model = new Company();
        $subscriptions = $model->select([
            'companies.company', 
            'sub.date_due_certificate',
            'sub.end_date', 
            'sub.start_date',
            'packages.name as package_name',
            'packages.quantity_document',
           '(SELECT count(*) FROM invoices WHERE invoices.type_documents_id IN (1, 2, 3 , 4, 5, 9, 10, 101, 102, 103, 104) AND
            invoices.invoice_status_id IN (2, 3, 4, 14) AND invoices.companies_id = companies.id AND invoices.created_at BETWEEN sub.start_date AND  sub.end_date) as count_invoices',
            '(SELECT count(*) FROM invoices INNER JOIN wallet ON invoices.id = wallet.invoices_id 
            WHERE wallet.soport IS NOT NULL AND invoices.companies_id = companies.id) as wallet'
        ])
        ->join('subscriptions as sub' , 'companies.id = sub.companies_id')
        ->join('packages' , 'packages.id = sub.packages_id')
        ->where($data)
        ->orderBy('sub.date_due_certificate', 'ASC')
        ->asObject()
        ->get()
        ->getResult();

        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return;
        }
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');
         //Encabezados
         $spreadsheet->setActiveSheetIndex(0)->setCellValue('A2', 'Empresa')->getStyle('A2')->getFont()->setBold(true);
         $spreadsheet->setActiveSheetIndex(0)->setCellValue('A3', 'Identificación')->getStyle('A3')->getFont()->setBold(true);
         $spreadsheet->setActiveSheetIndex(0)->setCellValue('A4', 'Fecha de reporte')->getStyle('A4')->getFont()->setBold(true);
         $spreadsheet->setActiveSheetIndex(0)->setCellValue('A5', 'Fecha de generación')->getStyle('A5')->getFont()->setBold(true);
         $spreadsheet->setActiveSheetIndex(0)->setCellValue('A6', 'Software de Facturación')->getStyle('A6')->getFont()->setBold(true);
         $spreadsheet->setActiveSheetIndex(0)
             ->setCellValue('B2', 'MAWII S.A.S')
             ->setCellValue('B3', '')
             ->setCellValue('B4', 'Reporte Detallado del Subscripciones')
             ->setCellValue('B5', date('Y-m-d H:i:s'))
             ->setCellValue('B6','MiFacturaLegal.com');
 
         $spreadsheet->getActiveSheet()->getStyle('A6')->getFont()->getColor()->setARGB('FF2874A6');
         $spreadsheet->getActiveSheet()->getStyle('B6')->getFont()->getColor()->setARGB('FF2874A6');
         $spreadsheet->setActiveSheetIndex(0)->getStyle('B6')->getFont()->setBold(true);
 
 
         //quitar cuadricula
         $spreadsheet->getActiveSheet()->setShowGridlines(false);
 
 
 
         // Columnas A8 hasta T8
         $styleArray = [
             'font' => [
                 'bold' => true,
             ],
             'alignment' => [
                 'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                 'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
             ],
             'fill' => [
                 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                 'startColor' => [
                     'argb' => 'FFD7DBDD',
                 ],
 
             ]
         ];
 
         $spreadsheet->getActiveSheet()->getStyle('A8:T8')->applyFromArray($styleArray);
         $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
         $spreadsheet->getActiveSheet()->getRowDimension('8')->setRowHeight(40);
         $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
         $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
         $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
         $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
         $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
         $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
         $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
         $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
         $spreadsheet->setActiveSheetIndex(0)
         ->setCellValue('A8', 'Compañia')
         ->setCellValue('B8', 'Fecha de V. Certificado')
         ->setCellValue('C8', 'Fecha de V. Subscripcion')
         ->setCellValue('D8', 'Paquete')
         ->setCellValue('E8', 'Paquete Actual')
         ->setCellValue('F8', 'Gastados')
         ->setCellValue('G8', 'Disponibles')
         ->setCellValue('H8', 'Estado');

        $i = 9;
        foreach ($subscriptions as $subscription) {
            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . $i, $subscription->company)
            ->setCellValue('B' . $i, $subscription->date_due_certificate )
            ->setCellValue('C' . $i, $subscription->end_date)
            ->setCellValue('D' . $i, $subscription->package_name)
            ->setCellValue('E' . $i, $subscription->quantity_document )
            ->setCellValue('F' . $i, $subscription->count_invoices + $subscription->wallet )
            ->setCellValue('G' . $i, $avaliable = $subscription->quantity_document  -  ($subscription->count_invoices + $subscription->wallet));
            $now = time(); // or your date as well
            $your_date = strtotime($subscription->end_date);
            $datediff = $your_date -$now;
            $days = round($datediff / (60 * 60 * 24));

            if($days <= 0) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('H' . $i, "V. Fecha ".$days. ' Dias');
            }

            if($avaliable <= 0) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('H' . $i, "V. Documentos". $avaliable. ' documentos');
            }
            if($days > 0 && $avaliable >0) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('H' . $i, "Activo");
            }
            $i++;
        }
        $spreadsheet->getActiveSheet()->setTitle('Reporte_Subscriptiones');
        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_Facturación_Detallado.xls"');
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

    public function Epayco()
    {
        $company = new Company();
        $validation = $company->where(['identification_number' => $this->request->getPost('nit') ])->countAllResults();
        $paquetes = new Packages();
        $paquete = $paquetes->orderBy('price', 'ASC')->get()->getResult();
        $tdocumentos = new TypeDocumentIdentifications();
        $Tdocumento = $tdocumentos->get()->getResult();
        $vendedores = new Sellers();
        $vendedor = $vendedores->where(['identification_number' => $_POST['vendedor']])->get()->getResult()[0];

        $data = [
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
        $id = $applicant->insert($data);
        if($validation < 1){
            //company
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

            $company->insert($data);
        }

        $packages = new Packages();
        $package = $packages->where(['id' => $_POST['plan']])->get()->getResult()[0];
        if($_POST['vendedor'] == '222222222' || $this->request->getPost('process') == 'renovacion'){
            $valor = $package->price;
        }else{
            $valor = $package->price - 30000;
        }

        $dato = [
            'valor' => $valor,
            'plan' => $package->name,
            'company_name' => $_POST['nempresa'],
            'nit' => $_POST['nit'],
            'phone' => 0000000,
            'email' => $_POST['email'],
            'epayco' => '3e68b295e5d6460972ba880ca1f8a967',
            'paquetes' => $paquete,
            'tdocumentos' => $Tdocumento
        ];
        echo view('solicitudes/registro', $dato);
    }

    public function Seguimiento($id)
    {
        $data =[
            'applicant_id' => $id,
            'user_id' => $_POST['user'],
            'date' => date('Y-m-d'),
            'log' => $_POST['log']
        ];
        $tracings = new Tracing();

        if ($tracings->save($data)){
            return redirect()->to(base_url() . '/solicitudes/info/' . $id)->with('success', 'Se agregó Seguimiento Correctamente.');
        } else {
            return redirect()->to(base_url() . '/solicitudes/info/' . $id)->with('error', 'No se agregó seguimiento');
        }
    }


}


