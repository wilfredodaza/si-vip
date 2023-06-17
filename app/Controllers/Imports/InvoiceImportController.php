<?php

namespace App\Controllers\Imports;


use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Controllers\Integrations\ShopifyController;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class InvoiceImportController extends BaseController
{
    public function index()
    {
        return view('import/invoice');
    }

    public function import()
    {
        if (!empty($_FILES['file']['name'])) {
            $pathinfo = pathinfo($_FILES["file"]["name"]);
            if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls') && $_FILES['file']['size'] > 0) {
                $inputFileName = $_FILES['file']['tmp_name'];
                $reader = ReaderEntityFactory::createReaderFromFile('/path/to/file.xlsx');
                $reader->open($inputFileName);
                $count = 0;         
                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {          
                        if ($count >= 1) {
                            $cells = $row->getCells();
                            $shopifyController =  new  ShopifyController();
                            $shopifyController->orders(173, $cells[0]);
                        }
                        $count++;
                    }
                }
            }
            return redirect()->to(base_url('import/invoice'))->with('success', 'El documento fue cargado con éxito.');
        }
    }
}
