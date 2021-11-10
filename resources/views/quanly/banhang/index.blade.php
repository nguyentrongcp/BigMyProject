@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Bán Hàng</li>
@stop

@section('style-custom')
    <style>
        .form-hanghoa-inline .select2-selection__arrow {
            display: none;
        }
        .form-hanghoa-inline .select2-selection__clear {
            margin-right: unset !important;
            width: 30px;
        }
        .form-hanghoa-inline .select2-selection.select2-selection--single {
            border: unset;
            border-radius: unset;
            border-bottom: 2px solid #ced4da;
            font-size: 1rem;
            padding: 0.55rem 0 0 0;
        }
        .form-hanghoa-inline .select2-selection__rendered {
            padding-right: unset;
        }
    </style>
@stop

@section('body')
    <div class="content-wrapper">
        <section class="content">
            <div class="row py-3">
                <div class="col" style="max-width: calc(100% - 400px)">
                    <div class="card card-outline card-info mb-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="form-row form-hanghoa-inline" id="boxHangHoa">
                                <div class="col-6">
                                    <select class="form-control" id="selHangHoa"></select>
                                </div>
                                <div class="col-6 d-flex">
                                    <input type="number" class="form-control non-border soluong" placeholder="Số lượng...">
                                    <input type="text" class="form-control non-border numeral giamgia ml-1" placeholder="Giảm giá...">
                                    <button id="btnThemHangHoa" style="width: 150px"
                                            class="btn-block h-100 btn bg-gradient-primary font-weight-bolder ml-1">Thêm</button>
                                </div>
                            </div>
                            <div class="divider my-1"></div>
                            <div id="tblHangHoa"></div>
                        </div>
                    </div>
                </div>
                <div class="col" style="max-width: 400px">
                    <div class="card card-outline card-info thongtin-phieu mb-0">
                        <div class="card-body">
{{--                            <div class="form-group mb-1">--}}
{{--                                <label>Chọn hàng hóa</label>--}}
{{--                                <select class="form-control" id="selHangHoa"></select>--}}
{{--                            </div>--}}
{{--                            <div class="form-row" id="boxHangHoa">--}}
{{--                                <div class="col-4">--}}
{{--                                    <input type="number" class="form-control non-border soluong" placeholder="Số lượng...">--}}
{{--                                </div>--}}
{{--                                <div class="col-5">--}}
{{--                                    <input type="text" class="form-control non-border numeral giamgia" placeholder="Giảm giá...">--}}
{{--                                </div>--}}
{{--                                <div class="col-3">--}}
{{--                                    <button id="btnThemHangHoa" class="btn-block h-100 btn bg-gradient-primary font-weight-bolder">Thêm</button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="divider mt-2 mb-2"></div>--}}
                            <div class="form-group mb-0" id="boxKhachHang">
                                <label class="w-100">
                                    Chọn khách hàng
                                    <span class="float-right text-primary c-pointer" data-toggle="modal" data-target="#modalThemMoi">Thêm mới</span>
                                </label>
                                <select class="form-control" id="selKhachHang"></select>
                                <div class="divider my-1"></div>
{{--                                <div class="d-flex">--}}
{{--                                    <strong style="min-width: 55px">Tên KH: </strong>--}}
{{--                                    <span class="lblTenKH"></span>--}}
{{--                                </div>--}}
                                <div class="form-row">
                                    <div class="col-6 d-flex">
                                        <strong style="min-width: 46px" class="d-flex justify-content-between mr-1">
                                            <span>Mã KH</span><span>:</span>
                                        </strong>
                                        <span class="lblMaKH"></span>
                                    </div>
                                    <div class="col-6">
                                        <strong class="mr-1">Công nợ: </strong>
                                        <span class="lblCongNoKH text-danger font-weight-bolder"></span>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <strong style="min-width: 46px" class="d-flex justify-content-between mr-1">
                                        <span>Địa chỉ</span><span>:</span>
                                    </strong>
                                    <span class="lblDiaChiKH text-justify"></span>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <button class="btn-sm btn bg-gradient-success btnThongTin">Thông Tin</button>
                                    <button class="btn-sm btn bg-gradient-danger btnThuCongNo d-none">Thu Công Nợ</button>
                                    <button class="btn-sm btn bg-gradient-info btnLichSu" data-toggle="modal" data-target="#modalLichSu">L.Sử Mua Hàng</button>
                                </div>
                            </div>
                            <div class="divider my-1"></div>
                            <div class="result-row">
                                <span>Tổng thành tiền:</span>
                                <span id="lblTongThanhTien">0</span>
                            </div>
                            <div class="divider mt-1"></div>
                            <div class="form-row">
                                <div class="col-6">
                                    <div class="result-row phuthu">
                                        <span>Phụ thu:</span>
                                        <input id="inpPhuThu" type="text" placeholder="0" class="form-control non-border numeral text-right">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="result-row giamgia">
                                        <span>Giảm giá:</span>
                                        <input id="inpGiamGia" type="text" placeholder="0" class="form-control non-border numeral text-right">
                                    </div>
                                </div>
                            </div>
                            <div class="divider my-1"></div>
                            <div class="result-row tienthanhtoan" style="font-size: 25px">
                                <span class="font-weight-bolder">Tiền thanh toán:</span>
                                <span id="lblTienThanhToan">0</span>
                            </div>
                            <div class="divider mt-1"></div>
                            <div class="result-row">
                                <span>Tiền khách đưa:</span>
                                <input id="inpTienKhachDua" type="text" placeholder="0" class="form-control non-border numeral text-right">
                            </div>
                            <div class="divider my-1"></div>
                            <div class="result-row font-weight-bolder">
                                <div id="boxTienThua" style="width: 100%">
                                    <span class="label">Tiền thừa:</span>
                                    <span class="float-right value">0</span>
                                </div>
                            </div>
                            <div class="divider mt-1 mb-2"></div>
                            <div class="form-group mb-0">
                                <label>Nhân viên tư vấn</label>
                                <select class="form-control" id="selNhanVienTuVan"></select>
                            </div>
                            <div class="divider my-2"></div>
                            <div class="form-group">
{{--                                <label>Ghi chú</label>--}}
                                <textarea id="inpGhiChu" rows="1" class="form-control" placeholder="Nhập ghi chú..."></textarea>
                            </div>
                            <div class="form-group mb-0">
                                <button class="btn bg-gradient-info font-weight-bolder" data-toggle="modal" data-target="#modalDanhSachPhieu"
                                        id="btnDanhSachPhieu">DANH SÁCH PHIẾU</button>
                                <button class="btn bg-gradient-success float-right font-weight-bolder" id="btnXemPhieu">XEM PHIẾU</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modalDanhSachPhieu">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title w-100 text-center">Danh Sách Phiếu Bán Hàng</h4>
                </div>
                <div class="modal-body">
                    <div class="d-flex">
                        <div class="d-flex box-search-table flex-grow-1" data-target="tblDanhSachPhieu">
                            <div class="input-search input-with-icon">
                                <input class="form-control non-border" type="text" placeholder="Nhập từ khóa tìm kiếm...">
                                <span class="icon">
                                    <i class="fa fa-times"></i>
                                </span>
                            </div>
                            <button class="btn bg-gradient-secondary excel font-weight-bolder">
                                <i class="fas fa-download mr-1"></i>
                                Xuất Excel
                            </button>
                        </div>
                        <div class="ml-auto d-flex">
                            <div style="width: 225px">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <input type="text" placeholder="Từ ngày..." class="form-control float-right" id="fromToDate" autocomplete="off">
                                </div>
                            </div>
                            <button style="width: 50px" class="btn bg-gradient-primary btnXem font-weight-bolder ml-1">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div id="tblDanhSachPhieu" class="mt-1"></div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="modalLichSu">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title w-100 text-center">Lịch Sử Mua Hàng</h4>
                </div>
                <div class="modal-body">
                    <div class="d-flex">
                        <div class="d-flex box-search-table flex-grow-1" data-target="tblLichSu">
                            <div class="input-search input-with-icon">
                                <input class="form-control non-border" type="text" placeholder="Nhập từ khóa tìm kiếm...">
                                <span class="icon">
                                    <i class="fa fa-times"></i>
                                </span>
                            </div>
                            <button class="btn bg-gradient-secondary excel font-weight-bolder">
                                <i class="fas fa-download mr-1"></i>
                                Xuất Excel
                            </button>
                        </div>
                    </div>
                    <div id="tblLichSu" class="mt-1"></div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="modalThuCongNo">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thu Công Nợ Khách Hàng</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Mã khách hàng</label>
                        <input type="text" class="form-control inpMa" readonly>
                    </div>
                    <div class="form-group">
                        <label>Tên khách hàng</label>
                        <input type="text" class="form-control inpTen" readonly>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" class="form-control inpDienThoai" readonly>
                    </div>
                    <div class="form-group">
                        <label>Công nợ hiện tại</label>
                        <input type="text" class="form-control inpCongNo text-danger font-weight-bolder" readonly>
                    </div>
                    <div class="form-group required">
                        <label>Nhập số tiền</label>
                        <input type="text" class="form-control inpSoTien numeral" placeholder="Nhập số tiền...">
                        <span class="error invalid-feedback">Số tiền không hợp lệ!</span>
                    </div>
                    <div class="form-group mb-0">
                        <label>Ghi chú</label>
                        <textarea rows="2" class="form-control inpGhiChu" placeholder="Nhập ghi chú..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-primary btnSubmit">Xem Phiếu</button>
                    <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>

    @include('quanly.danhmuc.hanghoa.index2')

    @include('quanly.danhmuc.khachhang.index2')

    @include('quanly.danhmuc.khachhang.index3')
@stop

@section('js-custom')
    @include('quanly.banhang.js')
    @include('quanly.danhmuc.khachhang.js2')
    @include('quanly.danhmuc.khachhang.js3')
@stop
