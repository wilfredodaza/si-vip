<?php namespace Config;

class Validation
{
	//--------------------------------------------------------------------
	// Setup
	//--------------------------------------------------------------------

	/**
	 * Stores the classes that contain the
	 * rules that are available.
	 *
	 * @var array
	 */
	public $ruleSets = [
		\CodeIgniter\Validation\Rules::class,
		\CodeIgniter\Validation\FormatRules::class,
		\CodeIgniter\Validation\FileRules::class,
		\CodeIgniter\Validation\CreditCardRules::class,
	];

	/**
	 * Specifies the views that are used to display the
	 * errors.
	 *
	 * @var array
	 */
	public $templates = [
		'list'   => 'CodeIgniter\Validation\Views\list',
		'single' => 'CodeIgniter\Validation\Views\single',
	];


	public $prueba = [
		
			'type_worker_id'                    => 'required|numeric|is_not_unique[type_workers.id]',
			'sub_type_worker_id'                => 'required|numeric|is_not_unique[sub_type_workers.id]',
			'type_document_identification_id'   => 'required|numeric|is_not_unique[type_document_identifications.id]',
			'municipality_id'                   => 'required|numeric|is_not_unique[municipalities.id]',
			'type_contract_id'                  => 'required|numeric|is_not_unique[type_contracts.id]',
			'high_risk_pension'                 => 'required|in_list[true, falso]',
			'identification_number'             => 'required|max_length[45]|is_unique[customers.identification_number,companies_id,{!$companies_id}]|is_unique[customers.identification_number,type_customer_id,3]',
			'surname'                           => 'required|max_length[45]',
			'second_surname'                    => 'max_length[45]',
			'first_name'                        => 'required|max_length[45]',
			'second_name'                       => 'required|max_length[45]',
			'address'                           => 'required|max_length[100]',
			'email'                             => 'required|valid_email',
			'integral_salary'                   => 'required|in_list[true, falso]',
			'salary'                            => 'required|numeric|max_length[23]',
			'bank_id'                           => 'required|numeric|is_not_unique[banks.id]',
			'bank_account_type_id'              => 'required|numeric|is_not_unique[bank_account_types.id]',
			'account_number'                    => 'required|numeric|max_length[45]',
			'admision_date'                     => 'required|valid_date',
			'payment_method_id'                 => 'required|numeric|is_not_unique[payment_methods.id]',
			'worker_code'                       => 'if_exist|max_length[5]'
		];
	

	//--------------------------------------------------------------------
	// Rules
	//--------------------------------------------------------------------
}
