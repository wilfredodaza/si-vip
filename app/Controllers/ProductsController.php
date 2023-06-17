<?php


namespace App\Controllers;


use App\Models\AccountingAcount;
use App\Models\Category;
use App\Models\Company;
use App\Models\Gender;
use App\Models\Groups;
use App\Models\Materials;
use App\Models\Prices;
use App\Models\Product;
use App\Models\ProductsDetails;
use App\Models\Providers;
use App\Models\Role;
use App\Models\SubGroup;
use App\Models\TypeGenerationTransmition;
use App\Models\TypeItemIdentification;
use App\Models\UnitMeasure;

class ProductsController extends BaseController
{
    public $tableProducts;
    public $tableProductsDetails;
    public $tableCategories;
    public $tableUnitMeasures;
    public $tableTypeItemIdentifications;
    public $tableAccountingAccount;
    public $tableTypeGenerationTransmitions;
    public $headquartersController;
    public $tableCompanies;
    public $tableProviders;
    public $tableGender;
    public $tableGroups;
    public $tablePrices;
    public $tableSubGroups;
    public $tableMaterials;

    public function __construct()
    {
        //tables
        $this->tableProducts = new Product();
        $this->tableProductsDetails = new ProductsDetails();
        $this->tableCategories = new Category();
        $this->tableUnitMeasures = new UnitMeasure();
        $this->tableTypeItemIdentifications = new TypeItemIdentification();
        $this->tableAccountingAccount = new AccountingAcount();
        $this->tableTypeGenerationTransmitions = new TypeGenerationTransmition();
        $this->tableCompanies = new Company();
        $this->tableProviders = new Providers();
        $this->tableGender = new Gender();
        $this->tableGroups = new Groups();
        $this->tablePrices = new Prices();
        $this->tableSubGroups = new SubGroup();
        $this->tableMaterials = new Materials();
        //controllers
        $this->headquartersController = new HeadquartersController();
    }

    public function index()
    {
        $product = $this->tableProducts
            ->join('groups', 'groups.id = products.group_id', 'left')
            ->join('sub_group', 'sub_group.id = products.sub_group_id', 'left')
            ->select(['groups.name as group', 'sub_group.name as subGroup', 'products.name as producto', 'products.id as productId', 'products.kind_product_id', 'products.tax_iva', 'products.valor', 'products.code', 'products.code_item', 'products.description']);
        $rol = $this->headquartersController->permissionManager(session('user')->role_id);
        $idCompanies = $this->headquartersController->idsCompaniesHeadquarters();
        $product->whereIn('companies_id', $idCompanies)->where('products.tax_iva !=', null);
        if (count($this->searchIndex()) != 0) {
            if (isset($_GET['product_name']) && isset($_GET['product_code']) && empty($_GET['product_name']) && empty($_GET['product_code'])) {
                $product->where($this->searchIndex());
            } else {
                if (!empty($_GET['product_name'])) {
                    $code = 'products.name';
                    $search = $_GET['product_name'];
                } elseif (!empty($_GET['product_code'])) {
                    $code = 'products.code';
                    $search = $_GET['product_code'];
                }
                $product->like($code, $search, 'both');
            }
        }
        $product->orderBy('products.id','DESC');
        // echo json_encode($product->get()->getResult());die();
        $data = [
            'products' => $product->paginate(10),
            'pager' => $product->pager,
            'categories' => $this->tableCategories->where(['expenses' => 'no'])->asObject()->get()->getResult(),
            'rol' => $rol
        ];
        echo view('products/index', $data);
    }

    public function searchIndex(): array
    {
        $data = [
            //'products.kind_product_id !=' => 3
        ];
        if (!empty($this->request->getGet('category'))) {
            $data['category.id'] = $this->request->getGet('category');
        }
        if (!empty($this->request->getGet('product_name'))) {
            $data['products.name'] = $this->request->getGet('product_name');
        }
        if (!empty($this->request->getGet('product_code'))) {
            $data['products.code'] = $this->request->getGet('product_code');
        }
        if (!empty($this->request->getGet('tax_iva'))) {
            $data['products.tax_iva'] = $this->request->getGet('tax_iva');
        }
        return $data;
    }

    public function create()
    {
        $data = [
            'categories' => $this->tableCategories->where('payroll', 'no')->asObject()->get()->getResult(),
            'unitMeasures' => $this->tableUnitMeasures->asObject()->get()->getResult(),
            'typeItemIdentifications' => $this->tableTypeItemIdentifications->asObject()->get()->getResult(),
            'accountingAccounts' => $this->tableAccountingAccount->asObject()->get()->getResult(),
            'typeGenerationTransmitions' => $this->tableTypeGenerationTransmitions->asObject()->get()->getResult(),
            'providers' => $this->tableProviders->asObject()->get()->getResult(),
            'gender' => $this->tableGender->asObject()->get()->getResult(),
            'groups' => $this->tableGroups->asObject()->get()->getResult(),
            'subGroup' => $this->tableSubGroups->asObject()->get()->getResult(),
            'materials' => $this->tableMaterials->asObject()->get()->getResult(),
        ];
        echo view('products/create', $data);
    }

    /**
     * funcion para crear producto nuevo
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function store()
    {
        try {
            $imagen = null;
            if ($file = $this->request->getFile('photo')) {
                if ($file->isValid()) {
                    $imagen = $file->getRandomName();
                    $ext1 = $file->getExtension();
                    $file->move('assets/upload/products/', $imagen);
                }
            }
            $providers = $this->tableProviders->where(['code' => $_POST['provider_id']])->asObject()->first();
            $gender = $this->tableGender->where(['code' => $_POST['gender_id']])->asObject()->first();
            $groups = $this->tableGroups->where(['code' => $_POST['group_id']])->asObject()->first();
            $subGroup = $this->tableSubGroups->where(['code' => $_POST['sub_group_id']])->asObject()->first();
            $materials = $this->tableMaterials->where(['code' => $_POST['material_id']])->asObject()->first();
            $unionCode = "{$_POST['provider_id']}{$_POST['gender_id']}{$_POST['group_id']}{$_POST['sub_group_id']}{$_POST['material_id']}";
            $code= substr($_POST['product_code'], 0, -2);
            if ($unionCode != $code) {
                throw  new \Exception('El código no coincide con la union de sus partes.');
            }
            $productExists = $this->validateCode($_POST['product_code'], $_POST['code_item']);
            if (!$productExists) {
                throw  new \Exception('El producto con el código: ' . $_POST['product_code'] . ' ya se encuentra creado.');
            }
            $id = $this->tableAccountingAccount->where(['code' => '0000000'])->asObject()->first();
            $data = [
                'name' => $_POST['product_name'],
                'code' => "{$_POST['product_code']}",
                'code_item' => $_POST['code_item'],
                'valor' => $_POST['product_value'],
                'value_one' => ($_POST['value_one'] ?? 0),
                'value_two' => ($_POST['value_two'] ?? 0),
                'value_three' => ($_POST['value_three'] ?? 0),
                'cost' => $_POST['product_cost'],
                'description' => $_POST['description'],
                'unit_measures_id' => $_POST['unitMeasure'],
                'type_item_identifications_id' => $_POST['typeItemDocument'],
                'reference_prices_id' => 1,
                'free_of_charge_indicator' => ($_POST['product_free'] == 'no') ? 'false' : 'true',
                'companies_id' => company()->id,
                'entry_credit' => $_POST['entry_credit'],
                'entry_debit' => $_POST['entry_debit'],
                'iva' => $_POST['iva'],
                'retefuente' => $_POST['reteFuente'],
                'reteica' => $_POST['reteIca'],
                'reteiva' => $_POST['reteIva'],
                'account_pay' => $_POST['account_pay'],
                'foto' => $imagen,
                'tax_iva' => 'F',
                'provider_id' => $providers->id,
                'gender_id' => $gender->id,
                'group_id' => $groups->id,
                'sub_group_id' => $subGroup->id,
                'material_id' => $materials->id
            ];
            //$products->update(['id' => $this->request->getPost('product')], $data)
            if ($this->tableProducts->save($data)) {
                $data['tax_iva'] = 'R';
                $data['iva'] = $id->id;
                $this->tableProducts->save($data);
                return redirect()->to(base_url() . route_to('products-index'))->with('success', 'Se cambio la foto exitosamente.');
            } else {
                throw  new \Exception('No se pudo guardar el producto');
            }

        } catch (\Exception $e) {
            return redirect()->to(base_url() . route_to('products-index'))->with('errors', $e->getMessage());
        }
    }

    /**
     * funcion para ver productos y sus detalles
     * @param $id
     */
    public function show($id)
    {
        $product = $this->tableProducts
            ->join('category', 'category.id = products.category_id')
            ->select('*, category.name as categoria, products.name as producto, products.id as productId')
            ->asObject()->find($id);
        $politics = $this->tableProductsDetails
            ->where(['status' => 'active', 'id_product' => $id])->asObject()->first();
        if (!is_null($politics)) {
            $product->cost = $politics->cost_value;
        }
        $details = $this->tableProductsDetails
            ->join('invoices', 'invoices.id = products_details.id_invoices', 'left')
            ->select('products_details.*, invoices.resolution, invoices.created_at as invoiceCreate, products_details.created_at as detailCreate')
            ->where(['id_product' => $id])
            ->orderBy('status', 'ASC');
        $data = [
            'product' => $product,
            'details' => $details->paginate(10),
            'pager' => $details->pager,
        ];
        echo view('products/show', $data);
    }

    public function edit($id)
    {
        $data = [
            'product' => $this->tableProducts->asObject()->find($id),
            'categories' => $this->tableCategories->where(['expenses' => 'no'])->asObject()->get()->getResult(),
            'unitMeasures' => $this->tableUnitMeasures->asObject()->get()->getResult(),
            'typeItemIdentifications' => $this->tableTypeItemIdentifications->asObject()->get()->getResult(),
            'accountingAccounts' => $this->tableAccountingAccount->asObject()->get()->getResult(),
            'typeGenerationTransmitions' => $this->tableTypeGenerationTransmitions->asObject()->get()->getResult(),
            'providers' => $this->tableProviders->asObject()->get()->getResult(),
            'gender' => $this->tableGender->asObject()->get()->getResult(),
            'groups' => $this->tableGroups->asObject()->get()->getResult(),
            'subGroup' => $this->tableSubGroups->asObject()->get()->getResult(),
            'materials' => $this->tableMaterials->asObject()->get()->getResult(),
        ];
        // echo json_encode($data['product']);die();
        echo view('products/edit', $data);
    }

    /**
     * Funcion para actualizar producto
     * @param $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        try {
            $productOriginal = $this->tableProducts->where('id', $id)->asObject()->first();
            $gender = $this->tableGender->where(['code' => $_POST['gender_id']])->asObject()->first();
            $providers = $this->tableProviders->where(['code' => $_POST['provider_id']])->asObject()->first();
            $materials = $this->tableMaterials->where(['code' => $_POST['material_id']])->asObject()->first();
            $subGroup = $this->tableSubGroups->where(['code' => $_POST['sub_group_id']])->asObject()->first();
            $groups = $this->tableGroups->where(['code' => $_POST['group_id']])->asObject()->first();
            $unionCode = "{$_POST['provider_id']}{$_POST['gender_id']}{$_POST['group_id']}{$_POST['sub_group_id']}{$_POST['material_id']}";
            $code= substr($_POST['product_code'], 0, -2);
            if ($unionCode != $code) {
                throw  new \Exception('El código no coincide con la union de sus partes.');
            }
            if($_POST['product_code'] != $productOriginal->code){
                $productExists = $this->validateCode($_POST['product_code'], $_POST['code_item']);
                if (!$productExists) {
                    throw  new \Exception('El producto con el código: ' . $_POST['product_code'] . ' ya se encuentra creado.');
                }
            }
            $productCopy = $this->tableProducts->where(['code' => $productOriginal->code, 'code_item' => $productOriginal->code_item, 'id !=' => $id])->asObject()->first();
            //echo json_encode($productCopy);die();
            $data = [
                'name' => $_POST['product_name'],
                'code' => "{$_POST['product_code']}",
                'code_item' => $_POST['code_item'],
                'valor' => $_POST['product_value'],
                'value_one' => ($_POST['value_one'] ?? 0),
                'value_two' => ($_POST['value_two'] ?? 0),
                'value_three' => ($_POST['value_three'] ?? 0),
                'cost' => $_POST['product_cost'],
                'description' => $_POST['description'],
                'unit_measures_id' => $_POST['unitMeasure'],
                'type_item_identifications_id' => $_POST['typeItemDocument'],
                'free_of_charge_indicator' => ($_POST['product_free'] == 'no') ? 'false' : 'true',
                'entry_credit' => $_POST['entry_credit'],
                'entry_debit' => $_POST['entry_debit'],
                'iva' => $_POST['iva'],
                'retefuente' => $_POST['reteFuente'],
                'reteica' => $_POST['reteIca'],
                'reteiva' => $_POST['reteIva'],
                'account_pay' => $_POST['account_pay'],
                'provider_id' => $providers->id,
                'gender_id' => $gender->id,
                'group_id' => $groups->id,
                'sub_group_id' => $subGroup->id,
                'material_id' => $materials->id
            ];

            if ($file = $this->request->getFile('photo')) {
                if ($file->isValid()) {
                    $imagen = $file->getRandomName();
                    $ext1 = $file->getExtension();
                    $file->move('assets/upload/products/', $imagen);
                    $data['foto'] = $imagen;
                }
            }
            if ($this->tableProducts->set($data)->whereIn('id', [$productOriginal->id, $productCopy->id])->update()) {
                return redirect()->to(base_url() . route_to('products-edit', $id))->with('success', 'Se cambio la foto exitosamente.');
            } else {
                throw  new \Exception('No se pudo guardar el producto');
            }

        } catch (\Exception $e) {
             return redirect()->to(base_url() . route_to('products-edit', $id))->with('errors', $e->getMessage());
        }
    }

    public function search()
    {
        echo view('inventory/search_product');
    }

    /**
     * Funcion para crear detalles de producto personalizados y deshabilitar los detalles anteriores
     * @param $idProduct
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function createDetails($idProduct)
    {
        try {
            $detailProduct = [
                'id_product' => $idProduct,
                'policy_type' => $_POST['policy_type'],
                'cost_value' => $_POST['cost_value'],
                'status' => 'active'
            ];
            if (!empty($_POST['observations'])) {
                $detailProduct['observations'] = $_POST['observations'];
            }
            if (!empty($_POST['remission'])) {
                $detailProduct['id_invoices'] = $_POST['remission'];
            }

            $detail = $this->tableProductsDetails
                ->where(['status' => 'active', 'id_product' => $idProduct])
                ->asObject()->first();
            if (!is_null($detail)) {
                if ($this->tableProductsDetails->update(['id' => $detail->id_products_details], ['status' => 'inactive'])) {
                    if ($this->tableProductsDetails->save($detailProduct)) {
                        return redirect()->to(base_url() . route_to('products-show', $idProduct))->with('success', 'Detalle guardado con exíto');
                    } else {
                        throw  new \Exception('No se puedo guardar nuevo detalle');
                    }
                } else {
                    throw  new \Exception('No se puedo actualizar detalle anterior');
                }
            } else {
                if ($this->tableProductsDetails->save($detailProduct)) {
                    return redirect()->to(base_url() . route_to('products-show', $idProduct))->with('success', 'Detalle guardado con exíto');
                } else {
                    throw  new \Exception('No se puedo guardar nuevo detalle');
                }
            }

        } catch (\Exception $e) {
            return redirect()->to(base_url() . route_to('products-show', $idProduct))->with('error', $e->getMessage());
        }
    }

    /**
     * Funcion creada para realizar activacion y desactivacion de detalles
     * @return false|string
     */
    public function changeStatusDetail()
    {
        header('Content-Type: application/json');
        try {
            if ($_POST['status'] == 'active') {
                $statusDetail = 'inactive';
            } else {
                $statusDetail = 'active';
                $dataUpdate = $this->tableProductsDetails
                    ->where(['id_product' => $_POST['idProduct'], 'status' => 'active'])
                    ->asObject()->get()->getResult();
                $this->tableProductsDetails
                    ->set(['status' => 'inactive'])
                    ->where(['id_product' => $_POST['idProduct']])
                    ->update();
            }
            if ($this->tableProductsDetails->update(['id' => $_POST['idDetail']], ['status' => $statusDetail])) {
                return json_encode([
                    'status' => 200,
                    'observation' => 'se actualizo correctamente el estado',
                    'newStatus' => $statusDetail,
                    'dataUpdate' => $dataUpdate ?? ''
                ]);
            } else {
                throw  new \Exception('No se puedo cambiar el estado');
            }
        } catch (\Exception $e) {
            return json_encode([
                'status' => 500,
                'observation' => $e->getMessage()
            ]);
        }
    }

    public function validateCode($code, $code_item = null): bool
    {
        if(!is_null($code_item)){
            $validate = $this->tableProducts->where(['code' => $code, 'code_item' => $code_item])->first();
        }else{
            $validate = $this->tableProducts->like('code', $code, 'both')->first();
        }
        if (is_null($validate)) {
            return true;
        } else {
            return false;
        }
    }

    public function jsonCode()
    {
        $code = explode('-',$_POST['code']);
        $validate = $this->validateCode("{$code[0]}{$code[1]}{$code[2]}{$code[3]}{$code[4]}{$code[5]}");
        $gender = $this->tableGender->where(['code' => $code[1]])->asObject()->first();
        $providers = $this->tableProviders->where(['code' => $code[0]])->asObject()->first();
        $materials = $this->tableMaterials->where(['code' => $code[4]])->asObject()->first();
        $subGroup = $this->tableSubGroups->where(['code' => $code[3]])->asObject()->first();
        $groups = $this->tableGroups->where(['code' => $code[2]])->asObject()->first();
        $itemsCode = [];
        for ($i = 0; $i <= 99; $i++) {
            $number = (strlen($i) == 1) ? "0{$i}" : "{$i}";
            array_push($itemsCode, ['id' => $number]);
        }
        $itemsDelete = [];
        if (!$validate) {
            //echo json_encode("{$code[1]}");die();
            $codesItems = $this->tableProducts->where(['provider_id' => $providers->id, 'gender_id' => $gender->id,
                'group_id' => $groups->id, 'sub_group_id' => $subGroup->id, 'material_id' => $materials->id
            ])->asObject()->get()->getResult();
            foreach ($codesItems as $codesItem) {
                unset($itemsCode[(int)$codesItem->code_item]);
            }
        }
        $itemsCode = array_values($itemsCode);
        return json_encode([
            'validate' => $validate,
            'items' => $itemsCode
        ]);
    }

    public function subGroups()
    {
        $group = $this->tableGroups->where(['code' => $_POST['id']])->asObject()->first();
        $subGroups = $this->tableSubGroups->select(['code', 'name'])->where(['group_id' => $group->id])->get()->getResult();
        return json_encode($subGroups);
    }
}