<?php

use CodeIgniter\I18n\Time;

function different($data)
{
    $myTime = new Time('now', 'America/Bogota', 'es_ES');
    $time = Time::parse($data, 'America/Bogota', 'es_ES');
    $diff =  $time->difference($myTime, 'America/Bogota');
    return $diff->humanize();
}


function formatDate($date)
{
    $year = date('Y', strtotime($date));
    $month = date('m', strtotime($date));
    $day = date('d', strtotime($date));
    return $day.'/'.$month.'/'.$year;
}



function differenceDays($date1, $date2, $invert = false) {
    $dateFirst  = new \DateTime($date1);
    $dateSecond = new \DateTime($date2);
    $diff = $dateFirst->diff($dateSecond);
    if($invert) {
      return  $days =  $diff->invert ?  '-'.$diff->days : $diff->days;
    }
    return $diff->days;
}

function date_fecha($date){
    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $dias = ["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"];
    $date = strtotime($date);
    $date = $dias[date('w', $date)].' '.date('d', $date).' de '.$meses[(date("m", $date)-1)].' del '.date("Y", $date);
    return $date;
}





function mes($position) {
    $mes = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre',' diciembre'];
    return $mes[$position - 1];
}