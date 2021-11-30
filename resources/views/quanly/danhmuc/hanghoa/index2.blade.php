<div class="modal fade" id="{{ url()->current() == route('danh-muc.hang-hoa') ? 'modalXem' : 'modalXemHH' }}">
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
                @if(url()->current() == route('danh-muc.hang-hoa') && in_array('role.gia-nhap',$info->phanquyen) !== false)
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
                @if(url()->current() == route('danh-muc.hang-hoa') && in_array('danh-muc.hang-hoa.action',$info->phanquyen) !== false)
                <button type="button" class="btn bg-gradient-danger delete mr-auto">Xóa Thông Tin</button>
                @endif
                <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
            </div>
        </div>
    </div>
</div>
