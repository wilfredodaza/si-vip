<?php


namespace App\Controllers\Managers;

use App\Controllers\Configuration\EmailController;
use App\Interfaces\IManager;
use App\Models\Config;
use App\Models\Resolution;
use App\Models\Invoice;

class ResolutionNumberManager implements IManager
{
    /**
     * Id de la compañia
     * @var int $companyId
     */
    private $companyId;


    /**
     * Array de resoluciones
     * @var array $resolutions 
     */
    private $resolutions;


    /**
     * Array de resoluciones vencidas
     * @var Array $resolutionOverdue
     */
    private $resolutionOverdue = [];


    /**
     * constructor
     * @param int $companyId Id de la compañia
     */


    public function __construct(int $companyId)
    {
        $session = session();
        $session->removeTempdata('notification_resolution_number');
        $this->companyId        = $companyId;
        $this->resolutions      = $this->getResolutions();
    }


   /**
     * Este metodo se encarga de traer todas las resoluciones de facturación
     * @return array Resoution
     */


    public function getResolutions(): array
    {
        $model = new Resolution();
        return  $model->select(['resolution', 'to', 'date_to', 'from'])->where([
            'type_documents_id'             => 1,
            'companies_id'                  => $this->companyId,
            ])
        ->get()
        ->getResult();
    }



    /**
     * Este metodod se encarga de traer el ultimo numero de resoluzion registrado
     * @param int $resolution Numero de resolucion
     */

    public function getLastNumberDocument(int $resolution) : int
    {
        $model = new Invoice();
        $number = $model->select(['invoices.resolution'])
            ->where(['resolution_id' => $resolution, 'invoice_status_id >' => 1, 'type_documents_id' => 1])
            ->orderBy('invoices.id', 'DESC')
            ->asObject()
            ->first();

        if(!is_null($number)) {
            return $number->resolution;
        }else {
            return 0;
        }

    }

    /**
     * Este metodo se encarga de informar si hay una resolucion pronta a vencer
     * @return array Resoution
     */

    public function activateNotification(): bool
    {

        $model = new Config();
        $config = $model
            ->where(['companies_id' => $this->companyId])
            ->asObject()
            ->first();
        if(isset($config) && $config->days_notification) {
            $cant = is_null($config->days_notification) ? $config->days_notification : 20;
        }else {
            $cant = 20;
        }
        foreach($this->resolutions as $item) {
            if( $this->getLastNumberDocument($item->resolution)  >=  $item->to - $cant ) {
                array_push($this->resolutionOverdue, $item);
                return true;
            }
        }

        return false;
    }

    /**
     * Este metodo se encarga de validar si la resolucion se encuentra vencida
     * @return array Resoution
     */


    public function expired(): bool
    {
        foreach($this->resolutions as $item) {
            if( $this->getLastNumberDocument($item->resolution)  <=  $item->to) {
                array_push($this->resolutionOverdue, $item);
                return true;
            }
        }
        return false;
    }

     /** 
     * El meotdo se encarga de crear la notificación
    */

    public function notification() 
    {
        $text = '';

        foreach($this->resolutionOverdue as $item) {
            $text.= 'La resolución de facturación con numero '.$item->resolution.' que va desde la '.$item->from.' hasta '.$item->to.' esta a '.( $item->to - $this->getLastNumberDocument($item->resolution) ).'  documentos de vencerse.<br>';
        }

        $session = session();
        $session->set('notification_resolution_number', [
            'type'    =>   'orange darker-2',
            'message' =>   $text.' Te recomendamos que saques una nueva resolución de facturación electrónica y nos la envíes por medio de correo electrónico o por el chat para realizar la activación.'
        ]);
    }

    /**
     * Este metodo es el encargado de realizar el envio de un correo electronico
     * informado que la resolución esta pronto a vencer
     * @param $company
     */


    public function sendEmail($company)
    {

        foreach($this->resolutions as $item) {
            if( $this->getLastNumberDocument($item->resolution)  <= $item->to - 20 ) {
                array_push($this->resolutionOverdue, $item);
                return true;
            }
        }

        $email = new EmailController();
        $email->send('soporte@planetalab.xyz', 'MiFacturaLegal.COM', $company->email, 'MFL-Notificación de resolución de facturación', view('emails/resolution_number', [
            'resolutions'   => $this->resolutionOverdue,
            'number'        => ($this->getLastNumberDocument($item->resolution) -  $item->to ),
            'company'       => $company
        ]));
    }

  

    
}