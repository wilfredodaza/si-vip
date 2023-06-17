<?php

namespace App\Traits;



use CodeIgniter\Validation\Exceptions\ValidationException;
use CodeIgniter\HTTP\IncomingRequest;
use Config\Services;


trait ValidationsTrait
{


    public function validateRequest($input, array $rules, array $messages =[]){

        $input = $this->getRequestInput($input);

        $this->validator = Services::Validation()->setRules($rules);

        if (is_string($rules)) {
            $validation = config('Validation');

            if (!isset($validation->$rules)) {
                throw ValidationException::forRuleNotFound($rules);
            }

            if (!$messages) {
                $errorName = $rules . '_errors';
                echo $messages = $validation->$errorName ?? [];
            }

            $rules = $validation->$rules;
        }
        return $this->validator->setRules($rules, $messages)->run((array) $input);
    }


    public function getRequestInput(IncomingRequest $request){
        $input = $request->getPost();
        if (empty($input)) {
            //convert request body to associative array
            $input = json_decode($request->getBody(), true);
        }
        return $input;
    }



}
