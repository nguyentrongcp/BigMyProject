@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Danh Mục</li>
    <li class="breadcrumb-item active">Nhân Viên</li>
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
                    <h5 class="modal-title">Thêm Nhân Viên Mới</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group required">
                        <label>Tên nhân viên</label>
                        <input type="text" class="form-control inpTen" placeholder="Nhập tên nhân viên...">
                        <span class="error invalid-feedback">Tên nhân viên không được bỏ trống!</span>
                    </div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="form-group required">
                                <label>Điện thoại</label>
                                <input type="text" class="form-control inpDienThoai" placeholder="Nhập số điện thoại...">
                                <span class="error invalid-feedback">Số điện thoại không được bỏ trống!</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group required">
                                <label>Ngày sinh</label>
                                <div class="input-group date" data-target-input="nearest" id="boxNgaySinh">
                                    <input type="text" class="form-control inpNgaySinh datetimepicker-input" data-target="#boxNgaySinh" placeholder="Ngày sinh...">
                                    <div class="input-group-append" data-target="#boxNgaySinh" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                                <span class="error invalid-feedback errorNgaySinh">Ngày sinh không được bỏ trống!</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Chức vụ</label>
                        <select class="form-control selLoai"></select>
                    </div>
                    <div class="form-group">
                        <label>Cửa hàng</label>
                        <select class="form-control selChiNhanh"></select>
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
    @endif

    <div class="modal fade" id="modalXem">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thông Tin Nhân Viên</h5>
                </div>
                <div class="modal-body row-thongtin">
                    <div class="form-row">
                        <div class="col-6">
                            <div class="col-thongtin" data-field="ma">
                                <strong>Mã</strong>
                                <span></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="col-thongtin" data-field="ten" data-title="Tên nhân viên">
                                <strong>Tên <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="col-thongtin" data-field="taikhoan">
                                <strong>Tài khoản</strong>
                                <span></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="col-thongtin" data-field="dienthoai" data-title="Số điện thoại">
                                <strong>Điện thoại <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="col-thongtin" data-field="ngaysinh" data-title="Ngày sinh">
                                <strong>Ngày sinh <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="col-thongtin" data-field="chucvu" data-title="Chức vụ">
                                <strong>Chức vụ <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="chinhanh_id" data-title="Cửa hàng">
                        <strong>Cửa hàng <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="ghichu" data-title="Ghi chú">
                        <strong>Ghi chú <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    @if($info->id == '1000000000')
                    <button type="button" class="btn bg-gradient-danger delete mr-auto">Xóa Thông Tin</button>
                    @endif
                    <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>
@stop

@include('quanly.danhmuc.nhanvien.js')
