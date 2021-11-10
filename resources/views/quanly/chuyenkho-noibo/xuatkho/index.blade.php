@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Chuyển Kho Nội Bộ</li>
    <li class="breadcrumb-item active">Xuất Kho</li>
@stop

@section('body')
    <div class="content-wrapper">
        <section class="content">
            <div class="row py-3">
                <div class="col" style="max-width: calc(100% - 400px)">
                    <div class="card card-outline card-info mb-0 h-100">
                        <div class="card-body">
                            <div id="tblHangHoa"></div>
                        </div>
                    </div>
                </div>
                <div class="col" style="max-width: 400px">
                    <div class="card card-outline card-info thongtin-phieu mb-0">
                        <div class="card-body">
                            <div class="form-group mb-1">
                                <label>Chọn hàng hóa</label>
                                <select class="form-control" id="selHangHoa"></select>
                            </div>
                            <div class="form-row" id="boxHangHoa">
                                <div class="col-7">
                                    <input type="number" class="form-control non-border soluong" placeholder="Số lượng...">
                                </div>
                                <div class="col-5">
                                    <button id="btnThemHangHoa" class="btn-block h-100 btn bg-gradient-primary font-weight-bolder">Thêm</button>
                                </div>
                            </div>
                            <div class="divider mt-2 mb-1"></div>
                            <div class="form-group mb-0">
                                <label class="w-100">
                                    Cửa hàng nhận
                                </label>
                                <select class="form-control" id="selChiNhanh"></select>
                            </div>
                            <div class="divider mt-2 mb-1"></div>
                            <div class="form-group mb-0">
                                <label class="w-100">
                                    Nhân viên soạn hàng
                                </label>
                                <select class="form-control" id="selNhanVien"></select>
                            </div>
                            <div class="divider mt-1 mb-2"></div>
                            <div class="form-group">
                                <label>Ghi chú</label>
                                <textarea id="inpGhiChu" rows="2" class="form-control" placeholder="Nhập ghi chú..."></textarea>
                            </div>
                            <div class="form-group mb-0">
                                <button class="btn bg-gradient-info font-weight-bolder"
                                        data-toggle="modal" data-target="#modalDanhSachPhieu"
                                        id="btnDanhSachPhieu">DANH SÁCH PHIẾU</button>
                                <button class="btn bg-gradient-success float-right font-weight-bolder" id="btnXemPhieu">XEM PHIẾU</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('quanly.extends.danhsachphieu')
@stop

@section('js-custom')
    @include('quanly.chuyenkho-noibo.xuatkho.js')
@stop
