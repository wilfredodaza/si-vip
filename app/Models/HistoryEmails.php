<?php

namespace App\Models;


use \CodeIgniter\Model;

class HistoryEmails extends Model
{
    protected $table = "history_emails";
    protected $primaryKey = "id";
    // protected $useTimestamps    = true;
    protected $allowedFields = [
        'id',
        'shopping_emails_id',
        'users_id',
        'observation',
        'file'
        // 'created_at'
      ];
}