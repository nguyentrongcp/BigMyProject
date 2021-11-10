@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Chuyển Kho Nội Bộ</li>
    <li class="breadcrumb-item active">Nhập Kho</li>
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
                            <div class="text-primary font-weight-bolder text-center c-pointer" id="lblMaPhieu" style="font-size: 25px">-----</div>
                            <div class="divider my-1"></div>
                            <div class="text-secondary font-weight-bolder text-center" id="lblChiNhanh" style="font-size: 15px">---</div>
                            <div class="divider my-1"></div>
                            <div class="text-center">
                                <button class="btn-sm btn bg-gradient-danger d-none" id="btnHuyPhieu">Trả Lại Hàng</button>
                                <button class="btn-sm btn bg-gradient-info btnThongTin"
                                        data-toggle="modal" data-target="#modalDanhSachPhieuXuat">
                                    Lấy Phiếu Xuất Kho
                                </button>
                            </div>
                            <div class="divider my-1"></div>
                            <div id="boxThongTin">
                                <div class="d-flex">
                                    <strong class="mr-1">
                                        <span>Người soạn hàng:</span>
                                    </strong>
                                    <span class="ten"></span>
                                </div>
                                <div class="d-flex">
                                    <strong class="mr-1">
                                        <span>Ghi chú:</span>
                                    </strong>
                                    <span class="ghichu"></span>
                                </div>
                            </div>
                            <div class="divider my-1"></div>
                            <div class="form-group mb-0">
                                <label class="w-100">
                                    Nhân viên kiểm hàng
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

    <div class="modal fade" id="modalDanhSachPhieuXuat">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title w-100 text-center">Danh Sách Phiếu Xuất Kho Nội Bộ</h4>
                </div>
                <div class="modal-body">
                    <div class="d-flex">
                        <div class="d-flex box-search-table flex-grow-1" data-target="tblPhieuXuat">
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
                            <button class="btn bg-gradient-primary font-weight-bolder ml-auto" id="btnChonHang" disabled>
                                <i class="fas fa-check mr-1"></i>
                                Chọn Hàng
                            </button>
                        </div>
                    </div>
                    <div id="tblPhieuXuat" class="mt-1"></div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    @include('quanly.extends.danhsachphieu')
@stop

@section('js-custom')
    @include('quanly.chuyenkho-noibo.nhapkho.js')
@stop
