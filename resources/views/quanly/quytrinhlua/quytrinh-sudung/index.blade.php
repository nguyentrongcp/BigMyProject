@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Quy Trình Lúa</li>
    <li class="breadcrumb-item active">Quy Trình Sử Dụng Phân Thuốc</li>
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
                                    <div style="width: 300px">
                                        <select id="selMuaVu"></select>
                                    </div>
                                    <button class="btn btn-default ml-1" id="btnLamMoi">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                    @if(in_array('quy-trinh-lua.quy-trinh.them-moi',$info->phanquyen) !== false)
{{--                                        <button class="btn bg-gradient-success font-weight-bolder ml-1" id="btnThemQuyTrinh">--}}
{{--                                            Thêm Nhóm--}}
{{--                                        </button>--}}
                                        <button class="btn bg-gradient-primary font-weight-bolder ml-1" data-toggle="modal" data-target="#modalThemMoi">
                                            Thêm Mới
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="card card-primary card-outline card-tabs">
                                <div class="card-header p-0 pt-1 border-bottom-0">
                                    <ul class="nav nav-tabs" role="tablist" id="boxTabMain">
                                        <li class="nav-item">
                                            <a class="nav-link active font-weight-bolder" data-title="Phân bón" id="tabPhanBon" data-toggle="pill" href="#boxPhanBon" role="tab">
                                                Bảng Quy Trình Sử Dụng Phân Bón
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link font-weight-bolder" data-title="Thuốc" id="tabThuoc" data-toggle="pill" href="#boxThuoc" role="tab">
                                                Bảng Quy Trình Sử Dụng Thuốc
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="boxPhanBon" role="tabpanel">
                                            <div id="tblDanhSachPhan"></div>
                                        </div>
                                        <div class="tab-pane fade" id="boxThuoc" role="tabpanel">
                                            <div id="tblDanhSachThuoc"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @if(in_array('quy-trinh-lua.quy-trinh.them-moi',$info->phanquyen) !== false)
        <div class="modal fade" id="modalThemMoi">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm Quy Trình Mới</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group required">
                            <label>Mùa vụ</label>
                            <input type="text" class="form-control inpMuaVu" readonly placeholder="Bạn chưa chọn mùa vụ...">
                        </div>
                        <div class="form-group required">
                            <label class="w-100">Chọn giai đoạn<span class="float-right">
                                    @if(in_array('quy-trinh-lua.quy-trinh.action',$info->phanquyen) !== false)
                                    <span class="text-danger mr-2 c-pointer btnCapNhatGiaiDoan btnXoaGiaiDoan">Xóa</span>
                                    @endif
                                    @if(in_array('quy-trinh-lua.quy-trinh.chinh-sua',$info->phanquyen) !== false)
                                    <span class="text-success mr-2 c-pointer btnCapNhatGiaiDoan btnSuaGiaiDoan">Chỉnh sửa</span>
                                    @endif
                                    <span class="text-primary c-pointer" data-toggle="modal" data-target="#modalThemGiaiDoan">Thêm mới</span>
                                </span>
                            </label>
                            <select class="form-control selGiaiDoan"></select>
                        </div>
                        <div class="form-row">
                            <div class="col-4">
                                <div class="form-group required">
                                    <label>Từ (ngày)</label>
                                    <input type="number" class="form-control inpFrom" readonly placeholder="Từ (ngày)...">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group required">
                                    <label>Đến (ngày)</label>
                                    <input type="number" class="form-control inpTo" readonly placeholder="Đến (ngày)...">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group required">
                                    <label>Phân loại</label>
                                    <input type="text" class="form-control inpPhanLoai" readonly placeholder="Phân loại...">
                                </div>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label>Chọn sản phẩm</label>
                            <select class="form-control selSanPham"></select>
                            <span class="error invalid-feedback">Bạn chưa chọn sản phẩm!</span>
                        </div>
                        <div class="form-group required">
                            <label>Công dụng</label>
                            <textarea rows="2" class="form-control inpCongDung" placeholder="Nhập công dụng..."></textarea>
                            <span class="error invalid-feedback">Công dụng không được bỏ trống!</span>
                        </div>
                        <div class="form-group required">
                            <label>Số lượng</label>
                            <input type="number" class="form-control inpSoLuong" placeholder="Nhập số lượng...">
                            <span class="error invalid-feedback">Số lượng không hợp lệ!</span>
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
        <div class="modal fade modal-secondary" id="modalThemGiaiDoan">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm Giai Đoạn Mới</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group required">
                            <label>Mùa vụ</label>
                            <input type="text" class="form-control inpMuaVu" readonly placeholder="Bạn chưa chọn mùa vụ...">
                        </div>
                        <div class="form-group required">
                            <label>Tên giai đoạn</label>
                            <input type="text" class="form-control inpTen" placeholder="Nhập tên giai đoạn...">
                            <span class="error invalid-feedback">Tên giai đoạn không được bỏ trống!</span>
                        </div>
                        <div class="form-row">
                            <div class="col-6">
                                <div class="form-group required">
                                    <label>Từ bao nhiêu ngày</label>
                                    <input type="number" class="form-control inpFrom" placeholder="Từ bao nhiêu ngày...">
                                    <span class="error invalid-feedback">Số ngày không hợp lệ!</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group required">
                                    <label>Đến bao nhiêu ngày</label>
                                    <input type="number" class="form-control inpTo" placeholder="Đến bao nhiêu ngày...">
                                    <span class="error invalid-feedback">Số ngày không hợp lệ!</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label>Chọn phân loại</label>
                            <select class="form-control selPhanLoai">
                                <option value="Phân bón">Phân bón</option>
                                <option value="Thuốc">Thuốc</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-primary btnSubmit">Xác Nhận</button>
                        <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade modal-secondary" id="modalSuaGiaiDoan">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Sửa thông tin giai đoạn</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group required">
                            <label>Tên giai đoạn</label>
                            <input type="text" class="form-control inpTen" placeholder="Nhập tên giai đoạn...">
                            <span class="error invalid-feedback">Tên giai đoạn không được bỏ trống!</span>
                        </div>
                        <div class="form-row">
                            <div class="col-6">
                                <div class="form-group required">
                                    <label>Từ bao nhiêu ngày</label>
                                    <input type="number" class="form-control inpFrom" placeholder="Từ bao nhiêu ngày...">
                                    <span class="error invalid-feedback">Số ngày không hợp lệ!</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group required">
                                    <label>Đến bao nhiêu ngày</label>
                                    <input type="number" class="form-control inpTo" placeholder="Đến bao nhiêu ngày...">
                                    <span class="error invalid-feedback">Số ngày không hợp lệ!</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label>Chọn phân loại</label>
                            <select class="form-control selPhanLoai">
                                <option value="Phân bón">Phân bón</option>
                                <option value="Thuốc">Thuốc</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
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
                    <div class="col-thongtin" data-field="giaidoan" data-title="Giai đoạn">
                        <strong>Giai đoạn <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="col-6 col-thongtin" data-field="tu" data-title="Từ (ngày)">
                            <strong>Từ (ngày) <i class="fa fa-edit text-info edit"></i></strong>
                            <span></span>
                        </div>
                        <div class="col-6 col-thongtin" data-field="den" data-title="Đến (ngày)">
                            <strong>Đến (ngày) <i class="fa fa-edit text-info edit"></i></strong>
                            <span></span>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="sanpham" data-title="Sản phẩm">
                        <strong>Sản phẩm <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="col-6 col-thongtin" data-field="donvitinh">
                            <strong>Đơn vị tính</strong>
                            <span></span>
                        </div>
                        <div class="col-6 col-thongtin" data-field="phanloai">
                            <strong>Phân loại</strong>
                            <span></span>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="col-4 col-thongtin" data-field="soluong" data-title="Số lượng">
                            <strong>Số lượng <i class="fa fa-edit text-info edit"></i></strong>
                            <span></span>
                        </div>
                        <div class="col-4 col-thongtin" data-field="dongia">
                            <strong>Đơn giá</strong>
                            <span></span>
                        </div>
                        <div class="col-4 col-thongtin" data-field="thanhtien">
                            <strong>Thành tiền</strong>
                            <span></span>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="congdung" data-title="Công dụng">
                        <strong>Công dụng <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="ghichu" data-title="Ghi chú">
                        <strong>Ghi chú <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    @if(in_array('quy-trinh-lua.quy-trinh.action',$info->phanquyen) !== false)
                        <button type="button" class="btn bg-gradient-danger delete mr-auto">Xóa Thông Tin</button>
                    @endif
                    <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js-custom')
    @include('quanly.quytrinhlua.quytrinh-sudung.js')
@stop
