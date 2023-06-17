<?php

/**
 * Conjunto de funciones encargas del descargue y
 * cargue de archivos para la carpeta Writable
 * @author wilson andres bachiller ortiz <wilson@mawii.com> wabo
 * @version
 */

use CodeIgniter\Files\File;

/**
 * Función encarga de descargar archivos de carpeta Writable
 * @param string $directory Nombre del directorio
 * @param string $filename Nombre de archivo con extension
 * @return false|string
 * @throws Exception
 */
function download($directory,  $filename){
    if (file_exists(WRITEPATH.'uploads/'.$directory.'/'.$filename)) {
        $fileDownload = new File(WRITEPATH.'uploads/'.$directory.'/'.$filename);
        header("Content-Description: File Transfer");
        header("Content-Type: application/force-download");
        header('Content-Disposition: attachment; filename="'.$fileDownload->getFilename().'"');
        readfile(WRITEPATH.'uploads/'.$directory.'/'.$filename);
    }else {
        throw new Exception('File not found.');
    }
}

/**
 *Función encargada de subir archivos al directorio de Writable
 * @param string $directory ruta de directorios
 * @param string $filename nombre del archivo
 * @return array|false
 */
function upload($directory, $filename) {
    if (!is_dir(WRITEPATH.'uploads/'.$directory)) {
        mkdir(WRITEPATH . 'uploads/' . $directory, 0777, true);
    }

    if ($filename->isValid() && !$filename->hasMoved()) {
            $nameFile   = $filename->getName();
            $newName    = $filename->getRandomName();
            $filename->move(WRITEPATH . '/uploads/' . $directory, $newName);
        return [
            'mime_type' => $filename->getClientMimeType(),
            'name'      => $nameFile,
            'new_name'  => $newName
        ];
    }
    return false;

}

/**
 * @param string $directory ruta de dictorios
 * @param string $filename nombre del archivo
 * @return string
 */
function base64file(string $directory, string $filename)
{
    try {
        $fileDownload = new File(WRITEPATH . 'uploads/' . $directory . '/' . $filename);
        return 'data:' . $fileDownload->getMimeType() . ';base64,' . base64_encode(file_get_contents(WRITEPATH . 'uploads/' . $directory . '/' . $filename));
    }catch (\Exception $e) {
        return 'not found';
    }
}

/**
 * Función encargada de la eliminacion de archivos
 * @param string $directory  ruta de directorios
 * @param string $filename nombre del archivo
 * @return void
 */
function deleteFile(string $directory, string $filename)
{
    if(file_exists(WRITEPATH.'uploads/'.$directory.'/'.$filename)) {
        unlink(WRITEPATH.'uploads/'.$directory.'/'.$filename);
    }
}
