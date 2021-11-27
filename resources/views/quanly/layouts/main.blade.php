<!DOCTYPE html>
<html>
@include('quanly.layouts.header')
<body class="sidebar-mini text-sm layout-fixed layout-navbar-fixed">
<div class="wrapper">

    <!-- Navbar -->
    @include('quanly.layouts.navbar')
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    @include('quanly.layouts.sidebar')

    <!-- Content Wrapper. Contains page content -->
    @section('body') @show

    <input type="password" name="password" autocomplete="new-password" class="d-none">
    <!-- /.content-wrapper -->
    <!-- Main Footer -->
{{--    <footer class="main-footer">--}}
{{--        <div class="d-flex">--}}
{{--            <div>--}}
{{--                <strong>Template Copyright &copy; 2014-2021 by </strong>AdminLTE.io--}}
{{--            </div>--}}
{{--            <div class="ml-auto">--}}
{{--                <strong>Project Copyright &copy; 2021 by </strong>--}}
{{--                Nguyễn Đình Trọng - <strong>Version </strong>1.0.0--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </footer>--}}
<!-- ./wrapper -->
</div>

<div class="modal fade" id="modalInfo">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thông Tin Cá Nhân</h5>
            </div>
            <div class="modal-body row-thongtin">
                <div class="form-row">
                    <div class="col-6">
                        <div class="col-thongtin" data-field="ma">
                            <strong>Mã</strong>
                            <span>{{ $info->ma }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="col-thongtin" data-field="ten">
                            <strong>Tên <i class="fa fa-edit text-info edit"></i></strong>
                            <span>{{ $info->ten }}</span>
                        </div>
                    </div>
                </div>
                <div class="divider my-3"></div>
                <div class="form-row">
                    <div class="col-6">
                        <div class="col-thongtin" data-field="taikhoan">
                            <strong>Tài khoản</strong>
                            <span>{{ $info->taikhoan }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="col-thongtin" data-field="dienthoai">
                            <strong>Điện thoại</strong>
                            <span>{{ $info->dienthoai }}</span>
                        </div>
                    </div>
                </div>
                <div class="divider my-3"></div>
                <div class="form-row">
                    <div class="col-6">
                        <div class="col-thongtin" data-field="ngaysinh">
                            <strong>Ngày sinh <i class="fa fa-edit text-info edit"></i></strong>
                            <span>{{ isset($info->ngaysinh) ? date('d-m-Y',strtotime($info->ngaysinh)) : '' }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="col-thongtin" data-field="chucvu">
                            <strong>Chức vụ</strong>
                            <span>{{ $info->chucvu_ten }}</span>
                        </div>
                    </div>
                </div>
                <div class="divider my-3"></div>
                <div class="col-thongtin" data-field="chinhanh_id">
                    <strong>Cửa hàng</strong>
                    <span>{{ $info->chinhanh_ten }}</span>
                </div>
                <div class="divider my-3"></div>
                <div class="col-thongtin" data-field="ghichu">
                    <strong>Ghi chú <i class="fa fa-edit text-info edit"></i></strong>
                    <span>{{ $info->ghichu }}</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
            </div>
        </div>
    </div>
</div>

{{--<div class="modal fade" id="modalTonKhoGiaBan">--}}
{{--    <div class="modal-dialog">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h5 class="modal-title">Danh Sách Tồn Kho & Giá Bán</h5>--}}
{{--            </div>--}}
{{--            <div class="modal-body">--}}
{{--                <div class="font-weight-bolder ten text-muted" style="font-size: 15px">ĐẠM PHÚ MỸ PHÂN LẠNH</div>--}}
{{--                <div class="divider my-3"></div>--}}
{{--                <div class="form-row">--}}
{{--                    <div class="col-6 d-flex justify-content-between">--}}
{{--                        <span class="text-muted">ĐƠN GIÁ:</span>--}}
{{--                        <span class="dongia font-weight-bolder text-danger"></span>--}}
{{--                    </div>--}}
{{--                    <div class="col-6 d-flex justify-content-between">--}}
{{--                        <span class="text-muted">TỒN KHO:</span>--}}
{{--                        <span class="tonkho font-weight-bolder text-info"></span>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="boxTonKho"></div>--}}
{{--            </div>--}}
{{--            <div class="modal-footer">--}}
{{--                <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

{{--<div class="modal fade" id="modalDoiMatKhau">--}}
{{--    <div class="modal-dialog modal-dialog-centered">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h5 class="modal-title">Thay Đổi Mật Khẩu</h5>--}}
{{--            </div>--}}
{{--            <div class="modal-body">--}}
{{--                <div class="form-group">--}}
{{--                    <label>Mật khẩu cũ</label>--}}
{{--                    <input class="form-control inpMKCu" type="password" placeholder="Nhập mật khẩu cũ...">--}}
{{--                    <span class="error invalid-feedback"></span>--}}
{{--                </div>--}}
{{--                <div class="form-group">--}}
{{--                    <label>Mật khẩu mới</label>--}}
{{--                    <input class="form-control inpMKMoi" type="password" placeholder="Nhập mật khẩu mới...">--}}
{{--                    <span class="error invalid-feedback"></span>--}}
{{--                </div>--}}
{{--                <div class="form-group">--}}
{{--                    <label>Nhập lại mật khẩu</label>--}}
{{--                    <input class="form-control inpMKNhapLai" type="password" placeholder="Nhập lại mật khẩu...">--}}
{{--                    <span class="error invalid-feedback"></span>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="modal-footer">--}}
{{--                <button type="button" class="btn bg-gradient-primary btnSubmit">Xác Nhận</button>--}}
{{--                <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

@include('quanly.layouts.footer')
</body>
</html>
