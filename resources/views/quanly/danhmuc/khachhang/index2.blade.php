<div class="modal fade" id="modalThemMoi">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Khách Hàng Mới</h5>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="col-9">
                        <div class="form-group required">
                            <label>Tên khách hàng</label>
                            <input type="text" class="form-control inpTen" placeholder="Nhập tên khách hàng...">
                            <span class="error invalid-feedback">Tên khách hàng không được bỏ trống!</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label>Danh xưng</label>
                            <select class="form-control selDanhXung"></select>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-6">
                        <div class="form-group required">
                            <label>Số điện thoại</label>
                            <input type="text" class="form-control inpDienThoai" placeholder="Nhập số điện thoại...">
                            <span class="error invalid-feedback">Số điện thoại không được bỏ trống!</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label>Số điện thoại 2</label>
                            <input type="text" class="form-control inpDienThoai2" placeholder="Nhập số điện thoại 2...">
                        </div>
                    </div>
                </div>
                <div class="diachi-container">
                    <div class="form-group">
                        <label>Chọn tỉnh/thành phố</label>
                        <select class="form-group tinh selTinh"></select>
                    </div>
                    <div class="form-group">
                        <label>Chọn quận/huyện/thị xã</label>
                        <select class="form-group huyen selHuyen"></select>
                    </div>
                    <div class="form-group">
                        <label>Chọn xã/phường/thị trấn</label>
                        <select class="form-group xa selXa"></select>
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ cụ thể</label>
                        <textarea rows="2" class="form-control diachi inpDiaChi" placeholder="Nhập địa chỉ cụ thể..."></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label>Cây trồng</label>
                    <select class="form-control selCayTrong" multiple></select>
                </div>
                <div class="form-group">
                    <label>Diện tích (công)</label>
                    <input type="text" class="form-control inpDienTich" placeholder="Nhập diện tích cây trồng...">
                </div>
                <div class="form-group mb-0">
                    <label>Ghi chú</label>
                    <textarea rows="2" class="form-control inpGhiChu" placeholder="Nhập ghi chú..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <div class="custom-control custom-checkbox mr-auto">
                    <input class="custom-control-input" type="checkbox" id="chkLienTuc">
                    <label class="custom-control-label" for="chkLienTuc">
                        Thêm liên tục
                    </label>
                </div>
                <button type="button" class="btn bg-gradient-primary btnSubmit">Xác Nhận</button>
                <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>
            </div>
        </div>
    </div>
</div>
