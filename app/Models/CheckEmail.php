<?php

namespace App\Models;


use \CodeIgniter\Model;

class CheckEmail extends Model
{
    protected $table = "check_emails";
    protected $primaryKey = "id";
    protected $useTimestamps    = true;
    protected $allowedFields = [
        'company_id',
        'folder',
        'date',
        'email_id'
      ];
}