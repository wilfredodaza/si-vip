<?php


namespace App\Models;

use CodeIgniter\Model;

class Configuration extends Model
{
    protected $table = 'configurations';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id',
        'name_app',
        'icon_app',
        'email',
        'logo_menu',
        'intro',
        'footer',
        'alert_title',
        'alert_body',
        'status_alert'
    ];
}