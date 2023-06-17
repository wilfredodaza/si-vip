<?php


function company(int $id = null)
{
    $companys = new \App\Models\Company();
    if (!is_null(session('user')) && is_null($id)) {
        $company = $companys->asObject()->find(session('user')->companies_id);
    } else {
        $integrationsShopify = new \App\Models\IntegrationShopify();
        $issetCompanyShopify = $integrationsShopify->where(['companies_id' => $id])->asObject()->first();
        if(!is_null($id) && !is_null($issetCompanyShopify)){
            $user = new \App\Models\User();
            $data = $user->select('users.*, roles.name as role_name')
                ->join('roles', 'users.role_id = roles.id')
                ->where(['roles.id' => 11, 'users.companies_id' => $id])
                ->asObject()
                ->first();
            session()->set('user', $data);
            return  $companys->asObject()->find($id);
        }else{
            return 'Error en sesion';
        }
    }
    if (!is_null($company)) {
        return $company;
    } else {
        return 'Error en sesion';
    }

}