<?php

namespace App\Controllers\Transforms;

class Transform
{
    protected static function data(  $data = [], $value = []) {

        if($data) {
            $transform = [];
            $info = $data;

            foreach ($value as $item => $key) {
                $valor = explode('->',  $item);
                if(count($valor)  == 1)  {
                    if(is_array($key)){
                        $transform[array_keys($key)[0]] = array_values($key)[0];
                    }else{
                        $transform[$key] = $info->$item;
                    }
                }else {
                    $item1 = $valor[0];
                    $item2 = $valor[1];
                    $transform[$key] = $info->$item1->$item2;
                    die();
                }
            }
            return $transform;
        }else {
            return (object) [];
        }


    }
}
