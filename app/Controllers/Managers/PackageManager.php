<?php

/**
 * @author IPlanet Colombia S.A.S
 * @author Wilson Andres Bachiller Ortiz
 * @date 31/05/2021
 * Administrador Paquetes
 * 
 * Esta clase esta encargada de brindar información actual del estado y consumo de los
 * paquetes de cada compañia en MiFacturaLegal.com
 */


namespace App\Controllers\Managers;

use App\Interfaces\IManager;
use App\Models\Config;
use App\Models\Invoice;
use App\Models\Wallet;
use App\Models\Subscription;
use App\Controllers\Configuration\EmailController;
use App\Traits\SubscriptionTrait;

class PackageManager implements IManager
{

    use SubscriptionTrait;

    public  function __construct($companyId) 
    {
       // echo json_encode($this->total());die();
        $this->subscription = $this->total();
        $session = session();
        $session->removeTempdata('notification_package');
    }



    /**
     * Este metodo es el cargado de confirmar si se debe enviar una 
     * norificacion de compra de un nuevo paquete.
     * @return Object
     */

    public function activateNotification(): bool
    {
        if($this->subscription->available <= 10) {
            return true;
        } else {
            return false;
        }
    }



    /**
     * El metodo esta diseñado para infomar por medio de true o false
     * si el paquete a expirado o sique activo
     * @return Object
     */

    public function expired(): bool
    {  
        if($this->subscription->available <= 10) {
            return true; 
        }else {
            return false;
        }
    }

     /** 
     * El metodo se encarga de crear la notificación informado
      * que el packete se encuentra apunto de vencerse.
    */

    public function notification(): void
    {
        $session = session();
        if(!is_null($this->subscription)) {
            $session->set('notification_package',
                [
                    'type'   =>  isset($this->subscription->avaliable) && $this->subscription->avaliable <= 0 ? 'red darken-2' : 'orange',
                    'message' => 'El paquete '.$this->subscription->package_name.' de '. $this->subscription->package_quantity.' documentos esta pronto a vencerse, solo te quedan  
                '.  $this->subscription->available. ' documentos disponibles. Te aconsejamos comunicarte con nosotros vía chat o correo electrónico soporte@mifacturalegal.com para iniciar con el proceso compra de un nuevo paquete de documentos.'
                ]
            );
        }else {
            $session->set('notification_package',
                [
                    'type'   =>   'orange' ,
                    'message' => 'No se le asignado ningún paquete al sistema.'
                ]
            );
        }

    }


    /**
     *Este metodo se encarga de realizar el envio de un correo electrónico
     * informando que el paquete se encuentra apunto de vencerse.
     * @param $company
     */
   /* public function sendEmail($company): void
    {
        $email = new EmailController();
        $email->send('soporte@planetalab.xyz', 'MiFacturaLegal.COM', $company->email,  'MFL-Notificación de paquete', view('emails/package_expiration', [
            'package'   => $this->getLastPackage(),
            'available' => $this->documentsAvailableValue,
            'company' => $company
        ]));
    }*/

}