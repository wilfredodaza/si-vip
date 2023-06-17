<?php

/**
 * Clase encargada de la importación de archivos
 * Cuentas Contables, Productos, Clientes y Facturación
 * @author Wilson Andres Bachiller Ortiz
 * @email wilson@mawii.com.co
 * @version v2.0.0
 */

namespace App\Controllers;


use App\Controllers\Imports\CustomerImportController;
use App\Models\AccountingAcount;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Gender;
use App\Models\Groups;
use App\Models\Materials;
use App\Models\Municipalities;
use App\Models\PayrollDate;
use App\Models\Prices;
use App\Models\Product;
use App\Models\Providers;
use App\Models\SubGroup;
use App\Models\typeAccountingAccount;
use App\Models\TypeDocumentIdentifications;
use App\Models\TypeItemIdentification;
use App\Models\TypeOrganizations;
use App\Models\TypeRegimes;
use App\Models\Accrued;
use App\Models\Deduction;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\SubPeriod;
use App\Models\Period;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\Resolution;
use App\Models\PayrollPeriod;
use App\Controllers\Api\Auth;
use App\Models\UnitMeasure;
use App\Traits\ExcelValidationTrait;
use Exception;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class ImportController extends BaseController
{

    use ExcelValidationTrait;

    private $documents;

    public function index()
    {
        $model = new Resolution();
        $resolutions = $model->select(['resolution', 'from', 'prefix', 'from', 'to'])
            ->where([
                'companies_id'      => Auth::querys()->companies_id,
                'type_documents_id' => 1
            ])
            ->asObject()
            ->get()
            ->getResult();

        $model = new PayrollPeriod();
        $payrollPeriods = $model->asObject()->get()->getResult();

        $model = new Period();
        $periods = $model->asObject()->get()->getResult();

        $controllerHeadquarters = new HeadquartersController();
        $companies = new Company();
        $headquarters = $companies
            ->select('companies.id, companies.company')
            ->whereIn('id', $controllerHeadquarters->idsCompaniesHeadquarters())
            ->where(['id !=' => 1])
            ->asObject()->get()->getResult();


        return  view('pages/import', ['resolutions' => $resolutions, 'periods' => $periods, 'payrollPeriods' => $payrollPeriods, 'sedes' => $headquarters]);
    }

    public function Upload()
    {

        $clientes = new Customer();
        $errores = [];
        if (!empty($_FILES['file']['name'])) {
            $pathinfo = pathinfo($_FILES["file"]["name"]);
            if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls')
                && $_FILES['file']['size'] > 0) {
                $inputFileName = $_FILES['file']['tmp_name'];
                // prueba
                $reader = ReaderEntityFactory::createReaderFromFile('/path/to/file.xlsx');
                $reader->setShouldFormatDates(true);
                $reader->open($inputFileName);
                $count = 1;

                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        // do stuff with the row
                        if ($count > 1) {
                            $cells = $row->getCells();
                            // Guardar Productos
                            if ($_POST['tipoD'] == 1) {
                                $account = new AccountingAcount();
                                $products = new Product();
                                $tableGender = new Gender();
                                $tableGroups = new Groups();
                                $tablePrices = new Prices();
                                $tableSubGroups = new SubGroup();
                                $tableMaterials = new Materials();
                                $tableProviders = new Providers();
                                $tableTypeItemIdentifications = new TypeItemIdentification();
                                $tableAccountingAccount = new AccountingAcount();
                                $tableUnit = new UnitMeasure();
                                // validations tables
                                // providers
                                $error = [];
                                if (isset($cells[0]) && trim($cells[0]) != '') {
                                    $code = explode('-', trim($cells[0]));
                                    $codeProvider = $code[0];
                                    $provider = $tableProviders->where(['code' => $code[0]])->asObject()->first();
                                    if (is_null($provider)) {
                                        array_push($error, "No existe código para 'proveedor'.  Fila # " . $count);
                                    }
                                } else {
                                    array_push($error, "El campo Proveedor es obligatorio.  Fila # " . $count);
                                }
                                // gender
                                if (isset($cells[1]) && trim($cells[1]) != '') {
                                    $code = explode('-', trim($cells[1]));
                                    $codeGender = $code[0];
                                    $gender = $tableGender->where(['code' => $code[0]])->asObject()->first();
                                    if (is_null($gender)) {
                                        array_push($error, "No existe código para 'genero'.  Fila # " . $count);
                                    }
                                } else {
                                    array_push($error, "El campo Genero es obligatorio.  Fila # " . $count);
                                }
                                // groups
                                if (isset($cells[2]) && trim($cells[2]) != '') {
                                    $code = explode('-', trim($cells[2]));
                                    $codeGroup = $code[0];
                                    $group = $tableGroups->where(['code' => $code[0]])->asObject()->first();
                                    if (is_null($group)) {
                                        array_push($error, "No existe código para 'grupo'.  Fila # " . $count);
                                    }
                                } else {
                                    array_push($error, "El campo Grupo es obligatorio.  Fila # " . $count);
                                }
                                // subgroups
                                if (isset($cells[3]) && trim($cells[3]) != '') {
                                    $code = explode('-', trim($cells[3]));
                                    $codeSubGroup = $code[0];
                                    $subGroup = $tableSubGroups->where(['code' => $code[0], 'group_id' => $group->id])->asObject()->first();
                                    if (is_null($subGroup)) {
                                        array_push($error, "No existe código para 'Sub Grupo'.  Fila # " . $count);
                                    }
                                } else {
                                    array_push($error, "El campo Sub Grupo es obligatorio.  Fila # " . $count);
                                }
                                // Materials
                                if (isset($cells[4]) && trim($cells[4]) != '') {
                                    $code = explode('-', trim($cells[4]));
                                    $codeMaterial = $code[0];
                                    $material = $tableMaterials->where(['code' => $code[0]])->asObject()->first();
                                    // echo json_encode($material);die();
                                    if (is_null($material)) {
                                        array_push($error, "No existe código para 'Material'.  Fila # " . $count);
                                    }
                                } else {
                                    array_push($error, "El campo Material es obligatorio.  Fila # " . $count);
                                }
                                // producto
                                if (isset($cells[5]) && trim($cells[5]) == '') {
                                    array_push($error, "El campo nombres es obligatorio.  Fila # " . $count);
                                }
                                // precio Producto
                                if (isset($cells[6]) && trim($cells[6]) != '') {
                                    /*if (!is_numeric(trim($cells[6]))) {
                                        $error = "El campo 'valor' deben ser solo tipo numericos. Fila # " . $count;
                                    }*/
                                } else {
                                    array_push($error, "El campo Valor es obligatorio.  Fila # " . $count);
                                }
                                // costo Producto
                                if (isset($cells[7]) && trim($cells[7]) != '') {
                                    /*if (!is_numeric(trim($cells[7]))) {
                                        $error = "El campo 'Costo' deben ser solo tipo numericos. Fila # " . $count;
                                    }¨*/
                                } else {
                                    array_push($error, "El campo Costo es obligatorio.  Fila # " . $count);
                                }
                                // validation Product FREE
                                /*if (isset($cells[8]) && trim($cells[8]) != '') {
                                    if (trim($cells[8]) != 'Si' && trim($cells[8]) != 'No') {
                                        $error = "Los parámetros son incorrectos. Fila # " . $count;
                                    }
                                } else {
                                    $error = "El campo Producto Gratis es obligatorio";
                                }*/
                                // validation entry credit
                                $entryCredit = $account->select('id')->where(['nature' => 'Crédito', 'type_accounting_account_id' => 1])->get()->getResult()[0];
                                /*if (isset($cells[9]) && trim($cells[9]) != '') {
                                    $code = explode('-', trim($cells[9]));
                                    $entryCredit = $code[0];
                                } else {
                                    $error = "El campo Entrada es obligatorio";
                                }*/
                                // validation campo entry debit
                                $entryDebit = $account->select('id')->where(['nature' => 'Débito', 'type_accounting_account_id' => 1])->get()->getResult()[0];
                                /*if (isset($cells[10]) && trim($cells[10]) != '') {
                                    $code = explode('-', trim($cells[10]));
                                    $entryDebit = $code[0];
                                } else {
                                    $error = "El campo Devolucion es obligatorio";
                                }*/
                                // validation campo iva
                                $sinIva = $account->select('id')->where(['code' => '0000000', 'type_accounting_account_id' => 2])->get()->getResult()[0];
                                $iva = $account->select('id')->where(['type_accounting_account_id' => 2])->get()->getResult()[0];
                                /*if (isset($cells[11]) && trim($cells[11]) != '') {
                                    $code = explode('-', trim($cells[11]));
                                    $iva = $code[0];
                                } else {
                                    $error = "El campo Iva es obligatorio";
                                }*/
                                // validation campo retefuente
                                $retefuente = $account->select('id')->where(['type_accounting_account_id' => 3])->get()->getResult()[0];
                                /*if (isset($cells[12]) && trim($cells[12]) != '') {
                                    $code = explode('-', trim($cells[12]));
                                    $reteFuente = $code[0];
                                } else {
                                    $error = "El campo Retención de fuente es obligatorio";
                                }*/
                                // validation campo reteica
                                $reteica = $account->select('id')->where(['type_accounting_account_id' => 3])->get()->getResult()[0];
                                /*if (isset($cells[13]) && trim($cells[13]) != '') {
                                    $code = explode('-', trim($cells[13]));
                                    $reteIca = $code[0];
                                } else {
                                    $error = "El campo Reteica es obligatorio";
                                }*/
                                // validation campo reteiva
                                $reteiva = $account->select('id')->where(['type_accounting_account_id' => 3])->get()->getResult()[0];
                                /*if (isset($cells[14]) && trim($cells[14]) != '') {
                                    $code = explode('-', trim($cells[14]));
                                    $reteIva = $code[0];
                                } else {
                                    $error = "El campo Reteiva es obligatorio";
                                }*/
                                //validation campo account pay
                                $account_pay = $account->select('id')->where(['type_accounting_account_id' => 4])->get()->getResult()[0];
                                /*if (isset($cells[15]) && trim($cells[15]) != '') {
                                    $code = explode('-', trim($cells[15]));
                                    $account_pay = $code[0];
                                } else {
                                    $error = "El campo cuenta por cobrar es obligatorio";
                                }*/
                                if (count($error) > 0) {
                                    array_push($errores, $error);
                                } else {
                                    $controllerProduct = new ProductsController();
                                    $serial = "{$codeProvider}{$codeGender}{$codeGroup}{$codeSubGroup}{$codeMaterial}00";
                                    $disponible = [];
                                    for ($i = 0; $i <= 99; $i++) {
                                        $number = (strlen($i) == 1) ? "0{$i}" : "{$i}";
                                        array_push($disponible, ['id' => $number]);
                                    }
                                    $validate = $controllerProduct->validateCode($serial);
                                    if (!$validate) {
                                        $codesItems = $products
                                            ->where(['provider_id' => $provider->id, 'gender_id' => $gender->id,
                                                'group_id' => $group->id, 'sub_group_id' => $subGroup->id, 'material_id' => $material->id])
                                            ->asObject()->get()->getResult();
                                        foreach ($codesItems as $codesItem) {
                                            unset($disponible[(int)$codesItem->code_item]);
                                        }
                                    }
                                    $disponible = array_values($disponible);
                                    if (!$validate) {
                                        $serial = substr($serial, 0, -2);
                                        $serial = "{$serial}{$disponible[0]['id']}";
                                    }
                                    $data = array(
                                        'name' => trim($cells[5]),
                                        'tax_iva' => 'F',
                                        'code' => $serial,
                                        'code_item' => $disponible[0],
                                        'valor' => trim($cells[6]),
                                        'cost' => trim($cells[7]),
                                        'description' => trim($cells[5]),
                                        'unit_measures_id' => 70,
                                        'type_item_identifications_id' => 4,
                                        'reference_prices_id' => 1,
                                        'free_of_charge_indicator' => 'false',
                                        'companies_id' => session('user')->companies_id,
                                        'entry_credit' => $entryCredit->id,
                                        'entry_debit' => $entryDebit->id,
                                        'iva' => $iva->id,
                                        'retefuente' => $retefuente->id,
                                        'reteica' => $reteica->id,
                                        'reteiva' => $reteiva->id,
                                        'account_pay' => $account_pay->id,
                                        'provider_id' => $provider->id,
                                        'gender_id' => $gender->id,
                                        'group_id' => $group->id,
                                        'sub_group_id' => $subGroup->id,
                                        'material_id' => $material->id
                                    );
                                    if ($products->insert($data)) {
                                        $data['tax_iva'] = 'R';
                                        $data['iva'] = $sinIva->id;
                                        $products->insert($data);
                                    }

                                }
                            }
                            // Guardar Clientes
                            if ($_POST['tipoD'] == 2) {
                                // validacion campo Regimen
                                if (isset($cells[8]) && trim($cells[8]) != '') {
                                    $regimen = new TypeRegimes();
                                    $idregimen = $regimen->where('id', trim($cells[8]))->countAllResults();
                                    if ($idregimen == 0) {
                                        $error = "El Id en 'regimen' no existe. Fila # " . $count;
                                    }
                                    if (!is_numeric(trim($cells[8]))) {
                                        $error = "El código del 'regimen' debe ser de valor numérico. Fila # " . $count;
                                    }
                                } else {
                                    $error = "El campo Regimen es obligatorio.";
                                }
                                // validacion campo Tipo documento
                                if (isset($cells[1]) && trim($cells[1]) != '') {
                                    $documentos = new TypeDocumentIdentifications();
                                    $documento = $documentos->where('id', trim($cells[1]))->countAllResults();
                                    if ($documento == 0) {
                                        $error = "El Id en 'Tipo de Documento' no existe. Fila # " . $count;
                                    }
                                    if (!is_numeric(trim($cells[1]))) {
                                        $error = "Id Tipo de 'Documento'  debe ser de valor numérico. Fila # " . $count;
                                    }
                                } else {
                                    $error = "El campo Tipo de Documento es obligatorio.";
                                }
                                // validacion campo municipio
                                if (isset($cells[9]) && trim($cells[9]) != '') {
                                    $municipios = new Municipalities();
                                    $municipio = $municipios->where(['id' => trim($cells[9])])->countAllResults();
                                    if ($municipio == 0) {
                                        $error = "El Id en 'Municipio' no existe. Fila # " . $count;
                                    }
                                    if (!is_numeric(trim($cells[9]))) {
                                        $error = "El código de 'municipio' debe ser de valor numérico. Fila # " . $count;
                                    }
                                } else {
                                    $error = "El campo Municipio es obligatorio.";
                                }
                                // validacion campo tipo organizacion
                                if (isset($cells[10]) && trim($cells[10]) != '') {
                                    $tipoOrganizacion = new TypeOrganizations();
                                    $organizacion = $tipoOrganizacion->where('id', trim($cells[10]))->countAllResults();
                                    if ($organizacion == 0) {
                                        $error = "El Id en 'Tipo de organizacion' no existe. Fila # " . $count;
                                    }
                                    if (!is_numeric(trim($cells[10]))) {
                                        $error = "El código de 'tipo de organización' debe ser de valor numérico. Fila # " . $count;
                                    }
                                } else {
                                    $error = "El campo Tipo de Organizacion es obligatorio.";
                                }
                                // validacion campo nombre
                                if (isset($cells[0]) && trim($cells[0]) != '') {
                                    if (!is_string(trim($cells[0]))) {
                                        $error = "Los 'nombres' deben ser de tipo string, no puede contener números. Fila # " . $count;
                                    }
                                } else {
                                    $error = "El campo nombre es obligatorio.";
                                }
                                // validacion campo telefono
                                if (isset($cells[3]) && trim($cells[3]) != '') {
                                    if (!is_numeric(trim($cells[3]))) {
                                        $error = "Número de 'Teléfono' debe ser de valor numérico. Fila # " . $count;
                                    }
                                } else {
                                    $error = "El campo Teléfono es obligatorio.";
                                }
                                //validacion campo correo
                                if (isset($cells[5]) && trim($cells[5]) != '') {
                                    if (!filter_var(trim($cells[5]), FILTER_VALIDATE_EMAIL)) {
                                        $error = "El 'correo electrico 1' no es valido. Fila # " . $count;;
                                    }
                                } else {
                                    $error = "El campo correo electrico es obligatorio.";
                                }
                                if (isset($cells[6]) && trim($cells[6]) != '') {
                                    if (!filter_var(trim($cells[6]), FILTER_VALIDATE_EMAIL)) {
                                        $error = "El 'correo electrico 2' no es valido. Fila # " . $count;;
                                    }
                                }
                                if (isset($cells[7]) && trim($cells[7]) != '') {
                                    if (!filter_var(trim($cells[7]), FILTER_VALIDATE_EMAIL)) {
                                        $error = "El 'correo electrico 3' no es valido. Fila # " . $count;;
                                    }
                                }
                                if (strlen(trim($cells[3])) < 7) {
                                    $error = "El campo 'Teléfono' debe tener minimo 7 caracteres. Fila # " . $count;;
                                }
                                if (isset($cells[2]) && trim($cells[2] != '')) {
                                    if (!is_numeric(trim($cells[2]))) {
                                        $error = "Número de Documento No puede llevar ningun tipo de caracter. Fila # " . $count;
                                    }
                                } else {
                                    $error = "El campo Número de Documento es obligatorio.";
                                }
                                //guardar datos clientes
                                if (!isset($error)) {
                                    $data = [
                                        'name' => trim($cells[0]),
                                        'type_document_identifications_id' => trim($cells[1]),
                                        'identification_number' => trim($cells[2]),
                                        'dv' => $this->calcularDV(trim($cells[2])),
                                        'phone' => trim($cells[3]),
                                        'address' => trim($cells[4]),
                                        'email' => trim($cells[5]),
                                        'email2' => (isset($cells[6]) && trim($cells[6]) != '') ? trim($cells[6]) : '',
                                        'email3' => (isset($cells[7]) && trim($cells[7]) != '') ? trim($cells[7]) : '',
                                        'merchant_registration' => '0000',
                                        'type_customer_id' => 1,
                                        'type_regime_id' => trim($cells[8]),
                                        'municipality_id' => trim($cells[9]),
                                        'companies_id' => session('user')->companies_id,
                                        'type_organization_id' => trim($cells[10])
                                    ];
                                    $clientes->insert($data);
                                }
                            }
                            // Guardar Cuentas Contables
                            if ($_POST['tipoD'] == 3) {
                                $account = new AccountingAcount();
                                // validacion campo tipo cuenta
                                if (isset($cells[0]) && trim($cells[0]) != '') {
                                    $type = new TypeAccountingAccount();
                                    $tipo = $type->where('id', trim($cells[0]))->countAllResults();
                                    if ($tipo == 0) {
                                        $error = "Id de 'tipó de cuenta'  no se encuentra en la base de datos";
                                    }
                                    if (!is_numeric(trim($cells[0]))) {
                                        $error = "Id 'Tipo de Cuenta'  debe ser de valor numérico. Fila # " . $count;
                                    }
                                } else {
                                    $error = 'El campo Tipo de Cuenta es obligatorio';
                                }
                                // validacion campo codigo
                                if (isset($cells[1]) && trim($cells[1]) != '') {
                                    if (!is_numeric(trim($cells[1]))) {
                                        $error = "'Código' debe ser de valor numérico. Fila # " . $count;
                                    }
                                } else {
                                    $error = 'El campo Código es obligatorio';
                                }
                                //validacion campo nombres
                                if (isset($cells[2]) && trim($cells[2]) != '') {
                                    if (!is_string(trim($cells[2]))) {
                                        $error = "Los 'nombres 'deben ser de tipo string, no puede contener números. Fila # " . $count;
                                    }
                                } else {
                                    $error = 'El campo Nombres es obligatorio';
                                }
                                // validacion campo porcentaje
                                if (isset($cells[3]) && trim($cells[3]) != '') {
                                    if (!is_numeric(trim($cells[3]))) {
                                        $error = "'Porcentaje' debe ser de valor numérico. Fila # " . $count;
                                    }
                                } else {
                                    $error = 'El campo porcentaje es obligatorio';
                                }
                                // validacion campo naturaleza
                                if (isset($cells[4]) && trim($cells[4]) != '') {
                                    if (trim($cells[4]) != 'Débito' && trim($cells[4]) != 'Crédito') {
                                        $error = "Los parámetros son incorrectos en 'naturaleza'. Fila # " . $count;
                                        if (trim($cells[4]) == 'Debito' || trim($cells[4]) == 'debito' || trim($cells[4]) == 'débito') {
                                            $error = "Los parámetros son incorrectos en 'naturaleza'. Asegúrese que la palabra este escrita de la forma correcta (Débito) Fila # " . $count;
                                        }
                                        if (trim($cells[4]) == 'Credito' || trim($cells[4]) == 'credito' || trim($cells[4]) == 'crédito') {
                                            $error = "Los parámetros son incorrectos en 'naturaleza'. Asegúrese que la palabra este escrita de la forma correcta (Crédito) Fila # " . $count;
                                        }
                                    }
                                } else {
                                    $error = 'El campo Naturaleza es obligatorio';
                                }
                                // validacion campo Estado
                                if (isset($cells[5]) && trim($cells[5]) != '') {
                                    if (trim($cells[5]) != 'Activa' && trim($cells[5]) != 'Inactiva') {
                                        $error = "Los parámetros son incorrectos en 'Estado'. Fila # " . $count;
                                    }
                                } else {
                                    $error = 'El campo Estado es obligatorio';
                                }

                                if (!isset($error)) {
                                    $data = [
                                        'companies_id' => session('user')->companies_id,
                                        'type_accounting_account_id' => trim($cells[0]),
                                        'code' => trim($cells[1]),
                                        'name' => trim($cells[2]),
                                        'percent' => trim($cells[3]),
                                        'nature' => trim($cells[4]),
                                        'status' => trim($cells[5]),
                                    ];
                                    $account->insert($data);
                                }
                            }
                            // Guardar cotizaciones
                            if ($_POST['tipoD'] == 4) {

                            }

                            if ($_POST['tipoD'] == 5) {
                                return $this->invoice($this->request->getFile('file'), $_POST['sede']);
                            }

                            if ($_POST['tipoD'] == 6) {
                                return $this->payrollRemovable($this->request->getFile('file'));
                            }

                            if ($_POST['tipoD'] == 7) {

                                $import = new CustomerImportController();
                                return $import->create();
                            }
                        }
                        $count++;
                    }
                }

                $reader->close();

                if ($_POST['tipoD'] != 1) {
                    if (!isset($error)) {
                        if ($_POST['tipoD'] == 2) {
                            $mensaje = "Todos los Clientes se han guardado con exito";
                        } elseif ($_POST['tipoD'] == 3) {
                            $mensaje = "Todas la Cuentas Contables se han guardado con exito";
                        } elseif ($_POST['tipoD'] == 4) {

                        }
                        return redirect()->to(base_url() . '/import')->with('success', $mensaje);
                    } elseif (isset($error)) {
                        return redirect()->to(base_url() . '/import')->with('errors', $error);
                    }
                } else {
                    if (count($error) > 0) {
                        $inconveniente = '';
                        foreach ($errores as $item) {
                            foreach ($item as $text){
                                $inconveniente .= '<br>' . $text;
                            }
                        }
                        return redirect()->to(base_url() . '/import')->with('errors', $inconveniente);
                    } else {
                        $mensaje = "Todos los Productos se han guardado con exito";
                        return redirect()->to(base_url() . '/import')->with('success', $mensaje);
                    }
                }
            }
        }
        return redirect()->to(base_url('import'));
    }

    public function resolutionData($typeDocument, $id = null)
    {
        $resolution = new Resolution();
        $resolution = $resolution->where(['companies_id' => 77]);
        if ($id) {
            $resolution->where(['resolution' => $id]);
            $consulta = ['type_documents_id' => 1];
            $resolution->where($consulta);
        } else {
            $resolution->where(['type_documents_id' => $typeDocument]);
        }

        $resolution = $resolution
            ->orderBy('id', 'DESC')
            ->asObject()
            ->first();


        $invoices = new Invoice();
        $invoices->select('invoices.resolution');
        if ($id) {
            $invoices->where(['companies_id' => 77, 'resolution_id =' => $id]);
        } else {
            $invoices->where(['companies_id' => 77, 'type_documents_id' => $typeDocument]);
        }

        $invoices = $invoices->orderBy('id', 'DESC')
            ->asObject()
            ->first();

        if (!$invoices) {
            return $resolution->from;
        } else {
            return ($invoices->resolution + 1);
        }
    }

    private function calcularDV($nit)
    {
        if (!is_numeric($nit)) {
            return 0;
        }

        $arr = array(1 => 3, 4 => 17, 7 => 29, 10 => 43, 13 => 59, 2 => 7, 5 => 19,
            8 => 37, 11 => 47, 14 => 67, 3 => 13, 6 => 23, 9 => 41, 12 => 53, 15 => 71);
        $x = 0;
        $y = 0;
        $z = strlen($nit);
        $dv = '';

        for ($i = 0; $i < $z; $i++) {
            $y = substr($nit, $i, 1);
            $x += ($y * $arr[$z - $i]);
        }

        $y = $x % 11;

        if ($y > 1) {
            $dv = 11 - $y;
            return $dv;
        } else {
            $dv = $y;
            return $dv;
        }

    }

    /**
     * Methodo de importacion de facturacion electronica
     *
     *
     *  */

    public function invoice($file, $idCompany)
    {

        try {
            $controllerHeadquarters = new HeadquartersController();
            $manager = $controllerHeadquarters->permissionManager(session('user')->role_id);
            /*if ($manager) {
                $idCompany = $controllerHeadquarters->idSearchBodega();
            } else {
                $idCompany = Auth::querys()->companies_id;
            }*/
            if(!$file) {
                return redirect()->back()->with('errors', 'Por favor ingresa un documento');
            }

            $excel                  = IOFactory::load($file);
            $documents              = [];
            $sheet                  = $excel->getSheet(0);
            $largestRowNumber       = $sheet->getHighestRow();

            for ($rowIndex = 2; $rowIndex <= $largestRowNumber; $rowIndex++) {
                // $this->required($sheet->getCell('A'.$rowIndex)->getValue(), 'Consecutivo', 'A'.$rowIndex);
                //$this->required($sheet->getCell('B'.$rowIndex)->getValue(), 'Tipo de Factura', 'B'.$rowIndex);
                $this->required($sheet->getCell('C'.$rowIndex)->getValue(), 'Código del Producto', 'A'.$rowIndex);
                //$this->required($sheet->getCell('D'.$rowIndex)->getValue(), 'Descripción de producto', 'D'.$rowIndex);
                //$this->required($sheet->getCell('E'.$rowIndex)->getValue(), 'Valor por Unidad', 'E'.$rowIndex);
                $this->required($sheet->getCell('F'.$rowIndex)->getValue(), 'Cantidad', 'C'.$rowIndex);
                // $this->required($sheet->getCell('G'.$rowIndex)->getValue(), 'Descuento por Unidad', 'G'.$rowIndex);
                //$this->required($sheet->getCell('H'.(string)$rowIndex)->getValue(), 'IVA %', 'H'.$rowIndex);
                //$this->required($sheet->getCell('I'.(string)$rowIndex)->getValue(), 'ReteFuente %', 'I'.$rowIndex);
                //$this->required($sheet->getCell('J'.(string)$rowIndex)->getValue(), 'ReteICA %', 'J'.$rowIndex);
                //$this->required($sheet->getCell('L'.$rowIndex)->getValue(), 'Número de identificación de cliente', 'L'.$rowIndex);
                //$this->required($sheet->getCell('M'.$rowIndex)->getValue(), 'Forma de pago', 'M'.$rowIndex);
                //$this->required($sheet->getCell('N'.$rowIndex)->getValue(), 'Método de pago', 'N'.$rowIndex);
                //$this->required($sheet->getCell('O'.$rowIndex)->getValue(), 'Moneda', 'O'.$rowIndex);
                //$this->validExistDB($sheet->getCell('C'.$rowIndex)->getValue(), 'Código del Producto', 'C'.$rowIndex, 'products', 'code');
                //$this->validExistDB($sheet->getCell('L'.$rowIndex)->getValue(), 'Número de identificación de cliente', 'L'.$rowIndex, 'customers', 'identification_number', true);
                //$this->validExistDB($sheet->getCell('M'.$rowIndex)->getValue(), 'Forma de pago', 'M'.$rowIndex, 'payment_forms', 'name');
                //$this->validExistDB($sheet->getCell('N'.$rowIndex)->getValue(), 'Método de pago', 'N'.$rowIndex, 'payment_methods', 'name');
                //$this->validExistDB($sheet->getCell('B'.$rowIndex)->getValue(), 'Tipo de Factura', 'B'.$rowIndex, 'type_documents', 'name');
                //$this->validExistDB($sheet->getCell('O'.$rowIndex)->getValue(), 'Moneda', 'O'.$rowIndex, 'type_currencies', 'name');
                //if($sheet->getCell('O'.$rowIndex)->getValue() != 35) {
                //    $this->required($sheet->getCell('P'.$rowIndex)->getValue(), 'TRM', 'P'.$rowIndex);
                //    $this->required($sheet->getCell('Q'.$rowIndex)->getValue(), 'Fecha TRM', 'Q'.$rowIndex);
                //}
            }
            if (count($this->getErrors()) > 0) {
                return redirect()->back()->with('errors', implode('<br>', $this->getErrors()));
            }

            for ($rowIndex = 2; $rowIndex <= $largestRowNumber; $rowIndex++) {
                if (key_exists($sheet->getCell('A' . $rowIndex)->getValue(), $documents)) {
                    $position = count($documents[$sheet->getCell('A' . $rowIndex)->getValue()]);
                } else {
                    $position = 0;
                }

                $documents[$sheet->getCell('A' . $rowIndex)->getValue()][(int)$position] = [
                    'type_document_id' => 101,
                    'cod_producto' => $sheet->getCell('A' . $rowIndex)->getValue(),
                    'description' => $sheet->getCell('B' . $rowIndex)->getValue(),
                    'value' => 0,
                    'quantity' => (int)$sheet->getCell('C' . $rowIndex)->getValue(),
                    'discount' => 0,
                    'iva' => 0,
                    'retefuente' => 0,
                    'reteICA' => 0,                    //'notes' => $sheet->getCell('K' . $rowIndex)->getValue(),
                    //'identification_number' => $sheet->getCell('L' . $rowIndex)->getValue(),
                    //'payment_form_id' => $sheet->getCell('M' . $rowIndex)->getValue(),
                    //'payment_method_id' => $sheet->getCell('N' . $rowIndex)->getValue(),
                    //'idcurrency' => $sheet->getCell('O' . $rowIndex)->getValue(),
                    //'calculationrate' => $sheet->getCell('P' . $rowIndex)->getValue(),
                    //'calculationratedate' => $this->tranformDate($sheet->getCell('Q' . $rowIndex)->getValue())
                ];
            }
            $model = new Customer();
            $customerId = $model->select(['id'])
                ->where([
                    'identification_number' => 900782726,
                    //'companies_id' => Auth::querys()->companies_id
                ])
                ->asObject()
                ->first();
            $model = new Invoice();
            $dataInvoice = [
                'payment_forms_id' => 1,
                'payment_methods_id' => 10,
                'type_documents_id' => 101,
                'idcurrency' => 35,
                'invoice_status_id' => 1,
                'customers_id' => $customerId->id,
                'companies_id' => $idCompany,
                'user_id' => Auth::querys()->id,
                'resolution' => 900782726,
                'resolution_id' => null,
                'payment_due_date' => date('Y-m-d'),
                'duration_measure' => 0,
                'line_extesion_amount' => 0,
                'tax_exclusive_amount' => 0,
                'tax_inclusive_amount' => 0,
                'allowance_total_amount' => 0,
                'charge_paid_amount' => 0,
                'payable_amount' => 0,
                'calculationrate' => 0,
                'issue_date' => date('Y-m-d'),
                'notes' => 'Inventario',
                'send' => 'True'
            ];
            $invoiceId = $model->insert($dataInvoice);
            $l = 0;
            foreach ($documents as $document) {

                $lineExtesionAmount = 0;
                $taxExclusiveAmount = 0;
                $taxInclusiveAmount = 0;
                $payableAmount = 0;
                $tax = 0;

                foreach ($document as $line) {
                    $line = (object)$line;
                    $model = new Product();
                    $productId = $model->select(['id','cost'])
                        ->where([
                            'code' => $line->cod_producto,
                            //'companies_id'          => Auth::querys()->companies_id,
                            'tax_iva' => 'R'
                        ])
                        ->asObject()
                        ->first();

                    $dataLine = [
                        'invoices_id' => $invoiceId,
                        'discounts_id' => 1,
                        'products_id' => $productId->id,
                        'discount_amount' => ($line->discount * $line->quantity),
                        'quantity' => $line->quantity,
                        'price_amount' => $productId->cost,
                        'line_extension_amount' => ($productId->cost * $line->quantity) - ($line->discount * $line->quantity),
                        'description' => $line->description
                    ];
                    $lineExtesionAmount += $dataLine['line_extension_amount'];

                    $modelLine = new LineInvoice();
                    $lineInvoiceId = $modelLine->insert($dataLine);

                    $modelTax = new LineInvoiceTax();

                    $dataTax = [
                        'line_invoices_id' => $lineInvoiceId,
                        'taxes_id' => 1,
                        'tax_amount' => ($line->iva == 0 ? 0 : $dataLine['line_extension_amount'] * $line->iva / 100),
                        'taxable_amount' => $dataLine['line_extension_amount'],
                        'percent' => $line->iva
                    ];

                    $modelTax->insert($dataTax);

                    $tax += $dataTax['tax_amount'];

                    $dataTax = [
                        'line_invoices_id' => $lineInvoiceId,
                        'taxes_id' => 5,
                        'tax_amount' => 0,
                        'taxable_amount' => $dataLine['line_extension_amount'],
                        'percent' => 0
                    ];
                    $modelTax->insert($dataTax);

                    $dataTax = [
                        'line_invoices_id' => $lineInvoiceId,
                        'taxes_id' => 6,
                        'tax_amount' => ($line->retefuente == 0 ? 0 : $dataLine['line_extension_amount'] * $line->retefuente / 100),
                        'taxable_amount' => $dataLine['line_extension_amount'],
                        'percent' => $line->retefuente
                    ];

                    $modelTax->insert($dataTax);

                    $dataTax = [
                        'line_invoices_id' => $lineInvoiceId,
                        'taxes_id' => 7,
                        'tax_amount' => ($line->reteICA == 0 ? 0 : $dataLine['line_extension_amount'] * $line->reteICA / 100),
                        'taxable_amount' => $dataLine['line_extension_amount'],
                        'percent' => $line->reteICA
                    ];
                    $modelTax->insert($dataTax);
                }
                $taxExclusiveAmount = $lineExtesionAmount;
                $payableAmount = $lineExtesionAmount + $tax;
                $taxInclusiveAmount = $lineExtesionAmount + $tax;


                $model = new Invoice();
                $model->where(['id' => $invoiceId])
                    ->set('line_extesion_amount', $lineExtesionAmount)
                    ->set('tax_exclusive_amount', $taxExclusiveAmount)
                    ->set('tax_inclusive_amount', $taxInclusiveAmount)
                    ->set('payable_amount', $payableAmount)
                    ->update();
                $l++;

            }

            return redirect()->back()->with('success', 'El documento excel fue cargado correctamente.');
        } catch (\exception $e) {
            return redirect()->back()->with('errors', $e->getMessage());
        }

    }

    public function payrollRemovable($file)
    {
        // header('Content-Type: application/json');
        if (!$file) {
            return redirect()->back()->with('errors', 'Por favor ingresa un documento');
        }

        $excel = IOFactory::load($file);
        $documents = [];
        $sheet = $excel->getSheet(0);
        $largestRowNumber = $sheet->getHighestRow();

        for ($rowIndex = 2; $rowIndex <= $largestRowNumber; $rowIndex++) {
            $this->required($sheet->getCell('B' . $rowIndex)->getValue(), 'Número de identificación de cliente', 'B' . $rowIndex);
            $this->validExistDB($sheet->getCell('B' . $rowIndex)->getValue(), 'Número de identificación de cliente', 'B' . $rowIndex, 'customers', 'identification_number', true);
            /*   $this->required($sheet->getCell('B'.$rowIndex)->getValue(), 'Tipo de Factura', 'B'.$rowIndex);
               $this->required($sheet->getCell('C'.$rowIndex)->getValue(), 'Código del Producto', 'C'.$rowIndex);
               $this->required($sheet->getCell('D'.$rowIndex)->getValue(), 'Descripción de producto', 'D'.$rowIndex);
               $this->required($sheet->getCell('E'.$rowIndex)->getValue(), 'Valor por Unidad', 'E'.$rowIndex);
               $this->required($sheet->getCell('F'.$rowIndex)->getValue(), 'Cantidad', 'F'.$rowIndex);
               $this->required($sheet->getCell('G'.$rowIndex)->getValue(), 'Descuento por Unidad', 'G'.$rowIndex);
               $this->required($sheet->getCell('H'.(string)$rowIndex)->getValue(), 'IVA %', 'H'.$rowIndex);
               $this->required($sheet->getCell('I'.(string)$rowIndex)->getValue(), 'ReteFuente %', 'I'.$rowIndex);
               $this->required($sheet->getCell('J'.(string)$rowIndex)->getValue(), 'ReteICA %', 'J'.$rowIndex);
               $this->required($sheet->getCell('M'.$rowIndex)->getValue(), 'Forma de pago', 'M'.$rowIndex);
               $this->required($sheet->getCell('N'.$rowIndex)->getValue(), 'Método de pago', 'N'.$rowIndex);
               $this->required($sheet->getCell('O'.$rowIndex)->getValue(), 'Moneda', 'O'.$rowIndex);
               $this->validExistDB($sheet->getCell('C'.$rowIndex)->getValue(), 'Código del Producto', 'C'.$rowIndex, 'products', 'code', true);
               $this->validExistDB($sheet->getCell('L'.$rowIndex)->getValue(), 'Número de identificación de cliente', 'L'.$rowIndex, 'customers', 'identification_number', true);
               $this->validExistDB($sheet->getCell('M'.$rowIndex)->getValue(), 'Forma de pago', 'M'.$rowIndex, 'payment_forms', 'id');
               $this->validExistDB($sheet->getCell('N'.$rowIndex)->getValue(), 'Método de pago', 'N'.$rowIndex, 'payment_methods', 'id');
               $this->validExistDB($sheet->getCell('B'.$rowIndex)->getValue(), 'Tipo de Factura', 'B'.$rowIndex, 'type_documents', 'id');
               $this->validExistDB($sheet->getCell('O'.$rowIndex)->getValue(), 'Moneda', 'O'.$rowIndex, 'type_currencies', 'id');
               if($sheet->getCell('O'.$rowIndex)->getValue() != 35) {
                   $this->required($sheet->getCell('P'.$rowIndex)->getValue(), 'TRM', 'P'.$rowIndex);
                   $this->required($sheet->getCell('Q'.$rowIndex)->getValue(), 'Fecha TRM', 'Q'.$rowIndex);
               }*/
        }

        if (count($this->getErrors()) > 0) {
            return redirect()->back()->with('errors', implode('<br>', $this->getErrors()));
        }

        $model = new Period();
        $period = $model->where(['id' => $this->request->getPost('period_id')])->asObject()->first();

        $subPeriod = new SubPeriod();
        $subPeriodId = $subPeriod->insert([
            'name' => $period->month . '_' . $period->year . '_' . $this->request->getPost('period') . '_' . $this->request->getPost('date_start') . '_' . $this->request->getPost('date_end'),
            'company_id' => Auth::querys()->companies_id
        ]);

        for ($rowIndex = 2; $rowIndex <= $largestRowNumber; $rowIndex++) {

            $model = new Customer();
            $customerId = $model->select(['id'])
                ->where(['identification_number' => $sheet->getCell('B' . $rowIndex)->getValue(), 'companies_id' => Auth::querys()->companies_id])
                ->asObject()
                ->first();

            $model = new Invoice();
            $invoiceId = $model->insert([
                'payment_forms_id' => null,
                'payment_methods_id' => null,
                'type_documents_id' => 109,
                'idcurrency' => 35,
                'invoice_status_id' => 18,
                'customers_id' => $customerId->id,
                'companies_id' => Auth::querys()->companies_id,
                'user_id' => Auth::querys()->id,
                'resolution' => null,
                'resolution_id' => null,
                'payment_due_date' => date('Y-m-d'),
                'duration_measure' => 0,
                'line_extesion_amount' => 0,
                'tax_exclusive_amount' => 0,
                'tax_inclusive_amount' => 0,
                'allowance_total_amount' => 0,
                'charge_paid_amount' => 0,
                'payable_amount' => 0,
                'calculationrate' => 0,
                'issue_date' => date('Y-m-d'),
                'notes' => null,
                'send' => 'True'
            ]);


            $payroll = new PayrollDate();
            $payrollId = $payroll->insert([
                'invoice_id' => $invoiceId,
                'payroll_date' => date('Y-m-d')
            ]);

            $payroll = new Payroll();
            $payrollId = $payroll->insert([
                'invoice_id' => $invoiceId,
                'period_id' => $this->request->getPost('period_id'),
                'settlement_start_date' => $this->request->getPost('date_start'),
                'settlement_end_date' => $this->request->getPost('date_end'),
                'worked_time' => $sheet->getCell('H' . $rowIndex)->getValue(),
                'sub_period_id' => $subPeriodId
            ]);


            $this->accrued($payrollId, '1', $sheet->getCell('I' . $rowIndex)->getValue());
            $this->accrued($payrollId, '2', $sheet->getCell('J' . $rowIndex)->getValue());
            $this->accrued($payrollId, '23', $sheet->getCell('K' . $rowIndex)->getValue(), 'Auxilio de alimentación');
            $this->accrued($payrollId, '23', $sheet->getCell('L' . $rowIndex)->getValue(), 'Auxilio de estudio');
            $this->accrued($payrollId, '23', $sheet->getCell('M' . $rowIndex)->getValue(), 'Auxilio de movilidad');
            $this->accrued($payrollId, '23', $sheet->getCell('N' . $rowIndex)->getValue(), 'Auxilio de vestuario');
            $this->accrued($payrollId, '30', $sheet->getCell('O' . $rowIndex)->getValue());
            $this->accrued($payrollId, '30', $sheet->getCell('P' . $rowIndex)->getValue());
            $this->accrued($payrollId, '30', $sheet->getCell('Q' . $rowIndex)->getValue());
            $this->accrued($payrollId, '30', $sheet->getCell('R' . $rowIndex)->getValue());
            $this->accrued($payrollId, '30', $sheet->getCell('S' . $rowIndex)->getValue());
            $this->accrued($payrollId, '30', $sheet->getCell('T' . $rowIndex)->getValue());


            $this->accrued($payrollId, '31', $sheet->getCell('U' . $rowIndex)->getValue());
            $this->accrued($payrollId, '31', $sheet->getCell('V' . $rowIndex)->getValue());
            $this->accrued($payrollId, '31', $sheet->getCell('W' . $rowIndex)->getValue());
            $this->accrued($payrollId, '31', $sheet->getCell('X' . $rowIndex)->getValue());
            $this->accrued($payrollId, '31', $sheet->getCell('Y' . $rowIndex)->getValue());
            $this->accrued($payrollId, '31', $sheet->getCell('Z' . $rowIndex)->getValue());
            $this->accrued($payrollId, '31', $sheet->getCell('AA' . $rowIndex)->getValue());
            $this->accrued($payrollId, '31', $sheet->getCell('AB' . $rowIndex)->getValue());
            $this->accrued($payrollId, '31', $sheet->getCell('AC' . $rowIndex)->getValue());
            $this->accrued($payrollId, '31', $sheet->getCell('AD' . $rowIndex)->getValue());
            $this->accrued($payrollId, '12', $sheet->getCell('AS' . $rowIndex)->getValue(), $sheet->getCell('AT' . $rowIndex)->getValue());
            $this->accrued($payrollId, '26', $sheet->getCell('AU' . $rowIndex)->getValue());
            $this->accrued($payrollId, '34', $sheet->getCell('BM' . $rowIndex)->getValue());
            $this->accrued($payrollId, '14', $sheet->getCell('BN' . $rowIndex)->getValue(), null, 0);

            $this->deduction($payrollId, '1', $sheet->getCell('AY' . $rowIndex)->getValue(), 3, '4.00');
            $this->deduction($payrollId, '2', $sheet->getCell('AZ' . $rowIndex)->getValue(), 5, '4.00');
            $fsp = $sheet->getCell('BA' . $rowIndex)->getValue();
            if (!is_null($fsp) && $fsp != '0') {
                $fsp = $fsp / 2;
                $this->deduction($payrollId, '3', $fsp, 9, '1.00');
                $this->deduction($payrollId, '4', $fsp, 9, '1.00');
            }


            $this->deduction($payrollId, '8', $sheet->getCell('BC' . $rowIndex)->getValue());
            $this->deduction($payrollId, '13', $sheet->getCell('BE' . $rowIndex)->getValue());
            $this->deduction($payrollId, '12', $sheet->getCell('BF' . $rowIndex)->getValue());
            $this->deduction($payrollId, '11', $sheet->getCell('BG' . $rowIndex)->getValue());


            $this->deduction($payrollId, '11', $sheet->getCell('BG' . $rowIndex)->getValue());
        }
    }


    public function accrued($payrollId, $type, $payment, $description = null, $quantity = null)
    {
        if ($payment != 0 && !is_null($payment)) {
            $model = new Accrued();
            $model->insert([
                'payroll_id' => $payrollId,
                'type_accrued_id' => $type,
                'payment' => $payment,
                'description' => $description,
                'quantity' => $quantity
            ]);
        }
    }

    public function deduction($payrollId, $type, $payment, $typeLaw = null, $percentage = null, $description = null)
    {

        if ($payment != 0 && !is_null($payment)) {
            $model = new Deduction();
            $model->insert([
                'payroll_id' => $payrollId,
                'payment' => $payment,
                'type_deduction_id' => $type,
                'type_law_deduction_id' => $typeLaw,
                'percentage' => $percentage
            ]);
        }
    }

    public function importSalud()
    {
        $model = new Resolution();
        $resolutions = $model->select(['resolution', 'from', 'prefix', 'from', 'to'])
            ->where([
                'companies_id' => Auth::querys()->companies_id,
                'type_documents_id' => 1
            ])
            ->asObject()
            ->get()
            ->getResult();
        return view('pages/import_head', ['resolutions' => $resolutions]);
    }


    public function uploadSalud()
    {
        if (!empty($_FILES['file']['name'])) {
            $pathinfo = pathinfo($_FILES["file"]["name"]);
            if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls')
                && $_FILES['file']['size'] > 0) {
                $inputFileName = $_FILES['file']['tmp_name'];
                // prueba
                $reader = ReaderEntityFactory::createReaderFromFile('/path/to/file.xlsx');
                $reader->setShouldFormatDates(true);
                $reader->open($inputFileName);
                $count = 1;

                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        if ($count > 1) {

                            $cells = $row->getCells();
                            $mensaje = 'Facturas Subidas con exito';
                            $notes = '
                         <table>
                             <tbody>
                                 <tr>
                                     <td>Fecha: ' . $cells[11] . '</td>
                                     <td colspan="2" style="padding-left:10px;">Tipo de usuario:' . $cells[13] . '</td>
                                 </tr>
                                 <tr>
                                     <td>Usuario: ' . $cells[1] . ' ' . $cells[2] . ' ' . $cells[3] . ' ' . $cells[4] . ' ' . $cells[5] . ' ' . $cells[6] . '</td>
                                     <td style="padding-left:10px;">Sexo: ' . $cells[8] . '</td>
                                     <td>Edad: ' . $cells[12] . '</td>
                                 </tr>
                                 <tr>
                                     <td>Zona de Recidencia: ' . $cells[10] . '</td>
                                     <td colspan="2" style="padding-left:10px;"> Municipio: ' . $cells[9] . '</td>
                                 </tr>
                             </tbody>
                        </table>';
                            $data = [
                                'resolution' => consecutive(1, $this->request->getPost('resolution')),
                                'resolution_id' => $this->request->getPost('resolution'),
                                'payment_forms_id' => '1',
                                'payment_methods_id' => '10',
                                'payment_due_date' => date('Y-m-d'),
                                'duration_measure' => 0,
                                'type_documents_id' => 1,
                                'line_extesion_amount' => 4500.00,
                                'tax_exclusive_amount' => 4500.00,
                                'tax_inclusive_amount' => 4500.00,
                                'allowance_total_amount' => '0.00',
                                'charge_total_amount' => '0.00',
                                'payable_amount' => 4500.00,
                                'customers_id' => 959, //canmbiar al subir 959 997
                                'created_at' => date('Y-m-d H:i:s'),
                                'invoice_status_id' => 1,
                                'notes' => $notes,
                                'companies_id' => Auth::querys()->companies_id,//cambiar
                                'idcurrency' => 35,
                                'calculationrate' => 1,
                                'calculationratedate' => date('Y-m-d'),
                                'status_wallet' => 'Pendiente',
                                'user_id' => '1',
                                'seller_id' => 65,
                                'send' => 'False'
                            ];
                            $invoice = new Invoice();
                            $invoiceId = $invoice->insert($data);
                            $lineInvoice = new LineInvoice();
                            $line = [
                                'invoices_id' => $invoiceId,
                                'discount_amount' => 0,
                                'discounts_id' => 1,
                                'quantity' => (double)1,
                                'line_extension_amount' => 4500.00,
                                'price_amount' => 4500.00,
                                'products_id' => 2462, //cambiar a subir
                                'description' => 'Citología cervicouterina',
                                'provider_id' => null
                            ];
                            $lineInvoiceId = $lineInvoice->insert($line);
                            $lineInvoice = new LineInvoiceTax();
                            $lineInvoice->save([
                                'taxes_id' => 1,
                                'tax_amount' => 0.00,
                                'percent' => 0,
                                'taxable_amount' => 4500,
                                'line_invoices_id' => $lineInvoiceId
                            ]);
                            $lineInvoice = new LineInvoiceTax();
                            $lineInvoice->save([
                                'taxes_id' => 5,
                                'tax_amount' => 0.00,
                                'percent' => 0,
                                'taxable_amount' => 4500,
                                'line_invoices_id' => $lineInvoiceId
                            ]);
                            $lineInvoice = new LineInvoiceTax();
                            $lineInvoice->save([
                                'taxes_id' => 6,
                                'tax_amount' => 0.00,
                                'percent' => 0,
                                'taxable_amount' => 4500,
                                'line_invoices_id' => $lineInvoiceId
                            ]);
                            $lineInvoice = new LineInvoiceTax();
                            $lineInvoice->save([
                                'taxes_id' => 7,
                                'tax_amount' => 0.00,
                                'percent' => 0,
                                'taxable_amount' => 4500,
                                'line_invoices_id' => $lineInvoiceId
                            ]);
                        }
                        $count++;
                    }
                }

                return redirect()->to(base_url('import_health'))->with('success', 'Facturas cargadas con exito.');
            }
        }
    }

    public function plantillaProductos()
    {

        // Columnas A8 hasta T8
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF4caf50',
                ],

            ]
        ];
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');

        //Encabezados
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A1', 'Proveedor')->getStyle('A1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('B1', 'Genero')->getStyle('B1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('C1', 'Grupo')->getStyle('C1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('D1', 'Precio')->getStyle('D1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('E1', 'Sub Grupo')->getStyle('E1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('F1', 'Material')->getStyle('F1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('G1', 'Producto')->getStyle('G1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('H1', 'Valor')->getStyle('H1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('I1', 'Costo')->getStyle('I1')->getFont()->setBold(true);
        // $spreadsheet->setActiveSheetIndex(0)->setCellValue('J1', 'Unidad de Medida')->getStyle('J1')->getFont()->setBold(true);
        // $spreadsheet->setActiveSheetIndex(0)->setCellValue('K1', 'Tipo de Documento')->getStyle('K1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('J1', 'Producto Gratis')->getStyle('L1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('K1', 'Entrada')->getStyle('M1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('L1', 'Devolucion')->getStyle('N1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('M1', 'Iva')->getStyle('O1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('N1', 'Retencion de fuente')->getStyle('P1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('O1', 'ReteIca')->getStyle('Q1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('P1', 'ReteIva')->getStyle('R1')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('Q1', 'Cuenta por cobrar')->getStyle('S1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:S1')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(15);

        $columnsCells = [
            (object)['cell' => 'A', 'option' => 'providers'],
            (object)['cell' => 'B', 'option' => 'gender'],
            (object)['cell' => 'C', 'option' => 'groups'],
            (object)['cell' => 'D', 'option' => 'prices'],
            (object)['cell' => 'E', 'option' => 'subGroup'],
            (object)['cell' => 'F', 'option' => 'material'],
            //(object)['cell' => 'J','option' => 'unitMeasure'],
            // (object)['cell' => 'K','option' => 'typeItemIdentifications'],
            (object)['cell' => 'J', 'option' => 'productFree'],
            (object)['cell' => 'K', 'option' => 'entry_credit'],
            (object)['cell' => 'L', 'option' => 'entry_debit'],
            (object)['cell' => 'M', 'option' => 'iva'],
            (object)['cell' => 'N', 'option' => 'reteFuente'],
            (object)['cell' => 'O', 'option' => 'reteIca'],
            (object)['cell' => 'P', 'option' => 'reteIva'],
            (object)['cell' => 'Q', 'option' => 'account_pay']
        ];
        for ($row = 2; $row < 500; $row++) {
            // Establecer el valor "ejemplo" en la celda correspondiente de la columna A
            foreach ($columnsCells as $columns) {
                $spreadsheet->getActiveSheet()->getColumnDimension("{$columns->cell}")->setWidth(20);
                $config = $spreadsheet->getActiveSheet()->getCell("{$columns->cell}" . $row);
                $validation = $config->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $validation->setFormula1('"' . implode(',', $this->optionsCell($columns->option)) . '"');
                $validation->setShowDropDown(true);
            }
        }

        // $spreadsheet->getActiveSheet()->getStyle('A6')->getFont()->getColor()->setARGB('FF2874A6');
        // $spreadsheet->getActiveSheet()->getStyle('B6')->getFont()->getColor()->setARGB('FF2874A6');
        // $spreadsheet->setActiveSheetIndex(0)->getStyle('B6')->getFont()->setBold(true);


        //quitar cuadricula
        //$spreadsheet->getActiveSheet()->setShowGridlines(false);


        //Totales
        //$spreadsheet->getActiveSheet()->getStyle('A' . ($i) . ':V' . ($i))->applyFromArray($styleArray);


        $spreadsheet->getActiveSheet()->setTitle('Plantilla Cargue de productos');
        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Plantilla_Cargue_de_productos.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function optionsCell($cell): array
    {
        $tableProviders = new Providers();
        $tableGender = new Gender();
        $tableGroups = new Groups();
        $tablePrices = new Prices();
        $tableSubGroups = new SubGroup();
        $tableMaterials = new Materials();
        $tableTypeItemIdentifications = new TypeItemIdentification();
        $tableAccountingAccount = new AccountingAcount();
        $tableUnit = new UnitMeasure();
        switch ($cell) {
            case 'providers':
                $data = $tableProviders->asObject()->get()->getResult();
                break;
            case 'gender':
                $data = $tableGender->asObject()->get()->getResult();
                break;
            case 'groups':
                $data = $tableGroups->asObject()->get()->getResult();
                break;
            case 'prices':
                $data = $tablePrices->asObject()->get()->getResult();
                break;
            case 'subGroup':
                $data = $tableSubGroups->asObject()->get()->getResult();
                break;
            case 'material':
                $data = $tableMaterials->asObject()->get()->getResult();
                break;
            case 'unitMeasure':
                $data = $tableUnit->whereIn('id', [70, 1056, 1076])->asObject()->get()->getResult();
                break;
            case 'typeItemIdentifications':
                $data = $tableTypeItemIdentifications->asObject()->get()->getResult();
                break;
            case 'productFree':
                $data = [
                    (object)['name' => 'Si'],
                    (object)['name' => 'No']
                ];
                break;
            case 'entry_credit':
                $data = $tableAccountingAccount->where(['nature' => 'Crédito', 'type_accounting_account_id' => 1])->asObject()->get()->getResult();
                break;
            case 'entry_debit':
                $data = $tableAccountingAccount->where(['nature' => 'Débito', 'type_accounting_account_id' => 1])->asObject()->get()->getResult();
                break;
            case 'iva':
                $data = $tableAccountingAccount->where(['type_accounting_account_id' => 2])->asObject()->get()->getResult();
                break;
            case 'reteIca':
            case 'reteIva':
            case 'reteFuente':
                $data = $tableAccountingAccount->where(['type_accounting_account_id' => 3])->asObject()->get()->getResult();
                break;
            case 'account_pay':
                $data = $tableAccountingAccount->where(['type_accounting_account_id' => 4])->asObject()->get()->getResult();
                break;
        }
        $list = [];
        foreach ($data as $item) {
            switch ($cell) {
                case 'providers':
                    array_push($list, "{$item->code}-{$item->name_providers}");
                    break;
                case 'gender':
                    array_push($list, "{$item->code}-{$item->gender}");
                    break;
                case 'subGroup':
                case 'material':
                case 'groups':
                case 'unitMeasure':
                    array_push($list, "{$item->code}-{$item->name}");
                    break;
                case 'prices':
                    array_push($list, "{$item->code}-{$item->price}");
                    break;
                case 'typeItemIdentifications':
                case 'entry_credit':
                case 'entry_debit':
                case 'iva':
                case 'reteFuente':
                case 'reteIca':
                case 'reteIva':
                case 'account_pay':
                    array_push($list, "{$item->id}-{$item->name}");
                    break;
                case 'productFree':
                    array_push($list, "{$item->name}");
                    break;
            }
        }
        return $list;
    }


}