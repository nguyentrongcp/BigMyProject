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
<div class="modal" id="modalMenu">
    <div class="modal-dialog modal-fullsize">
        <div class="modal-content h-100">
            <div class="modal-header p-0">
                <img style="width: 100%" src="/avatar-app.png">
            </div>
            <div class="modal-body overflow-auto px-1 pb-1" style="max-height: 100%; background: #edf2f7">
                <p class="text-center">Xin chào, <strong class="title">NGUYỄN ĐÌNH TRỌNG</strong></p>
                <div class="box-menu">
                    <div class="item">
                        <div data-href="{{ route('nong-dan.quytrinh-hientai') }}"
                             class="{{ url()->current() == route('nong-dan.quytrinh-hientai') ? ' active' : '' }}"
                             data-title="Quy Trình Hôm Nay">
                            <i class="fa fa-american-sign-language-interpreting text-success"></i>
                            <span>Quy Trình Hôm Nay</span>
                        </div>
                    </div>
                    <div class="item">
                        <div data-href="{{ route('nong-dan.thua-ruong') }}"
                             class="{{ url()->current() == route('nong-dan.thua-ruong') ? ' active' : '' }}"
                             data-title="Danh Mục Thửa Ruộng">
                            <i class="fa fa-user text-success"></i>
                            <span>Danh Mục Thửa Ruộng</span>
                        </div>
                    </div>
                    <div class="item">
                        <div data-href="{{ route('nong-dan.quy-trinh') }}"
                             class="{{ url()->current() == route('nong-dan.quy-trinh') ? ' active' : '' }}"
                             data-title="Danh Mục Quy Trình">
                            <i class="fa fa-user text-success"></i>
                            <span>Danh Mục Quy Trình</span>
                        </div>
                    </div>
                    <div class="item">
                        <div class="none-url doi-matkhau">
                            <i class="fas fa-lock text-success"></i>
                            <span>Đổi Mật Khẩu</span>
                        </div>
                    </div>
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
