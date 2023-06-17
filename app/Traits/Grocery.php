<?php


namespace App\Traits;

use GroceryCrud\Core\GroceryCrud;

include(APPPATH . 'Libraries/GroceryCrudEnterprise/autoload.php');

Trait Grocery
{
    protected  $crudTable;

    abstract protected function relations();

    abstract protected function rules();

    abstract protected function fieldType();

    abstract protected function callback();

    private function database()
    {
        $db = (new \Config\Database())->default;

        return [
            'adapter' => [
                'driver'    => 'Pdo_Mysql',
                'host'      => $db['hostname'],
                'database'  => $db['database'],
                'username'  => $db['username'],
                'password'  => $db['password'],
                'charset'   => 'utf8'
            ]
        ];
    }

    private function _getGroceryCrudEnterprise($bootstrap = true, $jquery = true)
    {
        $db = $this->database();
        $config = (new \Config\GroceryCrudEnterprise())->getDefaultConfig();

        $groceryCrud = new GroceryCrud($config, $db);
        return $groceryCrud;
    }

    public function viewTable($output, $title, $subtitle)
    {
        $output->title      = $title;
        $output->subtitle   = $subtitle;
        echo  view('pages/table', (array)$output);
    }

    public function  viewController($controller)
    {
        redirect()->to($controller);
    }

    public function createTable($table, $traslate)
    {
        $this->crudTable = $this->_getGroceryCrudEnterprise();
        $this->crudTable->setSkin('bootstrap-v3');
        $this->crudTable->setLanguage('Spanish');
        $this->crudTable->setTable($table);
        if($traslate) {
            $this->crudTable->displayAs(lang($traslate));
        }
    }

    public function columnHidden()
    {
        $hidden = isset($this->hidden) ? $this->hidden : [];
        foreach ($hidden as $item) {
            $this->crudTable->fieldType($item, 'hidden');
        }
    }

    public function columns()
    {
        $columns = isset($this->columns) ? $this->columns : [];
        $this->crudTable->columns($columns);
    }

    public function timestamps()
    {
        $this->crudTable->callbackBeforeInsert(function ($data) {
            if(array_key_exists('created_at', $data->data)){
                $data->data['created_at'] = date('Y-m-d H:i:s');
            }
            if(array_key_exists('updated_at', $data->data)){
                $data->data['updated_at']   = date('Y-m-d H:i:s');
            }
            return $data;
        });
        $this->crudTable->callbackBeforeUpdate(function ($data) {
            if(array_key_exists('updated_at', $data->data)){
                $data->data['updated_at']   = date('Y-m-d H:i:s');
            }
            return $data;
        });
    }

    public function init(string $table) {
        $this->createTable($table, $table.'.'.$table);
        $this->relations();
        $this->rules();
        $this->fieldType();
        $this->columns();
        $this->columnHidden();
        $this->timestamps();
        $this->callback();
        $output = $this->crudTable->render();
        if (isset($output->isJSONResponse) && $output->isJSONResponse) {
            header('Content-Type: application/json; charset=utf-8');
            echo $output->output;
            exit;
        }

    }
}