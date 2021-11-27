@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Danh Sách Điểm Danh</li>
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
                                @if($info->dienthoai == '0339883047')
                                    <button class="btn ml-auto bg-gradient-primary font-weight-bolder"
                                            data-toggle="modal" data-target="#modalDiemDanh">
                                        <i class="fas fa-qrcode mr-1"></i>
                                        Điểm Danh
                                    </button>
                                @endif
                            </div>
                            <div id="tblDanhSach"></div>
                        </div>
                    </div>
                </div>
                <div class="col" style="max-width: 400px">
                    <div class="card card-outline card-info thongtin-phieu mb-0">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Chọn tháng</label>
                                        <select class="form-control" id="selThang">
                                            @for($i=1; $i<=12; $i++)
                                                <option {{ date('m') == $i ? 'selected' : '' }} value="{{ $i }}">{{ $i < 10 ? '0'.$i : $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Chọn năm</label>
                                        <select class="form-control" id="selNam">
                                            @for($i=2021; $i<=date('Y'); $i++)
                                                <option {{ date('Y') == $i ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Chọn cửa hàng</label>
                                <select class="form-control" id="selChiNhanh" multiple>
                                    @foreach($chinhanhs as $chinhanh)
                                        <option value="{{ $chinhanh->id }}">{{ $chinhanh->ten }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Chọn chức vụ</label>
                                <select class="form-control" id="selChucVu" multiple>
                                    @foreach($chucvus as $chucvu)
                                        <option value="{{ $chucvu->loai }}">{{ $chucvu->ten }}</option>
                                    @endforeach
                                </select>
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

    @if($info->dienthoai == '0339883047')
    <div class="modal fade" id="modalDiemDanh">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Điểm Danh</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Chọn nhân viên</label>
                        <select class="form-control selNhanVien"></select>
                    </div>
                    <div class="form-group">
                        <label>Chức vụ</label>
                        <select class="form-control selChucVu">
                            <option value="banhang">Bán hàng</option>
                            <option value="vanphong">Văn phòng</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Loại</label>
                        <select class="form-control selLoai">
                            <option value="begin">Bắt đầu</option>
                            <option value="end">Kết thúc</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ngày</label>
                        <input type="date" class="form-control inpNgay" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Thời gian</label>
                        <input type="text" class="form-control inpThoiGian">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-primary btnSubmit">Điểm Danh</button>
                    <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="modal fade" id="modalChiTiet">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title w-100 text-center">Chi tiết điểm danh nhân viên <span class="title"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="d-flex">
                        <div class="d-flex box-search-table flex-grow-1" data-target="tblChiTiet">
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
                    </div>
                    <div id="tblChiTiet" class="mt-1"></div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@stop

@section('js-custom')
    @include('quanly.danhsach-diemdanh.js')
@stop
