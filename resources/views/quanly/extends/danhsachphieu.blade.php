<div class="modal fade" id="modalDanhSachPhieu">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title w-100 text-center">Danh Sách Phiếu <span class="danhsachphieu-title"></span></h4>
            </div>
            <div class="modal-body">
                <div class="d-flex">
                    <div class="d-flex box-search-table flex-grow-1" data-target="tblDanhSachPhieu">
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
                    <div class="ml-auto d-flex">
                        <div style="width: 225px">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                </div>
                                <input type="text" placeholder="Từ ngày..." class="form-control float-right" id="fromToDate" autocomplete="off">
                            </div>
                        </div>
                        <button style="width: 50px" class="btn bg-gradient-primary btnXem font-weight-bolder ml-1">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div id="tblDanhSachPhieu" class="mt-1"></div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
