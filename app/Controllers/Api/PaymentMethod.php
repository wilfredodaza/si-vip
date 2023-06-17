<?php


namespace App\Controllers\Api;


use App\Models\PaymentMethod as PaymentMethodModel;
use CodeIgniter\RESTful\ResourceController;


class PaymentMethod extends ResourceController 
{
  protected $format = 'json';

  public function index()
  {
      $paymentMethod = new PaymentMethodModel();
      $paymentMethods = $paymentMethod->where(['status' => 'Activo'])->asObject()->get()->getResult();
      return  $this->respond([ 'status' => 200, 'data' =>  $paymentMethods]);
  }
}