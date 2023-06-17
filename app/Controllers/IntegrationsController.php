<?php


namespace App\Controllers;


use App\Models\Integrations;
use App\Models\IntegrationShopify;

class IntegrationsController extends BaseController
{

    private $table_integration_shopify;
    private $tableIntegrations;

    public function __construct(){
        $this->table_integration_shopify = new IntegrationShopify();
        $this->tableIntegrations = new Integrations();
    }
    public function index(){
        $integrations= $this->tableIntegrations->where(['status' => 'Active'])->get()->getResult();
        foreach($integrations as $integration){
            if($this->activeIntegration($integration->name) > 0){
                $integration->statusCompany = true;
            }else{
                $integration->statusCompany = false;
            }
        }
        return view('integrations/index', ['apps' => $integrations]);
    }
    public function activeIntegration($app,$company = null,$idIntegrationShopify = null){
        $data = null;
        if(!is_null($company)){
            $idCompany = company($company)->id;
        }else{
            $idCompany = company()->id;
        }
        switch ($app){
            case 'shopify':
                if(is_null($idIntegrationShopify)){
                    $data = $this->table_integration_shopify->where(['companies_id' => $idCompany])->countAllResults();
                }else{
                    $data = $this->table_integration_shopify->where(['companies_id' => $idCompany, 'id' => $idIntegrationShopify])->countAllResults();
                }
                break;
        }
        return $data;
    }

}

