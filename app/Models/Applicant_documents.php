<?php


namespace App\Models;


use CodeIgniter\Model;

class Applicant_documents extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'applicant_documents';
    protected $allowedFields = [
        'id',
        'applicant_id',
        'documento',
        'archivo', 
        'status'
    ];
}