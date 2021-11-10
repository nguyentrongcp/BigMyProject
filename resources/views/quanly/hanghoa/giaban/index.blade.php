@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Hàng Hóa</li>
    <li class="breadcrumb-item active">Giá Bán</li>
@stop

@section('body')
    <div class="content-wrapper">
        <section class="content">
            <div class="row py-3">
                <div class="col" style="max-width: calc(100% - 400px)">
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
                            </div>
                            <div id="tblDanhSach"></div>
                        </div>
                    </div>
                </div>
                <div class="col" style="max-width: 400px">
                    <div class="card card-outline card-info thongtin-phieu mb-0">
                        <div class="card-body">
                            <div id="boxThongTin">
                                <div class="d-flex">
                                    <strong style="min-width: 55px" class="d-flex justify-content-between mr-1">
                                        <span>Tên HH</span><span>:</span>
                                    </strong>
                                    <span class="ten"></span>
                                </div>
                                <div class="d-flex">
                                    <strong style="min-width: 55px" class="d-flex justify-content-between mr-1">
                                        <span>Mã HH</span><span>:</span>
                                    </strong>
                                    <span class="ma"></span>
                                </div>
                                <div class="d-flex">
                                    <strong style="min-width: 55px" class="d-flex justify-content-between mr-1">
                                        <span>ĐVT</span><span>:</span>
                                    </strong>
                                    <span class="donvitinh"></span>
                                </div>
                                <div class="d-flex">
                                    <strong style="min-width: 55px" class="d-flex justify-content-between mr-1">
                                        <span>Q.Cách</span><span>:</span>
                                    </strong>
                                    <span class="quycach"></span>
                                </div>
                                <div class="d-flex">
                                    <strong style="min-width: 55px" class="d-flex justify-content-between mr-1">
                                        <span>Nhóm</span><span>:</span>
                                    </strong>
                                    <span class="nhom"></span>
                                </div>
                            </div>
                            <div class="divider my-2"></div>
                            <div class="form-group">
                                <label>Chọn hàng hóa</label>
                                <select class="form-control" id="selHangHoa"></select>
                            </div>
                            <div class="form-group mb-0">
                                <button class="btn bg-gradient-primary font-weight-bolder" disabled id="btnDongBo">
                                    <i class="fa fa-refresh mr-1"></i>
                                    ĐỒNG BỘ GIÁ
                                </button>
                                <button class="btn bg-gradient-success float-right font-weight-bolder" id="btnLoc">
                                    <i class="fa fa-filter mr-1"></i>
                                    LỌC DỮ LIỆU
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop

@section('js-custom')
    @include('quanly.hanghoa.giaban.js')
@stop
