<div class="modal fade" id="{{ url()->current() == route('danh-muc.khach-hang') ? 'modalXem' : 'modalXemKH' }}">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thông Tin Khách Hàng</h5>
            </div>
            <div class="modal-body row-thongtin">
                <div class="col-thongtin" data-field="ten" data-title="Tên khách hàng">
                    <strong>Tên <i class="fa fa-edit text-info edit"></i></strong>
                    <span></span>
                </div>
                <div class="divider my-3"></div>
                <div class="form-row">
                    <div class="col-6">
                        <div class="col-thongtin" data-field="ma">
                            <strong>Mã</strong>
                            <span></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="col-thongtin" data-field="danhxung" data-title="Danh xưng">
                            <strong>Danh xưng <i class="fa fa-edit text-info edit"></i></strong>
                            <span></span>
                        </div>
                    </div>
                </div>
                <div class="divider my-3"></div>
                <div class="form-row">
                    <div class="col-6">
                        <div class="col-thongtin" data-field="dienthoai" data-title="Số điện thoại">
                            <strong>Số điện thoại <i class="fa fa-edit text-info edit"></i></strong>
                            <span></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="col-thongtin" data-field="dienthoai2" data-title="Số điện thoại 2">
                            <strong>Số điện thoại 2 <i class="fa fa-edit text-info edit"></i></strong>
                            <span></span>
                        </div>
                    </div>
                </div>
                <div class="divider my-3"></div>
                <div class="form-row">
                    <div class="col-6">
                        <div class="col-thongtin" data-field="congno">
                            <strong>Công nợ</strong>
                            <span class="text-danger font-weight-bolder"></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="col-thongtin" data-field="lancuoi_muahang">
                            <strong>Lần cuối mua hàng</strong>
                            <span></span>
                        </div>
                    </div>
                </div>
                <div class="divider my-3"></div>
                <div class="col-thongtin" data-field="diachi" data-title="Địa chỉ">
                    <strong>Địa chỉ <i class="fa fa-edit text-info edit"></i></strong>
                    <span></span>
                </div>
                <div class="divider my-3"></div>
                <div class="col-thongtin" data-field="caytrong" data-title="Cây trồng">
                    <strong>Cây trồng <i class="fa fa-edit text-info edit"></i></strong>
                    <span></span>
                </div>
                <div class="divider my-3"></div>
                <div class="col-thongtin" data-field="dientich" data-title="Diện tích (công)">
                    <strong>Diện tích (công) <i class="fa fa-edit text-info edit"></i></strong>
                    <span></span>
                </div>
                <div class="divider my-3"></div>
                <div class="col-thongtin" data-field="ghichu" data-title="Ghi chú">
                    <strong>Ghi chú <i class="fa fa-edit text-info edit"></i></strong>
                    <span></span>
                </div>
            </div>
            <div class="modal-footer">
                @if(url()->current() == route('danh-muc.khach-hang') && in_array('danh-muc.khach-hang.action',$info->phanquyen) !== false)
                <button type="button" class="btn bg-gradient-danger delete mr-auto">Xóa Thông Tin</button>
                @endif
                <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
            </div>
        </div>
    </div>
</div>
