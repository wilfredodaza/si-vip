<?php


namespace App\Controllers;



class GoogleDriveController
{
	public $client;
    public $service;

    public function __construct()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=mifacturalegal-driver-dfec4e17d6d7.json');
        $this->client = new \Google_Client();
        $this->client->useApplicationDefaultCredentials();
        $this->client->SetScopes(['https://www.googleapis.com/auth/drive.file']);

        try {
            $this->service = new \Google_Service_Drive($this->client);
        } catch(\Google_Service_Exception $gs) {
            $error =  $gs->getMessage();
            echo $error;die();
        } catch(\Exception $e) {
            echo $e->getMessage();
        }  
    }

    /**
     * Return List Folders
     * @return Array Gooogle Drive Folders
     */

    public function listFolders()
    {
        $data = $this->service->files->listFiles([
            'q'         => ["mimeType = 'application/vnd.google-apps.folder'", 'trashed = false'],
            'fields'    => 'files(id, name, mimeType, trashed)'
        ]);
     
        $folders = [];
        foreach($data->files as $item) {
            array_push($folders, (Object) [
                'id'            => $item->id, 
                'name'          => $item->name, 
                'mimeType'      => $item->mimeType,
                'trashed'       => $item->trashed 
            ]);
        }
        return $folders; 
    }


    /**
     * Return List File Google
     * @param $folderId id de la carpeta
     * @return Array Gooogle Drive Files
     */


    public function listFile($folderId = null)
    {   

        $data = $this->service->files->listFiles([
            'q'         => ['"'.$folderId.'" in parents'],
            'fields'    => 'files(id, name, mimeType)',
        ]);

        //echo json_encode($data->files);die();
        $files = [];
        foreach($data->files as $item) {
            array_push($files, (Object) [
                'id'            => $item->id, 
                'name'          => $item->name, 
                'mimeType'      => $item->mimeType,
                'trashed'       => $item->trashed 
            ]);
        }
        return $files;
    }


    /**
    *File exist 
    * @param $nameFile name File
    * @return boolean
    */
   
    public function fileExist($nameFile)
    {
        $files = $this->service->files->listFiles([
            'q'         => ['name="'.$nameFile.'"'],
            'fields'    => 'files(id, name)'
        ]);

        if(count($files) > 0){
            return true;
        }else {
            return false;
        }
    }

    /**
    *Folder exist 
    * @param $nameFolder name Folder
    * @return boolean
    */


    public function folderExist($nameFolder)
    {
        $files = $this->service->files->listFiles([
            'q'         => ["name = '".$nameFolder."'"],
            'fields'    => 'files(id, name)'
        ]);

        if(count($files) > 0){
            return true;
        }else {
            return false;
        }
    }


    /**
    *upload File
    * @param $fileName Name File 
    * @param $mimeType 
    * @param $filePath
    * @param $folder
    * @return boolean
    */

    public function uploadFile($fileName, $mimeType, $filePath, $folder = [])
    {
        $file = new \Google_Service_Drive_DriveFile();
        $file->setName($fileName);
        $file->setParents($folder);
        $file->setMimeType($mimeType);

        return $this->service->files->create(
            $file,
            [
                'data'          => file_get_contents($filePath),
                'mimeType'      => $mimeType,
                'uploadType'    => 'media'
            ]
        );
    }


     /**
    *Created Folder
    * @param $fileName Name File 
    * @param $folder
    * @return boolean
    */

    public function createFolder($folderName, $folder) 
    {
        $file = new \Google_Service_Drive_DriveFile();
        $file->setName($folderName);
        $file->setParents($folder);
        $file->setMimeType('application/vnd.google-apps.folder');
        return $this->service->files->create($file);
    }


    /**
    * Delete File
    * @param $foleId id file
    * @return boolean
    */

    public function deleteFile($fileId) 
    {
        return $this->service->files->delete($fileId);
    }


    /**
    * Delete Folder
    * @param $folderId id folder
    * @return boolean
    */

    public function deleteFolder($folderId) 
    {
        return $this->service->files->delete($folderId);
    }


}    