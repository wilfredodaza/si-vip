<?php


namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Packages;
use App\Models\Sellers;
use App\Models\Subscription;
use App\Models\Tracing;
use App\Models\TypeDocumentIdentifications;
use Config\Services;
use App\Models\Config;
use App\Models\Company;
use App\Models\Tooltips;
use App\Models\Applicant;
use App\Models\Applicant_documents;
use App\Models\Applicant_subscription;
use App\Controllers\Configuration\EmailController;


class SolicitudesIncomController extends BaseController
{
    //vista de todas las solicitudes realizadas
    public function index()
    {
        $subcription = new Subscription();
        $applicant = new Applicant();
        $solicitudes = $applicant->select('*, applicant_status.status as estado, applicant.status as vestado, applicant.id as idsolicitud, sellers.id as idsellers, sellers.name as nameseller')
            ->like($this->search(), isset($_GET['value']) ? $this->request->getGet('value') : '', 'both')
            ->join('applicant_status', 'applicant.status = applicant_status.id')
            ->join('sellers', 'applicant.seller = sellers.id')
            ->orderBy('applicant.id', 'desc');

        $solicitudesIncompletas = $solicitudes->get()->getResult();
        $datos = [];

        //echo json_encode($solicitudesIncompletas); die();
        $esta = 'no';
        foreach ($solicitudesIncompletas as $solicitud){
            if($solicitud->estado == 'Solicitada') {
                $subscripciones = $subcription->where(['applicant_id'=>$solicitud->idsolicitud])->countAllResults();
                if($subscripciones == 0){
                    array_push($datos, $solicitud);
                }
            }
        }
        $data = [
            //'solicitudes' => $datos->paginate(10),
            //'pager' => $solicitudes->pager,
            //'count' => count($datos),
            'data' => $datos
        ];
        echo view('solicitudes/solicitudesIncompletas', $data);
    }

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
            }

        } else {
            $campo = 'applicant.company_name';
        }
        return $campo;
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
            ->where(['applicant.id' => $id])->get()
            ->getResult();

        if ($solicitud[0]->estado != 'Solicitada' && $solicitud[0]->estado != 'Solicitud de Documentos') {

            $applicant_documents = new Applicant_documents();
            $documentos = $applicant_documents->select('*')->where(['applicant_id' => $id, 'documento <>' => 'prueba1'])->where(['documento <>' => 'prueba2'])->get()->getResult();
            $img = base_url() . '/upload/documentos/' . $solicitud[0]->nit . '/';

            $pagos = new Applicant_subscription();
            $cantidadPago = $pagos->select('*')->join('packages', 'packages.id = subscriptions.packages_id')->where(['subscriptions.applicant_id' => $id])->countAllResults();
            if ($cantidadPago > 0) {
                $pago = $pagos->select('*')->join('packages', 'packages.id = subscriptions.packages_id')->where(['subscriptions.applicant_id' => $id])->orderBy('subscriptions.id', 'Desc')->get()->getResult()[0];
            }


        }

        $tracings= new Tracing();
        $tracing = $tracings->join('users', 'tracing.user_id = users.id')
            ->where(['tracing.applicant_id' => $id])
            ->orderBy('tracing.id','DESC')
            ->get()->getResult();

        return view('solicitudes/editarSolicitudIncom', ['solicitud' => $solicitud, 'documentos' => $documentos, 'ruta_img' => $img, 'pago' => $pago, 'tracings' => $tracing]);
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
            return redirect()->to(base_url() . '/solicitudes/incompletas/info/' . $id)->with('success', 'Datos editados correctamente');
        } else {
            return redirect()->to(base_url() . '/solicitudes/incompletas/info/' . $id)->with('error', 'No se editaron los datos');
        }

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
            return redirect()->to(base_url() . '/solicitudes/incompletas/info/' . $id)->with('success', 'Se agregó Seguimiento Correctamente.');
        } else {
            return redirect()->to(base_url() . '/solicitudes/incompletas/info/' . $id)->with('error', 'No se agregó seguimiento');
        }
    }

    public function solicitudesIncomEmail(){
        $email = new EmailController();
        $subcription = new Subscription();
        $applicant = new Applicant();
        $solicitudes = $applicant->select('*, applicant_status.status as estado, applicant.status as vestado, applicant.id as idsolicitud, sellers.id as idsellers, sellers.name as nameseller')
            ->like($this->search(), isset($_GET['value']) ? $this->request->getGet('value') : '', 'both')
            ->join('applicant_status', 'applicant.status = applicant_status.id')
            ->join('sellers', 'applicant.seller = sellers.id')
            ->orderBy('applicant.id', 'desc');

        $solicitudesIncompletas = $solicitudes->get()->getResult();
        $datos = [];

        //echo json_encode($solicitudesIncompletas); die();
        $esta = 'no';
        foreach ($solicitudesIncompletas as $solicitud){
            if($solicitud->estado == 'Solicitada') {
                $subscripciones = $subcription->where(['applicant_id'=>$solicitud->idsolicitud])->countAllResults();
                if($subscripciones == 0){
                    array_push($datos, $solicitud);
                }
            }
        }
        $cuerpo = '<h3>Solicitudes incompletas al Registrarse en MFL</h3><br>';
        foreach($datos as $dato){
            $cuerpo.='* Compañia :'.$dato->company_name.'<br>';
        }
        $email->send('soporte@mifacturalegal.com', 'Solicitudes Incompletas', 'pruebasapp@iplanetcolombia.com', 'MFL - Solicitudes Incompletas', $cuerpo);
    }
}