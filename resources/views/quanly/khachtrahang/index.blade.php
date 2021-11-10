@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Khách Trả Hàng</li>
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
                            <div id="boxThongTin">
                                <div class="form-group mb-1">
                                    <select class="form-control" id="selKhachHang"></select>
                                </div>
                                <div class="d-flex">
                                    <strong style="min-width: 62px" class="d-flex justify-content-between mr-1">
                                        <span>Tên KH</span><span>:</span>
                                    </strong>
                                    <span class="ten"></span>
                                </div>
                                <div class="form-row">
                                    <div class="col-6 d-flex">
                                        <strong style="min-width: 62px" class="d-flex justify-content-between mr-1">
                                            <span>Mã KH</span><span>:</span>
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
                                <div class="d-flex justify-content-between mt-1">
                                    <button class="btn-sm btn bg-gradient-success btnThongTin" disabled>Thông Tin</button>
                                    <button class="btn-sm btn bg-gradient-info btnLichSu"
                                            data-target="#modalLichSu" data-toggle="modal">Lịch Sử Mua Hàng</button>
                                </div>
                            </div>
                            <div class="divider my-1"></div>
                            <div class="result-row">
                                <span>Tổng thành tiền:</span>
                                <span id="lblTongThanhTien">0</span>
                            </div>
                            <div class="divider my-1"></div>
                            <div class="form-row">
                                <div class="col-6">
                                    <div class="result-row phuthu">
                                        <span>Phụ thu:</span>
                                        <span id="lblPhuThu">0</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="result-row giamgia">
                                        <span>Giảm giá:</span>
                                        <span id="lblGiamGia">0</span>
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
                                <textarea id="inpGhiChu" rows="2" class="form-control" placeholder="Nhập ghi chú..."></textarea>
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
                            <button class="btn bg-gradient-primary font-weight-bolder ml-auto" id="btnTraHang" disabled>
                                <i class="fas fa-check mr-1"></i>
                                Chọn Hàng
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

    <div class="modal fade" id="modalDanhSachPhieu">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title w-100 text-center">Danh Sách Phiếu Khách Trả Hàng</h4>
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

    <div class="modal fade" id="modalXemKH">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thông Tin Khách Hàng</h5>
                </div>
                <div class="modal-body row-thongtin">
                    <div class="col-thongtin" data-field="ten">
                        <strong>Tên <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="col-thongtin" data-field="ma">
                                <strong>Mã</strong>
                                <span></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="col-thongtin" data-field="danhxung">
                                <strong>Danh xưng <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="col-thongtin" data-field="dienthoai">
                                <strong>Số điện thoại <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="col-thongtin" data-field="dienthoai2">
                                <strong>Số điện thoại 2 <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="col-thongtin" data-field="congno">
                                <strong>Công nợ</strong>
                                <span></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="col-thongtin" data-field="lancuoi_muahang">
                                <strong>Lần cuối mua hàng</strong>
                                <span></span>
                            </div>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="diachi">
                        <strong>Địa chỉ <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="caytrong">
                        <strong>Cây trồng <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="dientich">
                        <strong>Diện tích (công) <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="ghichu">
                        <strong>Ghi chú <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js-custom')
    @include('quanly.khachtrahang.js')
@stop
