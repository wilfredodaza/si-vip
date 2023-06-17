<?php

namespace App\Models;


use CodeIgniter\Model;

class OtherConcepts extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'other_concepts';
    protected $allowedFields = [
        'id',
        'companies_id',
        'name',
        'type_concept',
        'status',
        'type_other',
        'concept_dian',
        'id_concept_helisa'
    ];
}
