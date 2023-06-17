<?php namespace Config;


use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{

    // Makes reading things below nicer,
    // and simpler to change out script that's used.
    public $aliases = [
        'csrf'          => \CodeIgniter\Filters\CSRF::class,
        'toolbar'       => \CodeIgniter\Filters\DebugToolbar::class,
        'honeypot'      => \CodeIgniter\Filters\Honeypot::class,
        'auth'          => \App\Filters\AuthFilter::class,
        'permission'    => \App\Filters\PermissionFilter::class,
        'wallet'        => \App\Filters\WalletFilter::class,
        'invoices'      => \App\Filters\InvoiceFilter::class,
        'note'          => \App\Filters\NoteFilter::class,
        'options'       => \App\Filters\OptionFilter::class,
        'authApi'       => \App\Filters\AuthApiFilter::class,
    ];

    // Always applied before every request
    public $globals = [
        'before' => [],
        'after' => [
            'toolbar',
        ],
    ];

    // Works on all of a particular HTTP method
    // (GET, POST, etc) as BEFORE filters only
    //     like: 'post' => ['CSRF', 'throttle'],
    public $methods = [];

    // List filter aliases and any before/after uri patterns
    // that they should run on, like:
    //    'isLoggedIn' => ['before' => ['account/*', 'profiles/*']],
    public $filters = [
        'csrf' => [
            'before' => [
                'documents/payment_upload/*',
                'documents/delete/*'
            ]
        ],
        'auth' => [
            'before' => [
                'home',
                'table/*',
                'config/*',
		        'document_support',
                'document_support/*',
                'invoice',
                'invoice/create',
                'invoice/edit/*',
                'invoice/pdf/*',
                'invoice/email/*',
                'invoice/send/*',
                'invoice/attached_document/*',
                'periods',
                'periods/*',
                'note_credit',
                'note_credit/*',
                'note_debit',
                'note_debit/*',
                'wallet',
                'wallet/*',
                'report',
                'report/*',
                'report_general',
                'report_general/*',
                'report_detail',
                'report_detail/*',
                'report_taxes',
                'report_taxes/*',
                'report_retention',
                'report_retention/*',
                'report_quotation',
                'report_quotation/*',
                'report_payroll',
                'report_payroll/*',
                'post',
                'post/*',
                //'notification/*',
                'quotation',
               // 'quotation/*',
                'tracking',
               // 'tracking/*',
                'graphic',
                'graphic/*',
                'import',
                'import/*',
                'documents',
                'documents/*',
                'payrolls',
                'payrolls/*',
                'workers',
                'workers/*',
                'period_adjusts',
                'period_adjusts/*',
                'other_concepts',
                'other_concepts/*',
                'other_banks',
                'other_banks/*',
                'inventory',
                'inventory/*',
                // 'reception_email',
                'shopping',
                'shopping/*',
                'work_certificate',
                'providers',
                'plantillaProductos'
            ]
        ],
        'options' => [
            'before' => [
                'api/*',
                ]
        ],
        'permission' => [
            'before' => [
                'home',
                'table/*',
                'config/*',
		        'document_support',
                'document_support/*',
                'invoice',
                'periods',
                'periods/*',
                'note_credit',
                'note_credit/*',
                'note_debit',
                'note_debit/*',
                'wallet',
                'wallet/*',
                'report',
                'report/*',
                'report_general',
                'report_general/*',
                'report_detail',
                'report_detail/*',
                'report_taxes',
                'report_taxes/*',
                'report_retention',
                'report_retention/*',
                'report_quotation',
                'report_quotation/*',
                'report_payroll',
                'report_payroll/*',
                'post',
                'post/*',
                //'notification/*',
                'quotation',
             //   'quotation/*',
                'tracking',
              //  'tracking/*',
                'graphic',
                'graphic/*',
                'import',
                'import/*',
                'documents',
                'documents/*',
                'payrolls',
                'payrolls/*',
                'workers',
                'workers/*',
                'period_adjusts',
                'period_adjusts/*',
                'other_concepts',
                'other_concepts/*',
                'other_banks',
                'other_banks/*',
                // 'inventory',
                //'inventory/*',
                'payroll_adjust',
                // 'reception_email',
                'shopping',
                'shopping/*',
                'work_certificate',
                'providers'
            ]
        ],
        'wallet' => [
            'before' => [
                'wallet/*',
            ]
        ],
        'invoices' => [
            'before' => [
                'invoice/edit/*',
            ]
        ],
        'note' => [
            'before' => [
                'noteDebit/*',
                'noteCredit/*',
            ]
        ],
    ];
}
