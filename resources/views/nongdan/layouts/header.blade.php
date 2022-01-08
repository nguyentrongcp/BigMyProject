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
        .product-description .product-label {
            font-weight: bolder;
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

        .modal {
            max-width: 100vw;
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
        .select2.select2-container .select2-selection.select2-selection--single .select2-selection__rendered,
        .select2.select2-container .select2-selection.select2-selection--single .select2-selection__clear {
            margin-top: -3px;
        }

        .card.card-outline-tabs .card-header a:hover {
            border-top: 3px solid #007bff;
        }

        .font-size-mobile {
            font-size: 14px !important;
        }
        .font-size-btn-sm-mobile {
            font-size: 12px !important;
        }

        #dropdownThongBao {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
        #dropdownThongBao .dropdown-header {
            font-weight: bolder;
            color: #6c757d;
        }
        #dropdownThongBao .dropdown-item > i {
            width: 20px;
        }
        #dropdownThongBao .dropdown-item.thongbao:not(:active) .tieude {
            color: #6c757d;
        }
        #dropdownThongBao .dropdown-item.quytrinh .tieude {
            font-weight: bolder;
        }
        #dropdownThongBao .dropdown-item.quytrinh .noidung .congdung {
            text-align: justify;
        }
        #dropdownThongBao .dropdown-item.quytrinh:not(:active) .tieude {
            color: #007bff;
        }
        #dropdownThongBao .dropdown-item.quytrinh:not(:active) .soluong {
            color: #17a2b8;
        }
        #dropdownThongBao .dropdown-item .dropdown-divider {
            margin: 0.25rem 0;
        }

        .timeline .timeline-trangthai {
            border-top: 1px solid rgba(0,0,0,.125);
            border-bottom: 1px solid rgba(0,0,0,.125);
            display: flex;
            justify-content: space-between;
        }
        .timeline .timeline-ghichu > div {
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
        .timeline .boxPhanHoi .item-phanhoi:not(:first-child) {
            border-top: 1px solid #ebebeb;
            padding-top: 0.25rem;
            margin-top: 0.25rem;
        }
        .timeline .boxPhanHoi .item-phanhoi .btnXoa {
            display: none;
        }
        .timeline .boxPhanHoi .item-phanhoi:last-child:not(.reply) .btnXoa {
            display: block;
        }
        .timeline .boxPhanHoi .item-phanhoi .thoigian {
            margin-left: auto;
        }
        .timeline .boxPhanHoi .item-phanhoi .box-action {
            font-size: 12px;
        }
        .boxPhanHoi .item-phanhoi.reply .ten {
            color: #007bff !important;
        }
    </style>
</head>
