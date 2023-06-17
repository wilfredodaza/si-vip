<?php


namespace App\Controllers;


use App\Models\TypeAccrued;
use App\Models\Company;
use App\Models\OtherConcepts;
use App\Models\TypeDeduction;

class OtherConceptsController extends BaseController
{
    protected $other_concepts;
    protected $companies;
    protected $type_accrueds;
    protected $type_deductions;

    public function __construct()
    {
        $this->other_concepts = new OtherConcepts();
        $this->companies = new Company();
        $this->type_accrueds = new TypeAccrued();
        $this->type_deductions = new TypeDeduction();
    }

    public function index()
    {
        $other_concepts = $this->other_concepts
            ->select([
                'other_concepts.id',
                'other_concepts.name',
                'other_concepts.type_concept',
                'other_concepts.companies_id as other_concepts_company',
                'other_concepts.concept_dian',
                'other_concepts.status',
                'other_concepts.type_other',
                'companies.company'
            ])
            ->join('companies', 'other_concepts.companies_id = companies.id')
            ->where(['companies.id' => company()->id])
            ->asObject();
        if (count($this->search()) != 0) {
            if (!empty($this->request->getGet('status'))) {
                $other_concepts->where($this->search());
            } else {
                $other_concepts->like($this->search());
            }
        }

        $data = [
            'other_concepts' => $other_concepts->paginate(10),
            'pager' => $other_concepts->pager,
            'accrueds' => $this->type_accrueds->get()->getResult(),
            'deductions' => $this->type_deductions->get()->getResult()
        ];
        //echo json_encode($model_periods);die();
        return view('import/other_concepts', $data);
    }

    public function search()
    {
        $data = [];
        if (!empty($this->request->getGet('name'))) {
            $data['other_concepts.name'] = $this->request->getGet('name');
        }
        if (!empty($this->request->getGet('type_concept'))) {
            $data['other_concepts.type_concept'] = $this->request->getGet('type_concept');
        }
        if (!empty($this->request->getGet('status'))) {
            $data['other_concepts.status'] = $this->request->getGet('status');
        }
        return $data;
    }

    public function create()
    {
        $concept_dian = ($_POST['accrueds'] ?? '');
        if ($_POST['concept_type'] == 'Deduccion') {
            $concept_dian = $_POST['deductions'];
        }
        if ($this->other_concepts->save([
            'companies_id' => company()->id,
            'name' => $_POST['concept_name'],
            'type_concept' => $_POST['concept_type'],
            'status' => $_POST['status'],
            'type_other' => ($_POST['type_incapacidad'] ?? NULL),
            'concept_dian' => $concept_dian
        ])) {
            return redirect()->to(base_url() . '/other_concepts')->with('success', 'El concepto fue guardado con exíto.');
        } else {
            return redirect()->to(base_url() . '/other_concepts')->with('errors', 'El concepto no se puede guardar.');
        }
    }

    public function edit($id)
    {
        $concept_dian = ($_POST['accrueds'] ?? '');
        if ($_POST['concept_type'] == 'Deduccion') {
            $concept_dian = $_POST['deductions'];
        }
        if ($this->other_concepts->set([
            'companies_id' => company()->id,
            'name' => $_POST['concept_name'],
            'type_concept' => $_POST['concept_type'],
            'status' => $_POST['status'],
            'type_other' => ($_POST['type_incapacidad'] ?? NULL),
            'concept_dian' => $concept_dian
        ])->where(['id' => $id])->update()) {
            return redirect()->to(base_url() . '/other_concepts')->with('success', 'El concepto fue Editado con exíto.');
        } else {
            return redirect()->to(base_url() . '/other_concepts')->with('errors', 'El concepto no se pudo Editar.');
        }
    }

}

