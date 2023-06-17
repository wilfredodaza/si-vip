<?php


namespace App\Controllers;


use App\Models\Company;
use App\Models\Role;


class HeadquartersController extends BaseController
{
    public $tableRoles;
    public $tableCompanies;

    public function __construct()
    {
        $this->tableRoles = new Role();
        $this->tableCompanies = new Company();
    }

    public function permissionManager($rol_id){
        $data = false;
        $rol = $this->tableRoles->asObject()->find($rol_id);
        if(strtolower($rol->name) == 'gerente'){
            $data = true;
        }
        return $data;
    }

    public function idsCompaniesHeadquarters($companies_id = null, $identificationNumber = null): array
    {
        $idCompanies=[];
        if(!is_null($companies_id)){
            $identificationNumber = $this->tableCompanies->where(['id' => $companies_id])->asObject()->first();
            $identification = $identificationNumber->identification_number;
        }else{
            $identification = company()->identification_number;
        }
        $companies = $this->tableCompanies
            ->select('companies.id')
            ->where(['identification_number' => $identification])->get()->getResult();
        foreach($companies as $company){
            array_push($idCompanies, (int)$company->id);
        }
        return $idCompanies;
    }

    public function idsCompaniesText(){
        $idsCompanies = '';
        foreach ($this->idsCompaniesHeadquarters() as $id => $item) {
            if ($id == 0) {
                $idsCompanies = $item;
            } else {
                $idsCompanies = $idsCompanies . ',' . $item;
            }
        }
        return $idsCompanies;
    }

    public function idSearchBodega(){
        $company = $this->tableCompanies->where(['company' => 'Sede Bodega'])->asObject()->first();
        return $company->id;
    }
}
