<script>
    let tblDanhSach;
    let views = localStorage.getItem('danhmuc.nhacungcap.views');
    views = isNull(views) ? {} : JSON.parse(views);
    init();
    actionThemMoi();
    initDanhSach();

    function init() {
        $('#modalThemMoi input, #modalThemMoi textarea, #modalThemQuyDoi input').keypress(function(e) {
            let keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                if ($('#modalThemMoi').hasClass('show')) {
                    $('#modalThemMoi .btnSubmit').click();
                }
                else {
                    $('#modalThemQuyDoi .btnSubmit').click();
                }
                e.preventDefault();
                return false;
            }
        });

        $('#btnLamMoi').click(() => {
            tblDanhSach.setData('/api/quan-ly/danh-muc/nha-cung-cap/danh-sach');
        })
    }

    function actionThemMoi() {
        $('#modalThemMoi input, #modalThemMoi textarea').on('input', function () {
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
            }
        });

        $('#modalThemMoi').on('shown.bs.modal', function () {
            $(this).find('.inpTen').focus();
        }).on('hidden.bs.modal', function() {
            $(this).find('.is-invalid').removeClass('is-invalid');
        })

        $('#modalThemMoi .btnSubmit').click(() => {
            let ten = $('#modalThemMoi .inpTen').val().trim();
            let dienthoai = $('#modalThemMoi .inpDienThoai').val().trim();
            let dienthoai2 = $('#modalThemMoi .inpDienThoai2').val().trim();
            let sotaikhoan = $('#modalThemMoi .inpSTK').val().trim();
            let sotaikhoan2 = $('#modalThemMoi .inpSTK2').val().trim();
            let nguoidaidien = $('#modalThemMoi .inpNguoiDaiDien').val().trim();
            let chucvu = $('#modalThemMoi .inpSTK2').val().trim();
            let diachi = $('#modalThemMoi .inpDiaChi').val().trim();
            let ghichu = $('#modalThemMoi .inpGhiChu').val().trim();
            let lientuc = $('#chkLienTuc')[0].checked;
            let checked = true;

            if (dienthoai === '')  {
                checked = false;
                showError('dienthoai');
            }
            if (ten === '') {
                checked = false;
                showError('ten', 'Tên nhà cung cấp không được bỏ trống!');
            }

            if (!checked) {
                return false;
            }

            sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
            $.ajax({
                url: '/api/quan-ly/danh-muc/nha-cung-cap/them-moi',
                type: 'get',
                dataType: 'json',
                data: {
                    ten, dienthoai, dienthoai2, sotaikhoan, sotaikhoan2, diachi, ghichu, nguoidaidien, chucvu
                }
            }).done((result) => {
                if (result.succ) {
                    $('#modalThemMoi input, #modalThemMoi textarea').val('');
                    lientuc ? $('#modalThemMoi .inpTen').focus() : $('#modalThemMoi').modal('hide');
                    autosize.update($('#modalThemMoi textarea'));
                    tblDanhSach.addData(result.data.model,true);
                }
                else if (!isUndefined(result.type)) {
                    if (!isUndefined(result.erro)) {
                        showError(result.type,result.erro)
                    }
                    else {
                        showError(result.type)
                    }
                }
            });
        });
    }

    function initDanhSach() {
        let xemThongTin = (e, cell) => {
            let data = cell.getData();
            $.each($('#modalXem .col-thongtin'), function(key, col) {
                clickXemThongTin(data,col);
            })
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
                        localStorage.setItem('danhmuc.nhacungcap.views', JSON.stringify(views))
                    }
                })
            }
            let menus = [
                {
                    label: '<i class="fa fa-info-circle text-info"></i> Chi tiết',
                    action: xemThongTin
                },
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
                {
                    label: '<i class="fa fa-eye"></i> Hiển thị',
                    menu: subMenus
                }
            ];
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
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", contextMenu,
                    formatter: "rownum", width: 40, headerSort: false, hozAlign: 'center'},
                {title: "Mã", field: "ma", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.ma},
                {title: "Tên", field: "ten", vertAlign: 'middle', headerSort: false, minWidth: 160, contextMenu,
                    visible: isNull(views) ? true : views.ten},
                {title: "Điện thoại", field: "dienthoai", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.dienthoai},
                {title: "Điện thoại 2", field: "dienthoai2", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.dienthoai2},
                {title: "Công nợ", field: "congno", vertAlign: 'middle', hozAlign: 'right', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.congno,
                    formatter: (cell) => {
                        return '<span class="text-danger">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }},
                {title: "Số tài khoản", field: "sotaikhoan", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.sotaikhoan},
                {title: "Số tài khoản 2", field: "sotaikhoan2", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.sotaikhoan2},
                {title: "Người đại diện", field: "nguoidaidien", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.nguoidaidien},
                {title: "Chức vụ", field: "chucvu", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.chucvu},
                {title: "Địa chỉ", field: "diachi", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.diachi},
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
            ajaxURL: '/api/quan-ly/danh-muc/nha-cung-cap/danh-sach',
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
            }
        });
        initSearchTable(tblDanhSach,['dienthoai','ten','nguoidaidien']);
    }

    function clickXemThongTin(data, col) {
        let field = $(col).attr('data-field');
        let ten = $(col).attr('data-title');
        let value = data[field];
        if (field === 'congno') {
            value = numeral(value).format(0,0);
        }
        $(col).find('span').text(value);
        let edit = $(col).find('i.edit');
        if (edit.length > 0) {
            edit.off('click').click(() => {
                clickSuaThongTin(field,data[field],ten,data,col);
            })
        }
    }

    function clickSuaThongTin(field, value, ten, data, col = null) {
        let onSubmit = () => {
            let value = $('#modalInput .value').val().trim();
            if (field === 'ten' || field === 'dienthoai') {
                showErrorModalInput(ten + ' không được bỏ trống!');
                return false;
            }
            sToast.confirm('Xác nhận cập nhật ' + ten.toLowerCase() + '?','',
                (result) => {
                    if (result.isConfirmed) {
                        sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/danh-muc/nha-cung-cap/cap-nhat',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                id: data.id,
                                field, value
                            }
                        }).done((result) => {
                            if (result.succ) {
                                $('#modalInput').modal('hide');
                                tblDanhSach.updateData([{...result.data.model}]);
                                setTimeout(() => {tblDanhSach.getColumns()[0].updateDefinition()},10);
                                if (!isNull(col)) {
                                    clickXemThongTin(data,col);
                                }
                            }
                        });
                    }
                });
        }
        if (['ten','dienthoai','dienthoai2','sotaikhoan','sotaikhoan2','nguoidaidien','chucvu'].indexOf(field) !== -1) {
            mInput(data.ten,value,field === 'ten' || field === 'dienthoai').text(ten,ten + '...',onSubmit);
        }
        if (field === 'ghichu' || field === 'diachi') {
            mInput(data.ten,value).textarea(ten,ten + '...',onSubmit);
        }
    }

    function clickXoaThongTin(cell) {
        sToast.confirm('Xác nhận xóa thông tin nhà cung cấp?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/danh-muc/nha-cung-cap/xoa',
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
        sToast.confirm('Xác nhận phục hồi thông tin nhà cung cấp?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang phục hồi dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/danh-muc/nha-cung-cap/phuc-hoi',
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

    function showError(type, erro = '') {
        let inputs = {
            ten: $('#modalThemMoi .inpTen'),
            dienthoai: $('#modalThemMoi .inpDienThoai'),
        }
        if (erro !== '') {
            $(inputs[type].parent()).find('span.error').text(erro);
        }
        inputs[type].addClass('is-invalid');
        inputs[type].focus();
    }
</script>
