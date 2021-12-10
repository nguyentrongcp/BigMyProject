@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Danh Mục</li>
    <li class="breadcrumb-item active">Nông Dân</li>
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
                                    <button class="btn btn-default ml-1" id="btnLamMoi">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                    @if(in_array('danh-muc.nong-dan.them-moi',$info->phanquyen) !== false)
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

    @if(in_array('danh-muc.nong-dan.them-moi',$info->phanquyen) !== false)
        <div class="modal fade" id="modalThemMoi">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm Nông Dân Mới</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="col-9">
                                <div class="form-group required">
                                    <label>Tên nông dân</label>
                                    <input type="text" class="form-control inpTen" placeholder="Nhập tên nông dân...">
                                    <span class="error invalid-feedback">Tên nông dân không được bỏ trống!</span>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label>Danh xưng</label>
                                    <select class="form-control selDanhXung"></select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-6">
                                <div class="form-group required">
                                    <label>Số điện thoại</label>
                                    <input type="text" class="form-control inpDienThoai" placeholder="Nhập số điện thoại...">
                                    <span class="error invalid-feedback">Số điện thoại không được bỏ trống!</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Số điện thoại 2</label>
                                    <input type="text" class="form-control inpDienThoai2" placeholder="Nhập số điện thoại 2...">
                                </div>
                            </div>
                        </div>
                        <div class="diachi-container">
                            <div class="form-group">
                                <label>Chọn tỉnh/thành phố</label>
                                <select class="form-group tinh selTinh"></select>
                            </div>
                            <div class="form-group">
                                <label>Chọn quận/huyện/thị xã</label>
                                <select class="form-group huyen selHuyen"></select>
                            </div>
                            <div class="form-group">
                                <label>Chọn xã/phường/thị trấn</label>
                                <select class="form-group xa selXa"></select>
                            </div>
                            <div class="form-group">
                                <label>Địa chỉ cụ thể</label>
                                <textarea rows="2" class="form-control diachi inpDiaChi" placeholder="Nhập địa chỉ cụ thể..."></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Cây trồng</label>
                            <select class="form-control selCayTrong" multiple></select>
                        </div>
                        <div class="form-group">
                            <label>Diện tích (công)</label>
                            <input type="text" class="form-control inpDienTich" placeholder="Nhập diện tích cây trồng...">
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
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thông Tin Nông Dân</h5>
                </div>
                <div class="modal-body row-thongtin">
                    <div class="col-thongtin" data-field="ten" data-title="Tên nông dân">
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
                            <div class="col-thongtin" data-field="danhxung" data-title="Danh xưng">
                                <strong>Danh xưng <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="col-thongtin" data-field="dienthoai" data-title="Số điện thoại">
                                <strong>Số điện thoại <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="col-thongtin" data-field="dienthoai2" data-title="Số điện thoại 2">
                                <strong>Số điện thoại 2 <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                    </div>
{{--                    <div class="divider my-3"></div>--}}
{{--                    <div class="form-row">--}}
{{--                        <div class="col-6">--}}
{{--                            <div class="col-thongtin" data-field="congno">--}}
{{--                                <strong>Công nợ</strong>--}}
{{--                                <span class="text-danger font-weight-bolder"></span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-6">--}}
{{--                            <div class="col-thongtin" data-field="lancuoi_muahang">--}}
{{--                                <strong>Lần cuối mua hàng</strong>--}}
{{--                                <span></span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="diachi" data-title="Địa chỉ">
                        <strong>Địa chỉ <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="caytrong" data-title="Cây trồng">
                        <strong>Cây trồng <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="dientich" data-title="Diện tích (công)">
                        <strong>Diện tích (công) <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="ghichu" data-title="Ghi chú">
                        <strong>Ghi chú <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    @if(in_array('danh-muc.nong-dan.action',$info->phanquyen) !== false)
                        <button type="button" class="btn bg-gradient-danger delete mr-auto">Xóa Thông Tin</button>
                    @endif
                    <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js-custom')
    @include('quanly.danhmuc.nongdan.js')
@stop
