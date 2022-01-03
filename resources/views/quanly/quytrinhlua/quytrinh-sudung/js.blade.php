@section('js-custom')
    <script>
        let table = {
            phan: null,
            thuoc: null
        };
        let giaidoans = [];
        let views = localStorage.getItem('quytrinhlua.quytrinh.views');
        views = isNull(views) ? {} : JSON.parse(views);
        let muavus = JSON.parse('{!! str_replace("'","\'",$muavus) !!}')
        init();

        function init() {
            $('#btnLamMoi').click(() => {
                setDataTable($('#boxTabMain .nav-link.active').attr('data-title'));
            })
            $('#selMuaVu').select2({
                data: muavus,
                minimumResultsForSearch: -1,
                placeholder: 'Bạn chưa chọn mùa vụ'
            }).change(function () {
                if (!isNull($(this).val())) {
                    $('#modalThemMoi .inpMuaVu, #modalThemGiaiDoan .inpMuaVu').val($(this).select2('data')[0].text);
                    $('#btnLamMoi').click();
                    getDataGiaiDoan();
                }
                else {
                    initSelGiaiDoan([]);
                }
            }).trigger('change');
            initDanhSach('phan');
            setDataTable('Phân bón');
            initDanhSach('thuoc');
            setDataTable('Thuốc')
            $('#tabPhanBon').on('shown.bs.tab', () => {
                if (table.phan == null) {
                    initDanhSach('phan');
                    setDataTable('Phân bón');
                }
                initSearchTable(table.phan,['sanpham'],'tblDanhSach');
            }).trigger('shown.bs.tab');
            $('#tabThuoc').on('shown.bs.tab', () => {
                if (table.thuoc == null) {
                    initDanhSach('thuoc');
                    setDataTable('Thuốc')
                }
                initSearchTable(table.thuoc,['sanpham'],'tblDanhSach');
            })

            @if(in_array('quy-trinh-lua.quy-trinh.chinh-sua',$info->phanquyen) === false)
            $('#modalXem .col-thongtin i').remove();
            @else
            offEnterTextarea($('#modalSuaGiaiDoan input'),() => {$('#modalSuaGiaiDoan .btnSubmit').click();})
            $('#modalThemMoi .btnSuaGiaiDoan').click(() => {
                if ($('#modalThemMoi .selGiaiDoan').val() != null) {
                    let data = $('#modalThemMoi .selGiaiDoan').select2('data')[0];
                    $('#modalSuaGiaiDoan .inpTen').val(data.ten);
                    $('#modalSuaGiaiDoan .inpFrom').val(data.tu);
                    $('#modalSuaGiaiDoan .inpTo').val(data.den);
                    $('#modalSuaGiaiDoan .selPhanLoai').val(data.phanloai).trigger('change');
                    $('#modalSuaGiaiDoan').modal('show').find('.btnSubmit').off('click').click(() => {
                        let ten = $('#modalSuaGiaiDoan .inpTen').val().trim();
                        let tu = parseInt($('#modalSuaGiaiDoan .inpFrom').val());
                        let den = parseInt($('#modalSuaGiaiDoan .inpTo').val());
                        let phanloai = $('#modalSuaGiaiDoan .selPhanLoai').val();
                        if (ten === '') {
                            $('#modalSuaGiaiDoan .inpTen').addClass('is-invalid').focus();
                            return false;
                        }
                        if (isNaN(tu)) {
                            $('#modalSuaGiaiDoan .inpFrom').addClass('is-invalid').focus();
                            return false;
                        }
                        if (isNaN(den)) {
                            $('#modalSuaGiaiDoan .inpTo').addClass('is-invalid').focus();
                            return false;
                        }

                        sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...');
                        $.ajax({
                            url: '/api/quan-ly/quy-trinh-lua/giai-doan/cap-nhat',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                ten,tu,den,phanloai, id: data.id
                            }
                        }).done((result) => {
                            if (result.succ) {
                                $('#modalSuaGiaiDoan input').val('').trigger('input');
                                $('#modalSuaGiaiDoan').modal('hide');
                                giaidoans.forEach((value) => {
                                    if (value.id === result.data.model.id) {
                                        value.ten = result.data.model.ten;
                                        value.tu = result.data.model.tu;
                                        value.den = result.data.model.den;
                                        value.phanloai = result.data.model.phanloai;
                                    }
                                })
                                initSelGiaiDoan(giaidoans,result.data.model.id);
                            }
                            else if (!isUndefined(result.type)) {
                                if (result.type === 'ten') {
                                    $('#modalSuaGiaiDoan .inpTen').addClass('is-invalid').focus();
                                }
                                if (result.type === 'tu') {
                                    $('#modalSuaGiaiDoan .inpFrom').addClass('is-invalid').focus();
                                }
                                if (result.type === 'den') {
                                    $('#modalSuaGiaiDoan .inpTo').addClass('is-invalid').focus();
                                }
                            }
                        });
                    })
                }
            })
            @endif
            @if(in_array('quy-trinh-lua.quy-trinh.action',$info->phanquyen) !== false)
            $('#modalThemMoi .btnXoaGiaiDoan').click(() => {
                let id = $('#modalThemMoi .selGiaiDoan').val();
                if (id == null) {
                    sToast.toast(0,'Bạn chưa chọn giai đoạn cần xóa!');
                    return false;
                }

                sToast.confirm('Xác nhận xóa giai đoạn?',
                    '<span class="text-danger">Lưu ý: Sau khi xóa giai đoạn thì các quy trình thuộc giai đoạn này cũng sẽ bị xóa!!!</span>',
                    (confirmed) => {
                        if (confirmed.isConfirmed) {
                            sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...');
                            $.ajax({
                                url: '/api/quan-ly/quy-trinh-lua/giai-doan/xoa',
                                type: 'get',
                                dataType: 'json',
                                data: {
                                    id
                                }
                            }).done((result) => {
                                if (result.succ) {
                                    giaidoans.forEach((value, key) => {
                                        if (value.id == id) {
                                            if ($('#boxTabMain .nav-link.active').attr('data-title') === value.phanloai) {
                                                $('#btnLamMoi').click();
                                            }
                                            giaidoans.splice(key,1);
                                        }
                                    })
                                    initSelGiaiDoan(giaidoans);
                                }
                            });
                        }
                    })
            })
            @endif
        }

        // Begin code xử lý thêm giai đoạn
        @if(in_array('quy-trinh-lua.quy-trinh.them-moi',$info->phanquyen) !== false)
        function getDataGiaiDoan() {
            $.ajax({
                url: '/api/quan-ly/quy-trinh-lua/giai-doan/danh-sach',
                type: 'get',
                dataType: 'json',
                data: {
                    muavu_id: $('#selMuaVu').val()
                }
            }).done((results) => {
                giaidoans = results;
                initSelGiaiDoan(giaidoans);
            });
        }
        function initSelGiaiDoan(data, id = null) {
            $('#modalThemMoi .selGiaiDoan').off('change').change(function () {
                if ($(this).val() != null) {
                    $('#modalThemMoi .btnCapNhatGiaiDoan').removeClass('d-none');
                    let giaidoan = $(this).select2('data')[0];
                    $('#modalThemMoi .inpFrom').val(giaidoan.tu);
                    $('#modalThemMoi .inpTo').val(giaidoan.den);
                    $('#modalThemMoi .inpPhanLoai').val(giaidoan.phanloai);
                }
                else {
                    if (!$('#modalThemMoi .btnCapNhatGiaiDoan').hasClass('d-none')) {
                        $('#modalThemMoi .btnCapNhatGiaiDoan').addClass('d-none');
                    }
                    $('#modalThemMoi .inpFrom, #modalThemMoi .inpTo, #modalThemMoi .inpPhanLoai').val('');
                }
            });
            initSelect2($('#modalThemMoi .selGiaiDoan'),data,{
                defaultText: 'ten',
                minimumResultsForSearch: -1,
                placeholder: 'Bạn chưa chọn giai đoạn'
            })
            if (id != null) {
                $('#modalThemMoi .selGiaiDoan').val(id);
            }
            $('#modalThemMoi .selGiaiDoan').trigger('change');
        }
        initActionThemGiaiDoan();
        function initActionThemGiaiDoan() {
            $('#modalThemGiaiDoan .selPhanLoai').select2({
                minimumResultsForSearch: -1
            })
            offEnterTextarea($('#modalThemGiaiDoan input'),() => {$('#modalThemGiaiDoan .btnSubmit').click();})

            $('#modalThemGiaiDoan, #modalSuaGiaiDoan').on('shown.bs.modal', function () {
                $(this).find('.inpTen').focus();
            }).on('hidden.bs.modal', function() {
                $(this).find('.is-invalid').removeClass('is-invalid');
                $('body').addClass('modal-open');
            })

            $('#modalThemGiaiDoan .btnSubmit').click(() => {
                let muavu_id = $('#selMuaVu').val();
                if (isNull(muavu_id)) {
                    sToast.toast(0,'Bạn chưa chọn mùa vụ!');
                    return false;
                }
                let ten = $('#modalThemGiaiDoan .inpTen').val().trim();
                let tu = parseInt($('#modalThemGiaiDoan .inpFrom').val());
                let den = parseInt($('#modalThemGiaiDoan .inpTo').val());
                let phanloai = $('#modalThemGiaiDoan .selPhanLoai').val();
                let checked = true;

                if (isNaN(den)) {
                    checked = false;
                    showError('den')
                }
                if (isNaN(tu)) {
                    checked = false;
                    showError('tu')
                }
                if (ten === '') {
                    checked = false;
                    showError('ten')
                }

                if (!checked) {
                    return false;
                }

                sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
                $.ajax({
                    url: '/api/quan-ly/quy-trinh-lua/giai-doan/them-moi',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        ten,tu,den,muavu_id,phanloai
                    }
                }).done((result) => {
                    if (result.succ) {
                        $('#modalThemGiaiDoan input').val('').trigger('input');
                        $('#modalThemGiaiDoan .inpTen').focus();
                        giaidoans.push(result.data.model);
                        initSelGiaiDoan(giaidoans);
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
            })
        }
        @endif
        // End code xử lý thêm giai đoạn

        // Begin code xử lý thêm quy trình
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
            $('#modalThemMoi input, #modalThemMoi textarea, #modalThemGiaiDoan input, #modalSuaGiaiDoan input').on('input', function () {
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

            $('#modalThemMoi .btnSubmit').click(() => {
                let muavu_id = $('#selMuaVu').val();
                if (isNull(muavu_id)) {
                    sToast.toast(0,'Bạn chưa chọn mùa vụ!');
                    return false;
                }
                let giaidoan = $('#modalThemMoi .selGiaiDoan').select2('data');
                if (giaidoan.length === 0) {
                    sToast.toast(0,'Bạn chưa chọn giai đoạn!');
                    return false;
                }
                giaidoan = giaidoan[0];
                let sanpham_id = $('#modalThemMoi .selSanPham').val();
                let tu = giaidoan.tu;
                let den = giaidoan.den;
                let phanloai = giaidoan.phanloai;
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

                if (!checked) {
                    return false;
                }

                sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
                $.ajax({
                    url: '/api/quan-ly/quy-trinh-lua/quy-trinh/them-moi',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        giaidoan: giaidoan.ten,
                        giaidoan_id: giaidoan.id,
                        tu,den,congdung,sanpham_id,soluong,ghichu,muavu_id,phanloai
                    }
                }).done((result) => {
                    if (result.succ) {
                        $('#modalThemMoi .inpSoLuong, #modalThemMoi textarea').val('').trigger('input');
                        $('#modalThemMoi .selSanPham').val(null).trigger('change');
                        lientuc ? $('#modalThemMoi .inpGiaiDoan').focus() : $('#modalThemMoi').modal('hide');
                        setDataTable(result.data.model.phanloai);
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
        // End code xử lý thêm quy trình

        function setDataTable(phanloai) {
            if (phanloai === 'Phân bón') {
                if (table.phan != null) {
                    table.phan.setData('/api/quan-ly/quy-trinh-lua/quy-trinh/danh-sach?phanloai=phan&muavu_id=' + $('#selMuaVu').val());
                }
            }
            else {
                if (table.thuoc != null) {
                    table.thuoc.setData('/api/quan-ly/quy-trinh-lua/quy-trinh/danh-sach?phanloai=thuoc&muavu_id=' + $('#selMuaVu').val());
                }
            }
        }
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
                    if (['phanloai','giaidoan','tu','den'].indexOf(field) > -1) {
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
                                table.phan.getColumn(field).hide();
                                table.thuoc.getColumn(field).hide();
                                views[field] = false;
                            }
                            else {
                                table.phan.getColumn(field).show();
                                table.thuoc.getColumn(field).show();
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
                menus.unshift({
                    label: '<i class="fa fa-edit text-primary"></i> Đổi giai đoạn',
                    action: (e, cell) => {
                        let field = 'giaidoan_id';
                        let data = cell.getData();
                        let value = data.giaidoan_id;
                        let ten = 'Giai đoạn';
                        clickSuaThongTin(field,value,ten,data);
                    }
                });
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
                    // {title: "Từ (ngày)", field: "tu", vertAlign: 'middle', headerSort: false, contextMenu, hozAlign: 'right',
                    //     visible: isNull(views) ? true : views.tu},
                    // {title: "Đến (ngày)", field: "den", vertAlign: 'middle', headerSort: false, contextMenu, hozAlign: 'right',
                    //     visible: isNull(views) ? true : views.den},
                    {title: "Sản phẩm", field: "sanpham", vertAlign: 'middle', headerSort: false, contextMenu,
                        visible: isNull(views) ? true : views.sanpham},
                    {title: "Đơn vị tính", field: "donvitinh", vertAlign: 'middle', headerSort: false, contextMenu,
                        visible: isNull(views) ? true : views.donvitinh},
                    // {title: "Phân loại", field: "phanloai", vertAlign: 'middle', headerSort: false, contextMenu,
                    //     visible: isNull(views) ? true : views.phanloai},
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
                groupBy: (data) => {
                    return data.giaidoan + '<span class="ml-3" style="color: #333"> (' + data.tu + ' đến ' + data.den + ')</span>';
                },
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
                if (['sanpham','giaidoan_id'].indexOf(field) === -1) {
                    value = value.trim();
                    if (['soluong'].indexOf(field) > -1) {
                        value = parseInt(value);
                        if (isNaN(value) || value < 0) {
                            showErrorModalInput('Số lượng không hợp lệ!');
                            return false;
                        }
                    }
                    else if (field !== 'ghichu' && value === '') {
                        showErrorModalInput(ten + ' không được bỏ trống!');
                        return false;
                    }
                }
                else if (value == null) {
                    showErrorModalInput(field === 'giaidoan_id' ? 'Bạn chưa chọn sản phẩm!' : 'Bạn chưa chọn giai đoạn!');
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
                                    if (field === 'giaidoan_id') {
                                        setDataTable('Phân bón');
                                        setDataTable('Thuốc');
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
            if (['ghichu','congdung'].indexOf(field) !== -1) {
                mInput(data.ten,value,field === 'congdung').textarea(ten,ten + '...',onSubmit);
            }
            if (['soluong'].indexOf(field) !== -1) {
                mInput(data.ten,value,true).number(ten,ten + '...',onSubmit);
            }
            if (field === 'sanpham') {
                mInput(data.ten,value,true).select2(ten,'Chọn sản phẩm...','/api/quan-ly/quy-trinh-lua/san-pham/tim-kiem',false,onSubmit);
            }
            if (field === 'giaidoan_id') {
                mInput(data.ten,value,true).select2(ten,'Chọn giai đoạn...',giaidoans,false,onSubmit);
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
                ten: $('#modalThemGiaiDoan .inpTen'),
                tu: $('#modalThemGiaiDoan .inpFrom'),
                den: $('#modalThemGiaiDoan .inpTo'),
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
