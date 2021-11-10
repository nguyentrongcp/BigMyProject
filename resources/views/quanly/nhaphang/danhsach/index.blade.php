@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Nhập Hàng</li>
    <li class="breadcrumb-item active">Danh Sách</li>
@stop

@section('body')
    <div class="content-wrapper">
        <section class="content">
            <div class="row py-3">
                <div class="col" style="max-width: calc(100% - 400px)">
                    <div class="card card-outline card-info mb-0 h-100">
                        <div class="card-body">
                            <div id="tblHangHoa"></div>
                        </div>
                    </div>
                </div>
                <div class="col" style="max-width: 400px">
                    <div class="card card-outline card-info thongtin-phieu mb-0">
                        <div class="card-body">
                            <div class="text-primary font-weight-bolder text-center c-pointer" id="lblMaPhieu" style="font-size: 25px">-----</div>
                            <div class="divider my-1"></div>
                            <div class="text-secondary font-weight-bolder text-center" id="lblChiNhanh" style="font-size: 15px">---</div>
                            <div class="divider my-1"></div>
                            <div class="text-center">
                                <button class="btn-sm btn bg-gradient-danger d-none" id="btnHuyPhieu">Hủy Phiếu</button>
                                <button class="btn-sm btn bg-gradient-info btnThongTin"
                                        data-toggle="modal" data-target="#modalDanhSachPhieuNhap">
                                    Lấy Phiếu Nhập
                                </button>
                            </div>
                            <div class="divider my-1"></div>
                            <div id="boxThongTin">
                                <div class="d-flex">
                                    <strong style="min-width: 62px" class="d-flex justify-content-between mr-1">
                                        <span>Tên NCC</span><span>:</span>
                                    </strong>
                                    <span class="ten"></span>
                                </div>
                                <div class="form-row">
                                    <div class="col-6 d-flex">
                                        <strong style="min-width: 62px" class="d-flex justify-content-between mr-1">
                                            <span>Mã NCC</span><span>:</span>
                                        </strong>
                                        <span class="ma"></span>
                                    </div>
                                    <div class="col-6 d-flex">
                                        <strong style="min-width: 68px" class="d-flex justify-content-between mr-1">
                                            <span>Điện thoại</span><span>:</span>
                                        </strong>
                                        <span class="dienthoai"></span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-6 d-flex">
                                        <strong style="min-width: 62px" class="d-flex justify-content-between mr-1">
                                            <span>Đ.Thoại 2</span><span>:</span>
                                        </strong>
                                        <span class="dienthoai2"></span>
                                    </div>
                                    <div class="col-6 d-flex">
                                        <strong style="min-width: 68px" class="d-flex justify-content-between mr-1">
                                            <span>Công nợ</span><span>:</span>
                                        </strong>
                                        <span class="congno text-danger font-weight-bolder"></span>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <strong class="text-nowrap">Địa chỉ: </strong>
                                    <span class="diachi ml-2"></span>
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
                            <div class="divider mt-1 mb-2"></div>
                            <div class="form-group">
                                <label>Ghi chú</label>
                                <textarea id="inpGhiChu" readonly rows="2" class="form-control" placeholder="Nhập ghi chú..."></textarea>
                            </div>
                            <div class="form-group mb-0">
                                <button class="btn bg-gradient-info font-weight-bolder"
                                        data-toggle="modal" data-target="#modalDanhSachPhieu"
                                        id="btnDanhSachPhieu">DANH SÁCH PHIẾU</button>
                                <button class="btn bg-gradient-success float-right font-weight-bolder" id="btnXemPhieu">XEM PHIẾU</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modalDanhSachPhieuNhap">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title w-100 text-center">Danh Sách Phiếu Nhập Hàng Chưa Duyệt</h4>
                </div>
                <div class="modal-body">
                    <div class="d-flex">
                        <div class="d-flex box-search-table flex-grow-1" data-target="tblDanhSachPhieuNhap">
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
                            <button class="btn bg-gradient-primary font-weight-bolder ml-auto" id="btnChonPhieu">
                                <i class="fas fa-check mr-1"></i>
                                Chọn Phiếu
                            </button>
                        </div>
                    </div>
                    <div id="tblDanhSachPhieuNhap" class="mt-1"></div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    @include('quanly.extends.danhsachphieu')
@stop

@section('js-custom')
    @include('quanly.nhaphang.danhsach.js')
@stop
