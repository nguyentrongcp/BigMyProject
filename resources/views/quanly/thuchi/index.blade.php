@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Thu Chi</li>
@stop

@section('body')
    <div class="content-wrapper">
        <section class="content">
            <div class="row py-3">
                <div class="col" style="max-width: calc(100% - 400px)">
                    <div class="card card-outline card-info mb-0">
                        <div class="card-body">
                            <div class="d-flex box-search-table mb-1" data-target="tblDanhSach">
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
                            <div id="tblDanhSach"></div>
                        </div>
                    </div>
                </div>
                <div class="col" style="max-width: 400px">
                    <div class="card card-outline card-info thongtin-phieu mb-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-sm bg-gradient-primary font-weight-bolder" id="btnDanhSach"
                                        data-toggle="modal" data-target="#modalDanhSachPhieu">
                                    Danh Sách Phiếu
                                </button>
                                <button class="btn btn-sm bg-gradient-success font-weight-bolder" id="btnLapPhieuThu">
                                    Lập Phiếu Thu
                                </button>
                                <button class="btn btn-sm bg-gradient-danger font-weight-bolder" id="btnLapPhieuChi">
                                    Lập Phiếu Chi
                                </button>
                            </div>
                            <div class="divider my-2"></div>
                            <div style="font-size: 25px" id="lblNgay" class="text-primary font-weight-bolder text-center"></div>
                            <div class="divider my-2"></div>
                            <div id="boxThongTin">
                                <div class="form-row">
                                    <div class="col-6 d-flex">
                                        <strong style="min-width: 50px" class="d-flex justify-content-between mr-1">
                                            <span>Đầu kỳ</span><span>:</span>
                                        </strong>
                                        <span class="dauky font-weight-bolder ml-auto"></span>
                                    </div>
                                    <div class="d-flex col-6">
                                        <strong style="min-width: 60px" class="d-flex justify-content-between mr-1">
                                            <span>Tổng thu</span><span>:</span>
                                        </strong>
                                        <span class="tongthu text-success font-weight-bolder ml-auto"></span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-6 d-flex">
                                        <strong style="min-width: 50px" class="d-flex justify-content-between mr-1">
                                            <span>Cuối kỳ</span><span>:</span>
                                        </strong>
                                        <span class="cuoiky font-weight-bolder text-info ml-auto"></span>
                                    </div>
                                    <div class="d-flex col-6">
                                        <strong style="min-width: 60px" class="d-flex justify-content-between mr-1">
                                            <span>Tổng chi</span><span>:</span>
                                        </strong>
                                        <span class="tongchi font-weight-bolder text-danger ml-auto"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="divider my-2"></div>
                            <div class="form-group">
                                <label>Chọn ngày</label>
                                <div class="input-group date" id="inpNgay" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" data-target="#inpNgay" placeholder="Chọn ngày...">
                                    <div class="input-group-append" data-target="#inpNgay" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Chọn cửa hàng</label>
                                <select class="form-control" id="selChiNhanh"></select>
                            </div>
                            <div class="form-group mb-0">
                                <button class="btn bg-gradient-primary font-weight-bolder" disabled id="btnKetSo">
                                    <i class="fa fa-book mr-1"></i>
                                    KẾT SỔ
                                </button>
                                <button class="btn bg-gradient-success float-right font-weight-bolder" id="btnLoc">
                                    <i class="fa fa-filter mr-1"></i>
                                    LỌC DỮ LIỆU
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modalLapPhieu">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lập Phiếu Thu</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Chọn khoản mục</label>
                        <select class="form-control selKhoanMuc"></select>
                    </div>
                    <div class="form-group">
                        <label class="w-100 required">
                            Chọn đối tượng
                            <span class="float-right text-primary c-pointer" data-toggle="modal" data-target="#modalThemMoi">Thêm mới</span>
                        </label>
                        <select class="form-control selDoiTuong"></select>
                        <span class="error invalid-feedback">Bạn chưa chọn đối tượng!</span>
                    </div>
                    <div class="form-group">
                        <label class="required">Số tiền</label>
                        <input type="text" class="form-control numeral inpSoTien" placeholder="Nhập số tiền...">
                        <span class="error invalid-feedback">Số tiền không hợp lệ!</span>
                    </div>
                    <div class="form-group">
                        <label class="required">Nội dung</label>
                        <textarea rows="2" class="form-control inpNoiDung" placeholder="Nhập nội dung..."></textarea>
                        <span class="error invalid-feedback">Bạn chưa nhập nội dung!</span>
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

    @include('quanly.danhmuc.doituong.index2')

    @include('quanly.extends.danhsachphieu')

    <div class="modal fade" id="modalDanhSachPhieu2">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title w-100 text-center"></h4>
                </div>
                <div class="modal-body">
                    <div class="d-flex">
                        <div class="d-flex box-search-table flex-grow-1" data-target="tblDanhSachPhieu2">
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
                    <div id="tblDanhSachPhieu2" class="mt-1"></div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@stop

@section('js-custom')
    @include('quanly.thuchi.js')
    @include('quanly.danhmuc.doituong.js2')
@stop
