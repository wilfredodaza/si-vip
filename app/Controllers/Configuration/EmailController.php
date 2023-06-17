<?php


namespace App\Controllers\Configuration;

use Config\Services;
use App\Controllers\BaseController;
use SendGrid\Mail\Mail;


class EmailController extends BaseController
{
    protected $email;

    public function __construct()
    {
        $this->email = Services::email();
    }

    public function send($from, $name, $to, $subject, $message, $archivos = [])
    {
        $email = Services::email();
        $email->setFrom( 'soporte@mifacturalegal.com', 'MiFacturaLegal.COM');
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($message);
        foreach ($archivos as $key) {
            $email->attach($key);
        }
        $email->send();

        /*
         $email = new Mail();
        $email->setFrom(getenv('EMAIL'), 'MiFacturaLegal.COM');
        $email->setSubject($subject);
        $email->addTo($to);
        $email->addContent("text/html",  $message);


        foreach ($archivos as $key) {
            $divider = explode('/', $key);
            $email->addAttachment(base64_encode(file_get_contents($key)), 'application/json', $divider[count($divider) - 1]);
        }
        $sendgrid = new \SendGrid(getenv('API_SENDGRID'));
        try {
            $response = $sendgrid->send($email);
        } catch (\Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
       */
    }

 
}