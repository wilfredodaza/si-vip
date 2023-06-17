<?php


/*
 * sale report
 * */

$chart = '{
    type:  "line",
    data: {
        labels:'.json_encode($salesOfMonth->data->labels).',
        datasets: [
            {
                    label: "En miles de pesos",
                data:'.json_encode($salesOfMonth->data->values).'
            }
        ]
    }, options:
     { 
     scales:{
        yAxes:[
            {
                ticks: {
                    callback:function(value){
                     return "$" + (new Intl.NumberFormat("de-DE").format(value));
                    }
                }
            }
            ]
        }
     }
}';

$urlMonth = "https://quickchart.io/chart?width=600&height=300&c=" . urlencode($chart);


/*
 * product report
 * */



$chart = '{
    type:  "pie",
    data: {
        labels:'.json_encode($salesOfProduct->data->labels).',
        datasets: [
            {
                label: "Informe de ventas",
                data:'.json_encode($salesOfProduct->data->values).'
            }
        ]
    }, 
    options:{
         plugins: {
              datalabels: {
                 color: "#fff",
                formatter: (value) => {
                  return value + "%";
              }
            }
         }
    }
}';

$urlProduct = "https://quickchart.io/chart?width=600&height=300&c=" . urlencode($chart);


/*
 * product twelve report
 * */




$chart = '{
    type:  "horizontalBar",
    data: {
        labels:'.json_encode($salesOfProductTwelve->data->labels).',
        datasets: [
            {
                label: "Ingreso por producto de los utimos 12 meses acumulado",
                data:'.json_encode($salesOfProductTwelve->data->values).'
            }
        ]
    },options: 
    { 
     scales:{
        xAxes:[
            {
                ticks: {
                    callback:function(value){
                    return "$" + (new Intl.NumberFormat("de-DE").format(value));
                    }
                }
            }
            ]
        }
    }
}';

$urlProductTwelve = "https://quickchart.io/chart??cht=gv&width=600&height=300&c=" . urlencode($chart);


/*
 * customer month report
 * */

$chart = '{
    type:  "pie",
    data: {
        labels:'.json_encode($salesOfCustomerMonth->data->labels).',
        datasets: [
            {
                label: "Informe de ventas",
                data:'.json_encode($salesOfCustomerMonth->data->values).'
            }
        ]
    }, options:{
         plugins: {
              datalabels: {
                 color: "#fff",
                formatter: (value) => {
                  return value + "%";
              }
            }
         }
     }
}';







$urlCustomerMonth = "https://quickchart.io/chart?width=600&height=300&c=" . urlencode($chart);



/*
 * customer month report
 * */

$chart = '{
    type:  "pie",
    data: {
        labels:'.json_encode($salesOfCustomerPrevius->data->labels).',
        datasets: [
            {
                label: "Informe de ventas",
                data:'.json_encode($salesOfCustomerPrevius->data->values).'
            }
        ]
    }, options:
     { 
        plugins: {
              datalabels: {
                 color: "#fff",
                formatter: (value) => {
                  return value + "%";
              }
            }
         }
     }
}';


$urlCustomerMonthPrevius = "https://quickchart.io/chart?width=600&height=300&c=" . urlencode($chart);

/*
 * customer accumaliteve report
 * */

$chart = '{
    type:  "pie",
    data: {
        labels:'.json_encode($salesOfCustomerAccumulated->data->labels).',
        datasets: [
            {
                label: "Informe de ventas",
                data:'.json_encode($salesOfCustomerAccumulated->data->values).'
            }
        ]
    }, options:{ 
        plugins: {
              datalabels: {
                color: "#fff",
                formatter: (value) => {
                  return value + "%";
              }
            }
         }
     }
}';


$urlCustomerMonthAccumulated = "https://quickchart.io/chart?width=600&height=300&c=" . urlencode($chart);




$customerWallet = [];
$totalWallet = 0;
$invoicesWallet = 0;
$dataWallet = [];

foreach ($salesOfWallet->data as $item) {
    array_push($dataWallet, $item->total);
    array_push($customerWallet, $item->customer);
    $totalWallet += $item->total;
    $invoicesWallet += $item->invoices;
}

$chartConfigArrWallet = '{
    type: "horizontalBar",
    data: {
         labels: '.json_encode($customerWallet).',
         datasets:[
            {
                label: "Informe de cartera",
                data: '.json_encode($dataWallet).'
            }
        ]
    },options:
    {
     scales:{
        xAxes:[
            {
                ticks: {
                    callback:function(value){
                     return "$" + (new Intl.NumberFormat("de-DE").format(value));
                    }
                }
            }
            ]
        }
    }
}';

/*
 *
 */



$urlWallet = "https://quickchart.io/chart?width=600&height=300&c=" . urlencode($chartConfigArrWallet);




/*
 * customer accumaliteve report
 * */

$chart = '{
    type:  "pie",
    data: {
        labels:'.json_encode($salesOfSellerMonth->data->labels).',
        datasets: [
            {
                label: "Informe de ventas",
                data:'.json_encode($salesOfSellerMonth->data->values).'
            }
        ]
    }, options:{ 
        plugins: {
              datalabels: {
                color: "#fff",
                formatter: (value) => {
                  return value + "%";
              }
            }
         }
     }
}';


$urlSellerMonth = "https://quickchart.io/chart?width=600&height=300&c=" . urlencode($chart);


/*
 * customer accumaliteve report
 * */

$chart = '{
    type:  "pie",
    data: {
        labels:'.json_encode($salesOfSellerPrevius->data->labels).',
        datasets: [
            {
                label: "Informe de ventas",
                data:'.json_encode($salesOfSellerPrevius->data->values).'
            }
        ]
    }, options: { 
        plugins: {
              datalabels: {
                 color: "#fff",
                formatter: (value) => {
                  return value + "%";
              }
            }
         }
     }
}';


$urlSellerPrevius = "https://quickchart.io/chart?width=600&height=300&c=" . urlencode($chart);


/*
 * customer accumaliteve report
 * */

$chart = '{
    type:  "pie",
    data: {
        labels:'.json_encode($salesOfSellerAccumulated->data->labels).',
        datasets: [
            {
                label: "Informe de ventas",
                data:'.json_encode($salesOfSellerAccumulated->data->values).'
            }
        ]
    }, options:{ 
        plugins: {
              datalabels: {
                color: "#fff",
                formatter: (value) => {
                  return value + "%";
              }
            }
         }
     }
}';


$urlSellerAccumulated = "https://quickchart.io/chart?width=600&height=300&c=" . urlencode($chart);



?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Report Email</title>
</head>
<body style="-moz-box-sizing:border-box;-ms-text-size-adjust:100%;-webkit-box-sizing:border-box;-webkit-text-size-adjust:100%;Margin:0;background:#f3f3f3!important;box-sizing:border-box;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;min-width:100%;padding:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;width:100%!important">
<style>@media only screen {
        html {
            min-height: 100%;
            background: #f3f3f3
        }
    }

    @media only screen and (max-width: 596px) {
        .small-float-center {
            margin: 0 auto !important;
            float: none !important;
            text-align: center !important
        }

        .small-text-center {
            text-align: center !important
        }

        .small-text-left {
            text-align: left !important
        }

        .small-text-right {
            text-align: right !important
        }
    }

    @media only screen and (max-width: 596px) {
        .hide-for-large {
            display: block !important;
            width: auto !important;
            overflow: visible !important;
            max-height: none !important;
            font-size: inherit !important;
            line-height: inherit !important
        }
    }

    @media only screen and (max-width: 596px) {
        table.body table.container .hide-for-large, table.body table.container .row.hide-for-large {
            display: table !important;
            width: 100% !important
        }
    }

    @media only screen and (max-width: 596px) {
        table.body table.container .callout-inner.hide-for-large {
            display: table-cell !important;
            width: 100% !important
        }
    }

    @media only screen and (max-width: 596px) {
        table.body table.container .show-for-large {
            display: none !important;
            width: 0;
            mso-hide: all;
            overflow: hidden
        }
    }

    @media only screen {
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important
        }
    }

    @media only screen and (max-width: 596px) {
        .menu.small-vertical .menu-item {
            padding-left: 0 !important;
            padding-right: 0 !important
        }
    }

    @media only screen and (max-width: 596px) {
        table.body img {
            width: auto;
            height: auto
        }

        table.body center {
            min-width: 0 !important
        }

        table.body .container {
            width: 95% !important
        }

        table.body .column, table.body .columns {
            height: auto !important;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            padding-left: 16px !important;
            padding-right: 16px !important
        }

        table.body .column .column, table.body .column .columns, table.body .columns .column, table.body .columns .columns {
            padding-left: 0 !important;
            padding-right: 0 !important
        }

        table.body .collapse .column, table.body .collapse .columns {
            padding-left: 0 !important;
            padding-right: 0 !important
        }

        td.small-1, th.small-1 {
            display: inline-block !important;
            width: 8.33333% !important
        }

        td.small-2, th.small-2 {
            display: inline-block !important;
            width: 16.66667% !important
        }

        td.small-3, th.small-3 {
            display: inline-block !important;
            width: 25% !important
        }

        td.small-4, th.small-4 {
            display: inline-block !important;
            width: 33.33333% !important
        }

        td.small-5, th.small-5 {
            display: inline-block !important;
            width: 41.66667% !important
        }

        td.small-6, th.small-6 {
            display: inline-block !important;
            width: 50% !important
        }

        td.small-7, th.small-7 {
            display: inline-block !important;
            width: 58.33333% !important
        }

        td.small-8, th.small-8 {
            display: inline-block !important;
            width: 66.66667% !important
        }

        td.small-9, th.small-9 {
            display: inline-block !important;
            width: 75% !important
        }

        td.small-10, th.small-10 {
            display: inline-block !important;
            width: 83.33333% !important
        }

        td.small-11, th.small-11 {
            display: inline-block !important;
            width: 91.66667% !important
        }

        td.small-12, th.small-12 {
            display: inline-block !important;
            width: 100% !important
        }

        .column td.small-12, .column th.small-12, .columns td.small-12, .columns th.small-12 {
            display: block !important;
            width: 100% !important
        }

        table.body td.small-offset-1, table.body th.small-offset-1 {
            margin-left: 8.33333% !important;
            Margin-left: 8.33333% !important
        }

        table.body td.small-offset-2, table.body th.small-offset-2 {
            margin-left: 16.66667% !important;
            Margin-left: 16.66667% !important
        }

        table.body td.small-offset-3, table.body th.small-offset-3 {
            margin-left: 25% !important;
            Margin-left: 25% !important
        }

        table.body td.small-offset-4, table.body th.small-offset-4 {
            margin-left: 33.33333% !important;
            Margin-left: 33.33333% !important
        }

        table.body td.small-offset-5, table.body th.small-offset-5 {
            margin-left: 41.66667% !important;
            Margin-left: 41.66667% !important
        }

        table.body td.small-offset-6, table.body th.small-offset-6 {
            margin-left: 50% !important;
            Margin-left: 50% !important
        }

        table.body td.small-offset-7, table.body th.small-offset-7 {
            margin-left: 58.33333% !important;
            Margin-left: 58.33333% !important
        }

        table.body td.small-offset-8, table.body th.small-offset-8 {
            margin-left: 66.66667% !important;
            Margin-left: 66.66667% !important
        }

        table.body td.small-offset-9, table.body th.small-offset-9 {
            margin-left: 75% !important;
            Margin-left: 75% !important
        }

        table.body td.small-offset-10, table.body th.small-offset-10 {
            margin-left: 83.33333% !important;
            Margin-left: 83.33333% !important
        }

        table.body td.small-offset-11, table.body th.small-offset-11 {
            margin-left: 91.66667% !important;
            Margin-left: 91.66667% !important
        }

        table.body table.columns td.expander, table.body table.columns th.expander {
            display: none !important
        }

        table.body .right-text-pad, table.body .text-pad-right {
            padding-left: 10px !important
        }

        table.body .left-text-pad, table.body .text-pad-left {
            padding-right: 10px !important
        }

        table.menu {
            width: 100% !important
        }

        table.menu td, table.menu th {
            width: auto !important;
            display: inline-block !important
        }

        table.menu.small-vertical td, table.menu.small-vertical th, table.menu.vertical td, table.menu.vertical th {
            display: block !important
        }

        table.menu[align=center] {
            width: auto !important
        }

        table.button.small-expand, table.button.small-expanded {
            width: 100% !important
        }

        table.button.small-expand table, table.button.small-expanded table {
            width: 100%
        }

        table.button.small-expand table a, table.button.small-expanded table a {
            text-align: center !important;
            width: 100% !important;
            padding-left: 0 !important;
            padding-right: 0 !important
        }

        table.button.small-expand center, table.button.small-expanded center {
            min-width: 0
        }
    }</style>
<span class="preheader"
      style="color:#f3f3f3;display:none!important;font-size:1px;line-height:1px;max-height:0;max-width:0;mso-hide:all!important;opacity:0;overflow:hidden;visibility:hidden"></span>
<table class="body"
       style="Margin:0;background:#f3f3f3!important;border-collapse:collapse;border-spacing:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;height:100%;line-height:1.3;margin:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;width:100%">
    <tbody>
    <tr style="padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top">
        <td class="center" align="center" valign="top"
            style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;word-wrap:break-word">
            <center style="min-width:580px;width:100%">
                <table align="center" class="wrapper header mifacturalegal float-center"
                       style="Margin:0 auto;background:#F74E6B;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:center;vertical-align:top;width:100%">
                    <tbody>
                    <tr style="padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top">
                        <td class="wrapper-inner"
                            style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:20px;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;word-wrap:break-word">
                            <table align="center" class="container"
                                   style="Margin:0 auto;background:0 0;border-collapse:collapse;border-spacing:0;margin:0 auto;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:inherit;vertical-align:top;width:580px">
                                <tbody>
                                <tr style="padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top">
                                    <td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;word-wrap:break-word">
                                        <table class="row collapse"
                                               style="border-collapse:collapse;border-spacing:0;display:table;padding:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;position:relative;text-align:left;vertical-align:top;width:100%">
                                            <tbody>
                                            <tr style="padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top">
                                                <th class="small-12 large-12 columns first"
                                                    style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0 auto;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0 auto;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;width:298px;word-wrap:break-word">
                                                    <table style="border-collapse:collapse;border-spacing:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;width:100%">
                                                        <tbody>
                                                        <tr style="padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top">
                                                            <th style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;word-wrap:break-word">
                                                                <img src="https://mifacturalegal.com/seo-agency/img/logo5.png"
                                                                     width="100" height="80px"
                                                                     style="-ms-interpolation-mode:bicubic;clear:both;display:block;max-width:100%;outline:0;text-decoration:none;width:auto">
                                                            </th>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </th>
                                                <th class="small-12 large-12 columns last"
                                                    style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0 auto;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0 auto;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;width:298px;word-wrap:break-word">
                                                    <table style="border-collapse:collapse;border-spacing:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;width:100%">
                                                        <tbody>
                                                        <tr style="padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top">
                                                            <th style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;word-wrap:break-word">
                                                                <p class="text-right bold"
                                                                   style="Margin:0;Margin-bottom:10px;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding-bottom:0;padding-left:0;padding-right:0;padding-top:15px;text-align:right">
                                                                    MiFacturaLegal.com</p></th>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </th>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table
                       style="Margin:0 auto;background:#fefefe;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:center;vertical-align:top;width:90%">
                    <tbody>
                    <tr style="padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top">
                        <td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;word-wrap:break-word">
                            <table class="spacer"
                                   style="border-collapse:collapse;border-spacing:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;width:100%">
                                <tbody>
                                <tr style="padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top">
                                    <td height="16"
                                        style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;hyphens:auto;line-height:16px;margin:0;mso-line-height-rule:exactly;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;word-wrap:break-word">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="row"
                                   style="border-collapse:collapse;border-spacing:0;display:table;padding:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;position:relative;text-align:left;vertical-align:top;width:100%">
                                <tbody>
                                <tr style="padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top">
                                    <th class="small-12 large-12 columns first last"
                                        style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0 auto;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0 auto;padding-bottom:16px;padding-left:16px;padding-right:16px;padding-top:0;text-align:left;vertical-align:top;width:564px;word-wrap:break-word">
                                        <table style="border-collapse:collapse;border-spacing:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;width:100%">
                                            <tbody>
                                            <tr style="padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top">
                                                <th style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;word-wrap:break-word">
                                                    <h1 style="Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:34px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;word-wrap:normal">
                                                        <?= $companies->company ?></h1>
                                                    <p style="Margin:0;Margin-bottom:10px;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left">
                                                        <?php setlocale(LC_TIME, "spanish"); ?>
                                                        Informe Financiero del 18 al <?= date('d')?> de <?= strftime("%B");?> de <?= date('Y')?> <br>
                                                        Generado el <?= date('d')?> de <?= strftime("%B");?> de  <?= date('Y')?> a las <?= date('H:s A')?> por <strong style="color: #18aed7;">MiFacturaLegal.com</strong>
                                                        <br><br>
                                                        Estimado <?= $companies->company ?>, a continuación, se presenta el informe de las operaciones de la compañía.

                                                    </p>
                                                    <?php
                                                    if($setting->data->sale_of_month): ?>
                                                        <h6 style="Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:34px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;word-wrap:normal; text-align: center;">
                                                            1.	Informe de Ventas</h6>
                                                        <p style="Margin:0;Margin-bottom:10px;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:center;">
                                                            Evolución de ingresos facturados por la compañía:</p>
                                                        <center style="min-width:532px;width:100%"><img
                                                                    src="<?= $urlMonth ?>"
                                                                    alt="" align="center" class="float-center"
                                                                    style="-ms-interpolation-mode:bicubic;Margin:0 auto;clear:both;display:block;float:none;margin:0 auto;max-width:350px;outline:0;text-align:center;text-decoration:none;width:auto">
                                                        </center>
                                                        <br>
                                                    <?php endif ?>
                                                    <?php if($setting->data->sale_of_product && count($salesOfProduct->data->values) > 1): ?>
                                                        <h6 style="Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:34px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;word-wrap:normal; text-align: center;">
                                                            2.	Informe de Productos
                                                        </h6>
                                                        <p style="Margin:0;Margin-bottom:10px;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:center">
                                                            Estos son los <?= count($salesOfProduct->data->values) ?> productos o servicios con mayor participación en el ingreso de la compañía.
                                                        </p>
                                                        <center style="min-width:532px;width:100%"><img
                                                                    src="<?= $urlProduct ?>"
                                                                    alt="" align="center" class="float-center"
                                                                    style="-ms-interpolation-mode:bicubic;Margin:0 auto;clear:both;display:block;float:none;margin:0 auto;max-width:350px;outline:0;text-align:center;text-decoration:none;width:auto">
                                                        </center>

                                                        <p style="Margin:0;Margin-bottom:10px;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:center">
                                                            Ingreso porporudcto de los utimos 12 meses acumulado.
                                                        </p>
                                                        <center style="min-width:532px;width:100%"><img
                                                                    src="<?= $urlProductTwelve ?>"
                                                                    alt="" align="center" class="float-center"
                                                                    style="-ms-interpolation-mode:bicubic;Margin:0 auto;clear:both;display:block;float:none;margin:0 auto;max-width:350px;outline:0;text-align:center;text-decoration:none;width:auto">
                                                        </center>

                                                    <?php endif; ?>
                                                    <br><br><br>
                                                    <?php if($setting->data->sale_of_customer): ?>
                                                    <h6 style="Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:34px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;word-wrap:normal; text-align: center;">
                                                        3.	Informe de Clientes
                                                    </h6>

                                                        <center style="min-width:532px;width:100%">

                                                            <div style="width: 100%; display: block;  border: transparent 1px solid; ">
                                                                <div style="width: 33%; float: left;">
                                                                    <h6 style="font-family:Helvetica,Arial,sans-serif;font-size: 20px; display:inline-block; margin-top: 0px; margin-bottom: 0px;">
                                                                        <small>Mes Anterior</small>
                                                                    </h6>
                                                                     <img src="<?= $urlCustomerMonthPrevius ?>"
                                                                        alt=""
                                                                        style="display: block; width: 100%;">
                                                                </div>
                                                                <div style="width: 33%; float: left;">
                                                                    <h6 style="font-family:Helvetica,Arial,sans-serif;font-size: 20px; display:inline-block; margin-top: 0px; margin-bottom: 0px;">
                                                                        <small>Mes Actual</small>
                                                                    </h6>
                                                                    <img
                                                                            src="<?= $urlCustomerMonth ?>"
                                                                            alt=""
                                                                            style="display: block; width: 100%;">

                                                                </div>
                                                                <div style="width: 33%; float: right;">
                                                                    <h6 style="font-family:Helvetica,Arial,sans-serif;font-size: 20px; display:inline-block; margin-top: 0px; margin-bottom: 0px;">
                                                                        <small>Año acumulado</small>
                                                                    </h6>
                                                                    <img
                                                                            src="<?=  $urlCustomerMonthAccumulated ?>"
                                                                            alt=""
                                                                            style="display: block; width: 100%;">
                                                                </div>
                                                            </div>

                                                        </center>

                                                    <?php endif; ?>
                                                    <br><br><br>     <br><br><br>     <br><br><br>
                                                    <?php if($setting->data->sale_of_wallet): ?>
                                                        <div style="height:50px;"></div>
                                                        <h1 style="Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:34px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:center;word-wrap:normal;">
                                                            4.	Informe de Cartera</h1>

                                                        <p style="Margin:0;Margin-bottom:10px;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:center;">
                                                            Cuentas por cobrar a la fecha de generación del informe.</p>
                                                        <center style="min-width:532px;width:100%"><img
                                                                    src="<?= $urlWallet ?>"
                                                                    alt="" align="center" class="float-center"
                                                                    style="-ms-interpolation-mode:bicubic;Margin:0 auto;clear:both;display:block;float:none;margin:0 auto;max-width:500px;outline:0;text-align:center;text-decoration:none;width:auto">
                                                        </center>
                                                    <p style="text-align: center;">Detalle de cuentas por cobrar por edad a la fecha de generación del informe y cantidad de facturas por cliente pendientes de cobro.</p>
                                                        <table class="table" border=""
                                                               style="--bs-table-active-bg:rgba(0,0,0,0.1);--bs-table-active-color:#212529;--bs-table-bg:transparent;--bs-table-hover-bg:rgba(0,0,0,0.075);--bs-table-hover-color:#212529;--bs-table-striped-bg:rgba(0,0,0,0.05);--bs-table-striped-color:#212529;border:none;border-collapse:collapse;border-color:#dee2e6;border-spacing:0;color:#212529;font-size:12px!important;margin-bottom:1rem;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;width:100%">
                                                            <thead style="font-weigth:bold!important;vertical-align:bottom">
                                                            <tr style="padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top">
                                                                <th style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-bottom-color:currentColor;border-bottom-width:1px;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;text-align:left;vertical-align:top;word-wrap:break-word;  padding:5px !important; font-size: 12px;">
                                                                   <strong>Cliente</strong>
                                                                </th>
                                                                <th style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-bottom-color:currentColor;border-bottom-width:1px;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;text-align:center;vertical-align:top;word-wrap:break-word;  padding:5px !important; font-size: 12px;">
                                                                    <strong> Corrientes</strong>
                                                                </th>
                                                                <th style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-bottom-color:currentColor;border-bottom-width:1px;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;text-align:center;vertical-align:top;word-wrap:break-word;  padding:5px !important; font-size: 12px;">
                                                                    <strong>de 1  a 30 días</strong>
                                                                </th>
                                                                <th style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-bottom-color:currentColor;border-bottom-width:1px;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;text-align:center;vertical-align:top;word-wrap:break-word;  padding:5px !important; font-size: 12px;">
                                                                    <strong>de 31  a 60 días</strong>
                                                                </th>
                                                                <th style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-bottom-color:currentColor;border-bottom-width:1px;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;text-align:center;vertical-align:top;word-wrap:break-word;  padding:5px !important; font-size: 12px;">
                                                                    <strong> de 61  a 90 días</strong>
                                                                </th>
                                                                <th style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-bottom-color:currentColor;border-bottom-width:1px;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;text-align:center;vertical-align:top;word-wrap:break-word;  padding:5px !important; font-size: 12px;">
                                                                    <strong>mayor a 90 días
                                                                </th>
                                                                <th style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-bottom-color:currentColor;border-bottom-width:1px;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;text-align:center;vertical-align:top;word-wrap:break-word;  padding:5px !important; font-size: 12px;">
                                                                    <strong> TOTAL</strong>
                                                                </th>
                                                                <th style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-bottom-color:currentColor;border-bottom-width:1px;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;text-align:center;vertical-align:top;word-wrap:break-word;  padding:5px !important; font-size: 12px;">
                                                                    <strong>Cantidad de factura</strong>
                                                                </th>

                                                            </tr>
                                                            </thead>
                                                            <tbody style="vertical-align:inherit">
                                                            <?php foreach ((array) $salesOfWallet->wallet_table as $item => $key ): ?>
                                                                <?php $key = (array) $key; ?>
                                                                <tr style="padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top">
                                                                    <th style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-bottom-width:1px;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;text-align:left;vertical-align:top;word-wrap:break-word;  padding:5px !important; font-size: 12px;">
                                                                        <?= $item ?>
                                                                    </th>
                                                                    <td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-bottom-width:1px;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;text-align:center;vertical-align:top;word-wrap:break-word;  padding:5px !important; font-size: 12px;">
                                                                        $ <?= isset($key['corrientes']) ? number_format($key['corrientes'], '0', '.', '.') : 0 ?>
                                                                    </td>
                                                                    <td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-bottom-width:1px;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;text-align:center;vertical-align:top;word-wrap:break-word;  padding:5px !important; font-size: 12px;">
                                                                        $ <?= isset($key['30']) ? number_format($key['30'], '0', '.', '.') : 0 ?>
                                                                    </td>
                                                                    <td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-bottom-width:1px;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;text-align:center;vertical-align:top;word-wrap:break-word;  padding:5px !important; font-size: 12px;">
                                                                        $ <?= isset($key['60']) ? number_format($key['60'], '0', '.', '.') : 0 ?>
                                                                    </td>
                                                                    <td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-bottom-width:1px;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;text-align:center;vertical-align:top;word-wrap:break-word;  padding:5px !important; font-size: 12px;">
                                                                        $ <?= isset($key['90']) ?  number_format($key['90'], ',', '.', '.') : 0 ?>
                                                                    </td>
                                                                    <td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-bottom-width:1px;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;text-align:center;vertical-align:top;word-wrap:break-word;  padding:5px !important; font-size: 12px;">
                                                                        $ <?= isset($key['120']) ? number_format($key['120'], ',', '.', '.') : 0 ?>
                                                                    </td>
                                                                    <td>$ <?=
                                                                        number_format((isset($key['corrientes'])      ? $key['corrientes'] : 0) +
                                                                        (isset($key['30'])              ? $key['30'] : 0 ) +
                                                                        (isset($key['60'])              ? $key['60']: 0) +
                                                                        (isset($key['90'])              ? $key['90'] : 0) +
                                                                        (isset($key['120'])             ? $key['120'] : 0),'0', '.', '.');

                                                                        ?></td>
                                                                    <td><?= $key['cantidad']?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    <?php endif; ?>
                                                    <br><br><br>
                                                    <?php if($setting->data->sale_of_seller): ?>

                                                        <h6 style="Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:34px;font-weight:400;line-height:1.3;margin:0;margin-bottom:10px;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;word-wrap:normal; text-align: center;">
                                                            5.	Informe de Vendedores
                                                        </h6>
                                                            <center>
                                                        <div style="width: 100%;">
                                                            <div style="width: 33%;  float: left;">
                                                                <h6 style="font-family:Helvetica,Arial,sans-serif; font-size: 20px; text-align: center;  display:inline-block; margin-top: 0px; margin-bottom: 0px;">
                                                                    <small>Mes actual</small>
                                                                </h6>
                                                                <img
                                                                        src="<?= $urlSellerMonth ?>"
                                                                        alt="" align="center" class="float-center"
                                                                        style="-ms-interpolation-mode:bicubic;Margin:0 auto;clear:both;display:block;float:none;margin:0 auto;max-width:100%;outline:0;text-align:center;text-decoration:none;width:auto">
                                                            </div>
                                                            <div style="width: 33%; float: left;">
                                                                <h6 style="font-family:Helvetica,Arial,sans-serif;font-size: 20px; text-align: center;  display:inline-block; margin-top: 0px; margin-bottom: 0px;">
                                                                    <small>Mes Anterior</small>
                                                                </h6>
                                                                <img
                                                                        src="<?= $urlSellerPrevius ?>"
                                                                        alt="" align="center" class="float-center"
                                                                        style="display: block; width: 100%;">

                                                            </div>
                                                            <div style="width: 33.3%; float: right">
                                                                <h6 style="font-family:Helvetica,Arial,sans-serif;font-size: 20px; text-align: center;  display:inline-block; margin-top: 0px; margin-bottom: 0px;">
                                                                    <small>Año acumulado</small>
                                                                </h6>
                                                             <img
                                                                            src="<?=  $urlSellerAccumulated ?>"
                                                                            alt=""
                                                                            style="display: block; width: 100%;">

                                                            </div>
                                                        </div>
                                                            </center>

                                                    <?php endif; ?>


                                                <th class="expander"
                                                    style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:12px!important;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0!important;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;visibility:hidden;width:0;word-wrap:break-word"></th>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </th>
                                </tr>
                                </tbody>
                            </table>
                            <table class="wrapper secondary" align="center"
                                   style="background:#f3f3f3;border-collapse:collapse;border-spacing:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;width:100%">
                                <tbody>
                                <tr style="padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top">
                                    <td class="wrapper-inner"
                                        style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;word-wrap:break-word">
                                        <table class="spacer"
                                               style="border-collapse:collapse;border-spacing:0;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;width:100%">
                                            <tbody>
                                            <tr style="padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top">
                                                <td height="16"
                                                    style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;hyphens:auto;line-height:16px;margin:0;mso-line-height-rule:exactly;padding-bottom:0;padding-left:0;padding-right:0;padding-top:0;text-align:left;vertical-align:top;word-wrap:break-word">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </center>
        </td>
    </tr>
    </tbody>
</table>
<div style="display:none;white-space:nowrap;font:15px courier;line-height:0">&amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp;
    &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp;
    &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp;
    &amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp;
</div>
</body>
</html>



