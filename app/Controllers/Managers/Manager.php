<?php

namespace App\Controllers\Managers;

use App\Controllers\BaseController;
use App\Models\Company;

class Manager extends BaseController
{
    /**
     * Id de la compañia
     * @var int
     */
    private $companyId;


    /**
     * Array de Archivos Managare
     * 
     * @var array
     */

    protected $managers = [
        'packages'              => \App\Controllers\Managers\PackageManager::class,
        'subscription'          => \App\Controllers\Managers\SubscriptionManager::class,
        'resolutionDateTo'      => \App\Controllers\Managers\ResolutionDateToManager::class,
        'resolutionNumber'      => \App\Controllers\Managers\ResolutionNumberManager::class
    ];

 

    /**
     * Permite asignar el id de la compañia a quien se realizara la monitorizacion 
     * de las notificaciónes
     * 
     * @param int $companyId Id de la compñaia
     */

    public function setCompanyId(int $companyId): void
    {
        $this->companyId = $companyId;
    
    }


    /**
     * Este metodo se encarga de crear las notificaciones que
     *  apareceran en la seccion de invoice
     * 
     */

    public function createNotification() 
    {


        foreach($this->managers as $manager) {
            $class = new $manager($this->companyId);
            if($class->activateNotification()) {
               $class->notification();
            }
        }
    }

    /**
     *Este metodo se encarga de enviar por email las  notificación de 
     * actualización de suscripción, paquete y resolución
     */

    public function emailNotification() 
    {
        $model = new Company();
        $companies = $model->asObject()->get()->getResult();

        foreach($companies as $item) {
            foreach($this->managers as $manager) {
                $class = new $manager($item->id);
                if($class->activateNotification()) {
                   $class->sendEmail($item);
                }
            }
        }
    }



    
}