<?php


namespace App\Controllers;


class NoteCreditController extends BaseController
{
    public function index($id)
    {
        return  view('angular/note_credit', ['id' => $id]);
    }
}