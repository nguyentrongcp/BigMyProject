@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Danh Mục</li>
    <li class="breadcrumb-item active">Cừa Hàng</li>
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
                                    @if(in_array('danh-muc.chi-nhanh.them-moi',$info->phanquyen) !== false)
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

    @if(in_array('danh-muc.chi-nhanh.them-moi',$info->phanquyen) !== false)
    <div class="modal fade" id="modalThemMoi">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Cửa Hàng Mới</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group required">
                        <label>Tên cửa hàng</label>
                        <input type="text" class="form-control inpTen" placeholder="Tên cửa hàng...">
                        <span class="error invalid-feedback">Tên cửa hàng không được bỏ trống!</span>
                    </div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="form-group required">
                                <label>Điện thoại cửa hàng</label>
                                <input type="text" class="form-control inpDienThoai" placeholder="Số điện thoại cửa hàng...">
                                <span class="error invalid-feedback">Số điện thoại cửa hàng không được bỏ trống!</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Điện thoại tổng đài</label>
                                <input type="text" class="form-control inpDienThoai2" placeholder="Số điện thoại tổng đài...">
                            </div>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label>Địa chỉ</label>
                        <textarea rows="2" class="form-control inpDiaChi" placeholder="Địa chỉ cửa hàng..."></textarea>
                        <span class="error invalid-feedback">Địa chỉ cửa hàng không được bỏ trống!</span>
                    </div>
                    <div class="form-group">
                        <label>Loại cửa hàng</label>
                        <select class="form-control selLoai">
                            <option value="cuahang">Cửa hàng</option>
                            <option value="congty">Công ty</option>
                            <option value="khohanghong">Kho hàng hỏng</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sao chép giá từ cửa hàng</label>
                        <select class="form-control selCopyGia"></select>
                        <span class="error invalid-feedback">Cửa hàng này không tồn tại hoặc đã bị xóa!</span>
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

    <div class="modal fade" id="modalXem">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thông Tin Cửa Hàng</h5>
                </div>
                <div class="modal-body row-thongtin">
                    <div class="col-thongtin" data-field="ten" data-title="Tên cửa hàng">
                        <strong>Tên <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="col-thongtin" data-field="dienthoai" data-title="Điện thoại cửa hàng">
                                <strong>Điện thoại cửa hàng <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="col-thongtin" data-field="dienthoai2" data-title="Điện thoại tổng đài">
                                <strong>Điện thoại tổng đài <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="diachi" data-title="Địa chỉ">
                        <strong>Địa chỉ <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="loai" data-title="Loại">
                        <strong>Loại <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="ghichu" data-title="Ghi chú">
                        <strong>Ghi chú <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    @if(in_array('danh-muc.chi-nhanh.action',$info->phanquyen) !== false)
                    <button type="button" class="btn bg-gradient-danger delete mr-auto">Xóa Thông Tin</button>
                    @endif
                    <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>
@stop

@include('quanly.danhmuc.chinhanh.js')
