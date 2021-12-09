<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Project 2021</title>

    <link rel="icon" href="/logo.png">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
{{--    <link rel="stylesheet" href="/giaodien/my_plugins/fontawesome/css/all.min.css">--}}
    <link rel="stylesheet" href="/giaodien/my_plugins/viewer/viewer.min.css" >
    <!-- IonIcons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/giaodien/dist/css/adminlte.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="/giaodien/my_plugins/select2/select2.min.css">
    <link rel="stylesheet" href="/giaodien/my_plugins/select2/custom.css">

    <!-- Tabulator -->
    <link rel="stylesheet" href="/giaodien/my_plugins/tabulator/tabulator.min.css">
    <link rel="stylesheet" href="/giaodien/my_plugins/tabulator/custom.css">

    <link rel="stylesheet" href="/giaodien/my_plugins/sweet-alert2/custom.css">

    <link rel="stylesheet" href="/giaodien/my_plugins/daterangepicker/daterangepicker.css">

    <link rel="stylesheet" href="/giaodien/my_plugins/tempusdominus/5-4.min.css" >

    <link rel="stylesheet" href="/giaodien/dist/css/custom.css?version=1.0">

    @yield('style-include')

    @yield('style-custom')

    <style>
        .layout-footer-fixed .wrapper .content-wrapper {
            padding-bottom: unset;
            height: calc(100vh - 114px);
            min-height: unset !important;
        }
        section.content {
            height: 100%;
        }
        section.content > div {
            min-height: 100%;
            max-height: 100%;
        }
        .content-wrapper>.content {
            padding: 0.5rem;
        }

        #modalThongTin div.field p img:not(:first-child) {
            margin-left: 0.5rem;
        }

        #boxDanhSach .box-search .action > button {
            min-width: 38px;
        }
        .products-list.box-content .product-description {
            display: flex !important;
            white-space: normal;
        }
        .products-list.box-content .product-description > span:first-child {
            /*min-width: 85px;*/
            font-weight: bolder;
            white-space: nowrap;
        }
        .products-list.box-content .product-description > span:last-child {
            /*margin-left: auto;*/
        }
        .box-content.products-list .product-img img {
            max-height: 100%;
            max-width: 100%;
            height: unset !important;
            width: unset !important;
        }
        .box-content.products-list .product-img {
            width: 50px;
            min-width: 50px;
            display: flex;
            align-items: center;
        }
        .box-content.products-list .product-info {
            margin-left: unset;
            width: 100%;
        }
        .box-content.products-list .product-img + .product-info {
            margin-left: 0.5rem;
        }
        .box-content.products-list > .item {
            display: flex;
        }

        .modal-dialog.modal-fullsize {
            height: 100% !important;
            max-height: 100% !important;
            margin: 0 !important;
            max-width: 100% !important;
        }
        .modal-dialog.modal-fullsize .modal-content {
            border: unset !important;
            border-radius: unset !important;
        }
        /*.select2-container {*/
        /*    width: unset !important;*/
        /*}*/
        .select2.select2-container .select2-selection.select2-selection--single .select2-selection__rendered {
            margin-top: -3px;
        }

        .card.card-outline-tabs .card-header a:hover {
            border-top: 3px solid #007bff;
        }
    </style>
</head>
