<?php

namespace App\Controllers;

class ModuleController extends BaseController
{
    public function ubication($postion = null)
    {
        $session = session();
        $session->set('module', $postion);
        echo json_encode($postion);die();
    }
}