@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Hàng Hóa</li>
    <li class="breadcrumb-item active">Số Lượng Bán</li>
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
                            <div class="form-group">
                                <label>Chọn thời gian</label>
                                <input type="text" placeholder="Từ ngày - đến ngày..." class="form-control" id="fromToDate" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label>Chọn cửa hàng</label>
                                <select class="form-control" id="selChiNhanh"></select>
                            </div>
                            <div class="form-group">
                                <label>Chọn hàng hóa</label>
                                <select class="form-control" id="selHangHoa" multiple></select>
                            </div>
                            <button class="btn bg-gradient-success btn-block font-weight-bolder" id="btnLoc">
                                <i class="fa fa-filter mr-1"></i>
                                LỌC DỮ LIỆU
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop

@section('js-custom')
    @include('quanly.hanghoa.soluongban.js')
@stop
