@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Tìm Phiếu</li>
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
                                <label>Chọn loại phiếu</label>
                                <select class="form-control" id="selLoaiPhieu"></select>
                            </div>
                            <div class="form-group mb-0">
                                <button class="btn bg-gradient-success btn-block font-weight-bolder" id="btnLoc">
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

    <div class="modal fade" id="modalDauKy">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Đầu Kỳ Hàng Hóa</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Mã hàng hóa</label>
                        <input type="text" class="form-control inpMa" readonly>
                    </div>
                    <div class="form-group">
                        <label>Tên hàng hóa</label>
                        <input type="text" class="form-control inpTen" readonly>
                    </div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Đơn vị tính</label>
                                <input type="text" class="form-control inpDonViTinh" readonly>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Quy cách</label>
                                <input type="text" class="form-control inpQuyCach" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Tồn kho hiện tại</label>
                                <input type="text" class="form-control inpTonKho text-info font-weight-bolder" readonly>
                                <span class="error invalid-feedback">Số lượng không hợp lệ!</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group required">
                                <label>Nhập tồn kho thực tế</label>
                                <input type="number" class="form-control inpSoLuong" placeholder="Nhập tồn kho thực tế...">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label>Ghi chú</label>
                        <textarea rows="2" class="form-control inpGhiChu" placeholder="Nhập ghi chú..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-primary btnSubmit">Tạo Phiếu</button>
                    <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js-custom')
    @include('quanly.timphieu.js')
@stop
