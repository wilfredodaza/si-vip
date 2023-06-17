<?php

namespace App\Traits;


use ZipArchive;
use App\Models\Company;
use App\Controllers\Api\Auth;


Trait ZipTrait
{
    private $zip;
    
    public function zipExtraction(String $file, String $path)
    {

	$model = new Company();
        $data = $model->where(['id' => Auth::querys()->companies_id])
        ->asObject()
        ->first();

        $documents = [];
        $this->zip = new ZipArchive();
        $this->zip->open(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/zip/'. $file);
        for($i = 0; $i < $this->zip->count(); $i++){
            array_push($documents,$this->zip->getNameIndex($i));   
        }
        $this->zip->extractTo($path);
        $this->zip->close(); 
        helper('text');
        $fecha = new \DateTime();
        $newDocuments = [];
        foreach($documents as $document) {
         
            if(strstr($document, '.xml')) {
                $name =  random_string('alnum', 20).$fecha->getTimestamp();
                array_push($newDocuments,$name.'.xml');
                if(file_exists(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/xml/'.$document)) {
                    rename(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/xml/'.$document, WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/xml/'.$name.'.xml');
                }
               
            }else if(strstr($document, '.pdf')) {
                $name =  random_string('alnum', 20).$fecha->getTimestamp();
                array_push($newDocuments,$name.'.pdf');
                if(file_exists(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/pdf/'.$document)) {
                    rename(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/pdf/'.$document, WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/pdf/'.$name.'.pdf');
                }
            }
        }
        return $newDocuments;
    }

    
}