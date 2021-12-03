<style>
    #modalMenu .box-menu {
        margin: -0.125rem;
        display: flex;
        flex-wrap: wrap;
    }
    #modalMenu .box-menu > .item {
        width: calc(100% / 3);
        padding: 0.125rem;
    }
    #modalMenu .box-menu > .item > div {
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        padding: 1rem;
        background: #fff;
        height: 100%;
    }
    #modalMenu .box-menu > .item > div.active {
        background: #28a745;
    }
    #modalMenu .box-menu > .item > div:active {
        background: #699273;
    }
    #modalMenu .box-menu > .item > div:active > i, #modalMenu .box-menu > .item > div:active > span,
    #modalMenu .box-menu > .item > div.active > i, #modalMenu .box-menu > .item > div.active > span {
        color: rgba(255,255,255,0.9) !important;
    }
    #modalMenu .box-menu > .item > div > i {
        text-align: center;
        font-size: 35px;
        margin-bottom: 0.5rem;
    }
    #modalMenu .box-menu > .item > div > span {
        text-align: center;
    }
    #modalMenu .modal-footer {
        background: #edf2f7;
        border-top: 1px solid #c2cfdc
    }
</style>
<div class="modal fade" id="modalMenu">
    <div class="modal-dialog modal-fullsize">
        <div class="modal-content h-100">
            <div class="modal-header p-0">
                <img style="width: 100%" src="/avatar-app.png">
            </div>
            <div class="modal-body overflow-auto px-1 pb-1" style="max-height: 100%; background: #edf2f7">
                <p class="text-center">Xin chào, <strong class="title">NGUYỄN ĐÌNH TRỌNG</strong></p>
                <div class="box-menu">
                    @if(in_array('danh-muc.nhan-vien.mobile',$info->phanquyen) !== false)
                    <div class="item">
                        <div data-href="{{ route('mobile.nhan-vien') }}"
                             class="{{ url()->current() == route('mobile.nhan-vien') ? ' active' : '' }}"
                             data-title="Danh Mục Nhân Viên">
                            <i class="fa fa-user text-success"></i>
                            <span>Nhân Viên</span>
                        </div>
                    </div>
                    @endif
                    <div class="item">
                        <div data-href="{{ route('mobile.lichsu-diemdanh') }}"
                             class="{{ url()->current() == route('mobile.lichsu-diemdanh') ? ' active' : '' }}"
                             data-title="Lịch Sử Điểm Danh">
                            <i class="fa fa-history text-success"></i>
                            <span>Lịch Sử Điểm Danh</span>
                        </div>
                    </div>
                    @if(in_array('danh-muc.hang-hoa.mobile',$info->phanquyen) !== false)
                    <div class="item">
                        <div data-href="{{ route('mobile.hang-hoa') }}"
                             class="{{ url()->current() == route('mobile.hang-hoa') ? ' active' : '' }}"
                             data-title="Danh Mục Hàng Hóa">
                            <i class="fa fa-inbox text-success"></i>
                            <span>Hàng Hóa</span>
                        </div>
                    </div>
                    @endif
{{--                    <div class="item">--}}
{{--                        <div data-href="{{ route('mobile.hang-hoa.quy-doi') }}"--}}
{{--                             class="{{ url()->current() == route('mobile.hang-hoa.quy-doi') ? ' active' : '' }}"--}}
{{--                             data-title="Danh Mục Quy Đổi">--}}
{{--                            <i class="fas fa-exchange-alt text-success"></i>--}}
{{--                            <span>Q.Đổi Hàng Hóa</span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    @if(in_array('danh-muc.chi-nhanh.mobile',$info->phanquyen) !== false)
                    <div class="item">
                        <div data-href="{{ route('mobile.chi-nhanh') }}" class="{{ url()->current() == route('mobile.chi-nhanh') ? ' active' : '' }}"
                             data-title="Danh Mục Cửa Hàng">
                            <i class="fa fa-university text-success"></i>
                            <span>Cửa Hàng</span>
                        </div>
                    </div>
                    @endif
{{--                    <div class="item">--}}
{{--                        <div data-href="{{ route('mobile.khach-hang') }}"--}}
{{--                             class="{{ url()->current() == route('mobile.khach-hang') ? ' active' : '' }}"--}}
{{--                             data-title="Danh Mục Khách Hàng">--}}
{{--                            <i class="fa fa-users text-success"></i>--}}
{{--                            <span>Khách Hàng</span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="item">--}}
{{--                        <div data-href="{{ route('mobile.nhap-hang.danh-sach') }}"--}}
{{--                             class="{{ url()->current() == route('mobile.nhap-hang.danh-sach') ? ' active' : '' }}"--}}
{{--                             data-title="Danh Sách Nhập Hàng">--}}
{{--                            <i class="far fa-list-alt text-success"></i>--}}
{{--                            <span>Danh Sách Nhập Hàng</span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    @if(in_array('danh-muc.thu-chi.mobile',$info->phanquyen) !== false)
                    <div class="item">
                        <div data-href="{{ route('mobile.thu-chi') }}"
                             class="{{ url()->current() == route('mobile.thu-chi') ? ' active' : '' }}"
                             data-title="Thu Chi">
                            <i class="far fa-money-bill-alt text-success"></i>
                            <span>Thu Chi</span>
                        </div>
                    </div>
                    @endif
{{--                    <div class="item">--}}
{{--                        <div data-href="{{ route('mobile.lich-nghi') }}"--}}
{{--                             class="{{ url()->current() == route('mobile.lich-nghi') ? ' active' : '' }}"--}}
{{--                             data-title="Lịch Nghỉ">--}}
{{--                            <i class="far fa-calendar-alt text-success"></i>--}}
{{--                            <span>Lịch Nghỉ</span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="item">--}}
{{--                        <div data-href="{{ route('mobile.lich-su-diem-danh') }}"--}}
{{--                             class="{{ url()->current() == route('mobile.lich-su-diem-danh') ? ' active' : '' }}"--}}
{{--                             data-title="Lịch Sử Điểm Danh">--}}
{{--                            <i class="fas fa-history text-success"></i>--}}
{{--                            <span>Lịch Sử Điểm Danh</span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="item">--}}
{{--                        <div data-href="">--}}
{{--                            <i class="fas fa-cogs text-success"></i>--}}
{{--                            <span>Cài Đặt</span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    {{--                    <div class="item">--}}
                    {{--                        <div>--}}
                    {{--                            <i class="far fa-money-bill-alt text-success"></i>--}}
                    {{--                            <span>Thu Chi</span>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                </div>
            </div>
            <div class="modal-footer" style="min-height: 64px; height: 64px">
                <button id="btnDangXuat" type="button" class="btn btn-danger btn-sm mr-auto">Đăng Xuất</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Thoát</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
