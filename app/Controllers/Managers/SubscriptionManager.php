<?php


/**
 * @author IPlanet Colombia S.A.S
 * @author Wilson Andres Bachiller Ortiz
 * @date 31/05/2021
 * Administrador Suscripciones
 * 
 * Esta clase esta encargada de brindar información actual del estado y consumo de los
 * paquetes de cada compañia en MiFacturaLegal.com
 */


namespace App\Controllers\Managers;


use App\Controllers\Configuration\EmailController;
use App\Models\Subscription;
use App\Traits\SubscriptionTrait;

class SubscriptionManager{
    
    use SubscriptionTrait;
    /**
     * Id de la compañia
     * @var int
     */
    private $companyId;


    /**
     * Dato de la suscripción
     * @var int
     */
    private $subscription;


    /**
     * constructor
     * @param int $companyId Id de la compañia
     */

    public function __construct(int $companyId)
    {
     
        $session = session();
        $session->removeTempdata('notification_subscription');
        $this->subscription = $this->total();
     
    }


    

    /**
     * Este metodo es el cargado de confirmar si se debe enviar una 
     * norificación de que sede comproar otra suscripción.
     * @return Object
     */

    public function activateNotification(): bool
    {

        if(!is_null($this->subscription)) {
            helper('time');
            $days  = differenceDays(date('Y-m-d'), $this->subscription->end_date, true);
            if($days <= 30)  {
                return true;
            }
            return false;
        }else {
            return false;
        }
      
    }


     /**
     * Este metodo es el cargado de informar si la subscripción 
     * acutal se encuntra expirada.
     * @return Object 
     */

    public function expired()
    {
        if(!is_null($this->subscription)) {
            helper('time');
            $days  = differenceDays(date('Y-m-d'), $this->subscription->end_date, true);
            if($days <= 0)  {
                return true;
            }
            return false;
        }else {
            return false;
        }
    }

    /** 
     * El meotdo se encarga de crear la notificación
    */

    public function notification(): void 
    {
        if(!is_null($this->subscription)) {
            helper('time');
            $days  = differenceDays(date('Y-m-d'), $this->subscription->end_date, true);

            $session = session();
            $session->set('notification_subscription', [
                'type'        =>     $days <= 0 ? 'red darken-2' : 'orange' ,
                'message'     =>    'La subcripción esta  a '.$days.' dias de vencer te recomendamos que te comuniques con nosotros para adquirir una nueva suscripción. los puedes realizar por medio de nuestro chat o por correo electrónico 
                soporte@mifacturalegal.com.'
            ]);
        }
    }

    /**
     * Este metodo se encarga de realizar el envio de correo electronico
     * cuando la subscripcion esta pronta a vencer
     * @param $company
     */

    public function sendEmail($company): void
    {
        if(!is_null($this->subscription)) {;
            helper('time');
            $days  = differenceDays(date('Y-m-d'), $this->subscription->end_date, true);
            $email = new EmailController();
            $email->send('soporte@planetalab.xyz', 'MiFacturaLegal.COM', $company->email, 'MFL-Notificación de subscripción', view('emails/subscription_ expiration', [
                'subscription'  => $this->subscription,
                'days'          => $days,
                'company' => $company
            ]));
        }
    }
}