@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Quy Trình Lúa</li>
    <li class="breadcrumb-item active">Danh Mục Sản Phẩm</li>
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
                                    @if(in_array('quy-trinh-lua.san-pham.them-moi',$info->phanquyen) !== false)
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

    @if(in_array('quy-trinh-lua.san-pham.them-moi',$info->phanquyen) !== false)
        <div class="modal fade" id="modalThemMoi">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm Sản Phẩm Mới</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Chọn hàng hóa (chọn để copy thông tin sản phẩm)</label>
                            <select class="form-control selHangHoa"></select>
                        </div>
                        <div class="form-group required">
                            <label>Tên sản phẩm</label>
                            <input type="text" class="form-control inpTen" placeholder="Nhập tên sản phẩm...">
                            <span class="error invalid-feedback">Tên sản phẩm không được bỏ trống!</span>
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
                                    <label>Nhóm</label>
                                    <select class="form-control selNhom"></select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label>Dạng sản phẩm</label>
                                    <select class="form-control selDang"></select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label>Phân loại</label>
                                    <select class="form-control selPhanLoai"></select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group required">
                                    <label>Đơn giá</label>
                                    <input type="text" class="form-control numeral inpDonGia" placeholder="Nhập đơn giá...">
                                    <span class="error invalid-feedback">Đơn giá không hợp lệ!</span>
                                </div>
                            </div>
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
                    <h5 class="modal-title">Thông Tin Sản Phẩm</h5>
                </div>
                <div class="modal-body row-thongtin">
                    <div class="col-thongtin" data-field="ma">
                        <strong>Mã</strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="ten" data-title="Tên sản phẩm">
                        <strong>Tên <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="col-thongtin" data-field="donvitinh" data-title="Đơn vị tính">
                                <strong>Đơn vị tính <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="col-thongtin" data-field="nhom" data-title="Nhóm">
                                <strong>Nhóm <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="col-4">
                            <div class="col-thongtin" data-field="dang" data-title="Dạng sản phẩm">
                                <strong>Dạng sản phẩm <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="col-thongtin" data-field="phanloai" data-title="Phân loại">
                                <strong>Phân loại <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="col-thongtin" data-field="dongia" data-title="Đơn giá">
                                <strong>Đơn giá <i class="fa fa-edit text-info edit"></i></strong>
                                <span></span>
                            </div>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="ghichu" data-title="Ghi chú">
                        <strong>Ghi chú <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    @if(in_array('quy-trinh-lua.san-pham.action',$info->phanquyen) !== false)
                        <button type="button" class="btn bg-gradient-danger delete mr-auto">Xóa Thông Tin</button>
                    @endif
                    <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js-custom')
    @include('quanly.quytrinhlua.sanpham.js')
@stop
