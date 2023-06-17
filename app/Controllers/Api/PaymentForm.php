<?php


namespace App\Controllers\Api;


use App\Models\PaymentForm as PaymentFormModel;
use CodeIgniter\RESTful\ResourceController;


class PaymentForm extends ResourceController 
{
  protected $format = 'json';

  public function index()
  {
      $paymentMethod = new PaymentFormModel();
      $paymentMethods = $paymentMethod->asObject()->get()->getResult();
      return  $this->respond([ 'status' => 200, 'data' =>  $paymentMethods]);
  }

}