<?php

namespace App\Traits;



use CodeIgniter\Validation\Exceptions\ValidationException;
use CodeIgniter\HTTP\IncomingRequest;
use Config\Services;


trait ValidationsTrait2
{

    public function getRequestInput(IncomingRequest $request){
        $input = $request->getPost();
        if (empty($input)) {
            $input = json_decode($request->getBody(), true);
        }
        return $input;
    }


    public function validateRequest($input, array $rules, array $messages =[]){
        $this->validator = Services::Validation()->setRules($rules);

        if (is_string($rules)) {
            $validation = config('Validation');
            if (!isset($validation->$rules)) {
                throw ValidationException::forRuleNotFound($rules);
            }

            if (!$messages) {
                $errorName = $rules . '_errors';
                $messages = $validation->$errorName ?? [];
            }

            $rules = $validation->$rules;
        }
        return $this->validator->setRules($rules, $messages)->run($input);
    }

    public function respondHTTP422()
    {
        return $this->respond([ 'status' => 422, 'code' => 422, 'message' => [ 'errors' => $this->validator->getErrors() ] ], 422);
    }




    
}