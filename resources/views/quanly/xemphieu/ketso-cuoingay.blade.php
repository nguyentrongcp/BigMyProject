<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>XEM PHIẾU</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="/giaodien/plugins/fontawesome-free/css/all.min.css">
    <!-- IonIcons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <link rel="stylesheet" href="/giaodien/my_plugins/sweet-alert2/custom.css">

    <link rel="stylesheet" href="/giaodien/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="/giaodien/dist/css/custom.css">

    <!-- jQuery -->
    <script src="/giaodien/plugins/jquery/jquery.min.js"></script>

    <script src="/giaodien/my_plugins/numeral-js/numeral.min.js"></script>
    <script src="/giaodien/my_plugins/sweet-alert2/SweetAlert2.min.js"></script>
    <script src="/giaodien/my_plugins/cookie/cookie.min.js"></script>
    <script src="/giaodien/my_plugins/print/jquery-print.js"></script>

    <script src="/giaodien/dist/js/function.js"></script>
    <style>
        #boxPhieuIn table td {
            padding: 3px;
            font-size: 12px;
            vertical-align: middle;
        }

        .btn-info {
            color: #fff !important;
        }
        .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
            border-bottom-width: 2px;
            border-bottom-color: inherit !important;
            padding: 3px;
            font-weight: bolder;
        }
        .table-bordered>tfoot>tr>th {
            font-weight: bolder;
            font-size: 12px;
            padding: 3px;
        }

        @media print {
            .page-break {
                display: block;
                page-break-before: always;
            }
            .print-hidden {
                display: none !important;
            }
        }
        body, html {
            height: fit-content;
            font-size: 13px;
            font-family: Verdana;
            line-height: 1.42857143;
        }
    </style>
</head>
<body class="overflow-hidden">
<div class="p-4">
    <div class="text-center pb-3">
        @if($controls->printable)
        <button id="btnInPhieu" class="btn btn-info btn-sm">In Phiếu</button>
        @endif
        {{--        Nếu is_xoaphieu chức năng xóa phiếu --}}
        @if($controls->deletable && $phieu->ngay == date('Y-m-d'))
            @if(!$phieu->trashed())
                <button id="btnXoaPhieu" class="btn btn-danger btn-sm">Xóa Phiếu</button>
            @endif
        @endif
    </div>
    <div class="d-flex justify-content-center" style="flex-wrap: wrap">
        <div style="width: 568px; height: fit-content" class="position-relative">
            <div class="print-hidden fw-bolder d-flex justify-content-center
            position-absolute align-items-center w-100 h-100"
                 id="textTrangThai" style="color: rgba(0,0,0,0.1)">
                @if($phieu->deleted_at !== null)
                    <span style="font-size: 120px;transform: rotate(-45deg);">ĐÃ XÓA</span>
                @endif
            </div>
            <div class="position-relative" id="boxPhieuIn">
                <div class="position-relative">
                    <div class="d-flex">
                        <div style="max-width: 85px">
                            <img id="logo" style="height: 85px" src="https://ui-banhang.hailua.center/i/logo.png">
                        </div>
                        <div class="d-flex flex-column">
                            <div class="fw-bolder" style="font-size: 13px">{{ $phieu->chinhanh->ten }}</div>
                            <div style="font-size: 11px; text-align: justify">{{ $phieu->chinhanh->diachi }}</div>
                            <div class="fw-bolder" style="font-size: 12px;">
                                <span>Điện thoại: {{ $phieu->chinhanh->dienthoai }}</span>
                                @if(isset($phieu->chinhanh->dienthoai2))
                                    <span class="ms-3">Tổng đài: {{ $phieu->chinhanh->dienthoai2 }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div style="font-size: 18px" class="fw-bolder text-center mt-3">
                        PHIẾU KẾT SỔ CUỐI NGÀY
                    </div>
                    @if($phieu->loaiphieu == 'PT' || $phieu->loaiphieu == 'PC')
                        <div class="text-center">{{ $phieu->khoanmuc }}</div>
                    @endif
                    <div class="text-center mb-3">
                        <span>{{ date_format(date_create($phieu->created_at),'d-m-Y H:i:s') }}</span>
                    </div>
                    <div>
                        <div>
                            <div class="d-flex">
                                <span class="fw-bolder" style="min-width: 72px">Mã phiếu</span>
                                <span class="me-1">:</span>
                                <span>{{ $phieu->maphieu }}</span>
                            </div>
                            <div class="d-flex">
                                <span class="fw-bolder" style="min-width: 72px">Đầu kỳ</span>
                                <span class="me-1">:</span>
                                <span class="fw-bolder">{{ number_format($phieu->tongthanhtien) }}</span>
                            </div>
                            <div class="d-flex">
                                <span class="fw-bolder" style="min-width: 72px">Tổng thu</span>
                                <span class="me-1">:</span>
                                <span class="text-success fw-bolder">{{ number_format($phieu->phuthu) }}</span>
                            </div>
                            <div class="d-flex">
                                <span class="fw-bolder" style="min-width: 72px">Tổng chi</span>
                                <span class="me-1">:</span>
                                <span class="text-danger fw-bolder">{{ number_format($phieu->giamgia) }}</span>
                            </div>
                            <div class="d-flex">
                                <span class="fw-bolder" style="min-width: 72px">Tổng cuối</span>
                                <span class="me-1">:</span>
                                <span style="color: #17a2b8" class="fw-bolder">{{ number_format($phieu->tienthanhtoan) }}</span>
                            </div>
                            <div class="d-flex">
                                <span class="fw-bolder" style="min-width: 72px">Bằng chữ</span>
                                <span class="me-1">:</span>
                                <span id="lblBangChu"></span>
                            </div>
                            <div class="d-flex">
                                <span class="fw-bolder" style="min-width: 72px">Ghi chú</span>
                                <span class="me-1">:</span>
                                <span>{{ $phieu->ghichu }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-end">
                            <div class="w-50 text-center">
                                <p class="fw-bolder m-0">Người Lập Phiếu</p>
                                <p class="m-0"><i>(Ký tên)</i></p>
                                <p class="mb-0" style="margin-top: 80px;">{{ $phieu->nhanvien->ten }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    setTimeout(() => {
        @if($auto_print)
        $('#phieuin').print();
        @endif
        // $('body').removeClass('overflow-hidden');
    },300);

    $('#lblBangChu').text(num2Word2.convert('{{ $phieu->tienthanhtoan }}'));

    let interval = setInterval(() => {
        if ($(window).height() > 0) {
            top.postMessage({type: 'autoHeight', height: $(window).height() + 'px'});
            clearInterval(interval);
        }
    }, 10);

    @if($controls->deletable)
    @if(!$phieu->trashed())
    $('#btnXoaPhieu').click(() => {
        sToast.confirm('Xác nhận xóa phiếu kết sổ cuối ngày?','',
            (confirmed) => {
                if (confirmed.isConfirmed) {
                    sToast.loading('Đang xóa phiếu. Vui lòng chờ...');
                    $.ajax({
                        url: '/api/quan-ly/phieu/xoa',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            maphieu: '{{ $phieu->maphieu }}',
                        }
                    }).done((result) => {
                        if (result.succ) {
                            sToast.notification(1,result.noti,() => {location.reload()})
                            let optionMessage = {type: 'xoa', maphieu: '{{ $phieu->maphieu }}', deleted_at: result.data.deleted_at};
                            top.postMessage(optionMessage);
                        }
                        else {
                            sToast.toast(0,result.noti);
                        }
                    });
                }
            })
    })
    @endif
    @endif
    @if($controls->printable)
    $('#btnInPhieu').click(() => {
        $('#boxPhieuIn').print()
    });
    @endif
</script>
</body>
