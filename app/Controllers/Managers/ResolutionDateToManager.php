<?php


namespace App\Controllers\Managers;


use App\Controllers\Configuration\EmailController;
use App\Models\Config;
use App\Models\Resolution;


class ResolutionDateToManager
{

    /**
     *  Id de la compañia
     * @var int $companyId 
     */

    private $companyId;


    /**
     * Array de resoluciones
     * @var Array $resolutions
     */

    private $resolutions;

    /**
     * Array de resoluciones vencidas
     * @var Array $resolutionOverdue
     */
    private $resolutionOverdue = [];

    /**
     * constructor
     * 
     */

    public function __construct(int $companyId)
    {
        $session = session();
        $session->removeTempdata('notification_resolution_date');
        $this->companyId        = $companyId;
        $this->resolutions      = $this->getResolutions();
    }



    /**
     * Este metodo se encarga de traer todas las resoluciones de facturación
     * @return array Resoution
     */

    public function getResolutions() 
    {
        $model = new Resolution();
        return  $model->select(['resolution', 'to', 'date_to', 'from'])
            ->where(['type_documents_id' => 1, 'companies_id'  => $this->companyId,  'status' =>  NULL])
            ->get()
            ->getResult();
    }

    /**
     * Este metodo se encarga de informar si hay una resolucion pronta a vencer
     * @return bool
     */

    public function activateNotification(): bool
    {
        foreach($this->resolutions as $item) {

            helper('time');
            $days  = differenceDays(date('Y-m-d'), $item->date_to, true);

            $model = new Config();
            $config = $model
                ->where(['companies_id' => $this->companyId])
                ->asObject()
                ->first();

            if(isset($config) && $config->days_notification) {
                $cant = !is_null($config->days_notification) ? $config->days_notification: 20 ;
            }else {
                $cant = 20;
            }



            if($days <= $cant){
                array_push($this->resolutionOverdue, $item);
                return true;
            }
        }

        return false;
    }


    /**
     * Este metodo se encarga de informar si la resolucion esta vencida
     * @param int $numberDays
     * @return bool
     */


    public function expired(): bool
    {
        foreach($this->resolutions as $item) {
            helper('time');
            $days  = differenceDays(date('Y-m-d'), $item->date_to, true);
            if($days <= 0){
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
        $danger = true;
        $text = '';
        foreach($this->resolutionOverdue as $item) {
            helper('time');
            $days  = differenceDays(date('Y-m-d'), $item->date_to, true);
            $text.= 'La resolución de facturación con numero  '.$item->resolution.' que va desde la '.$item->from.' hasta '.$item->to.' esta a '.$days.' dias de vencerse.<br>';

        }

        unset($_SESSION['notification']);
        $session = session();
        $session->set('notification_resolution_date',
            [
                'type'      =>  $danger ? 'red darken-2' : 'orange' ,
                'message'   =>  $text.' Te recomendamos que saques una nueva resolución de facturación electrónica y nos la envíes por medio de correo electrónico o por el chat para realizar la activación.'
            ]);
    }

    /**
     * Este metodo se encarga de notificar al cliente por medo de correo
     * electrónico anticipadamente que esta pronto a vencer la
     * resolucion de facturación.
     * @param $company Objeto de datos de la compañia
     */

    public function sendEmail($company)
    {
        $this->groupResolution();
        $email = new EmailController();
        $email->send('soporte@planetalab.xyz', 'MiFacturaLegal.COM', $company->email, 'MFL-Notificación de resolución de facturación', view('emails/resolution_date', [
            'resolutions' => $this->resolutionOverdue,
            'company' => $company
        ]));
    }


    public function groupResolution($quantityDays = 0)
    {
        foreach($this->resolutions as $item) {
            helper('time');
            $days  = differenceDays(date('Y-m-d'), $item->date_to, true);
            if($days <= $quantityDays){
                array_push($this->resolutionOverdue, $item);
            }
        }
    }


}