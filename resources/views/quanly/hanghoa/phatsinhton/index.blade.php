@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Hàng Hóa</li>
    <li class="breadcrumb-item active">Phát Sinh Tồn</li>
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
                                <div class="d-flex">
                                    <strong style="min-width: 55px" class="d-flex justify-content-between mr-1">
                                        <span>Tồn kho</span><span>:</span>
                                    </strong>
                                    <span class="tonkho font-weight-bolder text-info"></span>
                                </div>
                                <div class="divider my-2"></div>
                                <div class="form-row">
                                    <div class="col-6 d-flex">
                                        <strong style="min-width: 50px" class="d-flex justify-content-between mr-1">
                                            <span>Đầu kỳ</span><span>:</span>
                                        </strong>
                                        <span class="dauky font-weight-bolder ml-auto"></span>
                                    </div>
                                    <div class="d-flex col-6">
                                        <strong style="min-width: 56px" class="d-flex justify-content-between mr-1">
                                            <span>Tăng TK</span><span>:</span>
                                        </strong>
                                        <span class="tangtk text-success font-weight-bolder ml-auto"></span>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-6 d-flex">
                                        <strong style="min-width: 50px" class="d-flex justify-content-between mr-1">
                                            <span>Cuối kỳ</span><span>:</span>
                                        </strong>
                                        <span class="cuoiky font-weight-bolder text-info ml-auto"></span>
                                    </div>
                                    <div class="d-flex col-6">
                                        <strong style="min-width: 56px" class="d-flex justify-content-between mr-1">
                                            <span>Giảm TK</span><span>:</span>
                                        </strong>
                                        <span class="giamtk font-weight-bolder text-danger ml-auto"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="divider my-2"></div>
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
                                <select class="form-control" id="selHangHoa"></select>
                            </div>
                            <div class="form-group mb-0">
                                @if(in_array('hang-hoa.phat-sinh-ton.dau-ky',$info->phanquyen) !== false)
                                <button class="btn bg-gradient-primary font-weight-bolder" disabled id="btnDauKy">
                                    <i class="fa fa-refresh mr-1"></i>
                                    ĐẦU KỲ HÀNG HÓA
                                </button>
                                @endif
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

    @if(in_array('hang-hoa.phat-sinh-ton.dau-ky',$info->phanquyen) !== false)
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
    @endif
@stop

@section('js-custom')
    @include('quanly.hanghoa.phatsinhton.js')
@stop
