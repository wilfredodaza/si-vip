<?php

/**
 * Trait encagado para validación de datos de excel
 * @author Wilson Andres Bachiller Ortiz <wilson@mawii.com.co>
 * @version 1.0.0
 */

namespace App\Traits;

use App\Controllers\Api\Auth;
use PhpOffice\PhpSpreadsheet\Shared\Date;

Trait ExcelValidationTrait
{

    public  $errors = [];
    
    /**
     * El metodo se encarga de tranformar la fecha numerica
     * a fecha de tipo string
     * @param string $value
     * @return DataTime | null
     */

    protected function tranformDate($value)
    {
        try {
            return !empty($value) && !is_null($value) && !isset($value) ? null : Date::excelToDateTimeObject($value)->format('Y-m-d');
        }catch(Exception $e) {
            return true;
        }
    }

    /**
     * Metodo void para validar si el campo esta vacio o es nulo
     * @param string $value valor de la celda
     * @param string $column nombre de la columna del excel a corregir
     * @param string $celda ubicacion en el excel por fila y columna
     */

    protected function required($value, $column, $celda)
    {
        empty($value) && $value != 0 ? array_push($this->$errors, "El campo {$column} de la celda {$celda} es obligatorio.") : null;
    }


    /**
     * Metodo void para  validar si existe en base de datos los ids ingresados
     * de cualquier campo del excel
     * @param string $value valor de la celda
     * @param string $column nombre de la columna del excel a corregir
     * @param string $celda ubicacion en el excel por fila y columna
     * @param string $table nombre a la tabla a la cual se consultaran los datos
     * @param string $pimeryKey llave asignada a buscar en tabla de la bd
     * @param string $validCompany valide con llave foranea de compañia en bd
     * @param string $columnCompany como es nombrada la columna compañia en las tablas
     */

    protected function validExistDB($value, $column,  $celda, $table, $primeryKey, $validCompany =  false, $columnCompany = 'companies_id')
    {
        try {
            $db = \Config\Database::connect();
            $db = db_connect();
            $query = $validCompany == true ? "SELECT id FROM {$table} WHERE {$primeryKey} = '{$value}' AND {$columnCompany} = ".Auth::querys()->companies_id : "SELECT id FROM {$table} WHERE {$primeryKey} = '{$value}'";

            $query = $db->query($query);
            if (empty($value) || $query->getNumRows() == 0) {
                array_push($this->errors, "El campo {$column} de la celda {$celda} no existe este valor {$value} por favor ingréselo al sistema o corríjalo.");
            }else {
                return $query->getFirstRow();
            }

            $db->close();
        }catch( \Exception $e) {
            array_push($this->errors, "El campo {$column} de la celda {$celda} no existe este valor {$value} por favor ingréselo al sistema o corríjalo.");
        }
    }


    public function getErrors(): Array
    {
        return $this->errors;
    }

}