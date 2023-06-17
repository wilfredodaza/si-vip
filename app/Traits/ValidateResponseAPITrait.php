<?php

namespace App\Traits;

use App\Models\Invoice;

trait ValidateResponseAPITrait
{
    /**
     * enviroment or status in DIAN
     * @var
     */
    private $enviroment;

    /**
     * Type document send the DIAN
     * @var
     */
    private $typeDocumentId;

    /**
     * Valid status of response HTTP
     * @param int $id
     * @param int $enviroment
     * @param Object $response
     * @param int $typeDocumentId
     * @return Object
     */
    public function validStatusCodeHTTP(int $id, int $enviroment, Object $response, int $typeDocumentId): Object
    {

        $this->enviroment       = $enviroment;
        $this->typeDocumentId   = $typeDocumentId;
        $this->response         = $response;
        switch ($response->status) {
            case 200:
            case 299:
                 $data = $this->code200HTTP();
            break;
            case 401:
                $data =$this->code401HTTP();
            break;
            case 422:
                $data = $this->code422HTTP();
            break;
            case 500:
               $data = $this->code200HTTP();
            break;
        }
        $this->registerError($id, $data,   $this->response );
        return (Object) $data;
    }

    /**
     * Return  data status 200 of the response DIAN
     * @return array
     */
    private function code200HTTP(): array
    {

        if ($this->enviroment == 1 && ($this->typeDocumentId == 10 || $this->typeDocumentId == 9)) {
            if ((string)$this->response->data->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->IsValid == 'true' || ((string)$this->response->data->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->IsValid == 'false' &&
                    $this->response->data->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->StatusDescription == 'String was not recognized as a valid DateTime.') ||
                ((string)$this->response->data->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->IsValid == 'false' &&
                    $this->response->data->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->StatusDescription == 'An error was reported while committing a database transaction but it could not be determined whether the transaction succeeded or failed on the database server. See the inner exception and http://go.microsoft.com/fwlink/?LinkId=313468 for more information.')
                || ((string)$this->response->data->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->IsValid == 'false' && $this->response->data->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->StatusDescription == 'Ha ocurrido un error. Por favor int ntentelo de nuevo.')
            ) {
                return [
                    'error' => false,
                    'data' => [
                        'message' => $this->response->data->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->StatusDescription,
                        'uuid' => isset($this->response->data->cune) ? $this->response->data->cune : ''
                    ],
                    'response' => $this->response->data,
                    'messages' => $this->response->data->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->StatusDescription,
                ];
            } else {
                $errorsDIAN = $this->response->data->ResponseDian->Envelope->Body->SendNominaSyncResponse->SendNominaSyncResult->ErrorMessage->string;
                return [
                    'error' => true,
                    'data' => is_array($errorsDIAN) ? implode("<br>", $errorsDIAN) : $errorsDIAN,
                    'messages' => is_array($errorsDIAN) ? implode("<br>", $errorsDIAN) : $errorsDIAN,
                    'response' => $this->response->data

                ];
            }
        } else if ($this->enviroment == 2 && ($this->typeDocumentId == 10 || $this->typeDocumentId == 9)) {
            return [
                'error' => false,
                'data' => [
                    'zipkey' => $this->response->data->ResponseDian->Envelope->Body->SendTestSetAsyncResponse->SendTestSetAsyncResult->ZipKey,
                    'uuid' => $this->response->data->cune
                ],
                'messages' => $this->response->data->message
            ];
        }


        try {

            if ($this->enviroment == 1 && ($this->typeDocumentId == 11 || $this->typeDocumentId == 13 || $this->typeDocumentId == 1 || $this->typeDocumentId == 2 || $this->typeDocumentId == 4 || $this->typeDocumentId == 5)) {
                if ((string)$this->response->data->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid == 'true') {
                    if ($this->typeDocumentId == 1 || $this->typeDocumentId == 2) {
                        $uuid = isset($this->response->data->cufe) ? $this->response->data->cufe : '';
                    } else {
                        $uuid = isset($this->response->data->cuds) ? $this->response->data->cuds : '';
                    }
                    return [
                        'error' => false,
                        'data' => [
                            'message' => $this->response->data->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->StatusDescription,
                            'uuid' => $uuid
                        ],
                        'response' => $this->response->data,
                        'messages' => $this->response->data->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->StatusDescription,
                    ];
                } else {
                    $errorsDIAN = $this->response->data->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->ErrorMessage->string;
                    return [
                        'error' => true,
                        'data' => is_array($errorsDIAN) ? implode("<br>", $errorsDIAN) : $errorsDIAN,
                        'messages' => is_array($errorsDIAN) ? implode("<br>", $errorsDIAN) : $errorsDIAN,
                        'response' => $this->response->data,
                        'errors' => [
                            'type' => 'code200',
                            'data' => $errorsDIAN
                        ]
                    ];
                }
            }
        } catch (\ErrorException $e) {
            return [
                'error'         => true,
                'data'          => 'HTTP 500 - Falla en el servidor.',
                'messages'      => 'HTTP 500 - Falla en el servidor.',
                'response'      => $this->response->data
            ];
        }

    }

    /**
     * Return data status 401 of the response DIAN
     * @return array
     */
    private function code401HTTP(): array
    {
        return [
            'error'         => true,
            'data'          => '401 Not Unauthorized',
            'messages'      => '401 Not Unauthorized'
        ];
    }

    /**
     * Return data status 422 of the response DIAN
     * @return array
     */
    private function code422HTTP()
    {
        $errorText = '';
        $errorsKey = [];
        if($this->typeDocumentId == 9 || $this->typeDocumentId == 10 || $this->typeDocumentId == 13 ||  $this->typeDocumentId == 11 || $this->typeDocumentId == 1 || $this->typeDocumentId == 2|| $this->typeDocumentId == 4 || $this->typeDocumentId == 5) {
            foreach($this->response->data->errors as $key => $error) {
                array_push($errorsKey,[
                    'code'  => $key,
                    'error' => $error
                ]);
                foreach ($error as $value) {
                    $errorText.= '<p>' . $value . '</p>';
                }
            }
            return [
                'error'         => true,
                'data'          => $errorText,
                'messages'      => $errorText,
                'errors'        => [
                    'type' => 'code422',
                    'data' => $errorsKey
                ]
            ];
        } else {

            foreach($this->response->errors as $error => $key) {
                $errorText .= lang('payroll_errors.payroll_errors.'.$error).PHP_EOL;
            }



            return [
                'error'         => true,
                'data'          => $this->response->data->errors,
                'messages'      => $errorText,
            ];

        }
    }

    /**
     * Return data status 500 of the response DIAN
     * @return array
     */
    private function code500HTTP()
    {

        return [
            'error'         => true,
            'data'          =>  str_replace('"',"'" , json_encode($this->response->data)),
            'messages'      => 'HTTP 500 - Error del Servidor'
        ];
    }

    /**
     * update invoices the row error, uuid, zipkey
     * @param $id
     * @param $data
     * @param $response
     * @throws \ReflectionException
     */
    private function registerError($id, $data, $response)
    {
        $model = new Invoice();
        if(!empty($data['errors']['data'])){
           addErrors($data['errors']['data'], $data['errors']['type']);
        }
        if($data['error']) {
            $update = [
                'response'          => json_encode($response),
                'errors'            => (!empty($data['errors']['data']))?json_encode($data['errors']):$data['data'],
                'invoice_status_id' => 27
            ];

            if($this->typeDocumentId == 10 || $this->typeDocumentId == 9) {
                $update['invoice_status_id'] = 15;
            }else if ($this->typeDocumentId == 11 || $this->typeDocumentId == 13) {
                $update['invoice_status_id'] = 24;
            }
            $model->update($id,$update);

        }else {
            $data = [
                'uuid'              => $data['data']['uuid'],
                'zipkey'            => $this->enviroment == 2 ? $data['data']['zipkey'] : null,
                'response'          => json_encode($response)
            ];
            $model->update($id, $data);
        }
    }
}