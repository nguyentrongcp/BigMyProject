<script>
    let tblDanhSach;
    let views = localStorage.getItem('danhmuc.khachhang.views');
    views = isNull(views) ? {} : JSON.parse(views);
    init();
    initDanhSach();

    function init() {
        $('#btnLamMoi').click(() => {
            tblDanhSach.setData('/api/quan-ly/danh-muc/khach-hang/danh-sach' + $('#boxFilter label.active').attr('data-value'));
        })
        $('#boxFilter label').click(function() {
            if (!$(this).hasClass('active')) {
                tblDanhSach.setData('/api/quan-ly/danh-muc/khach-hang/danh-sach' + $(this).attr('data-value'));
            }
        })
        $('#modalActionCongNo').on('shown.bs.modal', function() {
            $(this).find('.inpSoTien').focus();
        }).on('hidden.bs.modal', function() {
            $(this).find('input.is-invalid').removeClass('is-invalid');
            $(this).find('.inpSoTien').val('').trigger('input');
            $(this).find('.inpGhiChu').val('');
        })
        $('#modalActionCongNo input, #modalActionCongNo textarea').keypress(function(e) {
            let keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                $('#modalActionCongNo button.btnSubmit').click();
                e.preventDefault();
                return false;
            }
        }).on('input', function() {
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
            }
        });
    }

    function initDanhSach() {
        let xemThongTin = (e, cell) => {
            let data = cell.getData();
            $.each($('#modalXem .col-thongtin'), function(key, col) {
                clickXemThongTin(data,col);
            })
            @if($info->id == '1000000000')
            if (isNull(data.deleted_at)) {
                $('#modalXem button.delete').attr('class','btn bg-gradient-danger delete')
                    .text('Xóa thông tin').off('click').click(() => {
                    clickXoaThongTin(cell);
                })
            }
            else {
                $('#modalXem button.delete').attr('class','btn bg-gradient-success delete')
                    .text('Phục hồi thông tin').off('click').click(() => {
                    clickPhucHoiThongTin(cell);
                })
            }
            @endif
            $('#modalXem').modal('show');
        }
        let contextMenu = (cell) => {
            let data = cell.getData();
            let subMenus = [];
            for (let col of $('#modalXem .col-thongtin')) {
                let field = $(col).attr('data-field');
                let column = tblDanhSach.getColumn(field);
                let visible = column.isVisible();
                subMenus.push({
                    label: '<i class="fa '
                        + (visible ? 'fa-check-square-o' : 'fa-square-o')
                        + '"></i> ' + $(col).find('strong').text(),
                    action: () => {
                        if (visible) {
                            column.hide();
                            views[field] = false;
                        }
                        else {
                            column.show();
                            views[field] = true;
                        }
                        localStorage.setItem('danhmuc.khachhang.views', JSON.stringify(views))
                    }
                })
            }
            let menus = [
                {
                    label: '<i class="fa fa-info-circle text-info"></i> Chi tiết',
                    action: xemThongTin
                },
                @if($info->id == '1000000000')
                {
                    label: '<i class="fas ' + (isNull(data.deleted_at) ? 'fa-trash-alt text-danger' : 'fa-trash-restore-alt text-success')
                        + '"></i> ' + (isNull(data.deleted_at) ? 'Xóa' : 'Phục hồi'),
                    action: () => {
                        if (isNull(data.deleted_at)) {
                            clickXoaThongTin(cell);
                        }
                        else {
                            clickPhucHoiThongTin(cell);
                        }
                    }
                },
                @endif
                {
                    label: '<i class="fa fa-eye"></i> Hiển thị',
                    menu: subMenus
                }
            ];
            let menuCongNo = [];
            @if($info->id == '1000000000')
                menuCongNo.push({
                    label: '<i class="fa fa-edit"></i> Điều chỉnh công nợ',
                    action: () => {
                        actionCongNo(data)
                    }
                });
            @endif
            if (cell.getData().congno > 0) {
                menuCongNo.unshift({
                    label: '<i class="fa fa-plus"></i> Thu công nợ',
                    action: () => {
                        actionCongNo(data,false)
                    }
                })
            }
            if (menuCongNo.length > 0) {
                menus.unshift({
                    label: '<i class="fa fa-usd text-secondary"></i> Công nợ',
                    menu: menuCongNo
                });
            }
            if ($('#modalXem .col-thongtin[data-field=' + cell.getField() + '] i.edit').length > 0) {
                menus.unshift({
                    label: '<i class="fa fa-edit text-primary"></i> Chỉnh sửa',
                    action: (e, cell) => {
                        let field = cell.getField();
                        let value = cell.getValue();
                        let data = cell.getData();
                        let ten = $('#modalXem .col-thongtin[data-field=' + field + ']').attr('data-title');
                        clickSuaThongTin(field,value,ten,data);
                    }
                });
            }

            return menus;
        }

        tblDanhSach = new Tabulator("#tblDanhSach", {
            columns: [
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", formatter: "rownum",
                    width: 40, headerSort: false, hozAlign: 'center', contextMenu},
                {title: "Mã", field: "ma", vertAlign: 'middle', contextMenu,
                    visible: isNull(views) ? true : views.ma},
                {title: "Tên", field: "ten", vertAlign: 'middle', contextMenu,
                    visible: isNull(views) ? true : views.ten},
                {title: "Danh xưng", field: "danhxung", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.danhxung},
                {title: "Điện thoại", field: "dienthoai", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.dienthoai},
                {title: "Điện thoại 2", field: "dienthoai2", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.dienthoai2},
                {title: "Công nợ", field: "congno", vertAlign: 'middle', hozAlign: 'right', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.congno,
                    formatter: (cell) => {
                        return '<span class="text-danger font-weight-bolder">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }},
                {title: "Địa chỉ", field: "diachi", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.diachi},
                {title: "Cây trồng", field: "caytrong", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.caytrong},
                {title: "Diện tích", field: "dientich", vertAlign: 'middle', hozAlign: 'right', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.dientich},
                {title: "Lần cuối mua hàng", field: "lancuoi_muahang", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.lancuoi_muahang,
                    formatter: (cell) => {
                        return doi_ngay(cell.getValue());
                    }},
                {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.ghichu},
            ],
            rowFormatter: (row) => {
                if (!isNull(row.getData().deleted_at)) {
                    $(row.getElement()).addClass('text-danger');
                }
                else {
                    $(row.getElement()).removeClass('text-danger');
                }
            },
            ajaxURL: '/api/quan-ly/danh-muc/khach-hang/danh-sach',
            height: '450px',
            movableColumns: false,
            pagination: 'local',
            paginationSize: 10,
            pageLoaded: () => {
                tblDanhSach.getColumns()[0].updateDefinition();
            },
            dataFiltered: function () {
                if (isNull(tblDanhSach) || isUndefined(tblDanhSach)) {
                    return false;
                }
                setTimeout(() => {tblDanhSach.getColumns()[0].updateDefinition()},10);
            },
            dataSorted: function () {
                if (isNull(tblDanhSach) || isUndefined(tblDanhSach)) {
                    return false;
                }
                setTimeout(() => {tblDanhSach.getColumns()[0].updateDefinition()},10);
            }
        });
        initSearchTable(tblDanhSach,['ma','dienthoai','dienthoai2','ten']);
    }

    @if($info->id == '1000000000')
    function clickXoaThongTin(cell) {
        sToast.confirm('Xác nhận xóa thông tin khách hàng?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/danh-muc/khach-hang/xoa',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            id: cell.getData().id
                        }
                    }).done((result) => {
                        if (result.succ) {
                            cell.getTable().updateData([{
                                id: cell.getData().id,
                                deleted_at: result.data.deleted_at
                            }])
                            if ($('#modalXem').hasClass('show')) {
                                $('#modalXem button.delete').attr('class','btn bg-gradient-success delete')
                                    .text('Phục hồi thông tin').off('click').click(() => {
                                    clickPhucHoiThongTin(cell);
                                })
                            }
                        }
                    });
                }
            });
    }

    function clickPhucHoiThongTin(cell) {
        sToast.confirm('Xác nhận phục hồi thông tin khách hàng?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang phục hồi dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/danh-muc/khach-hang/phuc-hoi',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            id: cell.getData().id
                        }
                    }).done((result) => {
                        if (result.succ) {
                            cell.getTable().updateData([{
                                id: cell.getData().id,
                                deleted_at: null
                            }])
                            if ($('#modalXem').hasClass('show')) {
                                $('#modalXem button.delete').attr('class','btn bg-gradient-danger delete')
                                    .text('Xóa thông tin').off('click').click(() => {
                                    clickXoaThongTin(cell);
                                })
                            }
                        }
                    });
                }
            });
    }
    @endif



    function actionCongNo(khachhang, is_dieuchinh = true) {
        let modal = $('#modalActionCongNo');
        modal.find('.inpMa').val(khachhang.ma);
        modal.find('.inpTen').val(khachhang.ten);
        modal.find('.inpDienThoai').val(khachhang.dienthoai);
        modal.find('.inpCongNo').val(numeral(khachhang.congno).format('0,0'));
        modal.find('.inpSoTien').val(numeral(khachhang.congno).format('0,0')).trigger('input');

        modal.find('.btnSubmit').text(is_dieuchinh ? 'Tạo Phiếu' : 'Xem Phiếu').off('click').click(() => {
            let sotien = parseFloat(modal.find('.inpSoTien').attr('data-value'));
            if (!is_dieuchinh && (isNaN(sotien) || sotien <= 0)) {
                modal.find('.inpSoTien').addClass('is-invalid');
                return false;
            }
            let ghichu = modal.find('.inpGhiChu').val().trim();

            let data = {
                doituong: {
                    id: khachhang.id,
                    ten: khachhang.ten,
                    dienthoai: khachhang.dienthoai,
                    diachi: khachhang.diachi
                },
                tienthanhtoan: sotien, ghichu
            }

            let actionTaoPhieu = () => {
                sToast.confirm('Xác nhận tạo phiếu ' + (is_dieuchinh ? 'điều chỉnh' : 'thu') + ' công nợ khách hàng?','',
                    (confirmed) => {
                        if (confirmed.isConfirmed) {
                            sToast.loading('Đang tạo phiếu. Vui lòng chờ...');
                            $.ajax({
                                url: '/api/quan-ly/congno-khachhang/' + (is_dieuchinh ? 'dieuchinh-congno' : 'thu-congno'),
                                type: 'get',
                                dataType: 'json',
                                data: {
                                    phieu: JSON.stringify(data)
                                }
                            }).done((result) => {
                                if (result.succ) {
                                    mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu).xemphieu();
                                    $('#modalActionCongNo .inpSoTien, #modalActionCongNo .inpGhiChu').val('').trigger('input');
                                    $('#modalActionCongNo').modal('hide');
                                    tblDanhSach.updateData([{
                                        id: khachhang.id,
                                        congno: result.data.congno
                                    }]);
                                }
                            });
                        }
                    })
            }

            if (!is_dieuchinh) {
                sToast.loading('Đang lấy thông tin phiếu. Vui lòng chờ...');
                $.ajax({
                    url: '/api/quan-ly/phieu/tao-phieu/' + (is_dieuchinh ? 'DCCNKH' : 'TCNKH'),
                    type: 'post',
                    dataType: 'json',
                    data: {
                        phieu: JSON.stringify(data)
                    }
                }).done((result) => {
                    if (result.succ) {
                        mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu).taophieu(actionTaoPhieu)
                    }
                });
            }
            else {
                actionTaoPhieu();
            }
        })

        $('#modalActionCongNo').modal('show').find('.modal-title')
            .text((is_dieuchinh ? 'Điều Chỉnh' : 'Thu') + ' Công Nợ Khách Hàng');
    }
</script>
