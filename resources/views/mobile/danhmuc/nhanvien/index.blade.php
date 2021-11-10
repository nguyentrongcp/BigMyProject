@extends('mobile.layouts.main')
@section('style')
    <style>
        #boxDanhSach .product-description {
            align-items: center;
        }
        #boxDanhSach .product-description > span:nth-child(2) {
            border-right: 1px solid #bbc1c7;
            height: 15px;
        }
        #boxDanhSach .product-description > span:last-child {
            margin-left: 0.5rem;
        }
    </style>
@endsection
@section('body')
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <div class="card card-info h-100 mb-0" id="boxDanhSach">
                <div class="card-body p-0">
                    <div class="d-flex h-100 flex-column">
                        <div class="d-flex box-search p-3">
                            <div class="ui icon input w-100">
                                <input class="prompt form-control input-search input-no-border" type="text" placeholder="Nhập từ khóa tìm kiếm...">
                                <i class="link icon times"></i>
                            </div>
{{--                            @if($quyen->chinhsua)--}}
{{--                            <div class="ml-auto d-flex pl-2">--}}
{{--                                <button class="btn btn-primary btn-sm font-weight-bolder" data-toggle="modal" data-target="#modalThemMoi">--}}
{{--                                    <i class="fa fa-plus"></i>--}}
{{--                                </button>--}}
{{--                            </div>--}}
{{--                            @endif--}}
                        </div>
                        <div style="height: calc(100% - 1rem)" class="overflow-auto px-3">
                            <ul class="products-list product-list-in-card box-content">

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>

{{--    @if($quyen->chinhsua)--}}
{{--    <div class="modal fade" id="modalThemMoi">--}}
{{--        <div class="modal-dialog">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title">Thêm Mới Thông Tin Nhân Viên</h5>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <div class="modal-body">--}}
{{--                    <div class="form-group">--}}
{{--                        <label class="required">Tên nhân viên</label>--}}
{{--                        <input type="text" class="form-control ten" placeholder="Nhập tên nhân viên...">--}}
{{--                    </div>--}}
{{--                    <div class="form-group">--}}
{{--                        <label class="required">Điện thoại</label>--}}
{{--                        <input type="text" class="form-control dienthoai" placeholder="Nhập SĐT...">--}}
{{--                    </div>--}}
{{--                    <div class="form-group">--}}
{{--                        <label class="required">Chi nhánh</label>--}}
{{--                        <select class="form-control chinhanh">--}}
{{--                            @foreach($chinhanhs as $id => $ten)--}}
{{--                                <option value="{{ $id  }}">{{ $ten }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="form-group">--}}
{{--                        <label class="required">Chức vụ</label>--}}
{{--                        <select class="form-control chucvu">--}}
{{--                            @foreach($chucvus as $loai => $ten)--}}
{{--                                <option value="{{ $loai  }}">{{ $ten }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="form-group">--}}
{{--                        <label>Ngày sinh</label>--}}
{{--                        <input type="date" class="form-control ngaysinh" placeholder="Chọn ngày sinh...">--}}
{{--                    </div>--}}
{{--                    <div class="form-group">--}}
{{--                        <label>Địa chỉ</label>--}}
{{--                        <textarea rows="2" class="form-control diachi" placeholder="Nhập địa chỉ..."></textarea>--}}
{{--                    </div>--}}
{{--                    <div class="form-group">--}}
{{--                        <label>Email</label>--}}
{{--                        <input type="email" class="form-control email" placeholder="Nhập email...">--}}
{{--                    </div>--}}
{{--                    <div class="form-group">--}}
{{--                        <label>Số CMND</label>--}}
{{--                        <input type="text" class="form-control cmnd" placeholder="Nhập số CMND...">--}}
{{--                    </div>--}}
{{--                    <div class="form-group">--}}
{{--                        <label>Chuyên ngành</label>--}}
{{--                        <input type="text" class="form-control chuyennganh" placeholder="Nhập chuyên ngành...">--}}
{{--                    </div>--}}
{{--                    <div class="form-group mb-0">--}}
{{--                        <label>Ghi chú</label>--}}
{{--                        <textarea rows="2" class="form-control ghichu" placeholder="Nhập ghi chú..."></textarea>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="modal-footer">--}}
{{--                    <button type="button" class="btn btn-primary submit">Xác Nhận</button>--}}
{{--                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Thoát</button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <!-- /.modal-content -->--}}
{{--        </div>--}}
{{--        <!-- /.modal-dialog -->--}}
{{--    </div>--}}
{{--    @endif--}}

{{--    @if($quyen->thongtin)--}}
{{--    <div class="modal fade" id="modalThongTin">--}}
{{--        <div class="modal-dialog">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title">Thông Tin Nhân Viên</h5>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <div class="modal-body">--}}
{{--                    <div class="form-row">--}}
{{--                        <div class="col ten field" data-field="ten">--}}
{{--                            <strong>Tên<i class="fa fa-edit text-info ml-1 c-pointer"></i></strong>--}}
{{--                            <p class="text-muted mb-0"></p>--}}
{{--                        </div>--}}
{{--                        <div class="col ma field" data-field="ma">--}}
{{--                            <strong>Mã</strong>--}}
{{--                            <p class="text-muted mb-0"></p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <hr>--}}
{{--                    <div class="form-row">--}}
{{--                        <div class="col dienthoai field" data-field="dienthoai">--}}
{{--                            <strong>Số điện thoại<i class="fa fa-edit text-info ml-1 c-pointer"></i></strong>--}}
{{--                            <p class="text-muted mb-0"></p>--}}
{{--                        </div>--}}
{{--                        <div class="col ngaysinh field" data-field="ngaysinh">--}}
{{--                            <strong>Ngày sinh</strong>--}}
{{--                            <p class="text-muted mb-0"></p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <hr>--}}
{{--                    <div class="chinhanh field" data-field="chinhanh_id">--}}
{{--                        <strong>Chi nhánh<i class="fa fa-edit text-info ml-1 c-pointer"></i></strong>--}}
{{--                        <p class="text-muted"></p>--}}
{{--                    </div>--}}
{{--                    <hr>--}}
{{--                    <div class="form-row">--}}
{{--                        <div class="col chuyennganh field" data-field="chuyennganh">--}}
{{--                            <strong>Chuyên ngành</strong>--}}
{{--                            <p class="text-muted mb-0"></p>--}}
{{--                        </div>--}}
{{--                        <div class="col chucvu field" data-field="loai">--}}
{{--                            <strong>Chức vụ<i class="fa fa-edit text-info ml-1 c-pointer"></i></strong>--}}
{{--                            <p class="text-muted mb-0"></p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <hr>--}}
{{--                    <div class="form-row">--}}
{{--                        <div class="col cmnd field" data-field="cmnd">--}}
{{--                            <strong>CMND</strong>--}}
{{--                            <p class="text-muted mb-0"></p>--}}
{{--                        </div>--}}
{{--                        <div class="col ngaycap field" data-field="ngaycap">--}}
{{--                            <strong>Ngày cấp</strong>--}}
{{--                            <p class="text-muted mb-0"></p>--}}
{{--                        </div>--}}
{{--                        <div class="col noicap field" data-field="noicap">--}}
{{--                            <strong>Nơi cấp</strong>--}}
{{--                            <p class="text-muted mb-0"></p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <hr>--}}
{{--                    <div class="form-row">--}}
{{--                        <div class="col anh_mattruoc field">--}}
{{--                            <strong>Ảnh mặt trước</strong>--}}
{{--                            <p class="text-muted mb-0 d-flex"></p>--}}
{{--                        </div>--}}
{{--                        <div class="col anh_matsau field">--}}
{{--                            <strong>Ảnh mặt sau</strong>--}}
{{--                            <p class="text-muted mb-0 d-flex"></p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <hr>--}}
{{--                    <div class="cmnd_thongtin field" data-field="cmnd_thongtin">--}}
{{--                        <strong>Ngày cấp, nơi cấp</strong>--}}
{{--                        <p class="text-muted mb-0"></p>--}}
{{--                    </div>--}}
{{--                    <hr>--}}
{{--                    <div class="diachi field" data-field="diachi">--}}
{{--                        <strong>Địa chỉ</strong>--}}
{{--                        <p class="text-muted"></p>--}}
{{--                    </div>--}}
{{--                    <hr>--}}
{{--                    <div class="ghichu field" data-field="ghichu">--}}
{{--                        <strong>Ghi chú<i class="fa fa-edit text-info ml-1 c-pointer"></i></strong>--}}
{{--                        <p class="text-muted"></p>--}}
{{--                    </div>--}}
{{--                    @if($quyen->xoakhoiphuc)--}}
{{--                    <hr>--}}
{{--                    <button class="btn btn-danger btn-sm btn-block font-weight-bolder delete">Xóa Thông Tin</button>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <!-- /.modal-content -->--}}
{{--        </div>--}}
{{--        <!-- /.modal-dialog -->--}}
{{--    </div>--}}
{{--    @endif--}}
@endsection

@section('js-include')
    @include('mobile.danhmuc.nhanvien.js')
@endsection
