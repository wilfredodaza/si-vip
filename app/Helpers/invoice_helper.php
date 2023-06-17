<?php
/**
 * @author Wilson Andres Bachiller Ortiz <wilson@mawii.com.co
 * @version 1.0
 *
 * Se realizar  helper para trabjar en funcionalidades repetivas al momento de subir
 * información de cualquier documento electronico
 *
 */

use App\Models\Resolution;
use App\Models\Invoice;
use App\Controllers\Api\Auth;

/**
 * Función encargada de trabajar con la resolución de cualquier documento soporte.
 * @param int $typeDocument  id del tipo de documento
 * @param int  $resolutionNumber número de resolución
 * @return int se devuelve el consecutivo del documento
 */

function consecutive($typeDocument, $resolutionNumber)
{
    $invoices = new Invoice();
    $invoices = $invoices->select(['invoices.resolution'])
        ->where([
            'invoices.companies_id'          => Auth::querys()->companies_id,
            'resolution_id'                  => $resolutionNumber,
            'type_documents_id'              => $typeDocument
        ])
        ->orderBy('id', 'DESC')
        ->asObject()
        ->first();

    if(!$invoices) {
        $resolution = new Resolution();
        $resolution = $resolution
            ->where([
                'companies_id'          => Auth::querys()->companies_id,
                'resolution'            => $resolutionNumber,
                'type_documents_id'     => $typeDocument
            ])->orderBy('id', 'DESC')
            ->asObject()
            ->first();
        return $resolution->from;
    }

    return $invoices->resolution + 1;
}

/**
 * Función encargado de calcular el digito de verificación del RUT
 * @param int $nit número del documento de la persona o la empresa
 * @return int retorna el digito de verificación
 */

function calculateDV($nit) {
    if (!is_numeric($nit)) {
        return 0;
    }
    $arr = [ 1   => 3, 4   => 17, 7   => 29, 10  => 43, 13  => 59, 2   => 7, 5   => 19, 8   => 37, 11  => 47, 14  => 67, 3   => 13, 6   => 23, 9   => 41, 12  => 53, 15  => 71];
    $x = 0;
    $y = 0;
    $z = strlen($nit);
    $dv = '';
    for ($i = 0; $i < $z; $i++) {
        $y = substr($nit, $i, 1);
        $x += ($y * $arr[$z - $i]);
    }
    $y = $x % 11;
    return $y > 1 ?  11 - $y :  $y;
}



