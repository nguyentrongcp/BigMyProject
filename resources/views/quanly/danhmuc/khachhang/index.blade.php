@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Danh Mục</li>
    <li class="breadcrumb-item active">Khách Hàng</li>
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
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons" id="boxFilter">
                                        <label class="btn bg-olive d-flex align-items-center" data-value="?is_congno=1">
                                            <input type="radio">
                                            Công Nợ
                                        </label>
                                        <label class="btn bg-olive active d-flex align-items-center" data-value="">
                                            <input type="radio" checked="">
                                            Danh Sách
                                        </label>
                                    </div>
                                    <button class="btn btn-default ml-1" id="btnLamMoi">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                    <button class="btn bg-gradient-primary font-weight-bolder ml-1" data-toggle="modal" data-target="#modalThemMoi">
                                        Thêm Mới
                                    </button>
                                </div>
                            </div>
                            <div id="tblDanhSach"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modalActionCongNo">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
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

    @include('quanly.danhmuc.khachhang.index2')

    @include('quanly.danhmuc.khachhang.index3')
@stop

@section('js-custom')
    @include('quanly.danhmuc.khachhang.js2')
    @include('quanly.danhmuc.khachhang.js')
    @include('quanly.danhmuc.khachhang.js3')
@stop
