@section('js-custom')
    <script>
        let tblDanhSach;
        let views = localStorage.getItem('quytrinhlua.sanpham.views');
        views = isNull(views) ? {} : JSON.parse(views);
        let donvitinhs = JSON.parse('{!! str_replace("'","\'",json_encode($donvitinhs)) !!}');
        let nhoms = JSON.parse('{!! str_replace("'","\'",json_encode($nhoms)) !!}');
        nhoms.forEach((value) => {
            value.id = value.text;
        })
        donvitinhs.forEach((value) => {
            value.id = value.text;
        })
        let dangs = [
            { id: 'Lỏng', text: 'Lỏng' },
            { id: 'Bột', text: 'Bột' },
            { id: 'Hạt', text: 'Hạt' }
        ]
        init();
        initDanhSach();

        function init() {
            $('#btnLamMoi').click(() => {
                tblDanhSach.setData('/api/quan-ly/quy-trinh-lua/san-pham/danh-sach');
            })

            @if(in_array('quy-trinh-lua.san-pham.chinh-sua',$info->phanquyen) === false)
            $('#modalXem .col-thongtin i').remove();
            @endif
        }

        @if(in_array('quy-trinh-lua.san-pham.them-moi',$info->phanquyen) !== false)
        initActionThemMoi();
        function initActionThemMoi() {
            initSelect2($('#modalThemMoi .selNhom'),nhoms)
            initSelect2($('#modalThemMoi .selDonViTinh'),donvitinhs)
            initSelect2($('#modalThemMoi .selDang'),dangs,{minimumResultsForSearch: -1, allowClear: true, placeholder: 'Dạng hàng hóa...'});
            $('#modalThemMoi .selHangHoa').html(null).select2({
                ajax: {
                    url: '/api/quan-ly/ban-hang/tim-kiem',
                    data: function (params) {
                        let query = {
                            q: params.term
                        };

                        // Query parameters will be ?search=[term]&type=public
                        return query;
                    },
                    delay: 300
                },
                templateResult: (value) => {
                    if (!isUndefined(value.ma)) {
                        return value.ma + ' - ' + value.ten;
                    }
                    else {
                        return value.text;
                    }
                },
                templateSelection: (value) => {
                    if (!isUndefined(value.ma)) {
                        return value.ma + ' - ' + value.ten;
                    }
                    else {
                        return value.text;
                    }
                },
                allowClear: true,
                placeholder: 'Chọn hàng hóa...'
            }).change(function () {
                if ($(this).val() != null) {
                    let hanghoa = $(this).select2('data')[0];
                    $('#modalThemMoi .inpTen').val(hanghoa.ten).trigger('input');
                    $('#modalThemMoi .selDonViTinh').val(hanghoa.donvitinh).trigger('change');
                    $('#modalThemMoi .selNhom').val(hanghoa.nhom).trigger('change');
                    $('#modalThemMoi .selDang').val(hanghoa.dang).trigger('change');
                    $('#modalThemMoi .inpDonGia').val(hanghoa.dongia).trigger('input');
                    setTimeout(() => {$('#modalThemMoi .inpGhiChu').focus()}, 100)
                }
            });

            offEnterTextarea($('#modalThemMoi input, #modalThemMoi textarea'),() => {$('#modalThemMoi .btnSubmit').click();})
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

            autosize($('#modalThemMoi textarea'));

            $('#modalThemMoi .btnSubmit').click(() => {
                let ten = $('#modalThemMoi .inpTen').val().trim();
                let donvitinh = $('#modalThemMoi .selDonViTinh').val();
                let dongia = parseInt($('#modalThemMoi .inpDonGia').attr('data-value'));
                let nhom = $('#modalThemMoi .selNhom').val();
                let dang = $('#modalThemMoi .selDang').val();
                let ghichu = $('#modalThemMoi .inpGhiChu').val().trim();
                let lientuc = $('#chkLienTuc')[0].checked;
                let checked = true;

                if (isNaN(dongia) || dongia < 0)  {
                    checked = false;
                    showError('dongia');
                }
                if (ten === '') {
                    checked = false;
                    showError('ten','Tên hàng hóa không được bỏ trống!');
                }

                if (!checked) {
                    return false;
                }

                sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
                $.ajax({
                    url: '/api/quan-ly/quy-trinh-lua/san-pham/them-moi',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        ten,donvitinh,nhom,dongia,ghichu,dang
                    }
                }).done((result) => {
                    if (result.succ) {
                        $('#modalThemMoi input, #modalThemMoi textarea').val('').trigger('input');
                        $('#modalThemMoi .selDang').val(null).trigger('change');
                        lientuc ? $('#modalThemMoi .selHangHoa').select2('open') : $('#modalThemMoi').modal('hide');
                        tblDanhSach.addData(result.data.model,true);
                        autosize.update($('#modalThemMoi textarea'));
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
        @endif

        function initDanhSach() {
            let xemThongTin = (e, cell) => {
                let data = cell.getData();
                $.each($('#modalXem .col-thongtin'), function(key, col) {
                    clickXemThongTin(data,col);
                })
                @if(in_array('quy-trinh-lua.san-pham.action',$info->phanquyen) !== false)
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
                            localStorage.setItem('quytrinhlua.sanpham.views', JSON.stringify(views))
                        }
                    })
                }
                let menus = [
                    {
                        label: '<i class="fa fa-info-circle text-info"></i> Chi tiết',
                        action: xemThongTin
                    },
                        @if(in_array('quy-trinh-lua.san-pham.action',$info->phanquyen) !== false)
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
                @if(in_array('quy-trinh-lua.san-pham.chinh-sua',$info->phanquyen) !== false)
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
                @endif

                return menus;
            }

            tblDanhSach = new Tabulator("#tblDanhSach", {
                columns: [
                    {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", contextMenu,
                        formatter: "rownum", headerSort: false, hozAlign: 'center'},
                    {title: "Mã", field: "ma", vertAlign: 'middle', contextMenu,
                        visible: isNull(views) ? true : views.ma},
                    {title: "Tên", field: "ten", vertAlign: 'middle', contextMenu,
                        visible: isNull(views) ? true : views.ten},
                    {title: "ĐVT", field: "donvitinh", vertAlign: 'middle', contextMenu,
                        visible: isNull(views) ? true : views.donvitinh},
                    {title: "Nhóm", field: "nhom", vertAlign: 'middle', contextMenu,
                        visible: isNull(views) ? true : views.nhom},
                    {title: "Đơn giá", field: "dongia", hozAlign: 'right', vertAlign: 'middle', headerSort: false,
                        contextMenu, visible: isNull(views) ? true : views.dongia,
                        formatter: (cell) => {
                            return numeral(cell.getValue()).format('0,0');
                        }},
                    {title: "Dạng", field: "dang", vertAlign: 'middle', headerSort: false, contextMenu,
                        visible: isNull(views) ? false : views.dang},
                    {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu,
                        visible: isNull(views) ? true : views.ghichu}
                ],
                @if(in_array('quy-trinh-lua.san-pham.action',$info->phanquyen) !== false)
                rowFormatter: (row) => {
                    if (!isNull(row.getData().deleted_at)) {
                        $(row.getElement()).addClass('text-danger');
                    }
                    else {
                        $(row.getElement()).removeClass('text-danger');
                    }
                },
                @endif
                dataSorted: () => {
                    if (isNull(tblDanhSach) || isUndefined(tblDanhSach)) {
                        return false;
                    }
                    setTimeout(() => {tblDanhSach.getColumns()[0].updateDefinition()},10);
                },
                ajaxURL: '/api/quan-ly/quy-trinh-lua/san-pham/danh-sach',
                height: '450px',
                movableColumns: false,
                pagination: 'local',
                paginationSize: 10,
                layoutColumnsOnNewData: true,
                pageLoaded: () => {
                    tblDanhSach.getColumns()[0].updateDefinition();
                },
                dataFiltered: function () {
                    if (isNull(tblDanhSach) || isUndefined(tblDanhSach)) {
                        return false;
                    }
                    setTimeout(() => {tblDanhSach.getColumns()[0].updateDefinition()},10);
                },
                dataChanged: () => {
                    tblDanhSach.getColumns()[0].updateDefinition();
                }
            });
            initSearchTable(tblDanhSach,['ma','ten','nhom']);
        }

        function clickXemThongTin(data, col) {
            let field = $(col).attr('data-field');
            let ten = $(col).attr('data-title');
            let value = data[field];
            if (['dongia'].indexOf(field) !== -1) {
                value = numeral(value).format('0,0');
            }
            $(col).find('span').text(value);
            @if(in_array('quy-trinh-lua.san-pham.chinh-sua',$info->phanquyen) !== false)
            let edit = $(col).find('i.edit');
            if (edit.length > 0) {
                edit.off('click').click(() => {
                    clickSuaThongTin(field,value,ten,data,col);
                })
            }
            @endif
        }

        @if(in_array('quy-trinh-lua.san-pham.chinh-sua',$info->phanquyen) !== false)
        function clickSuaThongTin(field, value, ten, data, col = null) {
            let onSubmit = () => {
                let value = $('#modalInput .value').val();
                if (field !== 'dang') {
                    value = value.trim();
                }
                if (field === 'dongia') {
                    value = $('#modalInput .value').attr('data-value');
                    if (isNaN(parseFloat(value)) || parseFloat(value) < 0) {
                        showErrorModalInput('Đơn giá không hợp lệ!');
                        return false;
                    }
                }
                if (field === 'ten' && value === '') {
                    showErrorModalInput('Tên hàng hóa không được bỏ trống!');
                    return false;
                }
                sToast.confirm('Xác nhận cập nhật ' + ten.toLowerCase() + '?','',
                    (result) => {
                        if (result.isConfirmed) {
                            sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                            $.ajax({
                                url: '/api/quan-ly/quy-trinh-lua/san-pham/cap-nhat',
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
                                    if (!isNull(col)) {
                                        clickXemThongTin(data,col);
                                    }
                                }
                            });
                        }
                    });
            }
            if (field === 'ten') {
                mInput(data.ten,value,true).text(ten,ten + '...',onSubmit);
            }
            if (['ghichu'].indexOf(field) !== -1) {
                mInput(data.ten,value).textarea(ten,ten + '...',onSubmit);
            }
            if (field === 'dongia') {
                mInput(data.ten,value).numeral(ten,ten + '...',onSubmit);
            }
            if (['donvitinh','nhom','dang'].indexOf(field) !== -1) {
                let fields = {
                    donvitinh: donvitinhs,
                    nhom: nhoms,
                    dangs: dangs,
                }
                mInput(data.ten,value).select2(ten,'',fields[field],true,onSubmit);
            }
        }
        @endif

        @if(in_array('quy-trinh-lua.san-pham.action',$info->phanquyen) !== false)
        function clickXoaThongTin(cell) {
            sToast.confirm('Xác nhận xóa thông tin sản phẩm?',cell.getData().ten,
                (result) => {
                    if (result.isConfirmed) {
                        sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/quy-trinh-lua/san-pham/xoa',
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
            sToast.confirm('Xác nhận phục hồi thông tin hàng hóa?','',
                (result) => {
                    if (result.isConfirmed) {
                        sToast.loading('Đang phục hồi dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/quy-trinh-lua/san-pham/phuc-hoi',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                id: cell.getData().id
                            }
                        }).done((result) => {
                            if (result.succ) {
                                $('#modalInput').modal('hide');
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

        function showError(type, erro = '') {
            let inputs = {
                ten: $('#modalThemMoi .inpTen'),
                dongia: $('#modalThemMoi .inpDonGia'),
            }
            if (erro !== '') {
                $(inputs[type].parent()).find('span.error').text(erro);
            }
            inputs[type].addClass('is-invalid');
            inputs[type].focus();
        }
    </script>
@stop
