<?php


namespace App\Controllers\Configuration;


use App\Controllers\Api\Auth;
use App\Models\User;
use App\Models\Role;
use CodeIgniter\Model;
use Config\Services;
use App\Controllers\BaseController;
use App\Models\Customer;

class AuthController extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function validation()
    {

        $errors = $this->validate([
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[6]|max_length[20]'
        ]);

        if ($errors) {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            $user = new User();
            $data = $user
                ->select('users.*, roles.name as role_name, roles.status as role_status')
                ->join('roles', 'users.role_id = roles.id')
                ->where(['username' => $username])
                ->get()
                ->getResult();




            if ($data) {
                if ($data[0]->status == 'active' || $data[0]->status == 'locked') {
                    if (password_verify($password, $data[0]->password)) {
                        $session = session();
                        $session->set('user', $data[0]);
                        if(session('user')->role_id == 5) {
                            $customer = new Customer();
                            $customers = $customer
                                ->where(['email' => session('user')->email])
                                ->asObject()
                                ->first();
                            if($customers->firm == null) {
                                return redirect()->to(base_url().'/home');
                            }else {
                                return redirect()->to(base_url().'/document_support');
                            }
                        }else {
                            if($data[0]->status == 'locked' ) {
                                return  redirect()->to(base_url().'/new_password');
                            }
                            return redirect()->to(base_url().'/home');
                        }
                    } else {
                        return redirect()->to(base_url().'/')->with('errors', 'Las credenciales no concuerdan.');
                    }
                } else {
                    return redirect()->to(base_url().'/')->with('errors', 'La cuenta no se encuentra activa.');
                }
            } else {
                return redirect()->to(base_url().'/')->with('errors', 'Las credenciales no concuerdan.');
            }
        } else {
            return redirect()->to(base_url().'/')->with('errors', 'Las credenciales no concuerdan.');
        }


    }

    public function register()
    {
        $validation = Services::validation();
        return view('auth/register', ['validation' => $validation]);
    }

    public function create()
    {
        if ($this->validate([
            'name' => 'required|max_length[45]',
            'username' => 'required|is_unique[users.username]|max_length[40]',
            'email' => 'required|valid_email|is_unique[users.email]|max_length[100]',
            'password' => 'required|min_length[8]|max_length[20]'
        ], [
            'name' => [
                'required' => 'El campo Nombres y Apellidos es obrigatorio.',
                'max_length' => 'El campo Nombres Y Apellidos no debe terner mas de 45 caracteres.'
            ],
            'username' => [
                'required' => 'El campo Nombre de Usuario es obligatorio',
                'is_username' => 'Lo sentimos. El nombre de usuario ya se encuntra registrado.',
                'max_length' => 'El campo Nombre de Usuario no puede superar mas de 20 caracteres.'
            ],
            'email' => [
                'required' => 'El campo Correo Electronico es obrigatorio.',
                'is_unique' => 'Lo sentimos. El correo ya se encuntra registrado.'
            ],
            'password' => [
                'required' => 'El campo Contraseña es obligatorio.',
                'min_length' => 'El campo Contraseña debe tener minimo 8 caracteres.',
                'max_length' => 'El campo Contraseña no debe tener mas de 20 caracteres.'
            ]

        ])) {
            $data = [
                'name' => $this->request->getPost('name'),
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'status' => 'active',
                'role_id' => 3,
                'companies_id' => 2
                
            ];

            $user = new User();
            $user->save($data);
            return redirect()->to(base_url().'/register');
        } else {
            return redirect()->to(base_url().'/register')->withInput();
        }
    }

    public function resetPassword()
    {
       
        return view('auth/reset_password');
    }

    public function forgotPassword()
    {
      
        $request = Services::request();
        $user = new User();
        $data = $user->where('email', $request->getPost('email'))->get()->getResult();
       
        if (count($data) > 0) {
       
            $email = new EmailController();
          
            $password = $this->encript();
            $user->set(['password' => password_hash($password, PASSWORD_DEFAULT)])
            ->where(['id' => $data[0]->id])
            ->update();

            
       
            $email->send('soporte@mifacturalegal.com', 'wabox', $request->getPost('email'), 'Recuperacion de contraseña', password($password));
            return redirect()->to(base_url().'/reset_password')
                ->with('success', 'Valida el correo te enviamos una nueva contraseña');
        } else {
            return redirect()->to(base_url().'/reset_password')
                ->with('danger', 'Las credenciales no coinciden con los datos ingresados.');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url().'/');
    }

    public function encript($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function newPassword()
    {

    }
}