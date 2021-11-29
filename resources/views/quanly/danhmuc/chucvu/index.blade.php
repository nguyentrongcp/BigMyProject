@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Danh Mục</li>
    <li class="breadcrumb-item active">Chức Vụ</li>
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
                            </div>
                            <div id="tblDanhSach"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modalPhanQuyen">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title w-100 text-center">Danh Sách Phân Quyền Chức Vụ "<span class="title"></span>"</h4>
                </div>
                <div class="modal-body">
                    <div class="d-flex">
                        <div class="d-flex box-search-table flex-grow-1" data-target="tblPhanQuyen">
                            <div class="input-search input-with-icon">
                                <input class="form-control non-border" type="text" placeholder="Nhập từ khóa tìm kiếm...">
                                <span class="icon">
                                    <i class="fa fa-times"></i>
                                </span>
                            </div>
                            <div class="d-flex align-items-end ml-auto" style="font-size: 20px">
                                Hiện tại đang có <span class="lblSoQuyen text-info mx-2"></span> quyền
                            </div>
                        </div>
                    </div>
                    <div id="tblPhanQuyen" class="mt-1"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn bg-gradient-primary mr-auto btnChinhSua font-weight-bolder">
                        <i class="fas fa-edit mr-1"></i>
                        Chỉnh Sửa
                    </button>
                    <button type="button" class="btn bg-gradient-primary btnSubmit d-none">Xác Nhận</button>
                    <button type="button" class="btn bg-gradient-danger btnCancel d-none">Hủy Bỏ</button>
                    <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@stop

@section('js-custom')
    @include('quanly.danhmuc.chucvu.js')
@stop
