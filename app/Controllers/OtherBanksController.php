<?php


namespace App\Controllers;


use App\Models\Bank;
use App\Models\Company;
use App\Models\OtherBank;

class OtherBanksController extends BaseController
{
    protected $other_banks;
    protected $companies;
    protected $banks;


    public function __construct()
    {
        $this->other_banks = new OtherBank();
        $this->companies = new Company();
        $this->banks = new Bank();
    }

    public function index()
    {

        $other_banks = $this->other_banks
            ->select([
                'other_bank.id',
                'other_bank.name',
                'other_bank.companies_id as other_bank_company',
                'other_bank.bank_id',
                'other_bank.status',
                'companies.company'
            ])
            ->join('companies', 'other_bank.companies_id = companies.id')
            ->where(['companies.id' => company()->id])
            ->asObject();
        if (count($this->search()) != 0) {
            if (!empty($this->request->getGet('status'))) {
                $other_banks->where($this->search());
            } else {
                $other_banks->like($this->search());
            }
        }
        $data = [
            'other_banks' => $other_banks->paginate(10),
            'pager' => $other_banks->pager,
            'banks' => $this->banks->get()->getResult()
        ];
        return view('import/other_bank', $data);
    }

    public function search()
    {
        $data = [];
        if (!empty($this->request->getGet('name'))) {
            $data['other_bank.name'] = $this->request->getGet('name');
        }
        if (!empty($this->request->getGet('status'))) {
            $data['other_bank.status'] = $this->request->getGet('status');
        }
        return $data;
    }

    public function create()
    {
        if ($this->other_banks->save([
            'companies_id' => company()->id,
            'name' => $_POST['name'],
            'bank_id' => $_POST['bank'],
            'status' => $_POST['status']
        ])) {
            return redirect()->to(base_url() . '/other_banks')->with('success', 'El banco fue guardado con exíto.');
        } else {
            return redirect()->to(base_url() . '/other_banks')->with('errors', 'El banco no se puede guardar.');
        }
    }

    public function edit($id)
    {
        if ($this->other_banks->set([
            'name' => $_POST['name'],
            'bank_id' => $_POST['bank'],
            'status' => $_POST['status']
        ])->where(['id' => $id])->update()) {
            return redirect()->to(base_url() . '/other_banks')->with('success', 'El banco fue Editado con exíto.');
        } else {
            return redirect()->to(base_url() . '/other_banks')->with('errors', 'El banco no se pudo Editar.');
        }
    }

}


