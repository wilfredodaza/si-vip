<?php


namespace App\Controllers;



use App\Models\Notification;

class NotificationController extends BaseController
{

    public function index()
    {

        return view('pages/notification.php');
    }

    public function view($id)
    {
        $notification = new Notification();
        $data = ['view' => 'true'];
        $notification
            ->set($data)
            ->where(['id' => $id])
            ->update();


        echo json_encode(['data' => 'ok']);
        //die();
    }

}