<?php

function encrypterUrl($id) {
    $encrypter = \Config\Services::encrypter();
    return bin2hex($encrypter->encrypt($id));
}

function decrypterUrl($base) {
    try{
        $encrypter = \Config\Services::encrypter();
        return $encrypter->decrypt(hex2bin($base));
    }catch(\Exception $e) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    
}