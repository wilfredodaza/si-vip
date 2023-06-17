<?php

namespace App\Models;

use CodeIgniter\Model;


class DocumentEvent extends Model
{
    protected $table = 'document_event';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'document_id',
        'event_id',
        'type_rejection_id',
        'uuid'
    ];
}