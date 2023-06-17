<?php

use App\Controllers\Api\Auth;
use App\Models\Notification;

function notification($type = 'all')
{
    $notification = new Notification();

    if(!is_null(Auth::querys()->companies_id)) {
        if($type == 'companies'){
            $data =  $notification
	    	->where(['created_at <= ' =>  date('Y-m-d')])
                ->where('companies_id = '. session('user')->companies_id.' or companies_id IS NULL and view = "false" and status = "Active"' )
		
                ->get()
                ->getResult();
        } else {
            $data =  $notification->where(['status' => 'Active'])
	    	->where(['created_at <= ' =>  date('Y-m-d')])
                ->where('companies_id = '. session('user')->companies_id.' or companies_id IS NULL' )
		->orderBy('id', 'desc')
                ->get()
                ->getResult();
        }
    }else {
        $data =  $notification->where(['status' => 'Active'])
            ->get()
            ->getResult();
    }

    foreach ($data as $item){
        $id = explode('°', $item->title);
        $item->url= $item->url.'/'.trim($id[1]);
    }
    return $data;
}

function countNotification()
{
    $notification = new Notification();
    $data =  $notification->findAll();
    return  count($data);
}