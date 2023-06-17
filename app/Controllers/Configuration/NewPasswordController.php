<?php


namespace App\Controllers\Configuration;


use App\Controllers\BaseController;
use App\Controllers\Api\Auth;
use App\Models\User;


class NewPasswordController extends BaseController
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    
    public function index()
    {
        return view('auth/new_password_init');
    }

    public function update($id = null)
    {
        if(Auth::querys()->id !== $id) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $validation = service('validation');
        $validation->setRules([
            'current_password'      => 'required|min_length[6]',
            'new_password'          => 'required|min_length[6]',
            'confirm_password'      => 'required|min_length[6]|max_length[20]|matches[new_password]'
        ],[
            'current_password' => [
                'required'      => 'El campo contraseña actual es requerido.',
                'min_length'    => 'El campo contraseña actual requiere mínimo 6 caracteres.'
            ],
            'new_password' => [
                'required'      => 'El campo contraseña nueva es requerido.',
                'min_length'    => 'El campo contraseña  nueva requiere mínimo 6 caracteres.'
            ],
            'confirm_password' => [
                'required'      => 'El campo confirmar contraseña  es requerido.',
                'min_length'    => 'El campo confirmar contraseña  requiere mínimo 6 caracteres.',
                'matches'       => 'La contraseña no coincide con los datos ingresados.'
            ]

        ]);

        if(!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }else {
            $currentPassword = $this->request->getPost('current_password');
            $newPassword     = $this->request->getPost('new_password');

            if($currentPassword ==  $newPassword) {
                return redirect()->back()->withInput()->with('error_password_exist', 'Por favor ingresa una nueva contraseña.');
            }

            $user = $this->user->asObject()->find(Auth::querys()->id);

            if(!password_verify($currentPassword, $user->password)){
                return redirect()->back()->withInput()->with('error', 'La contraseña actual no es valida.');
            }

            $user = new User();
            $user->set('password', password_hash($newPassword, PASSWORD_DEFAULT))
            ->set('status', 'active')
            ->where(['id' => Auth::querys()->id])
            ->update();
            unset($_SESSION['user']);
            return redirect()->to(base_url().'/')->withInput()->with('success', 'La contraseña ya fue actualizada correctamente.');
        }
    }

    public function newPassword() 
    {
        return view('auth/new_password');
    }
}