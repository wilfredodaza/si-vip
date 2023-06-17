<?php
     /***
      * @params $id int
      * @auhtor wilson andres bachiller ortiz
      *@return Array
      */

    function validationRowsNull(int $id): Array
    {
        $customer = new \App\Models\Customer();
        $customers = $customer->asObject()->find($id);



        $i = 0;
        $errors = [];
 
        is_null($customers->name)                               ? $errors[$i++] = 'El campo nombre no se encuentra registrado en el proveedor' : NULL;
        is_null($customers->type_document_identifications_id)   ? $errors[$i++] = 'El campo tipo de documento no se encuentra registrado en el proveedor' : NULL;
        is_null($customers->identification_number)              ? $errors[$i++] = 'El campo numero de identificacion no se encuentra registrado en el proveedor' : NULL;
        is_null($customers->dv)                                 ? $errors[$i++] = 'El campo digito de verificacion no se encuentra registrado en el proveedor' : NULL;
        is_null($customers->phone)                              ? $errors[$i++] = 'El campo teléfono no se encuentra registrado en el proveedor' : NULL;
        is_null($customers->address)                            ? $errors[$i++] = 'El campo direccion no se encuentra registrado en el proveedor' : NULL;
        is_null($customers->email)                              ? $errors[$i++] = 'El campo correo electronico no se encuentra registrado en el proveedor' : NULL;
        is_null($customers->merchant_registration)              ? $errors[$i++] = 'El campo registro mercantil no se encuentra registrado en el proveedor' : NULL;
        is_null($customers->type_regime_id)                     ? $errors[$i++] = 'El campo tipo de regimen no se encuentra registrado en el proveedor' : NULL;
        is_null($customers->municipality_id)                    ? $errors[$i++] = 'El campo municipio no se encuentra registrado en el proveedor' : NULL;
        is_null($customers->type_organization_id)               ? $errors[$i++] = 'El campo tipo organización no se encuentra registrado en el proveedor' : NULL;

        return $errors;
    }
