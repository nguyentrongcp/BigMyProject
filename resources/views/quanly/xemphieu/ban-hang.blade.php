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
            .text-giamgia, .text-tienthanhtoan, .text-secondary, .text-success, .text-danger {
                color: black !important;
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
        @if($controls->deletable)
            @if($phieu->trashed())
                <button id="btnPhucHoi" class="btn btn-success btn-sm">Phục Hồi</button>
            @else
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
                        PHIẾU BÁN HÀNG
                    </div>
                    <div class="text-center mb-3">
                        <span>{{ date_format(date_create($phieu->created_at),'d-m-Y H:i:s') }}</span>
                    </div>
                    <div>
                        <div>
                            <div>
                                <span>
                                    <span class="fw-bolder">Mã phiếu: </span>{{ $phieu->maphieu }}</span>
                            </div>
                            <div>
                                <span class="fw-bolder">Khách Hàng: </span>
                                <span>{{ $phieu->doituong->ten }}</span>
                            </div>
                            <div>
                                <span class="fw-bolder">Điện Thoại: </span>
                                <span>{{ isset($phieu->doituong->dienthoai) ? $phieu->doituong->dienthoai : '' }}</span>
                            </div>
                            <div style="text-align: justify">
                                <span class="fw-bolder">Địa Chỉ: </span>
                                <span>{{ isset($phieu->doituong->diachi) ? $phieu->doituong->diachi : '' }}</span>
                            </div>
                            <div>
                                <span class="fw-bolder">Nhân viên tư vấn: </span>
                                <span>{{ $phieu->nhanvien_tuvan->ten }}</span>
                            </div>
                            <div>
                                {!! $phieu->ghichu != '' ? '<span style="fw: bolder">Ghi chú: </span>'.$phieu->ghichu : '' !!}
                            </div>
                        </div>
                        <div class="mt-2">
                            <table style="font-size: 11px !important" class="table table-bordered w-100 mb-0">
                                <thead>
                                <tr>
                                    <th class="text-center">STT</th>
{{--                                    @if(isset($phieu->dshanghoa[0]->hanghoa->mamoi))--}}
{{--                                    <th class="print-hidden">MÃ MỚI</th>--}}
{{--                                    @endif--}}
                                    <th>TÊN</th>
                                    <th>ĐVT</th>
                                    <th class="text-end">GIÁ</th>
                                    <th class="text-end">GIẢM</th>
                                    <th class="text-end">SL</th>
                                    <th class="text-end">T.TIỀN</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($phieu->dshanghoa as $key => $value)
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
{{--                                        @if(isset($value->hanghoa->mamoi))--}}
{{--                                        <td class="print-hidden">{{ $value->hanghoa->mamoi }}</td>--}}
{{--                                        @endif--}}
                                        <td>{{ $value->hanghoa->ten }}</td>
                                        <td>{{ $value->hanghoa->donvitinh }}</td>
                                        <td class="text-end">{{ number_format($value->dongia) }}</td>
                                        <td class="text-end">{{ number_format($value->giamgia) }}</td>
                                        <td class="text-end">{{ $value->soluong }}</td>
                                        <td class="text-end">{{ number_format($value->thanhtien) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <th colspan="6" class="text-end">TỔNG</th>
                                <th class="text-end">{{ number_format($phieu->tongthanhtien) }}</th>
                                </tfoot>
                            </table>
                            <div class="mt-2 row fw-bolder">
                                <div class="col-6">
                                    <div class="d-flex">
                                        <div>
                                            TỔNG GIẢM GIÁ:
                                        </div>
                                        <div class="ms-auto text-giamgia">{{ number_format($phieu->giamgia) }}</div>
                                    </div>
                                    <div class="d-flex">
                                        <div>
                                            TỔNG PHỤ THU:
                                        </div>
                                        <div class="ms-auto text-secondary">{{ number_format($phieu->phuthu) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex">
                                        <div>
                                            TIỀN THANH TOÁN:
                                        </div>
                                        <div class="ms-auto text-tienthanhtoan">{{ number_format($phieu->tienthanhtoan) }}</div>
                                    </div>
                                    <div class="d-flex">
                                        <div>
                                            TIỀN KHÁCH ĐƯA:
                                        </div>
                                        <div class="ms-auto">{{ number_format($phieu->tienkhachdua) }}</div>
                                    </div>
                                    <div class="d-flex">
                                        <div>
                                            {{ $phieu->tienthua < 0 ? 'TIỀN CÒN NỢ:' : 'TIỀN THỪA:' }}
                                        </div>
                                        <div class="ms-auto{{ $phieu->tienthua >= 0 ? ' text-success' : ' text-danger' }}">{{ number_format(abs($phieu->tienthua)) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between">
                            <div class="w-50 text-center">
                                <p class="fw-bolder m-0">Khách Hàng</p>
                                <p class="m-0"><i>(Ký tên)</i></p>
                                @if($phieu->doituong->ma != 'KH000000')
                                    <p class="mb-0" style="margin-top: 80px;">{{ $phieu->doituong->ten }}</p>
                                @endif
                            </div>
                            <div class="w-50 text-center">
                                <p class="fw-bolder m-0">Người Lập Phiếu</p>
                                <p class="m-0"><i>(Ký tên)</i></p>
                                <p class="mb-0" style="margin-top: 80px;">{{ $phieu->nhanvien->ten }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-center fw-bolder pt-1" style="border-top: 1px solid #dee2e6">HAI LÚA xin cảm ơn QUÝ KHÁCH!</div>
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

    let interval = setInterval(() => {
        if ($(window).height() > 0) {
            top.postMessage({type: 'autoHeight', height: $(window).height() + 'px'});
            clearInterval(interval);
        }
    }, 10);

    @if($controls->deletable)
        @if(!$phieu->trashed())
        $('#btnXoaPhieu').click(() => {
            sToast.confirm('Xác nhận xóa phiếu bán hàng?','',
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
        @else
        $('#btnPhucHoi').click(() => {
            sToast.confirm('Xác nhận phục hồi phiếu bán hàng?','',
                (confirmed) => {
                    if (confirmed.isConfirmed) {
                        sToast.loading('Đang phục hồi phiếu. Vui lòng chờ...');
                        $.ajax({
                            url: '/api/quan-ly/phieu/phuc-hoi',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                maphieu: '{{ $phieu->maphieu }}',
                            }
                        }).done((result) => {
                            if (result.succ) {
                                sToast.notification(1,result.noti,() => {location.reload()})
                                let optionMessage = {type: 'phuc-hoi', maphieu: '{{ $phieu->maphieu }}'};
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
