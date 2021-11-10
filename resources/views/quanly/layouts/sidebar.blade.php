<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <span class="brand-link">
        <img src="/logo.jpg" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">MyProject Admin</span>
    </span>

    <!-- Sidebar -->
    <div class="sidebar" style="overflow-y: auto; overflow-x: hidden">
        <!-- Sidebar user panel (optional) -->
{{--        <div class="user-panel mt-3 pb-3 mb-3 d-flex">--}}
{{--            <div class="image">--}}
{{--                <img src="https://s120-ava-talk.zadn.vn/1/f/3/b/12/120/2353c3754ba06e6c79784cf7104bc86d.jpg" class="img-circle elevation-2" alt="User Image">--}}
{{--            </div>--}}
{{--            <div class="info">--}}
{{--                <a href="#" class="d-block">Nguyễn Đình Trọng</a>--}}
{{--            </div>--}}
{{--        </div>--}}

        <!-- SidebarSearch Form -->
{{--        <div class="form-inline">--}}
{{--            <div class="input-group" data-widget="sidebar-search">--}}
{{--                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">--}}
{{--                <div class="input-group-append">--}}
{{--                    <button class="btn btn-sidebar">--}}
{{--                        <i class="fas fa-search fa-fw"></i>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--            </div><div class="sidebar-search-results"><div class="list-group"><a href="#" class="list-group-item"><div class="search-title"><strong class="text-light"></strong>N<strong class="text-light"></strong>o<strong class="text-light"></strong> <strong class="text-light"></strong>e<strong class="text-light"></strong>l<strong class="text-light"></strong>e<strong class="text-light"></strong>m<strong class="text-light"></strong>e<strong class="text-light"></strong>n<strong class="text-light"></strong>t<strong class="text-light"></strong> <strong class="text-light"></strong>f<strong class="text-light"></strong>o<strong class="text-light"></strong>u<strong class="text-light"></strong>n<strong class="text-light"></strong>d<strong class="text-light"></strong>!<strong class="text-light"></strong></div><div class="search-path"></div></a></div></div>--}}
{{--        </div>--}}

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-header">DANH MỤC CHỨC NĂNG</li>
                <li class="nav-item">
                    <a href="{{ route('ban-hang') }}" class="nav-link{{ url()->current() == route('ban-hang') ? ' active' : '' }}">
                        <i class="nav-icon fa fa-shopping-cart"></i>
                        <p>
                            Bán Hàng
                            <span class="right badge badge-danger">Home</span>
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('khach-tra-hang') }}" class="nav-link{{ url()->current() == route('khach-tra-hang') ? ' active' : '' }}">
                        <i class="nav-icon fa fa-cart-arrow-down"></i>
                        <p>Khách Trả Hàng</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-bars"></i>
                        <p>
                            Danh Mục
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('danh-muc.hang-hoa') }}" class="nav-link{{ url()->current() == route('danh-muc.hang-hoa') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Hàng Hóa</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('danh-muc.chi-nhanh') }}" class="nav-link{{ url()->current() == route('danh-muc.chi-nhanh') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cửa Hàng</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('danh-muc.nhan-vien') }}" class="nav-link{{ url()->current() == route('danh-muc.nhan-vien') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Nhân Viên</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('danh-muc.khach-hang') }}" class="nav-link{{ url()->current() == route('danh-muc.khach-hang') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Khách Hàng</p>
                            </a>
                        </li>
                        @if($info->id == '1000000000')
                        <li class="nav-item">
                            <a href="{{ route('danh-muc.nha-cung-cap') }}" class="nav-link{{ url()->current() == route('danh-muc.nha-cung-cap') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Nhà Cung Cấp</p>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ route('danh-muc.doi-tuong') }}" class="nav-link{{ url()->current() == route('danh-muc.doi-tuong') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Đối Tượng</p>
                            </a>
                        </li>
                        @if($info->id == '1000000000')
{{--                            <li class="nav-item">--}}
{{--                                <a href="{{ route('nhap-hang.danh-sach') }}" class="nav-link{{ url()->current() == route('nhap-hang.danh-sach') ? ' active' : '' }}">--}}
{{--                                    <i class="far fa-circle nav-icon"></i>--}}
{{--                                    <p>Danh Sách</p>--}}
{{--                                </a>--}}
{{--                            </li>--}}
                        <li class="nav-item">
                            <a href="{{ route('danh-muc.phan-quyen') }}" class="nav-link{{ url()->current() == route('danh-muc.phan-quyen') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Phân quyền</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-plus"></i>
                        <p>
                            Nhập Hàng
                            <i class="right fas fa-angle-left"></i>
                            @if($info->id == '1000000000')
                            <span class="right badge badge-danger" id="lblSoPhieuNhap"></span>
                            @endif
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('nhap-hang.tao-phieu') }}" class="nav-link{{ url()->current() == route('nhap-hang.tao-phieu') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tạo Phiếu</p>
                            </a>
                        </li>
                        @if($info->id == '1000000000')
                        <li class="nav-item">
                            <a href="{{ route('nhap-hang.danh-sach') }}" class="nav-link{{ url()->current() == route('nhap-hang.danh-sach') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Danh Sách</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-truck"></i>
                        <p>
                            Chuyển Kho Nội Bộ
                            <i class="right fas fa-angle-left"></i>
                            <span class="right badge badge-danger" id="lblSoPhieuXuatKho"></span>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('xuatkho-noibo') }}" class="nav-link{{ url()->current() == route('xuatkho-noibo') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Xuất Kho</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('nhapkho-noibo') }}" class="nav-link{{ url()->current() == route('nhapkho-noibo') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Nhập Kho</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fa fa-barcode"></i>
                        <p>
                            Hàng Hóa
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('hang-hoa.ton-kho') }}" class="nav-link{{ url()->current() == route('hang-hoa.ton-kho') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tồn Kho</p>
                            </a>
                        </li>
                        @if($info->id == '1000000000')
                        <li class="nav-item">
                            <a href="{{ route('hang-hoa.gia-ban') }}"
                               class="nav-link{{ url()->current() == route('hang-hoa.gia-ban') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Giá Bán</p>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ route('hang-hoa.phat-sinh-ton') }}"
                               class="nav-link{{ url()->current() == route('hang-hoa.phat-sinh-ton') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Phát Sinh Tồn</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('hang-hoa.so-luong-ban') }}"
                               class="nav-link{{ url()->current() == route('hang-hoa.so-luong-ban') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Số Lượng Bán</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('hang-hoa.qrcode') }}"
                               class="nav-link{{ url()->current() == route('hang-hoa.qrcode') ? ' active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>In Qrocde</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('thu-chi') }}" class="nav-link{{ url()->current() == route('thu-chi') ? ' active' : '' }}">
                        <i class="nav-icon fa fa-money-bill-alt"></i>
                        <p>Thu Chi</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tim-phieu') }}" class="nav-link{{ url()->current() == route('tim-phieu') ? ' active' : '' }}">
                        <i class="nav-icon fa fa-search"></i>
                        <p>Tìm Phiếu</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="https://ui-banhang.hailua.center/index5.php" class="nav-link" target="_blank">
                        <i class="nav-icon fa fa-arrows-h"></i>
                        <p>Trang Bán Hàng Cũ</p>
                    </a>
                </li>
{{--                <li class="nav-item">--}}
{{--                    <a href="{{ route('phat-sinh-ton') }}" class="nav-link{{ url()->current() == route('phat-sinh-ton') ? ' active' : '' }}">--}}
{{--                        <i class="nav-icon fas fa-sort-amount-up-alt"></i>--}}
{{--                        <p>Tra cứu phát sinh tồn</p>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                <li class="nav-item">--}}
{{--                    <a href="{{ route('thu-chi') }}" class="nav-link{{ url()->current() == route('thu-chi') ? ' active' : '' }}">--}}
{{--                        <i class="nav-icon far fa-money-bill-alt"></i>--}}
{{--                        <p>Thu Chi</p>--}}
{{--                    </a>--}}
{{--                </li>--}}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
