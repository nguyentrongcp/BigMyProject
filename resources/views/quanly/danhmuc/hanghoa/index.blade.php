@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Danh Mục</li>
    <li class="breadcrumb-item active">Hàng Hóa</li>
@stop

@section('body')
    <div class="content-wrapper">
        <section class="content">
            <div class="row py-3">
                <div class="col">
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
                                <div class="ml-auto d-flex">
                                    <button class="btn btn-default" id="btnLamMoi">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                    <button class="btn bg-gradient-info font-weight-bolder ml-1" data-toggle="modal" data-target="#modalQuyDoi">
                                        Danh Mục Quy Đổi
                                    </button>
                                    @if($info->id == '1000000000')
                                    <button class="btn bg-gradient-primary font-weight-bolder ml-1" data-toggle="modal" data-target="#modalThemMoi">
                                        Thêm Mới
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <div id="tblDanhSach"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @if($info->id == '1000000000')
    <div class="modal fade" id="modalThemMoi">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Hàng Hóa Mới</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Mã PM mới</label>
                        <input type="text" class="form-control inpMa" placeholder="Nhập mã bên PM mới...">
                    </div>
                    <div class="form-group required">
                        <label>Tên hàng hóa</label>
                        <input type="text" class="form-control inpTen" placeholder="Nhập tên hàng hóa...">
                        <span class="error invalid-feedback">Tên hàng hóa không được bỏ trống!</span>
                    </div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Đơn vị tính</label>
                                <select class="form-control selDonViTinh"></select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Quy cách</label>
                                <input type="number" class="form-control inpQuyCach" placeholder="1">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Nhóm</label>
                                <select class="form-control selNhom"></select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Dạng hàng hóa</label>
                                <select class="form-control selDang"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Giá nhập</label>
                                <input type="text" class="form-control numeral inpGiaNhap" placeholder="0">
                                <span class="error invalid-feedback">Giá nhập không hợp lệ!</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group required">
                                <label>Đơn giá</label>
                                <input type="text" class="form-control numeral inpDonGia" placeholder="Nhập đơn giá...">
                                <span class="error invalid-feedback">Đơn giá không hợp lệ!</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Hoạt chất</label>
                        <textarea rows="2" class="form-control inpHoatChat" placeholder="Nhập hoạt chất..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Công dụng</label>
                        <textarea rows="2" class="form-control inpCongDung" placeholder="Nhập công dụng..."></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label>Ghi chú</label>
                        <textarea rows="2" class="form-control inpGhiChu" placeholder="Nhập ghi chú..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="custom-control custom-checkbox mr-auto">
                        <input class="custom-control-input" type="checkbox" id="chkLienTuc">
                        <label class="custom-control-label" for="chkLienTuc">
                            Thêm liên tục
                        </label>
                    </div>
                    <button type="button" class="btn bg-gradient-primary btnSubmit">Xác Nhận</button>
                    <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalThemQuyDoi">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Quy Đổi Hàng Hóa</h5>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label>Tên hàng hóa</label>
                            <input type="text" class="form-control inpTen" readonly>
                        </div>
                        <div class="form-group">
                            <label>Đơn vị tính</label>
                            <input type="text" class="form-control inpDonViTinh" readonly>
                        </div>
                        <div class="form-group required">
                            <label>Tên quy đổi</label>
                            <input type="text" class="form-control inpTenQuyDoi" placeholder="Nhập tên quy đổi...">
                            <span class="error invalid-feedback">Tên quy đổi không được bỏ trống!</span>
                        </div>
                        <div class="form-group">
                            <label>Đơn vị quy đổi</label>
                            <select class="form-control selDonViQuyDoi"></select>
                        </div>
                        <div class="form-row">
                            <div class="col-6">
                                <div class="form-group required mb-0">
                                    <label>Số lượng quy đổi</label>
                                    <input type="number" class="form-control inpSoLuong" placeholder="Nhập số lượng quy đổi...">
                                    <span class="error invalid-feedback">Số lượng quy đổi không hợp lệ!</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group required mb-0">
                                    <label>Giá quy đổi</label>
                                    <input type="text" class="form-control inpDonGia numeral" placeholder="Nhập giá quy đổi...">
                                    <span class="error invalid-feedback">Giá quy đổi không hợp lệ!</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-primary btnSubmit">Xác Nhận</button>
                    <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @include('quanly.danhmuc.hanghoa.index2')

    <div class="modal fade" id="modalQuyDoi">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title w-100 text-center">Danh Mục Quy Đổi Hàng Hóa</h4>
                </div>
                <div class="modal-body">
                    <div class="d-flex">
                        <div class="d-flex box-search-table flex-grow-1" data-target="tblQuyDoi">
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
                            <div class="ml-auto">
                                <button class="btn btn-default btnLamMoi">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="tblQuyDoi" class="mt-1"></div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@stop

@include('quanly.danhmuc.hanghoa.js')
