<?php

namespace App\Controllers;



class DocumentSupportAdjustController extends BaseController
{

    public function create($id = null)
    {
        return view('document_support_adjust/create', ['id' => $id]);
    }

    public function update($id = null)
    {
        return view('document_support_adjust/edit', ['id' => $id]);
    }
}