<?php    
    
    
    return ['payroll_errors' => [

            'novelty'                                           => 'El campo de novedad es obligatorio.',
            'novelty.novelty'                                   => 'El campo de novedad es obligatorio y debe ser Verdadero o Falso.',
            'novelty.uuidnov'                                   => 'El campo CUNE de la novedad es obligatoria.',

            // Period
            'period'                                            => 'El grupo de elemento de periodo es obligatorio.',
            'period.admision_date'                              => 'El campo fecha de admisión del empleado es obligatorio y debe tener el formato YYYY-MM-DD.',
            'period.retirement_date'                            => 'El campo fecha de retiro debe tener el formato YYYY-MM-DD,',
            'period.settlement_start_date'                      => 'El campo fecha inicio de la nomina debe ser obligatorio y debe tener el formato YYYY-MM-DD.',
            'period.settlement_end_date'                        => 'El campo fecha fin de la nómina debe ser obligatorio y debe tener el formato YYYY-MM-DD.',
            'period.worked_time'                                => 'El campo tiempo trabajado es obligatorio y debe ser numérico.',
            'period.issue_date'                                 => 'El campo fecha de generación del documento de nómina es obligatorio y debe tener el formato YYYY-MM-DD.',

            //Consecutivo.
            'consecutive'                                       => 'El campo consecutivo es obligatorio y de tipo numérico.',

            // General Information
            'payroll_period_id'                                 => 'El campo periodo de nomina es obligatorio.',
            'notes'                                             => 'El campo notas debe ser de tipo numérico.',

            // Worker
            'worker'                                            => 'El grupo de elemento de empleados es obligatorio.',
            'worker.type_worker_id'                             => 'El campo tipo de empleado es obligatorio.',
            'worker.sub_type_worker_id'                         => 'El campo subtipo de empleado es obligatorio.',
            'worker.payroll_type_document_identification_id'    => 'El campo tipo de documento de identificación es obligatorio.',
            'worker.municipality_id'                            => 'El campo municipio es obligatorio.',
            'worker.type_contract_id'                           => 'El campo tipo de contrato es obligatorio.',
            'worker.high_risk_pension'                          => 'El campo pension de riesgo es obligatorio y debe ser Verdadero o Falso.',
            'worker.identification_number'                      => 'El campo número de identificación es obligatorio.',
            'worker.surname'                                    => 'El campo primer apellido es obligatorio y debe ser de tipo texto.',
            'worker.second_surname'                             => 'El campo segundo apellido  debe ser de tipo texto.',
            'worker.first_name'                                 => 'El campo primer nombre es obligatorio y debe ser de tipo texto.',
            'worker.middle_name'                                => 'El campo segundo nombre debe ser de tipo texto.',
            'worker.address'                                    => 'El campo dirección es obligatorio y debe ser de tipo texto.',
            'worker.integral_salarary'                          => 'El campo salario integral es obligatorio debe ser Verdadero o Falso.',
            'worker.salary'                                     => 'El campo salario es obligatorio y de tipo numérico.',
            'worker.worker_code'                                => 'El campo código de empleado debe ser de tipo texto.',


            'payment'                                           => 'El grupo de elemento de pago es obligatorio.',
            'payment.payment_method_id'                         => 'El campo método de pago es obligatorio.',
            'payment.bank_name'                                 => 'El campo nombre de banco es obligatorio y de tipo texto.',
            'payment.account_type'                              => 'El campo tipo de cuenta es obligatorio.',
            'payment.account_number'                            => 'El campo número de cuenta es obligatorio.',
            
            //deduction
            'deductions.fondossp_sub_type_law_deductions_id'    => 'El campo sub fondo de subsistencia es obligatorio.',
            'deductions.fondosp_deduction_sub'                  => 'El campo sub fondo de subsistencia es obligatorio.',

            //accrueds
            'accrued.worked_days'                               => 'El campo cantidad de días trabajados es obligatorio.'
        ]
    ];