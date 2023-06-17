<?php
namespace App\Controllers;


use App\Models\Company;
use App\Controllers\Api\Auth;
use App\Models\Customer;
use App\Models\PersonalizationLaborCertificate;
use Mpdf\Mpdf;


class WorkCertificateController extends BaseController
{

    /**
     * Create pdf of certification work
     * @param int $id number identification of work
     * @return  Mpdf\Mpdf
     */

    public function pdf($id = null)
    {

        if(Auth::querys()->role_id == 7) {
            $model = new Customer();
            $customer = $model->where(['customers.user_id' => Auth::querys()->id])->asObject()->first();
            if($customer->id  != $id) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }
        $model = new Company();
        $company = $model->select([
            'companies.company',
            'companies.identification_number',
            'config.logo',
            'personalization_labor_certificates.address',
            'personalization_labor_certificates.telephone',
            'personalization_labor_certificates.firm',
            'personalization_labor_certificates.stamp',
            'personalization_labor_certificates.payroll_manager',
            'personalization_labor_certificates.address',
            'personalization_labor_certificates.email',
            'municipalities.name as municipality_name',
            'personalization_labor_certificates.payroll_work_manager',
            'personalization_labor_certificates.web_page',
            'departments.name as department_name'
            ])
        ->join('config', 'config.companies_id = companies.id', 'left')
        ->join('personalization_labor_certificates', 'personalization_labor_certificates.company_id = companies.id', 'left')
        ->join('municipalities', 'personalization_labor_certificates.municipality_id = municipalities.id', 'left')
        ->join('departments', 'departments.id = municipalities.department_id', 'left')
        ->where(['companies.id' => Auth::querys()->companies_id])
        ->asObject()
        ->first();


        $model = new Customer();
        $customer = $model->select([
            'customers.identification_number',
            'customer_worker.retirement_date',
            'customer_worker.admision_date',
            'customer_worker.second_name',
            'customer_worker.work',
            'customer_worker.salary',
            'customer_worker.surname',
            'customer_worker.second_surname',
            'customers.name as customer_name',
            'customer_worker.transportation_assistance',
            'customer_worker.non_salary_payment',
            'customer_worker.other_payments',
            'type_document_identifications.name as type_identification_name', 
            'type_contracts.name as type_worker_name',
            'customer_worker.type_contract_id'
        ])
        ->join('customer_worker', 'customer_worker.customer_id = customers.id')
        ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
        ->join('type_contracts', 'customer_worker.type_contract_id =  type_contracts.id')
        ->where(['customers.id' => $id])
        ->asObject()
        ->first();
       


        $mpdf  = new Mpdf([
            'mode'                      => 'utf-8', 
            'format'                    => 'A4',
            'default_font_size'         => 9,
            'default_font'              => 'Roboto',
            'margin_left'               => 10,
            'margin_right'              => 10,
            'margin_top'                => 35,
            'margin_bottom'             => 5,
            'margin_header'             => 10,
            'margin_footer'             => 2
        ]);

        $stylesheet = file_get_contents(base_url() . '/assets/css/bootstrap.css');


        $mpdf->SetTitle('Certificado Laboral');
        $mpdf->WriteHtml($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->SetHTMLHeader(view('pdfs/work_certificate/header', [
            'company'       => $company,
        ]));
        $mpdf->SetHTMLFooter(view('pdfs/work_certificate/footer', [
            'company'       => $company,
        ]));
        $mpdf->WriteHtml(view('pdfs/work_certificate/body', [
            'company'        => $company,
            'customer'       => $customer
        ]), \Mpdf\HTMLParserMode::HTML_BODY);
        $mpdf->Output();
        die();

    }

    /**
     * View of certificate work
     * @return string view
     */

    public function index()
    {
        $model = new Customer();
        $customer = $model->join('customer_worker', 'customers.id = customer_worker.customer_id')
        ->where(['user_id' => Auth::querys()->id])
        ->asObject()
        ->first();

        $personalization = new PersonalizationLaborCertificate();
        $validation =  $personalization->where(['company_id' => Auth::querys()->companies_id])->countAllResults();


        return view('certificate_work/index', [
            'customer'          => $customer,
            'validation'        => $validation
        ]);
    }

}