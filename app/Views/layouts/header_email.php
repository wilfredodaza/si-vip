    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width">
        <title>Documento soporte - Nevado lab</title>
    </head>

    <body style="-moz-box-sizing:border-box;-ms-text-size-adjust:100%;-webkit-box-sizing:border-box;-webkit-text-size-adjust:100%;Margin:0;box-sizing:border-box;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;min-width:100%;padding:0;text-align:left;width:100%!important">
        <style>
            @font-face {
                font-family: 'Trueno';
                src: url(<?= base_url('assets/fonts/TruenoLt.otf') ?>);
            }

            body,
            html,
            table,
            td,
            th,
            p,
            h1,
            a {
                font-family: Verdana, Geneva, Tahoma, sans-serif !important;
                font-size: 14px;
            }

            @media only screen {
                html {
                    min-height: 100%;
                    background: #f3f3f3
                }
            }

            @media only screen and (max-width:596px) {
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

            @media only screen and (max-width:596px) {
                .hide-for-large {
                    display: block !important;
                    width: auto !important;
                    overflow: visible !important;
                    max-height: none !important;
                    font-size: inherit !important;
                    line-height: inherit !important
                }
            }

            @media only screen and (max-width:596px) {

                table.body table.container .hide-for-large,
                table.body table.container .row.hide-for-large {
                    display: table !important;
                    width: 100% !important
                }
            }

            @media only screen and (max-width:596px) {
                table.body table.container .callout-inner.hide-for-large {
                    display: table-cell !important;
                    width: 100% !important
                }
            }

            @media only screen and (max-width:596px) {
                table.body table.container .show-for-large {
                    display: none !important;
                    width: 0;
                    mso-hide: all;
                    overflow: hidden
                }
            }

            @media only screen and (max-width:596px) {
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

                table.body .column,
                table.body .columns {
                    height: auto !important;
                    -moz-box-sizing: border-box;
                    -webkit-box-sizing: border-box;
                    box-sizing: border-box;
                    padding-left: 16px !important;
                    padding-right: 16px !important
                }

                table.body .column .column,
                table.body .column .columns,
                table.body .columns .column,
                table.body .columns .columns {
                    padding-left: 0 !important;
                    padding-right: 0 !important
                }

                table.body .collapse .column,
                table.body .collapse .columns {
                    padding-left: 0 !important;
                    padding-right: 0 !important
                }

                td.small-1,
                th.small-1 {
                    display: inline-block !important;
                    width: 8.33333% !important
                }

                td.small-2,
                th.small-2 {
                    display: inline-block !important;
                    width: 16.66667% !important
                }

                td.small-3,
                th.small-3 {
                    display: inline-block !important;
                    width: 25% !important
                }

                td.small-4,
                th.small-4 {
                    display: inline-block !important;
                    width: 33.33333% !important
                }

                td.small-5,
                th.small-5 {
                    display: inline-block !important;
                    width: 41.66667% !important
                }

                td.small-6,
                th.small-6 {
                    display: inline-block !important;
                    width: 50% !important
                }

                td.small-7,
                th.small-7 {
                    display: inline-block !important;
                    width: 58.33333% !important
                }

                td.small-8,
                th.small-8 {
                    display: inline-block !important;
                    width: 66.66667% !important
                }

                td.small-9,
                th.small-9 {
                    display: inline-block !important;
                    width: 75% !important
                }

                td.small-10,
                th.small-10 {
                    display: inline-block !important;
                    width: 83.33333% !important
                }

                td.small-11,
                th.small-11 {
                    display: inline-block !important;
                    width: 91.66667% !important
                }

                td.small-12,
                th.small-12 {
                    display: inline-block !important;
                    width: 100% !important
                }

                .column td.small-12,
                .column th.small-12,
                .columns td.small-12,
                .columns th.small-12 {
                    display: block !important;
                    width: 100% !important
                }

                table.body td.small-offset-1,
                table.body th.small-offset-1 {
                    margin-left: 8.33333% !important;
                    Margin-left: 8.33333% !important
                }

                table.body td.small-offset-2,
                table.body th.small-offset-2 {
                    margin-left: 16.66667% !important;
                    Margin-left: 16.66667% !important
                }

                table.body td.small-offset-3,
                table.body th.small-offset-3 {
                    margin-left: 25% !important;
                    Margin-left: 25% !important
                }

                table.body td.small-offset-4,
                table.body th.small-offset-4 {
                    margin-left: 33.33333% !important;
                    Margin-left: 33.33333% !important
                }

                table.body td.small-offset-5,
                table.body th.small-offset-5 {
                    margin-left: 41.66667% !important;
                    Margin-left: 41.66667% !important
                }

                table.body td.small-offset-6,
                table.body th.small-offset-6 {
                    margin-left: 50% !important;
                    Margin-left: 50% !important
                }

                table.body td.small-offset-7,
                table.body th.small-offset-7 {
                    margin-left: 58.33333% !important;
                    Margin-left: 58.33333% !important
                }

                table.body td.small-offset-8,
                table.body th.small-offset-8 {
                    margin-left: 66.66667% !important;
                    Margin-left: 66.66667% !important
                }

                table.body td.small-offset-9,
                table.body th.small-offset-9 {
                    margin-left: 75% !important;
                    Margin-left: 75% !important
                }

                table.body td.small-offset-10,
                table.body th.small-offset-10 {
                    margin-left: 83.33333% !important;
                    Margin-left: 83.33333% !important
                }

                table.body td.small-offset-11,
                table.body th.small-offset-11 {
                    margin-left: 91.66667% !important;
                    Margin-left: 91.66667% !important
                }

                table.body table.columns td.expander,
                table.body table.columns th.expander {
                    display: none !important
                }

                table.body .right-text-pad,
                table.body .text-pad-right {
                    padding-left: 10px !important
                }

                table.body .left-text-pad,
                table.body .text-pad-left {
                    padding-right: 10px !important
                }

                table.menu {
                    width: 100% !important
                }

                table.menu td,
                table.menu th {
                    width: auto !important;
                    display: inline-block !important
                }

                table.menu.small-vertical td,
                table.menu.small-vertical th,
                table.menu.vertical td,
                table.menu.vertical th {
                    display: block !important
                }

                table.menu[align=center] {
                    width: auto !important
                }

                table.button.small-expand,
                table.button.small-expanded {
                    width: 100% !important
                }

                table.button.small-expand table,
                table.button.small-expanded table {
                    width: 100%
                }

                table.button.small-expand table a,
                table.button.small-expanded table a {
                    text-align: center !important;
                    width: 100% !important;
                    padding-left: 0 !important;
                    padding-right: 0 !important
                }

                table.button.small-expand center,
                table.button.small-expanded center {
                    min-width: 0
                }
            }
            nav,
.card-panel,
.card,
.toast,
.btn,
.btn-large,
.btn-small,
.btn-floating,
.dropdown-content,
.collapsible,
.sidenav
{
    box-shadow: 0 2px 2px 0 rgba(0, 0, 0, .14), 0 3px 1px -2px rgba(0, 0, 0, .12), 0 1px 5px 0 rgba(0, 0, 0, .2);
}
.card-panel
{
    margin: .5rem 0 1rem 0;
    padding: 24px;

    -webkit-transition: box-shadow .25s;
            transition: box-shadow .25s;

    border-radius: 2px;
    background-color: #fff;
}

.card
{
    position: relative;

    margin: .5rem 0 1rem 0;

    -webkit-transition: box-shadow .25s;
            transition: box-shadow .25s;

    border-radius: 2px;
    background-color: #fff;
}

.card .card-title
{
    font-size: 24px;
    font-weight: 300;
}

.card .card-title.activator
{
    cursor: pointer;
}

.card.small,
.card.medium,
.card.large
{
    position: relative;
}

.card.small .card-image,
.card.medium .card-image,
.card.large .card-image
{
    overflow: hidden;

    max-height: 60%;
}

.card.small .card-image + .card-content,
.card.medium .card-image + .card-content,
.card.large .card-image + .card-content
{
    max-height: 40%;
}

.card.small .card-content,
.card.medium .card-content,
.card.large .card-content
{
    overflow: hidden;

    max-height: 100%;
}

.card.small .card-action,
.card.medium .card-action,
.card.large .card-action
{
    position: absolute;
    right: 0;
    bottom: 0;
    left: 0;
}

.card.small
{
    height: 300px;
}

.card.medium
{
    height: 400px;
}

.card.large
{
    height: 500px;
}

.card.horizontal
{
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display:         flex;
}

.card.horizontal.small .card-image,
.card.horizontal.medium .card-image,
.card.horizontal.large .card-image
{
    overflow: visible;

    height: 100%;
    max-height: none;
}

.card.horizontal.small .card-image img,
.card.horizontal.medium .card-image img,
.card.horizontal.large .card-image img
{
    height: 100%;
}

.card.horizontal .card-image
{
    max-width: 50%;
}

.card.horizontal .card-image img
{
    width: auto;
    max-width: 100%;

    border-radius: 0 2px 2px 0;
}

.card.horizontal .card-stacked
{
    position: relative;

    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display:         flex;
            flex-direction: column;

    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -webkit-flex-direction: column;
        -ms-flex-direction: column;
    -webkit-box-flex: 1;
    -webkit-flex: 1;
        -ms-flex: 1;
            flex: 1;
}

.card.horizontal .card-stacked .card-content
{
    -webkit-box-flex: 1;
    -webkit-flex-grow: 1;
    -ms-flex-positive: 1;
            flex-grow: 1;
}

.card.sticky-action .card-action
{
    z-index: 2;
}

.card.sticky-action .card-reveal
{
    z-index: 1;

    padding-bottom: 64px;
}

.card .card-image
{
    position: relative;
}

.card .card-image img
{
    position: relative;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;

    display: block;

    width: 100%;

    border-radius: 2px 2px 0 0;
}

.card .card-image .card-title
{
    position: absolute;
    right: 0;
    bottom: 0;

    max-width: 100%;
    padding: 24px;

    color: #fff;
}

.card .card-content
{
    padding: 24px;

    border-radius: 0 0 2px 2px;
}

.card .card-content p
{
    margin: 0;
}

.card .card-content .card-title
{
    line-height: 32px;

    display: block;

    margin-bottom: 8px;
}

.card .card-content .card-title i
{
    line-height: 32px;
}

.card .card-action
{
    position: relative;

    padding: 16px 24px;

    border-top: 1px solid rgba(160, 160, 160, .2);
    background-color: inherit;
}

.card .card-action:last-child
{
    border-radius: 0 0 2px 2px;
}

.card .card-action a:not(.btn):not(.btn-large):not(.btn-small):not(.btn-large):not(.btn-floating)
{
    margin-left: 24px;

    -webkit-transition: color .3s ease;
            transition: color .3s ease;
    text-transform: uppercase;

    color: #3949ab;
}

.card .card-action a:not(.btn):not(.btn-large):not(.btn-small):not(.btn-large):not(.btn-floating):hover
{
    color: #7885d2;
}

.card .card-reveal
{
    position: absolute;
    z-index: 3;
    top: 100%;
    right: 0;

    display: none;
    overflow-y: auto;

    width: 100%;
    height: 100%;
    padding: 24px;

    background-color: #fff;
}

.card .card-reveal .card-title
{
    display: block;

    cursor: pointer;
}

.cyan
{
    background-color: #00bcd4 !important;
}

.white-text
{
    color: #fff !important;
}
.red{
    background: #ff5252;
}

.card-action.red{
    background: #ff5252;
}


.green
{
    background-color: #4caf50 !important;
}
.green.lighten-1
{
    background-color: #66bb6a !important;
}

.orange.lighten-1
{
    background-color: #ffa726 !important;
}

.orange
{
    background-color: #ff9800 !important;
}

.red.accent-2
{
    background-color: #ff5252 !important;
}

.red
{
    background-color: #f44336 !important;
}
@font-face {
  font-family: 'Material Icons';
  font-style: normal;
  font-weight: 400;
  src: url(https://fonts.gstatic.com/s/materialicons/v81/flUhRq6tzZclQEJ-Vdg-IuiaDsNc.woff2) format('woff2');
}

.material-icons {
  font-family: 'Material Icons';
  font-weight: normal;
  font-style: normal;
  font-size: 24px;
  line-height: 1;
  letter-spacing: normal;
  text-transform: none;
  display: inline-block;
  white-space: nowrap;
  word-wrap: normal;
  direction: ltr;
  -webkit-font-feature-settings: 'liga';
  -webkit-font-smoothing: antialiased;
}

        </style>
          <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <table class="body" data-made-with-foundation="" style="Margin:0;background:#f3f3f3;border-collapse:collapse;border-spacing:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;height:100%;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;width:100%">
            <tbody>
                <tr style="padding:0;text-align:left;vertical-align:top">
                    <td class="float-center" align="center" valign="top" style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0 auto;border-collapse:collapse!important;color:#0a0a0a;float:none;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0 auto;padding:0;text-align:center;vertical-align:top;word-wrap:break-word">
                        <center data-parsed="" style="min-width:580px;width:100%">
                            <table align="center" class="container float-center" style="Margin:0 auto;background:#fefefe;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:580px">
                                <tbody>
                                    <tr style="padding:0;text-align:left;vertical-align:top">
                                        <td style="-moz-hyphens:auto;-webkit-hyphens:auto;Margin:0;border-collapse:collapse!important;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;hyphens:auto;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word">
                                            <table class="row" style="border-collapse:collapse;border-spacing:0;display:table;padding:0;position:relative;text-align:left;vertical-align:top;width:100%">
                                                <tbody>
                                                    <tr style="padding:0;text-align:left;vertical-align:top;background-image: url('<?= base_url('assets/img/email-header-01.png') ?>');height:218px;width:100%;">
                                                        <th>
                                                            <p style="text-align:right; padding-right:10px; color:white;font-size: 10px;">
                                                                <a href="https://mifacturalegal.com/" style="text-decoration:none; color:white;">www.mifacturalegal.com</a> <br>
                                                                +57 314 295 78 96 | +57 301 769 74 98 | +57 300 304 72 77 <br>
                                                                soporte@mifacturalegal.com <br>
                                                                Bogotá - Colombia - Sur América
                                                            </p>

                                                        </th>
                                                    </tr>
                                                    <tr style="padding:0;text-align:left;vertical-align:top">
                                                        <th class="small-12 large-12 columns first last" style="Margin:0 auto;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:16px;padding-left:16px;padding-right:16px;text-align:left;width:564px">
                                                            <table style="border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%">
                                                                <tbody>
                                                                    <tr style="padding:0;text-align:left;vertical-align:top">
                                                                        <th style="Margin:0;color:#0a0a0a;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left">