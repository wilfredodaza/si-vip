<?php


namespace App\Models;


use CodeIgniter\Model;

class AllowanceCharge extends Model
{
    protected $primaryKey       = 'id';
    protected $table            = 'allowance_charges';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'id',
        'discount_id',
        'invoice_id',
        'charge_indicator',
        'allowance_charge_reason',
        'amount',
        'base_amount'
    ];





}