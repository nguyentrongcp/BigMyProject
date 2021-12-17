@section('js-custom')
    <script>
        let table = {
            phan: null,
            thuoc: null
        };
        let views = localStorage.getItem('quytrinhlua.quytrinh.views');
        views = isNull(views) ? {} : JSON.parse(views);
        let muavus = JSON.parse('{!! str_replace("'","\'",$muavus) !!}')
        init();

        function init() {
            $('#btnLamMoi').click(() => {
                $('#boxTabMain .nav-link.active').trigger('shown.bs.tab');
            })
            $('#selMuaVu').select2({
                data: muavus,
                minimumResultsForSearch: -1,
                placeholder: 'Bạn chưa chọn mùa vụ'
            }).change(function () {
                if (!isNull($(this).val())) {
                    $('#modalThemMoi .inpMuaVu').val($(this).select2('data')[0].text);
                    $('#btnLamMoi').click();
                }
            }).trigger('change');
            $('#tabPhanBon').on('shown.bs.tab', () => {
                if (table.phan == null) {
                    initDanhSach('phan');
                }
                table.phan.setData('/api/quan-ly/quy-trinh-lua/quy-trinh/danh-sach?phanloai=phan&muavu_id=' + $('#selMuaVu').val());
            }).trigger('shown.bs.tab');
            $('#tabThuoc').on('shown.bs.tab', () => {
                if (table.thuoc == null) {
                    initDanhSach('thuoc');
                }
                table.thuoc.setData('/api/quan-ly/quy-trinh-lua/quy-trinh/danh-sach?phanloai=thuoc&muavu_id=' + $('#selMuaVu').val());
            })

            @if(in_array('quy-trinh-lua.quy-trinh.chinh-sua',$info->phanquyen) === false)
            $('#modalXem .col-thongtin i').remove();
            @endif
        }

        @if(in_array('quy-trinh-lua.quy-trinh.them-moi',$info->phanquyen) !== false)
        initActionThemMoi();
        function initActionThemMoi() {
            $('#modalThemMoi .selSanPham').html(null).select2({
                ajax: {
                    url: '/api/quan-ly/quy-trinh-lua/san-pham/tim-kiem',
                    data: function (params) {
                        let query = {
                            q: params.term
                        };

                        // Query parameters will be ?search=[term]&type=public
                        return query;
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: 'Chọn sản phẩm...'
            }).on('select2:select', () => {
                $('#modalThemMoi .selSanPham').removeClass('is-invalid');
            })

            offEnterTextarea($('#modalThemMoi input, #modalThemMoi textarea'),() => {$('#modalThemMoi .btnSubmit').click();})
            $('#modalThemMoi input, #modalThemMoi textarea').on('input', function () {
                if ($(this).hasClass('is-invalid')) {
                    $(this).removeClass('is-invalid');
                }
            });

            $('#modalThemMoi').on('shown.bs.modal', function () {
                $(this).find('.inpGiaiDoan').focus();
            }).on('hidden.bs.modal', function() {
                $(this).find('.is-invalid').removeClass('is-invalid');
            })

            autosize($('#modalThemMoi textarea'));

            $('#btnThemQuyTrinh').click(() => {
                mInput('Thêm Nhóm Quy Trình Mới','',true).text('Tên nhóm quy trình','Nhập tên nhóm quy trình...',
                    () => {
                        let value = $('#modalInput .value').val().trim();
                        if (value === '') {
                            showErrorModalInput('Tên nhóm quy trình không được bỏ trống!');
                            return false;
                        }
                        sToast.confirm('Xác nhận thêm nhóm quy trình mới?','',
                            (confirmed) => {
                                if (confirmed.isConfirmed) {
                                    sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
                                    $.ajax({
                                        url: '/api/quan-ly/quy-trinh-lua/quy-trinh/them-quy-trinh',
                                        type: 'get',
                                        dataType: 'json',
                                        data: {
                                            ten: value
                                        }
                                    }).done((result) => {
                                        if (result.succ) {
                                            $('#modalInput').modal('hide');
                                        }
                                        else if (!isUndefined(result.erro)) {
                                            showErrorModalInput(result.erro);
                                        }
                                    });
                                }
                            })
                    })
            })

            $('#modalThemMoi .btnSubmit').click(() => {
                let muavu_id = $('#selMuaVu').val();
                if (isNull(muavu_id)) {
                    sToast.toast(0,'Bạn chưa chọn mùa vụ!');
                    return false;
                }
                let giaidoan = $('#modalThemMoi .inpGiaiDoan').val().trim();
                let sanpham_id = $('#modalThemMoi .selSanPham').val();
                let tu = parseInt($('#modalThemMoi .inpFrom').val());
                let den = parseInt($('#modalThemMoi .inpTo').val());
                let congdung = $('#modalThemMoi .inpCongDung').val().trim();
                let ghichu = $('#modalThemMoi .inpGhiChu').val().trim();
                let soluong = parseFloat($('#modalThemMoi .inpSoLuong').val());
                let lientuc = $('#chkLienTuc')[0].checked;
                let checked = true;

                if (isNaN(soluong) || soluong < 0)  {
                    checked = false;
                    showError('soluong');
                }
                if (sanpham_id == null) {
                    checked = false;
                    showError('sanpham_id','Bạn chưa chọn sản phẩm!');
                }
                if (congdung === '') {
                    checked = false;
                    showError('congdung')
                }
                if (isNaN(den)) {
                    checked = false;
                    showError('den')
                }
                if (isNaN(tu)) {
                    checked = false;
                    showError('tu')
                }
                if (giaidoan === '') {
                    checked = false;
                    showError('giaidoan')
                }

                if (!checked) {
                    return false;
                }

                sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
                $.ajax({
                    url: '/api/quan-ly/quy-trinh-lua/quy-trinh/them-moi',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        giaidoan,tu,den,congdung,sanpham_id,soluong,ghichu,muavu_id
                    }
                }).done((result) => {
                    if (result.succ) {
                        $('#modalThemMoi .inpSoLuong, #modalThemMoi textarea').val('').trigger('input');
                        $('#modalThemMoi .selSanPham').val(null).trigger('change');
                        lientuc ? $('#modalThemMoi .inpGiaiDoan').focus() : $('#modalThemMoi').modal('hide');
                        if (result.data.model.phanloai === 'Phân bón') {
                            if ($('#tabPhanBon').hasClass('active')) {
                                $('#btnLamMoi').click();
                            }
                        }
                        else {
                            if ($('#tabThuoc').hasClass('active')) {
                                $('#btnLamMoi').click();
                            }
                        }
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

        function initDanhSach(name) {
            let xemThongTin = (e, cell) => {
                let data = cell.getData();
                $.each($('#modalXem .col-thongtin'), function(key, col) {
                    clickXemThongTin(data,col);
                })
                @if(in_array('quy-trinh-lua.quy-trinh.action',$info->phanquyen) !== false)
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
                    if (field === 'giaidoan') {
                        continue;
                    }
                    let column = table[name].getColumn(field);
                    let visible = column.isVisible();
                    subMenus.push({
                        label: '<i class="fa '
                            + (visible ? 'fa-check-square-o' : 'fa-square-o')
                            + '"></i> ' + $(col).find('strong').text(),
                        action: () => {
                            if (visible) {
                                table.phan.getColumn('donvitinh').hide();
                                table.thuoc.getColumn('donvitinh').hide();
                                views[field] = false;
                            }
                            else {
                                table.phan.getColumn('donvitinh').show();
                                table.thuoc.getColumn('donvitinh').show();
                                views[field] = true;
                            }
                            localStorage.setItem('quytrinhlua.quytrinh.views', JSON.stringify(views))
                        }
                    })
                }
                let menus = [
                    {
                        label: '<i class="fa fa-info-circle text-info"></i> Chi tiết',
                        action: xemThongTin
                    },
                        @if(in_array('quy-trinh-lua.quy-trinh.action',$info->phanquyen) !== false)
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
                @if(in_array('quy-trinh-lua.quy-trinh.chinh-sua',$info->phanquyen) !== false)
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

            table[name] = new Tabulator("#" + (name === 'phan' ? 'tblDanhSachPhan' : 'tblDanhSachThuoc'), {
                columns: [
                    {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", contextMenu,
                        formatter: "rownum", headerSort: false, hozAlign: 'center'},
                    {title: "Từ (ngày)", field: "tu", vertAlign: 'middle', headerSort: false, contextMenu, hozAlign: 'right',
                        visible: isNull(views) ? true : views.tu},
                    {title: "Đến (ngày)", field: "den", vertAlign: 'middle', headerSort: false, contextMenu, hozAlign: 'right',
                        visible: isNull(views) ? true : views.den},
                    {title: "Sản phẩm", field: "sanpham", vertAlign: 'middle', headerSort: false, contextMenu,
                        visible: isNull(views) ? true : views.sanpham},
                    {title: "Đơn vị tính", field: "donvitinh", vertAlign: 'middle', headerSort: false, contextMenu,
                        visible: isNull(views) ? true : views.donvitinh},
                    {title: "Phân loại", field: "phanloai", vertAlign: 'middle', headerSort: false, contextMenu,
                        visible: isNull(views) ? true : views.phanloai},
                    {title: "Công dụng", field: "congdung", vertAlign: 'middle', headerSort: false, contextMenu,
                        visible: isNull(views) ? true : views.congdung},
                    {title: "Đơn giá", field: "dongia", vertAlign: 'middle', headerSort: false, contextMenu, hozAlign: 'right',
                        visible: isNull(views) ? true : views.dongia, formatter: (cell) => {
                            return numeral(cell.getValue()).format('0,0');
                        }},
                    {title: "Số lượng", field: "soluong", vertAlign: 'middle', headerSort: false, contextMenu, hozAlign: 'right',
                        visible: isNull(views) ? true : views.soluong},
                    {title: "Thành tiền", field: "thanhtien", vertAlign: 'middle', headerSort: false, contextMenu, hozAlign: 'right',
                        visible: isNull(views) ? true : views.thanhtien, formatter: (cell) => {
                            return numeral(cell.getValue()).format('0,0');
                        }},
                    {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu,
                        visible: isNull(views) ? true : views.ghichu}
                ],
                @if(in_array('quy-trinh-lua.quy-trinh.action',$info->phanquyen) !== false)
                rowFormatter: (row) => {
                    if (!isNull(row.getData().deleted_at)) {
                        $(row.getElement()).addClass('text-danger');
                    }
                    else {
                        $(row.getElement()).removeClass('text-danger');
                    }
                },
                @endif
                height: '450px',
                layoutColumnsOnNewData: true,
                groupBy: 'giaidoan',
                groupHeader: function(value, count){
                    return value + '<span class="text-danger ml-2">(' + count + ' quy trình)</span>';
                },
                pageLoaded: () => {
                    table[name].getColumns()[0].updateDefinition();
                },
                dataFiltered: function () {
                    if (isNull(table[name]) || isUndefined(table[name])) {
                        return false;
                    }
                    setTimeout(() => {table[name].getColumns()[0].updateDefinition()},10);
                },
                dataChanged: () => {
                    table[name].getColumns()[0].updateDefinition();
                }
            });
            // initSearchTable(tblDanhSach,['sanpham']);
        }

        function clickXemThongTin(data, col) {
            let field = $(col).attr('data-field');
            let ten = $(col).attr('data-title');
            let value = data[field];
            if (field === 'dongia' || field === 'thanhtien') {
                value = numeral(value).format('0,0');
            }
            $(col).find('span').text(value);
            @if(in_array('quy-trinh-lua.quy-trinh.chinh-sua',$info->phanquyen) !== false)
            let edit = $(col).find('i.edit');
            if (edit.length > 0) {
                edit.off('click').click(() => {
                    clickSuaThongTin(field,value,ten,data,col);
                })
            }
            @endif
        }

        @if(in_array('quy-trinh-lua.quy-trinh.chinh-sua',$info->phanquyen) !== false)
        function clickSuaThongTin(field, value, ten, data, col = null) {
            let onSubmit = () => {
                let value = $('#modalInput .value').val();
                if (field !== 'sanpham') {
                    value = value.trim();
                    if (['tu','den','soluong'].indexOf(field) > -1) {
                        value = parseFloat(value);
                        if (field === 'soluong' && (isNaN(value) || value < 0)) {
                            showErrorModalInput('Số lượng không hợp lệ!');
                            return false;
                        }
                        if (['tu','den'].indexOf(field) > -1 && isNaN(value)) {
                            showErrorModalInput(ten + ' không hợp lệ!');
                            return false;
                        }
                    }
                    else if (field !== 'ghichu' && value === '') {
                        showErrorModalInput(ten + ' không được bỏ trống!');
                        return false;
                    }
                }
                else if (value == null) {
                    showErrorModalInput('Bạn chưa chọn sản phẩm!');
                    return false;
                }
                sToast.confirm('Xác nhận cập nhật ' + ten.toLowerCase() + '?','',
                    (result) => {
                        if (result.isConfirmed) {
                            sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                            $.ajax({
                                url: '/api/quan-ly/quy-trinh-lua/quy-trinh/cap-nhat',
                                type: 'get',
                                dataType: 'json',
                                data: {
                                    id: data.id,
                                    field: field === 'sanpham' ? 'sanpham_id' : field, value
                                }
                            }).done((result) => {
                                if (result.succ) {
                                    $('#modalInput').modal('hide');
                                    if (field === 'sanpham') {
                                        if (result.data.model.phanloai === 'Phân bón') {
                                            if ($('#tabPhanBon').hasClass('active')) {
                                                table.phan.updateData([{...result.data.model}])
                                            }
                                        }
                                        else {
                                            if ($('#tabThuoc').hasClass('active')) {
                                                table.thuoc.updateData([{...result.data.model}])
                                            }
                                        }
                                    }
                                    else {
                                        if ($('#tabPhanBon').hasClass('active')) {
                                            table.phan.updateData([{...result.data.model}])
                                        }
                                        else {
                                            table.thuoc.updateData([{...result.data.model}])
                                        }
                                    }
                                    if (!isNull(col)) {
                                        clickXemThongTin(data,col);
                                    }
                                }
                            });
                        }
                    });
            }
            if (field === 'giaidoan') {
                mInput(data.ten,value,true).text(ten,ten + '...',onSubmit);
            }
            if (['ghichu','congdung'].indexOf(field) !== -1) {
                mInput(data.ten,value,field === 'congdung').textarea(ten,ten + '...',onSubmit);
            }
            if (['tu','den','soluong'].indexOf(field) !== -1) {
                mInput(data.ten,value,true).number(ten,ten + '...',onSubmit);
            }
            if (field === 'sanpham') {
                mInput(data.ten,value,true).select2(ten,'Chọn sản phẩm...','/api/quan-ly/quy-trinh-lua/san-pham/tim-kiem',false,onSubmit);
            }
        }
        @endif

        @if(in_array('quy-trinh-lua.quy-trinh.action',$info->phanquyen) !== false)
        function clickXoaThongTin(cell) {
            sToast.confirm('Xác nhận xóa thông tin quy trình sử dụng?',cell.getData().ten,
                (result) => {
                    if (result.isConfirmed) {
                        sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/quy-trinh-lua/quy-trinh/xoa',
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
                            url: '/api/quan-ly/quy-trinh-lua/quy-trinh/phuc-hoi',
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
                giaidoan: $('#modalThemMoi .inpGiaiDoan'),
                tu: $('#modalThemMoi .inpFrom'),
                den: $('#modalThemMoi .inpTo'),
                congdung: $('#modalThemMoi .inpCongDung'),
                soluong: $('#modalThemMoi .inpSoLuong'),
                sanpham_id: $('#modalThemMoi .selSanPham'),
            }
            if (erro !== '') {
                $(inputs[type].parent()).find('span.error').text(erro);
            }
            inputs[type].addClass('is-invalid');
            inputs[type].focus();
        }
    </script>
@stop
