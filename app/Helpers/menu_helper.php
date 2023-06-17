<?php

use App\Models\Menu;
use App\Models\Permission;
use App\Models\ModuleRole;


function menu() {


    $module = new ModuleRole();
    $moduleId = $module->select('id')
        ->where(['role_id' => session()->get('user')->role_id, 'id' => session()->get('module')])
        ->asObject()
        ->first();


    $model = new Permission();
    $options = $model->select(['menus.*', 'CONVERT(position,UNSIGNED INTEGER) as position'])
        ->where([ 'module_role_id' => isset($moduleId->id) ? $moduleId->id : null ,
            'menus.status'  => 'active',
            'menus.type' => 'primario'
        ])
        ->join('menus', 'menus.id = permissions.menu_id')
        ->orderBy('menus.position', 'asc')
        ->asObject()
        ->findAll();




    $arrayReferences = [];
    foreach ($options as $option) {
        if(!in_array($option->id, $arrayReferences)){
            if($option->type == 'primario') {
                array_push($arrayReferences, $option->id);
            }
        }

        if(!in_array($option->references, $arrayReferences)) {
            if($option->type == 'secundario') {
                if(!is_null($option->references)) {
                    array_push($arrayReferences, $option->references);
                }
            }
        }
    }

     if(count($arrayReferences) == 0 ) {
         return [];
     }

     $permission = new Menu();
     $data = $permission->select([ 'menus.*', 'CONVERT(position,UNSIGNED INTEGER) as position'])
         ->whereIn('menus.id', $arrayReferences)
         ->orderBy('menus.position', 'asc')
         ->asObject()
         ->findAll();

    return $data;
}

function submenu($refences)
{
    $module = new ModuleRole();
    $moduleId = $module->select('id')
        ->where(['role_id' => session()->get('user')->role_id, 'id' => session()->get('module')])
        ->asObject()
        ->first();

    $menu = new Menu();
    if (session()->get('user')->role_id == 1) {
        $data = $menu
      
            ->where(['type' => 'secundario', 'menus.status' => 'active', 'references' => $refences])
            ->orderBy('menus.position', 'asc')
            ->asObject()
            ->findAll();
    } else {
        $permission = new Permission();
        $data = $permission->select('menus.*')
            ->where([ 'module_role_id' => isset($moduleId->id) ? $moduleId->id : null, 'menus.type' => 'secundario', 'menus.status' => 'active', 'menus.references' => $refences])
            ->orderBy('menus.position', 'asc')
            ->join('menus', 'menus.id = permissions.menu_id')
            //->join('roles', 'roles.id = permissions.role_id')
            ->asObject()
            ->findAll();
    }
    return $data;
}

function countMenu($references)
{
    $menu = new Menu();
    $data = $menu->where(['type' => 'secundario', 'status' => 'active', 'references' => $references])
        ->get()
        ->getResult();
    if (count($data) > 0) {
        return true;
    }
    return false;
}

function urlOption($references = null)
{
    if ($references) {
        $menu = new Menu();
        $data = $menu->find($references);
        if ($data['component'] == 'table') {
            return base_url().'/table/' . $data['url'];
        } else if ($data['component'] == 'controller') {
            return base_url().'/' . $data['url'];
        }
    } else {
        return 'JavaScript:void(0)';
    }

}

function isActive($data)
{
    if(base_url(uri_string()) == base_url($data)) {
        return 'active';
    }
}