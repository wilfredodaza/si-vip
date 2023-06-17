<?php

/**
 * Controlador encargado de la comunicación con el api para
 * la trasmisión del RADIAN por medio de eventos.
 * @author Wilson Andres Bachiller Ortiz <wilson@mawii.com> wabo
 * @version 1.0
 */
    namespace App\Controllers;

    use App\Models\Company;
    use App\Models\Document;
    use App\Models\DocumentEvent;
    use App\Models\Invoice;
    use Config\Services;
    use App\Controllers\Api\Auth;

    class EventController extends BaseController
    {

        /**
         * Método encargado del envió de envetos del RADIAN al API
         * @param $idDocumentElectronic  Id del documento de electronica tabla invoices
         * @param $event Id del evento table de events
         * @return \CodeIgniter\HTTP\RedirectResponse
         * @throws \ReflectionException
         */
        public static function event($idDocumentElectronic = null, $event = null,$type_rejection_id = null, $origin = null)
        {
            if($type_rejection_id == null || $type_rejection_id == 0){
                $type_rejection_id = '';
            }
            if($origin == 1) $origin = true;
            else $origin = false;
            $model = new Company();
            $company = $model->select(['token', 'identification_number'])
                ->where(['id' => Auth::querys()->companies_id])
                ->asObject()
                ->first();

            $model = new Document();
            $document = $model->select(['associate_document.*'])
                ->join('invoices', 'invoices.id = documents.invoice_id')
                ->join('associate_document', 'associate_document.documents_id = documents.id', 'left')
                ->where(['invoices.companies_id' => Auth::querys()->companies_id, 'invoices.id' => $idDocumentElectronic, 'associate_document.extension' => 'xml'])
                ->asObject()
                ->first();

            if(!$document && !$company) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }


            $client = Services::curlrequest();
            $client->setHeader('Content-Type', 'application/json');
            $client->setHeader('Accept', 'application/json');
            $client->setHeader('Authorization', "Bearer " . $company->token);

            $xml = base64_encode(file_get_contents(WRITEPATH.'uploads/document_reception/'.$company->identification_number.'/xml/'.$document->new_name));

            $res = $client->post(
                getenv('API').'/ubl2.1/send-event', [
                    'http_errors' => false,
                    'form_params' => [
                        'event_id'                           => $event,
                        'type_rejection_id'                  => $type_rejection_id,
                        'base64_attacheddocument_name'       => $document->name,
                        'base64_attacheddocument'            => $xml
                    ],
                ]
            );

            $json = json_decode($res->getBody());
            
            try {
                if (isset($json->success) && $json->success == false) {
                    if($origin) return json_encode(['status' => false, 'message' => $json->message]);
                    return redirect()->to(base_url() . route_to('document-show', $idDocumentElectronic))->with('errors', $json->message);
                } else if (isset($json->errors)) {
                    $dataErrors = [];
                    foreach ($json->errors as $error => $key) {
                        array_push($dataErrors, $key[0]);
                    }
                    if($origin) return json_encode(['status' => false, 'message' => implode('<br>', $dataErrors)]);
                    return redirect()->to(base_url() . route_to('document-show', $idDocumentElectronic))->with('errors', implode('<br>', $dataErrors));
                }

                if ($json->ResponseDian->Envelope->Body->SendEventUpdateStatusResponse->SendEventUpdateStatusResult->IsValid == 'false') {
                    $message = $json->ResponseDian->Envelope->Body->SendEventUpdateStatusResponse->SendEventUpdateStatusResult->ErrorMessage->string;
                    if($origin) return json_encode(['status' => false, 'message' => is_array($message) ? implode('<br>', $message) : $message]);
                    return redirect()->to(base_url() . route_to('document-show', $idDocumentElectronic))->with('errors', is_array($message) ? implode('<br>', $message) : $message);
                }

                $message = $json->ResponseDian->Envelope->Body->SendEventUpdateStatusResponse->SendEventUpdateStatusResult->StatusDescription;
                $model = new DocumentEvent();
                $data =[
                    'document_id'       => $document->documents_id,
                    'event_id'          => $event,
                    'type_rejection_id' => $type_rejection_id != '' ? $type_rejection_id : NULL,
                    'uuid'              => $json->cude
                ];
                $model->save($data);

            }catch (\Exception $e) {
                if($origin) return json_encode(['status' => false, 'message' => 'Ha ocurrido un error en el servidor.']);
                return redirect()->to(base_url().route_to('document-show', $idDocumentElectronic))->with('errors', 'Ha ocurrido un error en el servidor.');
            }
            
            if($origin) return json_encode(['status' => true, 'message' => $message]);
            return redirect()->to(base_url().route_to('document-show', $idDocumentElectronic))->with('success',$message);
        }

    }