<?php


namespace App\Controllers\Api;

use App\Models\Company;
use App\Models\TypeRegimes;
use App\Models\TypeCustomer;
use App\Models\Municipalities;
use App\Models\TypeOrganizations;
use App\Models\TypeDocumentIdentifications;

class PostController extends ServiceController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function municipalities()
    {
        $municipalities = new Municipalities();
        $municipality = $municipalities->get()->getResult();
        
        http_response_code(200);
        echo json_encode($municipality);
        die();
    }

    public function TypeDocumentIdentification()
    {
        $TdocumentI = new TypeDocumentIdentifications();
        $tDocuments = $TdocumentI->get()->getResult();
        
        http_response_code(200);
        echo json_encode($tDocuments);
        die();
    }
    
    public function typeCustomer()
    {
        $Tcustomers = new TypeCustomer();
        $Tcustomer = $Tcustomers->get()->getResult();
        
        http_response_code(200);
        echo json_encode($Tcustomer);
        die();
    }

    public function typeRegimes(){

        $Tregimes = new TypeRegimes();
        $Tregime = $Tregimes->get()->getResult();
        
        http_response_code(200);
        echo json_encode($Tregime);
        die();

    }
    
    public function typeOrganizations(){

        $Torganizations = new TypeOrganizations();
        $Torganization = $Torganizations->get()->getResult();
        
        http_response_code(200);
        echo json_encode($Torganization);
        die();

    }

    public function Company(){

        $companies = new Company();
        //$company = $companies->where(['companies_id' => session('user')->companies_id])->get()->getResult();
        $company = $companies->where(['id' => 69])->get()->getResult()[0];
        
        http_response_code(200);
        echo json_encode($company);
        die();

    }
}