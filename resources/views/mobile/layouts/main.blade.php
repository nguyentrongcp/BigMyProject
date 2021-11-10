<!DOCTYPE html>
<html>
@include('mobile.layouts.header')
<body class="hold-transition sidebar-collapse sidebar-mini layout-footer-fixed layout-fixed layout-navbar-fixed">
<div class="wrapper">

    <!-- Navbar -->
@include('mobile.layouts.navbar')
<!-- /.navbar -->

    <!-- Main Sidebar Container -->
@include('mobile.layouts.sidebar')

<!-- Content Wrapper. Contains page content -->
@section('body') @show
<!-- /.content-wrapper -->
@include('mobile.layouts.foot')
<!-- ./wrapper -->

<div class="modal fade" id="modalTonKhoGiaBan">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Danh Sách Tồn Kho & Giá Bán</h5>
            </div>
            <div class="modal-body">
                <div class="font-weight-bolder ten text-muted" style="font-size: 15px">ĐẠM PHÚ MỸ PHÂN LẠNH</div>
                <div class="divider my-3"></div>
                <div class="form-row">
                    <div class="col-6 d-flex justify-content-between">
                        <span class="text-muted">ĐƠN GIÁ:</span>
                        <span class="dongia font-weight-bolder text-danger"></span>
                    </div>
                    <div class="col-6 d-flex justify-content-between">
                        <span class="text-muted">TỒN KHO:</span>
                        <span class="tonkho font-weight-bolder text-info"></span>
                    </div>
                </div>
                <div class="boxTonKho"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
            </div>
        </div>
    </div>
</div>

@include('mobile.layouts.footer')
</body>
</html>
