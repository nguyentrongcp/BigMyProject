@extends('nongdan.layouts.main')
@section('style-custom')
    <style>

    </style>
@endsection
@section('body')
    <div class="content-wrapper" style="height: calc(100vh - 57px)">
        <!-- Main content -->
        <section class="content">
            <div class="card card-info h-100 mb-0" id="boxDanhSach">
                <div class="card-body p-0">
                    <div class="d-flex h-100 flex-column">
                        <div class="d-flex box-search p-3" style="border-bottom: 1px solid #f9f9f9">
{{--                            <div class="ui icon input w-100">--}}
{{--                                <input class="prompt form-control input-search input-no-border" type="text" placeholder="Nhập từ khóa tìm kiếm...">--}}
{{--                                <i class="link icon times"></i>--}}
{{--                            </div>--}}
                            <div class="ml-auto d-flex">
                                <button class="btn btn-primary btn-sm font-weight-bolder" data-toggle="modal" data-target="#modalThemMoi">
                                    Đăng Ký Mới
                                </button>
                            </div>
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

    <div class="modal fade" id="modalThemMoi">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Đăng Ký Thửa Ruộng Mới</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group required">
                        <label>Mùa vụ</label>
                        <select class="form-control selMuaVu">
                            @foreach($muavus as $muavu)
                                <option value="{{ $muavu->id }}">{{ $muavu->ten }}</option>
                            @endforeach
                        </select>
                        <span class="error invalid-feedback">Bạn chưa chọn mùa vụ!</span>
                    </div>
                    <div class="form-group required">
                        <label>Tên thửa ruộng</label>
                        <input type="text" class="form-control inpTen" placeholder="Nhập tên thửa ruộng...">
                        <span class="error invalid-feedback">Tên thửa ruộng không được bỏ trống!</span>
                    </div>
                    <div class="form-group required">
                        <label>Diện tích (ha)</label>
                        <input type="number" class="form-control inpDienTich" placeholder="Nhập diện tích thửa ruộng...">
                        <span class="error invalid-feedback">Diện tích không hợp lệ!</span>
                    </div>
                    <div class="form-group required">
                        <label>Ngày Sạ</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control inpNgaySa" placeholder="Chọn ngày sạ...">
                        <span class="error invalid-feedback">Ngày sạ không được bỏ trống!</span>
                    </div>
                    <div class="form-group">
                        <label>Tọa độ <span class="btnLayToaDo text-primary ml-2">(Cập nhật ngay)</span></label>
                        <input type="text" readonly class="form-control inpToaDo" placeholder="Chưa có tọa độ">
                    </div>
                    <div class="form-group mb-0">
                        <label>Ghi chú</label>
                        <textarea rows="2" class="form-control inpGhiChu" placeholder="Nhập ghi chú..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-primary btnSubmit">Xác Nhận</button>
                    <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalThongTin">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thông Tin Thửa Ruộng</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="ten field" data-field="ten" data-title="Tên thửa ruộng">
                        <strong>Tên thửa ruộng<i style="font-size: 14px" class="fa fa-edit text-info ml-2 c-pointer"></i></strong>
                        <p class="text-muted mb-0"></p>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="muavu field" data-field="muavu" data-title="Tên mùa vụ">
                        <strong>Tên mùa vụ<i style="font-size: 14px" class="fa fa-edit text-info ml-2 c-pointer"></i></strong>
                        <p class="text-muted mb-0"></p>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="dientich field col" data-field="dientich" data-title="Diện tích">
                            <strong>Diện tích<i style="font-size: 14px" class="fa fa-edit text-info ml-2 c-pointer"></i></strong>
                            <p class="text-muted mb-0"></p>
                        </div>
                        <div class="ngaysa field col" data-field="ngaysa" data-title="Ngày sạ">
                            <strong>Ngày sạ<i style="font-size: 14px" class="fa fa-edit text-info ml-2 c-pointer"></i></strong>
                            <p class="text-muted mb-0"></p>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="form-row">
                        <div class="toado field col" data-field="toado">
                            <strong>Tọa độ<i style="font-size: 14px" class="fa fa-edit text-info ml-2 c-pointer action-toado"></i></strong>
                            <p class="text-muted mb-0"></p>
                        </div>
                        <div class="status field col" data-field="status">
                            <strong>Trạng thái</strong>
                            <p class="text-muted mb-0"></p>
                        </div>
                    </div>
                    <div class="divider my-3"></div>
                    <div class="tinhtrang_hoanthanh field" data-field="tinhtrang_hoanthanh">
                        <strong>Tình trạng hoàn thành</strong>
                        <p class="text-muted mb-0"></p>
                    </div>
                    <div class="divider my-3"></div>
                    <button class="btn btn-danger btn-sm btn-block font-weight-bolder delete">Xóa Thông Tin</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection

@section('js-include')
    @include('nongdan.thuaruong.js')
@endsection
