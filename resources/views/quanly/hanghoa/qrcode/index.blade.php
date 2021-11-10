@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Hàng Hóa</li>
    <li class="breadcrumb-item active">In Mã QR-CODE</li>
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
                                    <input type="number" class="form-control non-border soluong" placeholder="Nhập số lượng...">
                                </div>
                                <div class="col-5">
                                    <button id="btnThemHangHoa" class="btn-block h-100 btn bg-gradient-primary font-weight-bolder">Thêm</button>
                                </div>
                            </div>
                            <div class="divider my-3"></div>
                            <button class="btn bg-gradient-success btn-block font-weight-bolder" id="btnIn">
                                <i class="fa fa-qrcode mr-1"></i>In Qrcode</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop

@section('js-custom')
    @include('quanly.hanghoa.qrcode.js')
@stop
