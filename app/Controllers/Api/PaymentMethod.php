<?php


namespace App\Controllers\Api;


use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\AccountingAcount as AccountingAcount;
use CodeIgniter\RESTful\ResourceController;


class PaymentMethod extends ResourceController 
{
  protected $format = 'json';

  public function index()
  {
    if($this->request->getGet('type_sales')){
      $paymentMethod = new AccountingAcount();
      $paymentMethods = $paymentMethod->where(['status' => 'Activa', 'type_accounting_account_id' => 5])->asObject()->get()->getResult();
    }else{
      $paymentMethod = new PaymentMethodModel();
      $paymentMethods = $paymentMethod->where(['status' => 'Activo'])->asObject()->get()->getResult();
    }
      return  $this->respond([ 'status' => 200, 'data' =>  $paymentMethods]);
  }
}