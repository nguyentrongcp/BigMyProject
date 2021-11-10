@extends('mobile.layouts.main')
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
                        </div>
                        <div style="height: calc(100% - 1rem)" class="overflow-auto px-3">
                            <ul class="products-list product-list-in-card box-content"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>

    <div class="modal fade" id="modalXem">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thông Tin Hàng Hóa</h5>
                </div>
                <div class="modal-body row-thongtin">
                    <div class="form-row">
                        <div class="col-6 col-thongtin" data-field="ma">
                            <strong>Mã</strong>
                            <span></span>
                        </div>
                        <div class="col-6 col-thongtin" data-field="mamoi" data-title="Mã PM mới">
                            <strong>Mã PM mới <i class="fa fa-edit text-info edit"></i></strong>
                            <span></span>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="ten" data-title="Tên hàng hóa">
                        <strong>Tên <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="col-6 col-thongtin" data-field="donvitinh" data-title="Đơn vị tính">
                            <strong>Đơn vị tính <i class="fa fa-edit text-info edit"></i></strong>
                            <span></span>
                        </div>
                        <div class="col-6 col-thongtin" data-field="quycach" data-title="Quy cách">
                            <strong>Quy cách <i class="fa fa-edit text-info edit"></i></strong>
                            <span></span>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="col-6 col-thongtin" data-field="nhom" data-title="Nhóm">
                            <strong>Nhóm <i class="fa fa-edit text-info edit"></i></strong>
                            <span></span>
                        </div>
                        <div class="col-6 col-thongtin" data-field="dang" data-title="Dạng hàng hóa">
                            <strong>Dạng hàng hóa <i class="fa fa-edit text-info edit"></i></strong>
                            <span></span>
                        </div>
                    </div>
                    @if($info->id == '1000000000')
                        <div class="divider my-3"></div>
                        <div class="col-thongtin" data-field="gianhap" data-title="Giá nhập">
                            <strong>Giá nhập <i class="fa fa-edit text-info edit"></i></strong>
                            <p><span></span><sup>đ</sup></p>
                        </div>
                    @endif
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="hoatchat" data-title="Hoạt chất">
                        <strong>Hoạt chất <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="congdung" data-title="Công dụng">
                        <strong>Công dụng <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="col-thongtin" data-field="ghichu" data-title="Ghi chú">
                        <strong>Ghi chú <i class="fa fa-edit text-info edit"></i></strong>
                        <span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-custom')
    @include('mobile.danhmuc.hanghoa.js')
@endsection
