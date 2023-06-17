<?php
/**
 * @author John Vergara <john@mawii.com.co>
 * @version 1.0
 * Helper que permite mostrar y guardar errores retornados tanto de la DIAN como del api
 * también cuenta con la función de quitar tildes de string
 */
use App\Models\Errors;

/**
 * Function que permite retornar los errores ya con un desglose realizado y una solución
 * @param null $data
 * @param null $type
 * @return string
 */
function showErrors($data = null, $type = null): string
{
    $errors = new Errors();
    $dataErrors = '';
    switch ($type) {
        case 'code422':
            foreach ($data as $error) {
                //echo json_encode($error);die();
                if(isset($error->code)){
                    $code = $error->code;
                }else{
                    $code = $error['code'];
                }

                $textError = $errors->where(['code' => $code])->asObject()->first();
                if (!is_null($textError)) {
                    $dataErrors .= '<li> Inconveniente: ' . $textError->breakdown . ' || Solución: ' . $textError->solution . '. </li>';
                }
            }
            break;
        case 'code200':

            if(is_array($data)) {
                foreach ($data as $error) {
                    $errorDivider = explode(',',$error);
                    $code = removeAccents($error);
                    if(removeAccents($errorDivider[0]) != 'Regla: SinCodigo'){
                        $code = removeAccents($errorDivider[0]);
                    }
                    $textError = $errors->where(['code' => $code])->asObject()->first();
                    if (!is_null($textError)) {
                        $dataErrors .= '<li> Inconveniente: ' . $textError->breakdown . ' || Solución: ' . $textError->solution . '. </li>';
                    }
                }
            }else {
                $errorDivider = explode(',',$data);
                $code = removeAccents($data);
                if(removeAccents($errorDivider[0]) != 'Regla: SinCodigo'){
                    $code = removeAccents($errorDivider[0]);
                }
                $textError = $errors->where(['code' => $code])->asObject()->first();
                if (!is_null($textError)) {
                    $dataErrors .= '<li> Inconveniente: ' . $textError->breakdown . ' || Solución: ' . $textError->solution . '. </li>';
                }
            }

            break;
        default;
            $dataErrors = '';
            break;
    }

    return $dataErrors;
}

/**
 * Function que permite guardar nuevos errores retornados tanto de la DIAN como del api
 * @param null $data
 * @param null $type
 * @throws ReflectionException
 */
function addErrors($data = null, $type = null){
    $errors = new Errors();
    switch ($type) {
        case 'code422':
            foreach ($data as $error) {
                $save = [
                    'code' => $error['code'],
                    'breakdown' =>  $error['error'],
                    'solution' => 'Por favor comunicarse con soporte'
                ];
                $existsCodeError = $errors->where(['code' => $save['code']])->asObject()->first();
                if(is_null($existsCodeError)){
                    $errors->save($save);
                }
            }
            break;
        case 'code200':
           // echo json_encode($data);die();
            if(is_array($data)) {
                foreach ($data as $error) {
                    $save = [
                        'code' => removeAccents($error),
                        'breakdown' =>  $error,
                        'solution' => 'Por favor comunicarse con soporte'
                    ];
                    $errorDivider = explode(',',$error);
                    if(isset($errorDivider[0]) && removeAccents($errorDivider[0]) != 'Regla: SinCodigo'){
                        $save['code'] = removeAccents($errorDivider[0]);
                    }
                    $existsCodeError = $errors->where(['code' => $save['code']])->asObject()->first();
                    if(is_null($existsCodeError)){
                        $errors->save($save);
                    }
                }
            }else {
                $save = [
                    'code'      => removeAccents($data),
                    'breakdown' =>  $data,
                    'solution'  => 'Por favor comunicarse con soporte'
                ];
                $errorDivider = explode(',',$data);
                if(isset($errorDivider[0]) && removeAccents($errorDivider[0]) != 'Regla: SinCodigo'){
                    $save['code'] = removeAccents($errorDivider[0]);
                }
                $existsCodeError = $errors->where(['code' => $save['code']])->asObject()->first();
                if(is_null($existsCodeError)){
                    $errors->save($save);
                }
            }
            break;

    }
}

/**
 * Function que permite retornar valores string sin tildes
 * @param string $text
 * @return array|string|string[]
 */
function removeAccents(string $text){
    $text = str_replace(
        array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
        array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
        $text
    );
    $text = str_replace(
        array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
        array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
        $text);
    $text = str_replace(
        array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
        array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
        $text);
    $text = str_replace(
        array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
        array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
        $text);

    $text = str_replace(
        array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
        array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
        $text);
    $text = str_replace(
        array('Ñ', 'ñ', 'Ç', 'ç'),
        array('N', 'n', 'C', 'c'),
        $text
    );

    return $text;
}
