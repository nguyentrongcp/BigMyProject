@section('js-custom')
<script>
    let tblDanhSach;
    let tblQuyDoi;
    let views = localStorage.getItem('danhmuc.hanghoa.views');
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
    initTblQuyDoi();

    function init() {
        $('#btnLamMoi').click(() => {
            tblDanhSach.setData('/api/quan-ly/danh-muc/hang-hoa/danh-sach');
        })
        $('#modalQuyDoi .btnLamMoi').click(() => {
            tblQuyDoi.setData('/api/quan-ly/danh-muc/hang-hoa/danhmuc-quydoi');
        });

        @if(in_array('danh-muc.hang-hoa.chinh-sua',$info->phanquyen) === false)
        $('#modalXem .col-thongtin i').remove();
        @endif

        @if(in_array('danh-muc.hang-hoa.quy-doi',$info->phanquyen) !== false)
        $('#modalThemQuyDoi input').on('input', function () {
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
            }
        });
        initSelect2($('#modalThemQuyDoi .selDonViQuyDoi'),donvitinhs)
        @endif
    }

    @if(in_array('danh-muc.hang-hoa.them-moi',$info->phanquyen) !== false)
    initActionThemMoi();
    function initActionThemMoi() {
        initSelect2($('#modalThemMoi .selNhom'),nhoms)
        initSelect2($('#modalThemMoi .selDonViTinh'),donvitinhs)
        initSelect2($('#modalThemMoi .selDang'),dangs,{minimumResultsForSearch: -1, allowClear: true, placeholder: 'Dạng hàng hóa...'})

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
        }).on('input', function () {
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
            let mamoi = $('#modalThemMoi .inpMa').val().trim();
            let donvitinh = $('#modalThemMoi .selDonViTinh').val();
            let gianhap = parseInt($('#modalThemMoi .inpGiaNhap').attr('data-value') === ''
                ? 0 : $('#modalThemMoi .inpGiaNhap').attr('data-value'));
            let dongia = parseInt($('#modalThemMoi .inpDonGia').attr('data-value'));
            let nhom = $('#modalThemMoi .selNhom').val();
            let quycach = $('#modalThemMoi .inpQuyCach').val();
            quycach = quycach === '' ? 1 : quycach;
            let dang = $('#modalThemMoi .selDang').val();
            let congdung = $('#modalThemMoi .inpCongDung').val().trim();
            let hoatchat = $('#modalThemMoi .inpHoatChat').val().trim();
            let ghichu = $('#modalThemMoi .inpGhiChu').val().trim();
            let lientuc = $('#chkLienTuc')[0].checked;
            let checked = true;

            if (isNaN(gianhap) || gianhap < 0)  {
                checked = false;
                showError('gianhap');
            }
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
                url: '/api/quan-ly/danh-muc/hang-hoa/them-moi',
                type: 'get',
                dataType: 'json',
                data: {
                    ten,donvitinh,nhom,quycach,gianhap,dongia,ghichu,congdung,hoatchat,dang,mamoi
                }
            }).done((result) => {
                if (result.succ) {
                    $('#modalThemMoi input, #modalThemMoi textarea').val('').trigger('input');
                    lientuc ? $('#modalThemMoi .inpTen').focus() : $('#modalThemMoi').modal('hide');
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
            @if(in_array('danh-muc.hang-hoa.action',$info->phanquyen) !== false)
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
                        localStorage.setItem('danhmuc.hanghoa.views', JSON.stringify(views))
                    }
                })
            }
            let menus = [
                {
                    label: '<i class="fa fa-info-circle text-info"></i> Chi tiết',
                    action: xemThongTin
                },
                @if(in_array('danh-muc.hang-hoa.action',$info->phanquyen) !== false)
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
            @if(in_array('danh-muc.hang-hoa.chinh-sua',$info->phanquyen) !== false)
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
            @if(in_array('danh-muc.hang-hoa.quy-doi',$info->phanquyen) !== false)
            if (!data.is_quydoi && isNull(data.deleted_at)) {
                menus.unshift({
                    label: '<i class="fa fa-plus text-success"></i> Quy đổi',
                    action: () => {
                        $('#modalThemQuyDoi .inpTen').val(data.ten);
                        $('#modalThemQuyDoi .inpDonViTinh').val(data.donvitinh);
                        $('#modalThemQuyDoi').modal('show');
                        $('#modalThemQuyDoi .btnSubmit').off('click').click(() => {
                            let ten = $('#modalThemQuyDoi .inpTenQuyDoi').val().trim();
                            let donvitinh = $('#modalThemQuyDoi .selDonViQuyDoi').val();
                            let quydoi = parseInt($('#modalThemQuyDoi .inpSoLuong').val());
                            let dongia = parseFloat($('#modalThemQuyDoi .inpDonGia').attr('data-value'));
                            let checked = true;

                            if (isNaN(dongia) || dongia < 0)  {
                                checked = false;
                                showError('giaquydoi');
                            }
                            if (isNaN(quydoi) || quydoi <= 0)  {
                                checked = false;
                                showError('soluong_quydoi');
                            }
                            if (ten === '') {
                                checked = false;
                                showError('tenquydoi','Tên quy đổi không được bỏ trống!');
                            }

                            if (!checked) {
                                return false;
                            }

                            sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...')
                            $.ajax({
                                url: '/api/quan-ly/danh-muc/hang-hoa/them-moi',
                                type: 'get',
                                dataType: 'json',
                                data: {
                                    id_cha: data.id,
                                    ten,donvitinh,dongia,quydoi
                                }
                            }).done((result) => {
                                if (result.succ) {
                                    $('#modalThemQuyDoi input').val('').trigger('input');
                                    $('#modalThemQuyDoi').modal('hide');
                                    tblDanhSach.addData(result.data.model,true);
                                }
                                else if (!isUndefined(result.type)) {
                                    if (result.type === 'ten') {
                                        result.type = 'tenquydoi';
                                    }
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
                {title: "Mã PM mới", field: "mamoi", vertAlign: 'middle', contextMenu,
                    visible: isNull(views) ? true : views.mamoi},
                {title: "Tên", field: "ten", vertAlign: 'middle', contextMenu,
                    visible: isNull(views) ? true : views.ten},
                {title: "ĐVT", field: "donvitinh", vertAlign: 'middle', contextMenu,
                    visible: isNull(views) ? true : views.donvitinh},
                {title: "Nhóm", field: "nhom", vertAlign: 'middle', contextMenu,
                    visible: isNull(views) ? true : views.nhom},
                {title: "Quy cách", field: "quycach", vertAlign: 'middle', headerSort: false, hozAlign: 'right', contextMenu,
                    visible: isNull(views) ? true : views.quycach},
                @if(in_array('role.gia-nhap',$info->phanquyen) !== false)
                {title: "Giá nhập", field: "gianhap", hozAlign: 'right', vertAlign: 'middle', headerSort: false,
                    contextMenu, visible: isNull(views) ? true : views.gianhap,
                    formatter: (cell) => {
                        return numeral(cell.getValue()).format('0,0');
                    }},
                @endif
                {title: "Dạng", field: "dang", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? false : views.dang},
                {title: "Hoạt chất", field: "hoatchat", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? false : views.hoatchat},
                {title: "Công dụng", field: "congdung", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? false : views.congdung},
                {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.ghichu}
            ],
            @if(in_array('danh-muc.hang-hoa.action',$info->phanquyen) !== false)
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
            ajaxURL: '/api/quan-ly/danh-muc/hang-hoa/danh-sach',
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
        initSearchTable(tblDanhSach,['ma','ten','hoatchat']);
    }

    function initTblQuyDoi() {
        $('#modalThemQuyDoi').on('shown.bs.modal', function() {
            $(this).find('.inpTenQuyDoi').focus();
        })

        let contextMenu = (cell) => {
            @if(in_array('danh-muc.hang-hoa.quy-doi',$info->phanquyen) !== false)
            let menus = [
                {
                    label: '<i class="fa fa-trash-alt text-danger"></i> Xóa',
                    action: () => {
                        sToast.confirm('Xác nhận xóa quy đổi hàng hóa?',cell.getData().tenle,
                            (result) => {
                                if (result.isConfirmed) {
                                    sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
                                    $.ajax({
                                        url: '/api/quan-ly/danh-muc/hang-hoa/xoa',
                                        type: 'get',
                                        dataType: 'json',
                                        data: {
                                            id: cell.getData().id_con
                                        }
                                    }).done((result) => {
                                        if (result.succ) {
                                            tblDanhSach.getRow(cell.getData().id_con).delete();
                                            cell.getRow().delete();
                                        }
                                    });
                                }
                            });
                    }
                },
            ];
            let field = cell.getField();
            let value = cell.getValue();
            let data = cell.getData();
            if (field === 'soluong') {
                menus.unshift({
                    label: '<i class="fa fa-edit text-primary"></i> Chỉnh sửa',
                    action: () => {
                        mInput('Cập nhật quy đổi',value).number('Số lượng quy đổi','Nhập số lượng quy đổi...',
                            () => {
                                let value = $('#modalInput .value').val();
                                sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                                $.ajax({
                                    url: '/api/quan-ly/danh-muc/hang-hoa/capnhat-quydoi',
                                    type: 'get',
                                    dataType: 'json',
                                    data: {
                                        id: data.id,
                                        value
                                    }
                                }).done((result) => {
                                    if (result.succ) {
                                        $('#modalInput').modal('hide');
                                        tblQuyDoi.updateData([{...result.data.model}]);
                                    }
                                });
                            });
                    }
                })
            }

            return menus;
            @else
            return [];
            @endif
        }

        tblQuyDoi = new Tabulator("#tblQuyDoi", {
            columns: [
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", contextMenu,
                    formatter: "rownum", width: 40, headerSort: false, hozAlign: 'center'},
                {title: "Mã", field: "ma", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Tên", field: "ten", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "ĐVT", field: "donvitinh", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Mã quy đổi", field: "male", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Tên quy đổi", field: "tenle", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Đơn vị quy đổi", field: "donvile", hozAlign: 'right', vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Số lượng quy đổi", field: "soluong", vertAlign: 'middle', headerSort: false, hozAlign: 'right',
                    contextMenu, formatter: (cell) => {
                        return '<span class="text-info font-weight-bolder">' + cell.getValue() + '</span>';
                    }},
            ],
            height: '465px',
            pagination: 'local',
            paginationSize: 10,
            ajaxURL: '/api/quan-ly/danh-muc/hang-hoa/danhmuc-quydoi',
            pageLoaded: () => {
                if(!isUndefined(tblQuyDoi)) {
                    // tblQuyDoi.getColumns()[0].updateDefinition();
                }
            },
            dataFiltered: function () {
                if (isNull(tblQuyDoi) || isUndefined(tblQuyDoi)) {
                    return false;
                }
                setTimeout(() => {tblQuyDoi.getColumns()[0].updateDefinition()},10);
            }
        });

        initSearchTable(tblQuyDoi,['ma','ten','male','tenle']);

        $('#modalQuyDoi').on('shown.bs.modal', function() {
            $(this).find('.btnLamMoi').click();
        });
    }

    function clickXemThongTin(data, col) {
        let field = $(col).attr('data-field');
        let ten = $(col).attr('data-title');
        let value = data[field];
        if (['gianhap'].indexOf(field) !== -1) {
            value = numeral(value).format('0,0');
        }
        $(col).find('span').text(value);
        @if(in_array('danh-muc.hang-hoa.chinh-sua',$info->phanquyen) !== false)
        let edit = $(col).find('i.edit');
        if (edit.length > 0) {
            edit.off('click').click(() => {
                clickSuaThongTin(field,value,ten,data,col);
            })
        }
        @endif
    }

    @if(in_array('danh-muc.hang-hoa.chinh-sua',$info->phanquyen) !== false)
    function clickSuaThongTin(field, value, ten, data, col = null) {
        let onSubmit = () => {
            let value = $('#modalInput .value').val();
            if (field !== 'dang') {
                value = value.trim();
            }
            if (field === 'gianhap') {
                value = $('#modalInput .value').attr('data-value');
                if (isNaN(parseFloat(value)) || parseFloat(value) < 0) {
                    showErrorModalInput('Giá nhập không hợp lệ!');
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
                            url: '/api/quan-ly/danh-muc/hang-hoa/cap-nhat',
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
        if (field === 'mamoi') {
            mInput(data.ten,value,false).text(ten,ten + '...',onSubmit);
        }
        if (['congdung','hoatchat','ghichu'].indexOf(field) !== -1) {
            mInput(data.ten,value).textarea(ten,ten + '...',onSubmit);
        }
        if (field === 'gianhap') {
            mInput(data.ten,value).numeral(ten,ten + '...',onSubmit);
        }
        if (field === 'quycach') {
            mInput(data.ten,value).number(ten,ten + '...',onSubmit);
        }
        if (['donvitinh','nhom','dang'].indexOf(field) !== -1) {
            mInput(data.ten,value).select2(ten,'',field === 'nhom' ? nhoms : (field === 'dang' ? dangs : donvitinhs),true,onSubmit);
        }
    }
    @endif

    @if(in_array('danh-muc.hang-hoa.action',$info->phanquyen) !== false)
    function clickXoaThongTin(cell) {
        sToast.confirm('Xác nhận xóa thông tin hàng hóa?',cell.getData().ten,
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/danh-muc/hang-hoa/xoa',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            id: cell.getData().id
                        }
                    }).done((result) => {
                        if (result.succ) {
                            if (cell.getData().is_quydoi) {
                                cell.getRow().delete();
                            }
                            else {
                                cell.getTable().updateData([{
                                    id: cell.getData().id,
                                    deleted_at: result.data.deleted_at
                                }])
                                if (result.data.id_cons.length > 0) {
                                    result.data.id_cons.forEach((value) => {
                                        tblDanhSach.getRow(value).delete();
                                    })
                                }
                                if ($('#modalXem').hasClass('show')) {
                                    $('#modalXem button.delete').attr('class','btn bg-gradient-success delete')
                                        .text('Phục hồi thông tin').off('click').click(() => {
                                        clickPhucHoiThongTin(cell);
                                    })
                                }
                            }
                        }
                    });
                }
            });
    }

    function clickPhucHoiThongTin(cell) {
        mInput(cell.getData().ten).numeral('Đơn giá','Vui lòng nhập lại đơn giá mới...',
            () => {
                let value = $('#modalInput .value').attr('data-value');
                sToast.confirm('Xác nhận phục hồi thông tin hàng hóa?','',
                    (result) => {
                        if (result.isConfirmed) {
                            sToast.loading('Đang phục hồi dữ liệu. Vui lòng chờ...')
                            $.ajax({
                                url: '/api/quan-ly/danh-muc/hang-hoa/phuc-hoi',
                                type: 'get',
                                dataType: 'json',
                                data: {
                                    id: cell.getData().id,
                                    dongia: value
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
                                else if (!isUndefined(result.erro)) {
                                    $('#modalInput span.error').text(result.erro);
                                    $('#modalInput .value').addClass('is-invalid');
                                    $('#modalInput .value').focus();
                                }
                                else {
                                    $('#modalInput').modal('hide');
                                }
                            });
                        }
                    });
            })
    }
    @endif

    function showError(type, erro = '') {
        let inputs = {
            ten: $('#modalThemMoi .inpTen'),
            dongia: $('#modalThemMoi .inpDonGia'),
            gianhap: $('#modalThemMoi .inpGiaNhap'),
            tenquydoi: $('#modalThemQuyDoi .inpTenQuyDoi'),
            soluong_quydoi: $('#modalThemQuyDoi .inpSoLuong'),
            giaquydoi: $('#modalThemQuyDoi .inpDonGia')
        }
        if (erro !== '') {
            $(inputs[type].parent()).find('span.error').text(erro);
        }
        inputs[type].addClass('is-invalid');
        inputs[type].focus();
    }
</script>
@stop
