<?php


namespace App\Controllers;


use App\Controllers\Api\Auth;
use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use CodeIgniter\Model;


class RoleController extends BaseController
{
    public $tableRoles;

    public function __construct()
    {
        $this->tableRoles = new Role();
    }

    public function index()
    {
        $role = new Role();
        if(Auth::querys()->role_id == 2) {
            return view('roles/index', [
                'roles' => $role
                    ->whereNotIn('id', [1,  4,5,6])
                    ->whereIn('id', [3, 2])
                    ->orWhereIn('companies_id', [Auth::querys()->companies_id])
                    ->asObject()
                    ->paginate(10),
                'pager' => $role->pager,
            ]);
        }else if (Auth::querys()->role_id ==  1){
            return view('roles/index', [
                'roles' => $role->join('companies', 'companies.id = roles.companies_id', 'left')->asObject()->paginate(10),
                'pager' => $role->pager,
            ]);
        }
    }

    public function store()
    {

        $validation =  \Config\Services::validation();
        $validation->setRules([
            'name'                              => 'required|max_length[40]',
            'description'                       => 'max_length[255]',
        ],[
            'name' => [
                'required'       => 'El campo rol es obligatorio.',
                'max_length'     => 'El campo rol solo permite un máximo de 40 caracteres.'
            ],
            'type_document_identification' => [
                'required' => 'El campo descripción solo permite un máximo de 255 caracteres.'
        ]]);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = '';
            foreach($validation->getErrors() as $item) {
                $errors .= $item. '<br>';
            }
            return redirect()->to(base_url('/roles'))->with('errors', $errors);
        }


        $data = [
           'name'               =>  $this->request->getPost('name'),
            'description'       =>  $this->request->getPost('description'),
            'type'              =>  'Personalizado',
            'companies_id'      =>  Auth::querys()->companies_id
        ];

        $model = new Role;
        $model->insert($data);
        return redirect()->to(base_url().'/roles')->with('success', 'El rol se ha registrado correctamente.');
    }

    public function edit($id = null) {
        $model = new Role();
        $rol = $model->asObject()->find($id);
        echo json_encode($rol);
        die();
    }

    public function update($id = null)
    {
        $model = new Role();
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'name'                              => 'required|max_length[40]',
            'description'                       => 'max_length[255]',
        ],[
            'name' => [
                'required'       => 'El campo rol es obligatorio.',
                'max_length'     => 'El campo rol solo permite un máximo de 40 caracteres.'
            ],
            'type_document_identification' => [
                'required' => 'El campo descripción solo permite un máximo de 255 caracteres.'
            ]]);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = '';
            foreach($validation->getErrors() as $item) {
                $errors .= $item. '<br>';
            }
            return redirect()->to(base_url('/roles'))->with('errors', $errors);
        }

        $model  = new Role();
        $role = $model->where(['id' => $id, 'companies_id' => Auth::querys()->companies_id ])->get()->getResult();
        if(count($role) == 0) {
            return redirect()->to(base_url().'/roles')->with('errors', 'Rol no encontrado.');
        }

        $data = [
            'name'               =>  $this->request->getPost('name'),
            'description'       =>  $this->request->getPost('description'),
        ];

        $model = new Role;
        $model->update($id, $data);

        return redirect()->to(base_url().'/roles')->with('success', 'El rol se ha actualizado correctamente.');

    }

    public function permissions($id = null)
    {

        $model = new Menu();
        $options =  $model->where(['to_list' => 'Si'])
            ->get()
            ->getResult();

        $model = new Permission();
        $activeOptions = $model->select(['menu_id'])->where(['role_id' => $id])->asObject()->findAll();
        $optionMenus = [];
        foreach ($activeOptions as $option) {
            array_push($optionMenus,(int) $option->menu_id);
        }
        return view('roles/permissions', ['options' => $options, 'id' => $id, 'activeOptions' => $optionMenus]);
    }

    public  function storePermissions($id = null)
    {
        $options = $this->request->getPost('permissions_id');
        $model = new Permission();
        $count = $model->where(['role_id' => $id])->countAllResults();

        if($count == 0) {
            foreach ($options as $option) {
                $model = new Permission();
                $model->insert([
                    'role_id' => $id,
                    'menu_id' => $option
                ]);
                  /*$model = new Menu();
                  $menu = $model->asObject()->find($option);
                  if(!is_null($menu->references)){
                      $model = new Menu();
                      $menu2 = $model->where(['id' => $menu->references])->countAllResults();
                      if($menu2 != 0) {
                          $model = new Permission();
                          $model->insert([
                              'role_id' => $id,
                              'menu_id' => $menu->references
                          ]);
                      }

                  }*/
            }
        } else {
            $model          = new Permission();
            $activeOptions  = $model->select(['menu_id'])->where(['role_id' => $id])->asObject()->findAll();

            $optionMenus = [];
            foreach ($activeOptions as $option) {
                array_push($optionMenus,(int) $option->menu_id);
            }

            $noExist    = [];
            foreach ($options as $option) {
                if(!in_array($option, $optionMenus)) {
                    array_push($noExist, $option);
                }
            }

            foreach ($noExist as $option) {
                $model = new Permission();
                $model->insert([
                    'role_id' => $id,
                    'menu_id' => $option
                ]);
            }

            $delete     = [];
            foreach ($activeOptions as $option) {
                if(!in_array($option->menu_id, $options)) {
                    array_push($delete, $option->menu_id);
                }
            }

            foreach ($delete as $option) {
                $model = new Permission();
                $permissionId = $model->select(['id'])->where(['role_id' => $id, 'menu_id' => $option])->first();

                $model = new Permission();
                $model->delete($permissionId);
            }
        }

        return redirect()->to(base_url().'/permissions/'.$id)->with('success', 'Los permisos se han guadado  correctamente.');
    }
}