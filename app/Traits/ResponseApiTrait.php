<?php


namespace App\Traits;


trait ResponseApiTrait
{
    public function messageError() {
        return $this->respond([
            'status'    => 400,
            'message'   => 'Bat Request',
        ], 400);
    }

    public function messageSuccess($data) {
        return $this->respond([
            'status'  => '200',
            'message' => 'ok',
            'data'    => $data
        ], 200);
    }

    public function messageCreate($data) {
        return $this->respond([
            'status'    => 201,
            'message'   => 'created success',
            'data'    => $data
        ], 201);
    }

    public function messageNotFount() {
        return $this->respond([
            'status' => 404,
            'message'   => 'Not Fount'
        ], 404);
    }
}