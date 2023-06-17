<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

class Queue extends BaseConfig
{

    //--------------------------------------------------------------------
    // maintenance mode file path
    //--------------------------------------------------------------------
    // 
    //
    public $queueConnection = 'pdo';


    public $codeigniter = [
		'persistor'			=> 'CodeigniterExt\Queue\Persistor\Codeigniter\Codeigniter',
		'params'    		=> [
			'db_group'		=> false,
			'table_name'	=> 'queue_tasks',
		],
	];
	
	public $pdo = [
		'persistor'			=> 'CodeigniterExt\Queue\Persistor\Pdo\Pdo',
		'params'    		=> [
			'dsn'       => 'mysql:host=localhost;dbname=mifacturalegal_facturador;charset=utf8',
			'username'  => 'mifacturalegal_facturador',
			'password'  => 'M49bx3kk!!',
			'table_name'=> 'queue_tasks'
		],
	];

	//TODO: will be added
	// public $redis = [
	// 	'persistor'			=> 'CodeigniterExt\Queue\Persistor\Pdo\Pdo',
	// 	'params'    		=> [
	// 		'host'  => '127.0.0.1',
	// 		'port'  => 6379
	// 	],
	// ];

}
