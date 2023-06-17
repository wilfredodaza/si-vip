<?php


namespace App\Controllers\Configuration;


use App\Controllers\Api\Auth;
use App\Models\Configuration;
use App\Models\ModuleRole;
use App\Models\Role;
use App\Traits\Grocery;
use App\Controllers\BaseController;
use CodeIgniter\Model;

class ConfigController extends BaseController
{
    use Grocery;

    private $crud;

    public function __construct()
    {
        $this->crud = $this->_getGroceryCrudEnterprise();
        $this->crud->setSkin('bootstrap-v3');
        $this->crud->setLanguage('Spanish');
    }

    public function index($data)
    {

        $this->crud->setTable($data);
        switch ($data) {
            case 'users':
                $title = 'Usuarios';
                $subtitle = 'Listado de usuarios.';
                $this->crud->setRelation('companies_id', 'companies', 'company');
                $this->crud->uniqueFields(['username']);
                $this->crud->fieldType('password', 'password');
                $this->crud->displayAs(lang('users.users'));
                if (session('user')->role_id == 2) {
                    $role = new Role();
                    $roles = $role->select(['id', 'name'])
                        ->whereNotIn('id', [1, 2, 4,5,6])
                        ->whereIn('id', [3, 7,10])
                        ->orWhereIn('companies_id', [Auth::querys()->companies_id])
                        ->get()
                        ->getResult();

                    $rolesData = [];
                    foreach ($roles as $rol) {
                        $rolesData[(string) $rol->id] = $rol->name;
                    }

                    $this->crud->fieldType('role_id','dropdown', $rolesData);
                    $this->crud->where(['companies_id' => session('user')->companies_id]);
                    $this->crud->fieldType('companies_id', 'hidden');
                    $this->crud->unsetColumns(['companies_id', 'role_id', 'password']);
                    $this->crud->callbackAddForm(function ($data) {
                        $data['companies_id'] = session('user')->companies_id;
                        return $data;
                    });
                }else if(session('user')->role_id == 1){
                    $this->crud->setRelation('role_id', 'roles', 'name' );
                }
                $this->crud->callbackBeforeInsert(function ($stateParameters) {
                    $stateParameters->data['password'] = password_hash($stateParameters->data['password'], PASSWORD_DEFAULT);
                    return $stateParameters;
                });
                $this->crud->callbackBeforeUpdate(function ($stateParameters) {
                    if (strlen($stateParameters->data['password']) < 20) {
                        $stateParameters->data['password'] = password_hash($stateParameters->data['password'], PASSWORD_DEFAULT);
                    }
                    return $stateParameters;
                });
                $this->crud->setFieldUpload('photo', 'assets/upload/images', '/assets/upload/images');
                break;
            case 'permissions':
                $title = 'Permisos';
                $subtitle = 'Listado de permisos.';
                $this->crud->setRelation('role_id', 'roles', 'name');
                // $this->crud->setRelation('module_role_id', 'module_role', '{module_id} - {role_id}');
                $this->crud->setRelation('menu_id', 'menus', '{option} - {type}');
                $module = new ModuleRole();
                $modules = $module->select(['module_role.id', 'roles.name as role_name', 'modules.name as module_name'])
                    ->join('roles', 'module_role.role_id = roles.id')
                    ->join('modules', 'module_role.module_id = modules.id')
                    ->asObject()
                    ->get()
                    ->getResult();
                $option = [];
                foreach ($modules as $item) {
                    $option = array_merge($option, ["$item->id-" =>  $item->role_name." - ".$item->module_name ]);
                }

                $this->crud->fieldType('module_role_id', 'dropdown_search', $option);
                $this->crud->callbackBeforeInsert(function ($stateParameters) {
                    $stateParameters->data['module_role_id'] = $this->moduleId($stateParameters);
                    return $stateParameters;
                });

                $this->crud->callbackBeforeUpdate(function ($stateParameters) {
                    $stateParameters->data['module_role_id'] = $this->moduleId($stateParameters);
                    return $stateParameters;
                });
                break;
            case 'menus':
                $title = 'Opciones del Menu';
                $subtitle = 'Listado de opciones de menu.';
                $this->crud->setTexteditor(['description']);
                $this->crud->setRelation('references', 'menus', 'option');
                break;
            case 'roles':
                $title = 'Roles';
                $subtitle = 'Listado de roles.';
                break;
            case 'notifications':
                $title = 'Notificaciones';
                $subtitle = 'Listado de Notificaciones.<a target="_blank"  href="https://material.io/resources/icons/?style=baseline"> Iconos aqui </a>';
                $this->crud->setTexteditor(['body']);
                $this->crud->displayAs(lang('notifications.notifications'));
                $this->crud->setRelation('companies_id', 'companies', 'company');
                $this->crud->callbackAddForm(function ($data) {
                    $data['icon'] = 'add_to_queue';
                    return $data;
                });
                break;
            case 'configurations':
                $title = 'Configuraciones';
                $subtitle = 'Listado de configuraciones.';
                $config = new Configuration();
                $data = $config->findAll();
                $this->crud->setTexteditor(['footer', 'intro', 'alert_body']);
                $this->crud->setFieldUpload('logo_menu', 'assets/img', '/assets/img');


                if (count($data)  > 0) {
                    $this->crud->unsetAdd();
                    $this->crud->unsetDelete();

                }

                break;
            case 'modules':
                $title = 'Modulos';
                $subtitle = 'Listo de modulos.';
                $this->crud->setFieldUpload('img', 'assets/img', '/assets/img');
                $this->crud->fieldType('updated_at', 'hidden');
                $this->crud->fieldType('deleted_at', 'hidden');
                $this->crud->fieldType('created_at', 'hidden');
                $this->crud->unsetColumns(['updated_at', 'created_at', 'deleted_at']);
                break;
            case 'module_role':
                $title = 'Modulos y Roles';
                $subtitle = 'Asociación de modulos y roles.';
                $this->crud->setRelation('role_id', 'roles', 'name');
                $this->crud->setRelation('module_id', 'modules', 'name');

                break;


        }


        $output = $this->crud->render();
        if (isset($output->isJSONResponse) && $output->isJSONResponse) {
            header('Content-Type: application/json; charset=utf-8');
            echo $output->output;
            exit;
        }



        $this->viewTable($output, $title, $subtitle);
    }


    protected function relations()
    {
        // TODO: Implement relations() method.
    }

    protected function rules()
    {
        // TODO: Implement rules() method.
    }

    protected function fieldType()
    {
        // TODO: Implement fieldType() method.
    }

    protected function callback()
    {
        // TODO: Implement callback() method.
    }


    protected function moduleId($data)
    {
      return str_replace('-', '', (string)$data->data['module_role_id']);
    }
}