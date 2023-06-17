<?php

/**
 * Conjunto de funciones encargado del desempaquetado de archivos con extensión .ZIP
 * @auhtor Wilson Andres Bachiller Ortiz <wilson@mawii.com> wabo
 * @version 1.0
 */


/**
 * Función encargada de  descomprimir archivos .ZIP
 * @param $urlFile
 * @param $urlDecompress
 * @return array|void
 */

function decompress($urlFile, $urlDecompress) {
    $zip = new \ZipArchive();
    if($zip->open($urlFile) === TRUE) {
        $file_count = $zip->count();
        $documents = [];
        for($i = 0; $i < $file_count; $i++) {
            array_push($documents, $zip->getNameIndex($i));
        }
        $zip->extractTo($urlDecompress);
        $zip->close();
        return $documents;
    }
}