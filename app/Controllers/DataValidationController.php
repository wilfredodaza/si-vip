<?php

namespace App\Controllers;

use App\Controllers\companies\Functions_Payroll;
use App\Models\Cargue;
use App\Models\PaymentMethod;

class DataValidationController extends BaseController
{
    public $cargue;
    public $payment_method;
    public $functions_payroll;
    public $companies_as_nomina;
    public $companies_tyc;
    private $document;
    private $period;

    public function __construct()
    {
        $this->cargue = new Cargue();
        $this->payment_method = new PaymentMethod();
        $this->functions_payroll = new Functions_Payroll();
        $this->companies_as_nomina = [
            800174904, // etipress
            900306514, // zona de seguridad
            830015914, //polyuprotec
            800210669, // transgalaxia
            900739531, // servimos integral
            860000824, // Niver
            830065471, // grancolservig
            800188477, // grancolombiana
            830090360, // san marcos
            901131030, // master energy
            860054862, //ingemol
            900803958, // altara
            830056362, // andina rodilos
            800198348, // ventas institucionales
            900444608, //iplanet
            901084243, //market
            900804617, // giron
            901347237
        ];
        $this->companies_tyc = [
            900782726, //tyc
            901030030, // simetrik
            901400629, // commure
            901441683, // sumer
            901233605, // mubler
            901515179, // fjm
            901427659, // melonn
            901433542, // heal room
            901465526, // onza
            901005608, // tyc contadores
            901525415 //gelt
        ];
    }

    public function validation($nit, $month)
    {
        if (in_array(company()->identification_number, $this->companies_as_nomina)) {
            return $this->asnomina_validation($nit, $month);
        } elseif (in_array(company()->identification_number, $this->companies_tyc)) {
            return $this->tyc_validation($nit, $month);
        } elseif (company()->identification_number == 900515864 || company()->identification_number == 900082400) {
            return $this->suarez_leon_validation($nit, $month);
        } elseif (company()->identification_number == 830010090 || company()->identification_number == 900061320 ) {
            return $this->am_validation($nit, $month);
        }elseif (company()->identification_number == 901112882){
            return $this->biotech_validation($nit, $month);
        }elseif (company()->identification_number == 900127454 || company()->identification_number == 900744829){
            return $this->Tampa_validation($nit, $month);
        }
    }

    private function asnomina_validation($nit, $month_payroll)
    {
        $code_method_payment = [2, 3, 4, 5, 6, 7, 21, 22, 30, 31, 42, 45, 46, 47];
        $exception_validation_bank = ['CHEQUE', 'EFECTIVO', 'PAGOS EFECTIVO'];
        $errores = [];
        $ajust = 0;
        $cedulas = [];
        $where = ['nit' => $nit, 'month_payroll' => $month_payroll, 'status' => 'Inactive'];
        $data = $this->cargue->where($where)->get()->getResult();
        if (count($data) < 1 || $data == null || empty($data)) {
            return $response = ['status' => 'vacio'];
        }
        foreach ($data as $info) {
            $json = json_decode($info->data);
            $error = [];

            if (in_array($json->Numero_de_identificacion, $cedulas)) {
                $ajust++;
                $this->document = $info->type_document_payroll;
                $this->period = $info->period_id;
                // se realiza validación del campo Tipo trabajador
                if (empty($json->Tipo_trabajador) || $json->Tipo_trabajador == 0) {
                    array_push($error, 'El campo tipo trabajador es un campo obligatorio( El cero se toma como campo vacio). ');
                }
                if (!is_numeric($json->Tipo_trabajador)) {
                    array_push($error, 'El campo tipo trabajador debe ser un dato Númerico. ');
                }
                // se realiza validación del campo Subtipo trabajador
                if (!isset($json->Subtipo_trabajador)) {
                    array_push($error, 'El campo subtipo trabajador es un campo obligatorio');
                }
                if (!is_numeric($json->Subtipo_trabajador)) {
                    array_push($error, 'El campo subtipo trabajador debe ser un dato Númerico. ');
                }
                // se realiza validación del campo tipo documento
                if (empty($json->Tipo_de_documento)) {
                    array_push($error, 'El campo tipo de documento es un campo obligatorio.');
                }
                if (!is_numeric($json->Tipo_de_documento)) {
                    array_push($error, 'El campo tipo documento debe ser un dato Númerico.');
                }
                if ($json->Tipo_de_documento == 0 || $json->Tipo_de_documento > 6) {
                    array_push($error, 'El Tipo de documento no esta registrado en el sistemas.');
                }
                // se realiza validación del campo Municipio
                if (empty($json->Municipio)) {
                    array_push($error, 'El campo Municipio es un campo obligatorio.');
                }
                if (!is_numeric($json->Tipo_de_documento)) {
                    array_push($error, 'El campo Municipio debe ser un dato Númerico. ');
                }
                // se realiza validación del campo Tipo de contrato
                if (empty($json->Tipo_de_contrato)) {
                    array_push($error, 'El campo  tipo de contrato es obligatorio. ');
                }
                if (!is_numeric($json->Tipo_de_contrato)) {
                    array_push($error, 'El campo  tipo de contrato debe ser un dato Númerico. ');
                }
                // se realiza validación del campo numero de indentificacion
                if (empty($json->Numero_de_identificacion)) {
                    array_push($error, 'El campo número de identificación es obligatorio. ');
                }
                if (!is_numeric($json->Numero_de_identificacion)) {
                    array_push($error, 'El campo número de identificación debe contener solo datos numericos Númericos. ');
                }
                // se realiza validación del apellido, segundo apellido , primer nombre
                if (empty($json->Apellido)) {
                    array_push($error, 'El campo apellido es obligatorio. ');
                }
                // segundo apellido
                if (empty($json->Primer_nombre)) {
                    array_push($error, 'El campo primer nombre es obligatorio. ');
                }
                if (strlen($json->Apellido) > 60) {
                    array_push($error, 'El campo apellido debe tener maximo 60 caracteres. ');
                }
                if (strlen($json->Segundo_apellido) > 60) {
                    array_push($error, 'El campo segundo apellido debe tener maximo 60 caracteres ');
                }
                if (strlen($json->Primer_nombre) > 60) {
                    array_push($error, 'El campo primer nombre debe tener maximo 60 caracteres ');
                }
                // se realiza validacion de campo
                if (in_array($json->metodo_de_pago, $code_method_payment)) {
                    // se realiza validacion al campo nombre del banco
                    if (empty($json->Nombre_banco)) {
                        array_push($error, 'El campo nombre banco es obligatorio. ');
                    }
                    if (empty($json->Tipo_de_cuenta)) {
                        array_push($error, 'El campo Tipo de cuenta es obligatorio. ');
                    }
                    if (!is_numeric($json->Tipo_de_cuenta)) {
                        array_push($error, 'El campo Tipo de cuenta debe ser un dato Númerico. ');
                    }
                }
                if (!is_numeric($json->dias_trabajados)) {
                    array_push($error, 'El campo dias trabajados debe contener datos Númerico. ');
                }

                array_push($cedulas, $json->Numero_de_identificacion);
            }
            //tipo de contrato
            if ($json->Tipo_de_contrato < 1 && $json->Tipo_de_contrato > 2) {
                array_push($error, 'El campo tipo de contrato no coincide con los codigos asignados en el sistema. ');
            }

            // se realiza validacion salario
            if (empty($json->Salario)) {
                array_push($error, 'El campo salario es obligatorio. ');
            }
            if (!is_numeric($json->Salario)) {
                array_push($error, 'El campo salario debe contener solo caracteres numericos. ');
            }
            // se realiza validacion del campo Fecha ingreso
            if (empty($json->Fecha_ingreso)) {
                array_push($error, 'El campo fecha ingreso es obligatorio. ');
            }
            // se realiza validacion del campo Fecha inicio liquidacion
            if (empty($json->Fecha_inicio_liquidacion)) {
                array_push($error, 'El campo fecha inicio liquidacion es obligatorio. ');
            }
            // se realiza validacion del campo Fecha fin liquidacion
            if (empty($json->fecha_fin_liquidacion)) {
                array_push($error, 'El campo fecha fin liquidacion es obligatorio. ');
            }
            // se realiza validacion del campo periodo de nomina
            if (empty($json->periodo_de_nomina)) {
                array_push($error, 'El periodo de nomina es obligatorio. ');
            }
            if (!is_numeric($json->periodo_de_nomina)) {
                array_push($error, 'El periodo de nomina debe ser un dato Númerico. ');
            }
            // se realiza validacion al campo metodo de pago
            if (empty($json->metodo_de_pago)) {
                array_push($error, 'El metodo de pago es obligatorio. ');
            } else {
                if ($this->payment_method->where(['id' => $json->metodo_de_pago])->countAllResults() < 1) {
                    array_push($error, 'El metodo de pago no se encuentra en el sistema. ');
                }
            }
            //validacion de bancos
            if (in_array($json->metodo_de_pago, $code_method_payment)) {
                if ($this->functions_payroll->eliminar_tildes($json->Nombre_banco) != '' && !in_array($this->functions_payroll->eliminar_tildes($json->Nombre_banco), $exception_validation_bank)) {
                    if ($this->functions_payroll->validation_banks($this->functions_payroll->eliminar_tildes($json->Nombre_banco))) {
                        array_push($error, 'El banco: "' . $json->Nombre_banco . '" No esta registrado en el sistema o esta Inactivo, para Validarlo de click <a href="' . base_url('/other_banks') . '">aqui.</a>');
                    }
                }
            }
            //validacion campo valor
            if (!is_numeric($json->Valor)) {
                array_push($error, 'El campo Valor debe contener datos Númerico. ');
            }
            //validation fecha
            if (!$this->validateDate($json->Fecha_inicio_liquidacion) && !empty($json->Fecha_inicio_liquidacion)) {
                array_push($error, 'La fecha de inicio de liquidación es obligatorio. ');
            }
            if (!$this->validateDate($json->Fecha_ingreso) && !empty($json->Fecha_ingreso)) {
                array_push($error, 'La fecha de Ingreso es obligatoria. ');
            }
            //validacion de conceptos inactivos
            if ($this->functions_payroll->validation_concepts($this->functions_payroll->eliminar_tildes($json->Conceptos_pagos))) {
                array_push($error, 'El Concepto "' . $json->Conceptos_pagos . '" se encuentra Inactivo en el sistema, para activarlo de click <a href="' . base_url('/other_concepts') . '">aqui.</a>');
            }
            //validacion de conceptos existentes
            if ($this->functions_payroll->validation_concepts($this->functions_payroll->eliminar_tildes($json->Conceptos_pagos), false)) {
                array_push($error, 'El concepto: "' . $json->Conceptos_pagos . '" No esta registrado en el sistema, para registrarlo de click <a href="' . base_url('/other_concepts') . '">aqui.</a>');
            }

            if (count($error) > 0) {
                $data_error = [
                    'Empleado' => $json->Numero_de_identificacion,
                    'errores' => $error
                ];
                array_push($errores, $data_error);
            }
        }
        if($ajust > $this->functions_payroll->quantity_workers($this->period) && $this->document == 10){
            $data_error = [
                'Empleado' => 'soporte',
                'errores' => 'No se puede realizar la validacion correspondiente ya que se encuentran mas datos en el archivo'
            ];
            array_push($errores, $data_error);
        }
        if (count($errores) > 0) {
            $response = [
                'status' => 'error',
                'data' => $errores,
            ];
            foreach ($data as $info) {
                $this->cargue->delete($info->id);
            }
        } else {
            $response = [
                'status' => 'success'
            ];
        }
        return $response;
    }

    private function tyc_validation($nit, $month_payroll): array
    {
        $ajust = 0 ;
        $errores = [];
        $code_method_payment = [2, 3, 4, 5, 6, 7, 21, 22, 30, 31, 42, 45, 46, 47];
        $where = ['nit' => $nit, 'month_payroll' => $month_payroll, 'status' => 'Inactive'];
        $data = $this->cargue->where($where)->get()->getResult();
        if (count($data) < 1 || $data == null || empty($data)) {
            return $response = ['status' => 'vacio'];
        }
        foreach ($data as $info) {
            $json = json_decode($info->data);
            $ajust++;
            $this->document = $info->type_document_payroll;
            $this->period = $info->period_id;
            $error = [];
            // se realiza validación del campo tipo documento
            if (empty($json->TIPO_DE_DOCUMENTO)) {
                array_push($error, 'El campo tipo de identificacion es un campo obligatorio.');
            }
            // se realiza validación del campo Municipio
            /*if (empty($json->Municipio)) {
                array_push($error, 'El campo Municipio es un campo obligatorio.');
            }
            if (!is_numeric($json->Municipio)) {
                array_push($error, 'El campo Municipio debe ser un dato Númerico. ');
            }*/
            // se realiza validación del campo Tipo de contrato
            if (empty($json->CONTRATO)) {
                array_push($error, 'El campo  tipo de contrato es obligatorio. ');
            }
            /*if (!is_string($json->Tipo_de_contrato)) {
                array_push($error, 'El campo  tipo de contrato debe ser un dato alfanumerico. ');
            }*/
            // se realiza validación del campo numero de indentificacion
            if (empty($json->IDENTIFICACION)) {
                array_push($error, 'El campo número de identificación es obligatorio. ');
            }
            // se realiza validacion del campo direccion
            if (empty($json->DIRECCION)) {
                array_push($error, 'El campo dirección es obligatorio. ');
            }/*
            // se realiza validacion salario integral
            if (empty($json->Tiene_salario_Integral)) {
                array_push($error, 'El campo salario integral es obligatorio. ');
            }*/
            // se realiza validacion salario
            /*if (empty($json->Salario_nomina)) {
                array_push($error, 'El campo salario es obligatorio. ');
            }
            if (!is_numeric($json->Salario_nomina)) {
                array_push($error, 'El campo salario debe contener solo caracteres numericos. ');
            }*/
            // se realiza validacion del campo Fecha ingreso
            if (empty($json->FECHA_INGRESO)) {
                array_push($error, 'El campo fecha ingreso es obligatorio. ');
            }
            // validacion bancos
            if(!empty($json->BANCO)){
                if ($this->functions_payroll->validation_banks($json->BANCO)) {
                    array_push($error, 'El banco: "' . $json->BANCO . '", No esta registrado en el sistema');
                }
            }
            // se realiza validacion del campo Fecha inicio liquidacion
            /*if (empty($json->Fecha_inicio_de_liquidacion)) {
                array_push($error, 'El campo fecha inicio liquidacion es obligatorio. ');
            }
            // se realiza validacion del campo Fecha fin liquidacion
            if (empty($json->Fecha_fin_liquidacion)) {
                array_push($error, 'El campo fecha fin liquidacion es obligatorio. ');
            }*/

            // validacion de campo dias trabajados
            /*if (empty($json->Dias_Laborados_nomina)) {
                array_push($error, 'El campo dias trabajados es obligatorio. ');
            }
            if (!is_numeric($json->Dias_Laborados_nomina)) {
                array_push($error, 'El campo dias trabajados debe contener datos Númerico. ');
            }*/
            // validacion dias vacaciones compensadas
            /*if($json->Vacaciones_compensadas > 0){
                if(empty($json->Dias_Habiles_de_Vacaciones_compensadas)){
                    array_push($error, 'Cantidad de dias en vacaciones debe ser mayor a cero');
                }
            }
            // validacion dias vacaciones
            if($json->Vacaciones > 0){
                if(empty($json->Dias_Calendario_de_Vacaciones)){
                    array_push($error, 'Cantidad de dias en vacaciones debe ser mayor a cero');
                }
            }*/
            //validation
            if (count($error) > 0) {
                $data_error = [
                    'Empleado' => $json->IDENTIFICACION,
                    'errores' => $error
                ];
                array_push($errores, $data_error);
            }
        }
        if($ajust > $this->functions_payroll->quantity_workers($this->period) && $this->document == 10){
            $data_error = [
                'Empleado' => 'soporte',
                'errores' => 'No se puede realizar la validacion correspondiente ya que se encuentran mas datos en el archivo'
            ];
            array_push($errores, $data_error);
        }
        if (count($errores) > 0) {
            $response = [
                'status' => 'error',
                'data' => $errores,
            ];
            foreach ($data as $info) {
                $this->cargue->delete($info->id);
            }
        } else {
            $response = [
                'status' => 'success'
            ];
        }
        return $response;
    }

    private function suarez_leon_validation($nit, $month_payroll): array
    {
        $errores = [];
        $response = [];
        $where = ['nit' => $nit, 'month_payroll' => $month_payroll, 'status' => 'Inactive'];
        $data = $this->cargue->where($where)->get()->getResult();
        if (count($data) < 1 || $data == null || empty($data)) {
            return $response = ['status' => 'vacio'];
        }
        foreach ($data as $info) {
            $json = json_decode($info->data);
            $error = [];
            // se realiza validación del campo Tipo trabajador
            if (empty($json->TIPO_DE_EMPLEADO) || $json->TIPO_DE_EMPLEADO == '') {
                array_push($error, 'El campo tipo trabajador es un campo obligatorio. ');
            }
            // se realiza validación del campo Subtipo trabajador
            if (!isset($json->SUBTIPO_DE_TRABAJADOR)) {
                array_push($error, 'El campo subtipo trabajador es un campo obligatorio');
            }
            if (!is_numeric($json->SUBTIPO_DE_TRABAJADOR)) {
                array_push($error, 'El campo subtipo trabajador debe ser un dato alfanumerico. ');
            }
            // se realiza validación del campo tipo documento
            if (empty($json->TIPO_DE_DOCUMENTO)) {
                array_push($error, 'El campo tipo de identificacion es un campo obligatorio.');
            }
            // se realiza validación del campo Municipio
            /*if (empty($json->Municipio)) {
                array_push($error, 'El campo Municipio es un campo obligatorio.');
            }
            if (!is_numeric($json->Municipio)) {
                array_push($error, 'El campo Municipio debe ser un dato Númerico. ');
            }*/
            // se realiza validación del campo Tipo de contrato
            if (empty($json->TIPO_DE_CONTRATO)) {
                array_push($error, 'El campo  tipo de contrato es obligatorio. ');
            }
            // se realiza validación del campo numero de indentificacion
            if (empty($json->NUMERO_DE_IDENTIFICACION)) {
                array_push($error, 'El campo número de identificación es obligatorio. ');
            }
            // se realiza validacion del campo direccion
            /*if (empty($json->Direccion)) {
                array_push($error, 'El campo dirección es obligatorio. ');
            }*
            // se realiza validacion salario integral
            if (empty($json->Tiene_salario_Integral)) {
                array_push($error, 'El campo salario integral es obligatorio. ');
            }
            // se realiza validacion salario
            /*if (empty($json->Salario_nomina)) {
                array_push($error, 'El campo salario es obligatorio. ');
            }
            if (!is_numeric($json->Salario_nomina)) {
                array_push($error, 'El campo salario debe contener solo caracteres numericos. ');
            }*/
            // se realiza validacion del campo Fecha ingreso
            if (empty($json->FECHA_DE_ADMISION)) {
                array_push($error, 'El campo fecha ingreso es obligatorio. ');
            }
            // se realiza validacion del campo Fecha inicio liquidacion
            /*if (empty($json->Fecha_inicio_de_liquidacion)) {
                array_push($error, 'El campo fecha inicio liquidacion es obligatorio. ');
            }
            // se realiza validacion del campo Fecha fin liquidacion
            if (empty($json->Fecha_fin_liquidacion)) {
                array_push($error, 'El campo fecha fin liquidacion es obligatorio. ');
            }*/

            // validacion de campo dias trabajados
            /*if (empty($json->DIAS_TRABAJADOS)) {
                array_push($error, 'El campo dias trabajados es obligatorio. ');
            }*/
            // validacion dias vacaciones compensadas
            /*if($json->Vacaciones_compensadas > 0){
                if(empty($json->Dias_Habiles_de_Vacaciones_compensadas)){
                    array_push($error, 'Cantidad de dias en vacaciones debe ser mayor a cero');
                }
            }
            // validacion dias vacaciones
            if($json->Vacaciones > 0){
                if(empty($json->Dias_Calendario_de_Vacaciones)){
                    array_push($error, 'Cantidad de dias en vacaciones debe ser mayor a cero');
                }
            }*/
            //validation
            if (count($error) > 0) {
                $data_error = [
                    'Empleado' => $json->NUMERO_DE_IDENTIFICACION,
                    'errores' => $error
                ];
                array_push($errores, $data_error);
            }
        }
        if (count($errores) > 0) {
            $response = [
                'status' => 'error',
                'data' => $errores,
            ];
            foreach ($data as $info) {
                $this->cargue->delete($info->id);
            }
        } else {
            $response = [
                'status' => 'success'
            ];
        }
        return $response;
    }

    private function am_validation($nit, $month_payroll): array
    {
        $errores = [];
        $response = [];
        $where = ['nit' => $nit, 'month_payroll' => $month_payroll, 'status' => 'Inactive'];
        $data = $this->cargue->where($where)->get()->getResult();
        if (count($data) < 1 || $data == null || empty($data)) {
            return $response = ['status' => 'vacio'];
        }
        foreach ($data as $info) {
            $json = json_decode($info->data);
            $error = [];
            // se realiza validación del campo Tipo trabajador
            if (empty($json->TIPO_TRABAJADOR) || $json->TIPO_TRABAJADOR == '') {
                array_push($error, 'El campo tipo trabajador es un campo obligatorio. ');
            }
            // se realiza validación del campo Subtipo trabajador
            if (!isset($json->subtipo_de_trabajo)) {
                array_push($error, 'El campo subtipo trabajador es un campo obligatorio');
            }
            if (is_numeric($json->subtipo_de_trabajo)) {
                array_push($error, 'El campo subtipo trabajador debe ser un dato alfanumerico. ');
            }
            // se realiza validación del campo tipo documento
            if (empty($json->TIPO_DOCUMENTO)) {
                array_push($error, 'El campo tipo de identificacion es un campo obligatorio.');
            }
            // se realiza validación del campo Municipio
            /*if (empty($json->Municipio)) {
                array_push($error, 'El campo Municipio es un campo obligatorio.');
            }
            if (!is_numeric($json->Municipio)) {
                array_push($error, 'El campo Municipio debe ser un dato Númerico. ');
            }*/
            // se realiza validación del campo Tipo de contrato
            if (empty($json->TIPO_CONTRATO)) {
                array_push($error, 'El campo  tipo de contrato es obligatorio. ');
            }
            // se realiza validación del campo numero de indentificacion
            if (empty($json->Identificacion)) {
                array_push($error, 'El campo número de identificación es obligatorio. ');
            }
            // se realiza validacion del campo direccion
            /*if (empty($json->Direccion)) {
                array_push($error, 'El campo dirección es obligatorio. ');
            }*
            // se realiza validacion salario integral
            if (empty($json->Tiene_salario_Integral)) {
                array_push($error, 'El campo salario integral es obligatorio. ');
            }
            // se realiza validacion salario
            /*if (empty($json->Salario_nomina)) {
                array_push($error, 'El campo salario es obligatorio. ');
            }
            if (!is_numeric($json->Salario_nomina)) {
                array_push($error, 'El campo salario debe contener solo caracteres numericos. ');
            }*/
            // se realiza validacion del campo Fecha ingreso
            if (empty($json->FECHA_DE_ADMISION)) {
                array_push($error, 'El campo fecha ingreso es obligatorio. ');
            }
            // se realiza validacion del campo Fecha inicio liquidacion
            /*if (empty($json->Fecha_inicio_de_liquidacion)) {
                array_push($error, 'El campo fecha inicio liquidacion es obligatorio. ');
            }
            // se realiza validacion del campo Fecha fin liquidacion
            if (empty($json->Fecha_fin_liquidacion)) {
                array_push($error, 'El campo fecha fin liquidacion es obligatorio. ');
            }*/

            // validacion de campo dias trabajados
            /*if (empty($json->DIAS_TRABAJADOS)) {
                array_push($error, 'El campo dias trabajados es obligatorio. ');
            }*/
            // validacion dias vacaciones compensadas
            /*if($json->Vacaciones_compensadas > 0){
                if(empty($json->Dias_Habiles_de_Vacaciones_compensadas)){
                    array_push($error, 'Cantidad de dias en vacaciones debe ser mayor a cero');
                }
            }
            // validacion dias vacaciones
            if($json->Vacaciones > 0){
                if(empty($json->Dias_Calendario_de_Vacaciones)){
                    array_push($error, 'Cantidad de dias en vacaciones debe ser mayor a cero');
                }
            }*/
            //validation
            if (count($error) > 0) {
                $data_error = [
                    'Empleado' => $json->Identificacion,
                    'errores' => $error
                ];
                array_push($errores, $data_error);
            }
        }
        if (count($errores) > 0) {
            $response = [
                'status' => 'error',
                'data' => $errores,
            ];
            foreach ($data as $info) {
                $this->cargue->delete($info->id);
            }
        } else {
            $response = [
                'status' => 'success'
            ];
        }
        return $response;
    }

    private function biotech_validation($nit, $month_payroll): array
    {
        $errores = [];
        $code_method_payment = [2, 3, 4, 5, 6, 7, 21, 22, 30, 31, 42, 45, 46, 47];
        $where = ['nit' => $nit, 'month_payroll' => $month_payroll, 'status' => 'Inactive'];
        $data = $this->cargue->where($where)->get()->getResult();
        if (count($data) < 1 || $data == null || empty($data)) {
            return $response = ['status' => 'vacio'];
        }
        foreach ($data as $info) {
            $json = json_decode($info->data);
            $error = [];
            // se realiza validación del campo Tipo trabajador
            if (empty($json->Tipo_de_trabajador) || $json->Tipo_de_trabajador == '') {
                array_push($error, 'El campo tipo trabajador es un campo obligatorio. ');
            }

            // se realiza validación del campo Subtipo trabajador
            if (!isset($json->Subtipo_de_trabajador)) {
                array_push($error, 'El campo subtipo trabajador es un campo obligatorio');
            }

            // se realiza validación del campo tipo documento
            if (empty($json->Tipo_de_identificacion)) {
                array_push($error, 'El campo tipo de identificacion es un campo obligatorio.');
            }

            // se realiza validación del campo Tipo de contrato
            if (empty($json->Tipo_de_contrato)) {
                array_push($error, 'El campo  tipo de contrato es obligatorio. ');
            }
            /*if (!is_string($json->Tipo_de_contrato)) {
                array_push($error, 'El campo  tipo de contrato debe ser un dato alfanumerico. ');
            }*/
            // se realiza validación del campo numero de indentificacion
            if (empty($json->Numero_de_Identificacion)) {
                array_push($error, 'El campo número de identificación es obligatorio. ');
            }
            // se realiza validacion del campo direccion
            if (empty($json->Direccion)) {
                array_push($error, 'El campo dirección es obligatorio. ');
            }
            // se realiza validacion del campo Fecha ingreso
            if (empty($json->Fecha_Ingreso)) {
                array_push($error, 'El campo fecha ingreso es obligatorio. ');
            }
            // validacion bancos
            if (in_array($json->Codigo_Metodo_de_pago, $code_method_payment)) {
                if ($this->functions_payroll->validation_banks($this->functions_payroll->eliminar_tildes($json->BANCO))) {
                    array_push($error, 'El banco: "' . $json->BANCO . '" No esta registrado en el sistema o esta Inactivo, para Validarlo de click <a href="' . base_url('/other_banks') . '">aqui.</a>');
                }
            }
            // se realiza validacion del campo Fecha inicio liquidacion
            /*if (empty($json->Fecha_inicio_de_liquidacion)) {
                array_push($error, 'El campo fecha inicio liquidacion es obligatorio. ');
            }
            // se realiza validacion del campo Fecha fin liquidacion
            if (empty($json->Fecha_fin_liquidacion)) {
                array_push($error, 'El campo fecha fin liquidacion es obligatorio. ');
            }*/

            // validacion de campo dias trabajados
            /*if (empty($json->Dias_Laborados_nomina)) {
                array_push($error, 'El campo dias trabajados es obligatorio. ');
            }
            if (!is_numeric($json->Dias_Laborados_nomina)) {
                array_push($error, 'El campo dias trabajados debe contener datos Númerico. ');
            }*/
            // validacion dias vacaciones compensadas
            /*if($json->Vacaciones_compensadas > 0){
                if(empty($json->Dias_Habiles_de_Vacaciones_compensadas)){
                    array_push($error, 'Cantidad de dias en vacaciones debe ser mayor a cero');
                }
            }
            // validacion dias vacaciones
            if($json->Vacaciones > 0){
                if(empty($json->Dias_Calendario_de_Vacaciones)){
                    array_push($error, 'Cantidad de dias en vacaciones debe ser mayor a cero');
                }
            }*/
            //validation
            if (count($error) > 0) {
                $data_error = [
                    'Empleado' => $json->Numero_de_Identificacion,
                    'errores' => $error
                ];
                array_push($errores, $data_error);
            }
        }
        if (count($errores) > 0) {
            $response = [
                'status' => 'error',
                'data' => $errores,
            ];
            foreach ($data as $info) {
                $this->cargue->delete($info->id);
            }
        } else {
            $response = [
                'status' => 'success'
            ];
        }
        return $response;
    }

    private function Tampa_validation($nit, $month_payroll): array
    {
        $errores = [];
        $response = [];
        $where = ['nit' => $nit, 'month_payroll' => $month_payroll, 'status' => 'Inactive'];
        $data = $this->cargue->where($where)->get()->getResult();
        if (count($data) < 1 || $data == null || empty($data)) {
            return $response = ['status' => 'vacio'];
        }
        foreach ($data as $info) {
            $json = json_decode($info->data);
            $error = [];
            // se realiza validación del campo Tipo trabajador
            if (empty($json->TIPO_TRABAJADOR) || $json->TIPO_TRABAJADOR == '') {
                array_push($error, 'El campo tipo trabajador es un campo obligatorio. ');
            }
            // se realiza validación del campo Subtipo trabajador
            if (!isset($json->subtipo_de_trabajo)) {
                array_push($error, 'El campo subtipo trabajador es un campo obligatorio');
            }

            // se realiza validación del campo tipo documento
            if (empty($json->TIPO_DOCUMENTO)) {
                array_push($error, 'El campo tipo de identificacion es un campo obligatorio.');
            }
            // se realiza validación del campo Municipio
            /*if (empty($json->Municipio)) {
                array_push($error, 'El campo Municipio es un campo obligatorio.');
            }
            if (!is_numeric($json->Municipio)) {
                array_push($error, 'El campo Municipio debe ser un dato Númerico. ');
            }*/
            // se realiza validación del campo Tipo de contrato
            if (empty($json->TIPO_CONTRATO)) {
                array_push($error, 'El campo  tipo de contrato es obligatorio. ');
            }
            // se realiza validación del campo numero de indentificacion
            if (empty($json->Identificacion)) {
                array_push($error, 'El campo número de identificación es obligatorio. ');
            }
            // se realiza validacion del campo direccion
            /*if (empty($json->Direccion)) {
                array_push($error, 'El campo dirección es obligatorio. ');
            }*
            // se realiza validacion salario integral
            if (empty($json->Tiene_salario_Integral)) {
                array_push($error, 'El campo salario integral es obligatorio. ');
            }
            // se realiza validacion salario
            /*if (empty($json->Salario_nomina)) {
                array_push($error, 'El campo salario es obligatorio. ');
            }
            if (!is_numeric($json->Salario_nomina)) {
                array_push($error, 'El campo salario debe contener solo caracteres numericos. ');
            }*/
            // se realiza validacion del campo Fecha ingreso
            if (empty($json->FECHA_DE_ADMISION)) {
                array_push($error, 'El campo fecha ingreso es obligatorio. ');
            }
            // se realiza validacion del campo Fecha inicio liquidacion
            /*if (empty($json->Fecha_inicio_de_liquidacion)) {
                array_push($error, 'El campo fecha inicio liquidacion es obligatorio. ');
            }
            // se realiza validacion del campo Fecha fin liquidacion
            if (empty($json->Fecha_fin_liquidacion)) {
                array_push($error, 'El campo fecha fin liquidacion es obligatorio. ');
            }*/

            // validacion de campo dias trabajados
            /*if (empty($json->DIAS_TRABAJADOS)) {
                array_push($error, 'El campo dias trabajados es obligatorio. ');
            }*/
            // validacion dias vacaciones compensadas
            /*if($json->Vacaciones_compensadas > 0){
                if(empty($json->Dias_Habiles_de_Vacaciones_compensadas)){
                    array_push($error, 'Cantidad de dias en vacaciones debe ser mayor a cero');
                }
            }
            // validacion dias vacaciones
            if($json->Vacaciones > 0){
                if(empty($json->Dias_Calendario_de_Vacaciones)){
                    array_push($error, 'Cantidad de dias en vacaciones debe ser mayor a cero');
                }
            }*/
            //validation
            if (count($error) > 0) {
                $data_error = [
                    'Empleado' => $json->Identificacion,
                    'errores' => $error
                ];
                array_push($errores, $data_error);
            }
        }
        if (count($errores) > 0) {
            $response = [
                'status' => 'error',
                'data' => $errores,
            ];
            foreach ($data as $info) {
                $this->cargue->delete($info->id);
            }
        } else {
            $response = [
                'status' => 'success'
            ];
        }
        return $response;
    }

    function validateDate($date): bool
    {
        $valores = explode('/', $date);
        if (count($valores) == 3) {
            return true;
        }
        return false;
    }
}
