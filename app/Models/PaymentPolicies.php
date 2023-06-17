<?php


namespace App\Models;


use CodeIgniter\Model;

class PaymentPolicies extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'payment_policies';
    protected $allowedFields = [
        'id',
        'days',
    ];





}
