<!DOCTYPE html>
<?php
    function validExistId($accrueds ,$id , $otherPayment =  false) {
         foreach($accrueds as $accrued){
            if($accrued->type_accrued_id == $id) {
                if($otherPayment) {
                    return number_format($accrued->other_payments, '0', '.', '.');
                }
                return number_format($accrued->payment, '0', '.', '.');
            }
        }
         return 0;
    }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>

<table>
    <tbody>
        <tr >
            <td colspan="7">
                <img class="logo " src="<?= base_url('/images/logo_dian.jpg') ?>" alt="" width="123px">
            </td>
            <td colspan="37" class="text-center title">
                Certificado de Ingresos y Retenciones por Rentas de Trabajo y de Pensiones Año gravable 2021
            </td>
            <td colspan="7" class="code">220</td>
        </tr>
        <tr>
            <td colspan="27" rowspan="2" class="arial-8 h-10 text-center ">Antes de diligenciar este formulario lea cuidadosamente las instrucciones</td>
            <td colspan="24" class="arial-10 h-10  border-bottom">
                4. Número de formulario

               <!-- <p  style="background: red; width: 100px;"> </p>-->
            </td>
        </tr>
        <tr>
            <td colspan="24" class="arial-10 h-10  border-top text-center">
                <?= $invoice->resolution ?>
            </td>
        </tr>
        <tr >
            <td rowspan="4" class="arial-8 ">
                <p class="retetion">Retenedor</p>
            </td>
            <td colspan="13" class="arial-8 h-10 border-right border-bottom">5. Numero de Identificacion Tributaria (NIT)</td>
            <td class="arial-8 border-bottom ">6. DV.</td>
            <td colspan="8" class="arial-8 border-right border-bottom">7. Primer Apellido</td>
            <td colspan="14" class="arial-8 border-right border-bottom">8. Segundo Apellido</td>
            <td colspan="9" class="arial-8 border-right border-bottom">9. Primer Nombre</td>
            <td colspan="5" class="arial-8 border-bottom">10. Otros Nombres</td>
        </tr>
        <tr>
            <td colspan="13" class="arial-8 h-10 border-top text-center"><?= strtoupper($invoice->nit) ?></td>
            <td class="arial-8 border-top text-center"><?= strtoupper($invoice->dv) ?></td>
            <td colspan="8" class="arial-8 border-top"></td>
            <td colspan="14" class="arial-8 border-top"></td>
            <td colspan="9" class="arial-8 border-top"></td>
            <td colspan="5" class="arial-8 border-top"></td>
        </tr>
        <tr>
            <td colspan="50" class="h-10 arial-8 border-bottom vertical-align">11.  Razón Social</td>
        </tr>
        <tr>
            <td colspan="50" class="h-10 arial-8  border-top"><?= strtoupper($invoice->company) ?></td> 
        </tr>
        <tr>
            <td rowspan="2" width="20px" class="arial-8 font-bold " style="position:relative;">
                <p class="job">Empleado</p>
            </td>
            <td colspan="5" class="arial-8 h-10 border-bottom">24. Tipo de documento</td>
            <td colspan="11" class="arial-8 border-bottom vertical-align">25. Número de Identificación</td>
            <td colspan="7" class="arial-8 text-center border-bottom border-right"><?= strtoupper($invoice->surname) ?></td>
            <td colspan="12" class="arial-8 text-center border-bottom border-right"><?= strtoupper($invoice->second_surname)  ?></td>
            <td colspan="9" class="arial-8 text-center border-bottom border-right"><?= strtoupper($invoice->name) ?></td>
            <td colspan="6" class="arial-8 text-center border-bottom"><?= strtoupper($invoice->second_name)  ?></td>
        </tr>
        <tr>
            <td colspan="5" class="arial-8 h-10 text-center border-top" ><?= $invoice->identification_code ?></td>
            <td colspan="11" class="arial-8 text-center border-top"><?= $invoice->identification_number ?></td>
            <td colspan="7" class="arial-8 text-center border-top">26. Primer apellido</td>
            <td colspan="12" class="arial-8 text-center border-top">27. Segundo apellido</td>
            <td colspan="9" class="arial-8 text-center border-top">28. Primer nombre</td>
            <td colspan="6" class="arial-8 text-center border-top"> 29. Otros nombres</td>
        </tr>
        <tr>
            <td colspan="15" class="arial-8 text-center h-10  border-bottom vertical-align" >Período de la Certificación</td>
            <td colspan="7" class="arial-8  text-center border-bottom vertical-align"  style="letter-spacing: -0.5px;">32. Fecha de expedición</td>
            <td colspan="23" class="arial-8 vertical-align-top  border-bottom vertical-align">33. Lugar donde se practicó la retención</td>
            <td colspan="2" class="arial-8  border-bottom"> 34. Cód. Dpto.</td>
            <td colspan="4" class="arial-8  border-bottom"> 35. Cód. Ciudad/Municipio</td>
        </tr>
        <tr>
            <td colspan="2" class="arial-8 h-10 border-top  border-right font-bold" style="letter-spacing: -0.5px;">30. DE:</td>
            <td colspan="2" class="arial-8 text-center border-right  border-top"><?= date("Y", strtotime($invoice->settlement_start_date)) ?></td>
            <td colspan="2" class="arial-8 text-center border-right  border-top"><?= date("m", strtotime($invoice->settlement_start_date)) ?></td>
            <td colspan="2" class="arial-8 text-center border-right  border-top"><?= date("d", strtotime($invoice->settlement_start_date)) ?></td>
            <td colspan="2" class="arial-8 h-10 border-top  border-right  font-bold"  style="letter-spacing: -0.5px;">31. A:</td>
            <td colspan="2" class="arial-8 text-center border-right  border-top"><?= date("Y", strtotime($invoice->settlement_end_date)) ?></td>
            <td colspan="2" class="arial-8 text-center border-right  border-top"><?= date("m", strtotime($invoice->settlement_end_date)) ?></td>
            <td colspan="1" class="arial-8 text-center   border-top"><?= date("d", strtotime($invoice->settlement_end_date)) ?></td>
            <td colspan="3" class="arial-8 text-right border-right  border-top "><?= date("Y", strtotime($invoice->issue_date)) ?></td>
            <td colspan="2" class="arial-8  text-right border-right  border-top"><?= date("m", strtotime($invoice->issue_date)) ?></td>
            <td colspan="2" class="arial-8 text-right  border-top"><?= date("d", strtotime($invoice->issue_date)) ?></td>

            <td colspan="23" class="arial-8 text-center border-top"><?= $invoice->municipality_name ?></td>
            <td colspan="2" class="arial-8 text-right border-top"><?= $invoice->department_code ?></td>
            <td colspan="4" class="arial-8 text-right border-top"><?= $invoice->municipality_code ?></td>
        </tr>
        <tr>
            <td colspan="41" class="text-center font-bold arial-8 h-10 bg-blue">Concepto de los Ingresos</td>
            <td colspan="10" class="text-center font-bold arial-8 bg-blue  border-bottom">Valor</td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10  border-top-bottom-none">Pagos por salarios o emolumentos eclesiásticos</td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none">36</td>
            <td colspan="9" class="text-center  arial-8   border-top-bottom-none text-right"><?= validExistId($accrueds, 1) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10 bg-blue-sure  border-top-bottom-none">Pagos realizados con bonos electrónicos o de papel de servicio, cheques, tarjetas, vales, etc.</td>
            <td colspan="1" class="text-center arial-8 h-10 bg-blue-sure border-top-bottom-none">37</td>
            <td colspan="9" class="text-center  arial-8   bg-blue-sure border-top-bottom-none text-right"><?= validExistId($accrueds, 30) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10  border-top-bottom-none">Pagos por honorarios</td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none">38</td>
            <td colspan="9" class="text-center  arial-8  border-top-bottom-none text-right"><?= validExistId($accrueds, 43) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10 bg-blue-sure  border-top-bottom-none">Pagos por servicios</td>	
            <td colspan="1" class="text-center arial-8 h-10 bg-blue-sure border-top-bottom-none">39</td>
            <td colspan="9" class="text-center  arial-8    bg-blue-sure border-top-bottom-none text-right"><?= validExistId($accrueds, 44) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10  border-top-bottom-none">Pagos por comisiones</td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none">40</td>
            <td colspan="9" class="text-center  arial-8    border-top-bottom-none text-right"><?= validExistId($accrueds, 34) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10 bg-blue-sure  border-top-bottom-none">Pagos por prestaciones sociales</td>	
            <td colspan="1" class="text-center arial-8 h-10 bg-blue-sure border-top-bottom-none">41</td>
            <td colspan="9" class="text-center  arial-8    bg-blue-sure border-top-bottom-none text-right"><?= validExistId($accrueds, 45) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10  border-top-bottom-none">Pagos por viáticos</td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none">42</td>
            <td colspan="9" class="text-center  arial-8    border-top-bottom-none text-right"><?= validExistId($accrueds, 10) ?></td>
        <tr>
            <td colspan="41" class="arial-8 h-10 bg-blue-sure  border-top-bottom-none">Pagos por gastos de representación</td>	
            <td colspan="1" class="text-center  arial-8 h-10 bg-blue-sure border-top-bottom-none">43</td>
            <td colspan="9" class="text-center  arial-8   bg-blue-sure   border-top-bottom-none text-right"><?= validExistId($accrueds, 46) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10  border-top-bottom-none">Pagos por compensaciones por el trabajo asociado cooperativo</td>
            <td colspan="1" class="text-center arial-8 h-10  border-top-bottom-none">44</td>
            <td colspan="9" class="text-center  arial-8      border-top-bottom-none text-right"><?= validExistId($accrueds, 47) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10 bg-blue-sure  border-top-bottom-none">Otros pagos</td>	
            <td colspan="1" class="text-center arial-8 h-10 bg-blue-sure border-top-bottom-none">45</td>
            <td colspan="9" class="text-center  arial-8    bg-blue-sure  border-top-bottom-none text-right"><?= validExistId($accrueds, 26) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10  border-top-bottom-none">Cesantías e intereses de cesantías efectivamente pagadas en el periodo</td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none">46</td>
            <td colspan="9" class="text-center  arial-8     border-top-bottom-none text-right"><?= validExistId($accrueds, 16) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10 bg-blue-sure  border-top-bottom-none">Cesantías consignadas al fondo de cesantías</td>	
            <td colspan="1" class="text-center arial-8 h-10 bg-blue-sure border-top-bottom-none">47</td>
            <td colspan="9" class="text-center  arial-8  bg-blue-sure   border-top-bottom-none text-right"><?= validExistId($accrueds, 16, true) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10  border-top-bottom-none">Pensiones de jubilación, vejez o invalidez</td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none">48</td>
            <td colspan="9" class="text-center  arial-8    border-top-bottom-none text-right"><?= validExistId($accrueds, 48) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10 fond-bold bg-blue-sure  border-top">Total de ingresos brutos (Sume 36 a 48)</td>	
            <td colspan="1" class="arial-8 h-10 bg-blue-sure border-top text-center ">49</td>
            <td colspan="9" class="text-center  arial-8  bg-blue-sure border-top text-right"><?= validExistId($accrueds, 55) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="text-center font-bold arial-8 h-10 ">Concepto de los aportes</td>
            <td colspan="10" class="text-center font-bold arial-8">Valor</td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10  border-bottom">Aportes obligatorios por salud a cargo del trabajador</td>
            <td colspan="1" class="text-center  arial-8 h-10  border-bottom">50</td>
            <td colspan="9" class="text-center  arial-8  border-bottom text-right"><?= validExistId($accrueds, 49) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10 bg-blue-sure  border-top-bottom-none">Aportes obligatorios a fondos de pensiones y solidaridad pensional a cargo del trabajador</td>
            <td colspan="1" class="text-center arial-8 h-10 bg-blue-sure border-top-bottom-none">51</td>
            <td colspan="9" class="text-center  arial-8  bg-blue-sure border-top-bottom-none text-right"><?= validExistId($accrueds, 50) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10  border-top-bottom-none">Cotizaciones voluntarias al régimen de ahorro individual con solidaridad - RAIS</td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none">52</td>
            <td colspan="9" class="text-center  arial-8  border-top-bottom-none text-right"><?= validExistId($accrueds, 51) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10 bg-blue-sure  border-top-bottom-none">Aportes voluntarios a fondos de pensiones</td>	
            <td colspan="1" class="text-center arial-8 h-10 bg-blue-sure border-top-bottom-none">53</td>
            <td colspan="9" class="text-center  arial-8   bg-blue-sure border-top-bottom-none text-right"><?= validExistId($accrueds, 52) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10  border-top-bottom-none">Aportes a cuentas AFC</td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top">54</td>
            <td colspan="9" class="text-center  arial-8  border-top-bottom-none text-right"><?= validExistId($accrueds, 53) ?></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10 bg-blue-black    font-bold">Valor de la retención en la fuente por rentas de trabajo y pensiones</td>
            <td colspan="1" class="text-center arial-8 h-10 bg-blue-sure font-bold ">55</td>
            <td colspan="9" class="text-center  arial-8  bg-blue-sure  text-right"><?= validExistId($accrueds, 54) ?></td>
        </tr>
        <tr>
            <td colspan="51" class="arial-8 h-10">Nombre del pagador o agente retenedor</td>	
        </tr>
        <tr>
            <td colspan="51" class="arial-8 h-10 text-center fond-bold">Datos a cargo del trabajador o pensionado</td>	
        </tr>
        <tr>
            <td colspan="25" class="arial-8 h-10 bg-blue-sure text-center font-bold text-center">Concepto de otros ingresos</td>
            <td colspan="16" class="text-center bg-blue-sure  arial-8 h-10   font-bold">Valor recibido</td>
            <td colspan="10" class="text-center bg-blue-sure  arial-8  font-bold ">Valor retenido</td>
        </tr>
        <tr>
            <td colspan="25" class="arial-8 h-10  border-bottom">Arrendamientos</td>
            <td colspan="1" class="text-center  arial-8 h-10  border-bottom">56</td>
            <td colspan="15" class="text-center  arial-8 h-10  border-bottom"></td>
            <td colspan="1" class="text-center  arial-8 h-10  border-bottom">63</td>
            <td colspan="9" class="text-center  arial-8   border-bottom"></td>
        </tr>
        <tr>
            <td colspan="25" class="arial-8 h-10 bg-blue-sure  border-top-bottom-none">Honorarios, comisiones y servicios</td>	
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none bg-blue-sure">58</td>
            <td colspan="15" class="text-center arial-8 h-10 bg-blue-sure border-top-bottom-none"></td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none bg-blue-sure">64</td>
            <td colspan="9" class="text-center arial-8 bg-blue-sure  border-top-bottom-none"></td>
        </tr>
        <tr>
            <td colspan="25" class="arial-8 h-10  border-top-bottom-none">Intereses y rendimientos financieros</td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none">58</td>
            <td colspan="15" class="text-center  arial-8 h-10  border-top-bottom-none"></td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none">65</td>
            <td colspan="9" class="text-center  arial-8   border-top-bottom-none"></td>
        </tr>
        <tr>
            <td colspan="25" class="arial-8 h-10 bg-blue-sure  border-top-bottom-none">Enajenación de activos fijos</td>	
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none bg-blue-sure">59</td>
            <td colspan="15" class="text-center arial-8 h-10 bg-blue-sure border-top-bottom-none"></td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none bg-blue-sure">66</td>
            <td colspan="9" class="text-center arial-8 bg-blue-sure  border-top-bottom-none"></td>
        </tr>
        <tr>
            <td colspan="25" class="arial-8 h-10  border-top-bottom-none">Loterías, rifas, apuestas y similares</td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none">60</td>
            <td colspan="15" class="text-center  arial-8 h-10  border-top-bottom-none"></td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none">67</td>
            <td colspan="9" class="text-center  arial-8   border-top-bottom-none"></td>
        </tr>
        <tr>
            <td colspan="25" class="arial-8 h-10 bg-blue-sure  border-top-bottom-none">Otros</td>	
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none bg-blue-sure">61</td>
            <td colspan="15" class="text-center arial-8 h-10 bg-blue-sure border-top-bottom-none"></td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none bg-blue-sure">68</td>
            <td colspan="9" class="text-center arial-8 bg-blue-sure  border-top-bottom-none"></td>
        </tr>
        <tr>
            <td colspan="25" class="arial-8 h-10   border-top-bottom-none"><span class="font-bold"> Totales: (Valor recibido:</span> Sume 56 a 61), (<span class="font-bold"> Valor retenido:</span> Sume 63 a 68) </td>	
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none font-bold">62</td>
            <td colspan="15" class="text-right arial-8 h-10 border-top-bottom-none font-bold">0</td>
            <td colspan="1" class="text-center  arial-8 h-10  border-top-bottom-none font-bold">69</td>
            <td colspan="9" class="text-right arial-8   border-top-bottom-none font-bold">0</td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10 bg-blue-sure  border-top"><span class="font-bold">Total retenciones año gravable 2021</span> (Sume 55 + 69)</td>	
            <td colspan="1" class="text-center  arial-8 h-10  border-top font-bold bg-blue-sure">70</td>
            <td colspan="9" class="text-right arial-8 bg-blue-sure font-bold border-top"><?= validExistId($accrueds, 54) ?></td>
        </tr>
        <tr>
            <td colspan="2" class="text-center font-bold arial-8 h-10 bg-blue border-bottom">item</td>
            <td colspan="40" class="text-center font-bold arial-8 h-10 bg-blue border-bottom">71. Identificación de los bienes y derechos poseídos</td>
            <td colspan="9" class="text-center font-bold arial-8 bg-blue  border-bottom">72. Valor patrimonial</td>
        </tr>
        <tr>
            <td colspan="2" class="arial-8 h-10   border-top-bottom-none font-bold text-center">1</td>	
            <td colspan="40" class="text-center  arial-8 h-10  border-top-bottom-none font-bold"></td>
            <td colspan="9" class="text-center arial-8 h-10  border-top-bottom-none font-bold"></td>
        </tr>
        <tr>
            <td colspan="2" class="arial-8 h-10  bg-blue-sure  border-top-bottom-none font-bold text-center">2</td>	
            <td colspan="40" class="text-center  bg-blue-sure arial-8 h-10  border-top-bottom-none   font-bold"></td>
            <td colspan="9" class="text-center bg-blue-sure arial-8 h-10  border-top-bottom-none  font-bold"></td>
        </tr>
        <tr>
            <td colspan="2" class="arial-8 h-10   border-top-bottom-none font-bold text-center">3</td>	
            <td colspan="40" class="text-center  arial-8 h-10  border-top-bottom-none font-bold"></td>
            <td colspan="9" class="text-center arial-8 h-10  border-top-bottom-none font-bold"></td>
        </tr>
        <tr>
            <td colspan="2" class="arial-8 h-10  bg-blue-sure  border-top-bottom-none font-bold text-center">4</td>	
            <td colspan="40" class="text-center  bg-blue-sure arial-8 h-10  border-top-bottom-none   font-bold"></td>
            <td colspan="9" class="text-center bg-blue-sure arial-8 h-10  border-top-bottom-none  font-bold"></td>
        </tr>
        <tr>
            <td colspan="2" class="arial-8 h-10   border-top-bottom-none font-bold text-center">5</td>	
            <td colspan="40" class="text-center  arial-8 h-10  border-top-bottom-none font-bold"></td>
            <td colspan="9" class="text-center arial-8 h-10  border-top-bottom-none font-bold"></td>
        </tr>
        <tr>
            <td colspan="2" class="arial-8 h-10  bg-blue-sure  border-top font-bold text-center">6</td>	
            <td colspan="40" class="text-center  bg-blue-sure arial-8 h-10  border-top   font-bold"></td>
            <td colspan="9" class="text-center bg-blue-sure arial-8 h-10  border-top  font-bold"></td>
        </tr>
        <tr>
            <td colspan="41" class="arial-8 h-10 bg-blue-black  font-bold">Deudas vigentes a 31 de diciembre de 2021</td>	
            <td colspan="1" class="text-center arial-8 h-10  ">73</td>
            <td colspan="9" class="text-center arial-8"></td>
        </tr>
        <tr>
            <td colspan="51" class="text-center bg-blue font-bold arial-8 h-10">Identificación del dependiente económico de acuerdo al parágrafo 2 del artículo 387 del Estatuto Tributario</td>
        </tr>

        <tr>
            <td colspan="7" class="arial-8 h-20 vertical-align">74. Tipo documento</td>
            <td colspan="9" class="arial-8 h-20 vertical-align">75. No. Documento</td>
            <td colspan="28" class="arial-8 vertical-align">76. Apellidos y Nombres</td>
            <td colspan="7" class="arial-8 vertical-align">77 . Parentesco</td>
        </tr>
        <tr>
            <td colspan="38" class="arial-6 vertical-align">
            <br/>
                Deudas vigentes a 31 de diciembre de 2021<br/>
                <br/>
                Aportes a cuentas AFC<br/>
                1. Mi patrimonio bruto no excedió de 4.500 UVT ($163.386.000).<br/>
                2. Mis ingresos brutos fueron inferiores a 1.400 UVT ($50.831.000).<br/>
                3. No fui responsable del impuesto sobre las ventas.<br/>
                4. Mis consumos mediante tarjeta de crédito no excedieron la suma de 1.400 UVT ($50.831.000).<br/>
                5. Que el total de mis compras y consumos no superaron la suma de 1.400 UVT ($50.831.000).<br/>
                6. Que el valor total de mis consignaciones bancarias, depósitos o inversiones financieras no excedieron los 1.400 UVT ($50.831.000).<br/>
                <br/>
                Por lo tanto, manifiesto que no estoy obligado a presentar declaración de renta y complementario por el año gravable 2021.<br/>
                <br/>
            </td>
            <td colspan="13"  class="arial-8 vertical-align">
                Firma del Trabajador o Pensionado
            </td>
        </tr>

 
    </tbody>
</table>
<span class="arial-6"><strong>Nota:</strong> este certificado sustituye para todos los efectos legales la declaración de Renta y Complementario para el trabajador o pensionado que lo firme. <br/>
Para aquellos trabajadores independientes contribuyentes del impuesto unificado deberán presentar la declaración anual consolidada del Régimen Simple de Tributación (SIMPLE).</span>
</body>
</html>

