<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
          content="Materialize is a Material Design Admin Template,It's modern, responsive and based on Material Design by Google.">
    <meta name="keywords"
          content="materialize, admin template, dashboard template, flat admin template, responsive admin template, eCommerce dashboard, analytic dashboard">
    <meta name="author" content="ThemeSelect">
    <title><?= isset(configInfo()['name_app']) ? configInfo()['name_app'] : '' ?></title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- BEGIN: VENDOR CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/vendors.min.css">
    <!-- END: VENDOR CSS-->
    <!-- BEGIN: Page Level CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/materialize2.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/style.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/dashboard.css">
    <!-- END: Page Level CSS-->
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/custom.min.css">
    <!-- END: Custom CSS-->

    <link rel="stylesheet" href="<?= base_url() ?>/grocery-crud/css/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="<?= base_url() ?>/grocery-crud/css/grocery-crud-v2.8.1.0659b25.css">
    <link rel="stylesheet" href="<?= base_url() ?>/grocery-crud/css/bootstrap/bootstrap.css">
    <script src="<?= base_url() ?>/assets/ckeditor/ckeditor.js"></script>
      <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />

</head>
<body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 2-columns   "
      data-open="click" data-menu="vertical-dark-menu" data-col="2-columns">
