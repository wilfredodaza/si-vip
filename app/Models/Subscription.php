<?php


namespace App\Models;


use CodeIgniter\Model;

class Subscription extends Model
{
    protected $table = 'subscriptions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'companies_id',
                    'applicant_id',
                    'packages_id',
                    'start_date',
                    'end_date',
                    'status',
                    'date_due_certificate',
                    'sopport_invoice',
                    'ref_epayco',
                    'price',
                    'seller',
                    'seller_tip'
        ];
}