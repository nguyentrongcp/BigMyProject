<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

{{--    <div style="font-size: 16px">Bạn đang ở trang <span class="font-weight-bolder text-primary">Bán Hàng</span></div>--}}
    <ol class="breadcrumb">
        @yield('breadcrumb')
    </ol>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
{{--        <li class="nav-item">--}}
{{--            <a class="nav-link" data-widget="navbar-search" href="#" role="button">--}}
{{--                <i class="fas fa-search"></i>--}}
{{--            </a>--}}
{{--            <div class="navbar-search-block" style="display: none;">--}}
{{--                <form class="form-inline">--}}
{{--                    <div class="input-group input-group-sm">--}}
{{--                        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">--}}
{{--                        <div class="input-group-append">--}}
{{--                            <button class="btn btn-navbar" type="submit">--}}
{{--                                <i class="fas fa-search"></i>--}}
{{--                            </button>--}}
{{--                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">--}}
{{--                                <i class="fas fa-times"></i>--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </li>--}}
        <!-- Notifications Dropdown Menu -->
        <li class="d-flex align-items-center px-3">
            <div>Bạn đang ở cửa hàng <span id="lblTenChiNhanh" class="font-weight-bolder text-info">{{ $info->chinhanh_ten }}</span></div>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge" id="lblSoThongBao"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="boxThongBaoGia"
                 style="max-height: 496px; overflow-y: auto; width: 350px; max-width: 350px">

            </div>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link user-panel" data-toggle="dropdown" href="#" aria-expanded="false">
                <div class="image">
                    <img src="/logo.jpg" class="img-circle" alt="User Image">
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
                <span class="dropdown-item dropdown-header font-weight-bolder dropdown-header-user">{{ $info->ten }}</span>
                <div class="dropdown-divider"></div>
                <div href="#" class="dropdown-item c-pointer" data-toggle="modal" data-target="#modalInfo">
                    <i class="fas fa-users mr-2"></i> Thông tin cá nhân
{{--                    <span class="float-right text-muted text-sm">3 mins</span>--}}
                </div>
                @if(in_array('role.chi-nhanh.tat-ca',$info->phanquyen) !== false)
                <div href="#" class="dropdown-item c-pointer" id="btnChuyenCuaHang">
                    <i class="fa fa-refresh mr-2"></i> Chuyển cửa hàng
                    {{--                    <span class="float-right text-muted text-sm">3 mins</span>--}}
                </div>
                @endif
                <div href="#" class="dropdown-item c-pointer" id="btnDoiMatKhau">
                    <i class="fas fa-lock mr-2"></i> Đổi mật khẩu
                </div>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer dropdown-footer-logout font-weight-bolder" id="btnLogout">Đăng Xuất</a>
            </div>
        </li>
    </ul>
</nav>
