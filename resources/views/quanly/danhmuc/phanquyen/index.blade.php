@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Danh Mục</li>
    <li class="breadcrumb-item active">Phân Quyền</li>
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
                    <h5 class="modal-title">Thêm Phân Quyền Mới</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-6">
                            <div class="form-group required">
                                <label>Số thứ tự</label>
                                <input type="number" class="form-control inpSTT" placeholder="Nhập số thứ tự...">
                                <span class="error invalid-feedback"></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group required">
                                <label>Loại</label>
                                <select class="form-control selLoai">
                                    <option value="Role">Role</option>
                                    <option value="Page">Page</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label>Mã</label>
                        <input type="text" class="form-control inpMa" placeholder="Nhập mã quyền...">
                        <span class="error invalid-feedback"></span>
                    </div>
                    <div class="form-group required">
                        <label>Tên quyền</label>
                        <input type="text" class="form-control inpTen" placeholder="Nhập tên quyền...">
                        <span class="error invalid-feedback"></span>
                    </div>
                    <div class="form-group required">
                        <label>Tên chức năng</label>
                        <input type="text" class="form-control inpChucNang" placeholder="Nhập tên chức năng...">
                        <span class="error invalid-feedback">Tên chức năng không được bỏ trống!</span>
                    </div>
                    <div class="form-group">
                        <label>URL</label>
                        <input type="text" class="form-control inpUrl" placeholder="Nhập url...">
                    </div>
                    <div class="form-group mb-0">
                        <label>Ghi chú</label>
                        <textarea rows="2" class="form-control inpGhiChu" placeholder="Ghi chú..."></textarea>
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
@stop

@include('quanly.danhmuc.phanquyen.js')
