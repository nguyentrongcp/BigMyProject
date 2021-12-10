@section('js-custom')
<script>
    let tblDanhSach;
    let views = localStorage.getItem('danhmuc.nhanvien.views');
    views = isNull(views) ? {} : JSON.parse(views);
    let chucvus = JSON.parse('{!! str_replace("'","\'",json_encode($chucvus)) !!}');
    let chinhanhs = JSON.parse('{!! str_replace("'","\'",json_encode($chinhanhs)) !!}');
    init();
    initDanhSach();

    function init() {
        $('#btnLamMoi').click(() => {
            tblDanhSach.setData('/api/quan-ly/danh-muc/nhan-vien/danh-sach');
        })

        @if(in_array('danh-muc.nhan-vien.chinh-sua',$info->phanquyen) === false)
        $('#modalXem .col-thongtin i').remove();
        @endif
    }

    @if(in_array('danh-muc.nhan-vien.them-moi',$info->phanquyen) !== false)
    initActionThemMoi();
    function initActionThemMoi() {
        initSelect2($('#modalThemMoi .selLoai'),chucvus,{minimumResultsForSearch: -1});
        initSelect2($('#modalThemMoi .selChiNhanh'),chinhanhs,{minimumResultsForSearch: -1});

        $('#boxNgaySinh').datetimepicker({
            format: 'DD/MM/YYYY',
            keepOpen: false
        });
        $('#boxNgaySinh .inpNgaySinh').focus(() => {
            $('#boxNgaySinh').datetimepicker('show');
            $('#modalThemMoi .errorNgaySinh').removeClass('d-block');
        })

        $('#modalThemMoi input, #modalThemMoi textarea').keypress(function(e) {
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

        $('#modalThemMoi .btnSubmit').click(() => {
            let ten = $('#modalThemMoi .inpTen').val().trim();
            let dienthoai = $('#modalThemMoi .inpDienThoai').val().trim();
            let ngaysinh = $('#boxNgaySinh').datetimepicker('viewDate').format('YYYY-MM-DD');
            let chucvu = $('#modalThemMoi .selLoai').val();
            let chinhanh_id = $('#modalThemMoi .selChiNhanh').val();
            let ghichu = $('#modalThemMoi .inpGhiChu').val().trim();
            let lientuc = $('#chkLienTuc')[0].checked;
            let checked = true;

            if ($('#modalThemMoi .inpNgaySinh').val().trim() === '')  {
                checked = false;
                showError('ngaysinh');
            }
            if (dienthoai === '')  {
                checked = false;
                showError('dienthoai', 'Số điện thoại không được bỏ trống!');
            }
            if (ten === '') {
                checked = false;
                showError('ten');
            }

            if (!checked) {
                return false;
            }

            sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
            $.ajax({
                url: '/api/quan-ly/danh-muc/nhan-vien/them-moi',
                type: 'get',
                dataType: 'json',
                data: {
                    ten, dienthoai, chucvu, ngaysinh, ghichu, chinhanh_id
                }
            }).done((result) => {
                if (result.succ) {
                    $('#modalThemMoi input, #modalThemMoi textarea').val('').trigger('input');
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
    @endif

    function initDanhSach() {
        let xemThongTin = (e, cell) => {
            let data = cell.getData();
            $.each($('#modalXem .col-thongtin'), function(key, col) {
                clickXemThongTin(data,col);
            })
            @if(in_array('danh-muc.nhan-vien.action',$info->phanquyen) !== false)
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
                        localStorage.setItem('danhmuc.nhanvien.views', JSON.stringify(views))
                    }
                })
            }
            let menus = [
                {
                    label: '<i class="fa fa-info-circle text-info"></i> Chi tiết',
                    action: xemThongTin
                },
                @if(in_array('danh-muc.nhan-vien.action',$info->phanquyen) !== false)
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
                @if(in_array('danh-muc.nhan-vien.reset-matkhau',$info->phanquyen) !== false)
                {
                    label: '<i class="fa fa-lock text-warning"></i> Reset mật khẩu',
                    action: () => {
                        resetMatKhau(data.id)
                    }
                },
                @endif
                @if(in_array('danh-muc.nhan-vien.phan-quyen',$info->phanquyen) !== false)
                {
                    label: '<i class="fa fa-user text-dark"></i> Phân quyền',
                    action: () => {
                        sToast.loading('Đang lấy dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/danh-muc/nhan-vien/danhsach-phanquyen',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                id: data.id
                            }
                        }).done((result) => {
                            if (result.succ) {
                                $('#modalPhanQuyen').off('shown.bs.modal').on('shown.bs.modal', () => {
                                    initDanhSachPhanQuyen(result.data,data.id);
                                }).modal('show').find('.title').text(data.ten);
                            }
                        });
                    }
                },
                @endif
                {
                    label: '<i class="fa fa-eye"></i> Hiển thị',
                    menu: subMenus
                }
            ];
            @if(in_array('danh-muc.nhan-vien.chinh-sua',$info->phanquyen) !== false)
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
                    formatter: "rownum", width: 40, headerSort: false, hozAlign: 'center'},
                {title: "Mã", field: "ma", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.ma},
                {title: "Tên", field: "ten", vertAlign: 'middle', headerSort: false, minWidth: 160, contextMenu,
                    visible: isNull(views) ? true : views.ten},
                {title: "Điện thoại", field: "dienthoai", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.dienthoai},
                {title: "Tài khoản", field: "taikhoan", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.taikhoan},
                {title: "Ngày sinh", field: "ngaysinh", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.ngaysinh,
                    formatter: (cell) => {
                        return doi_ngay(cell.getValue());
                    }},
                {title: "Chức vụ", field: "chucvu", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.chucvu,
                    formatter: (cell) => {
                        return getChucVu(cell.getValue());
                    }},
                {title: "Cửa hàng", field: "chinhanh_id", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.chinhanh_id,
                    formatter: (cell) => {
                        return getChiNhanh(cell.getValue());
                    }},
                {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.ghichu},
            ],
            @if(in_array('danh-muc.nhan-vien.action',$info->phanquyen) !== false)
            rowFormatter: (row) => {
                if (!isNull(row.getData().deleted_at)) {
                    $(row.getElement()).addClass('text-danger');
                }
                else {
                    $(row.getElement()).removeClass('text-danger');
                }
            },
            @endif
            ajaxURL: '/api/quan-ly/danh-muc/nhan-vien/danh-sach',
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
        initSearchTable(tblDanhSach,['dienthoai','ten','ma']);
    }

    function clickXemThongTin(data, col) {
        let field = $(col).attr('data-field');
        let ten = $(col).attr('data-title');
        let value = data[field];
        $(col).find('span').text(field === 'chucvu' ? getChucVu(value) : (field === 'chinhanh_id' ? getChiNhanh(value) : value));
        @if(in_array('danh-muc.nhan-vien.chinh-sua',$info->phanquyen) !== false)
        let edit = $(col).find('i.edit');
        if (edit.length > 0) {
            edit.off('click').click(() => {
                clickSuaThongTin(field,value,ten,data,col);
            })
        }
        @endif
    }

    @if(in_array('danh-muc.nhan-vien.chinh-sua',$info->phanquyen) !== false)
    function clickSuaThongTin(field, value, ten, data, col = null) {
        let onSubmit = () => {
            let value = $('#modalInput .value').val().trim();
            if (['ten','dienthoai','ngaysinh'].indexOf(field) !== -1 && value === '') {
                showErrorModalInput(ten + ' không được bỏ trống!');
                return false;
            }
            if (field === 'ngaysinh') {
                value = $('#boxInputDate').datetimepicker('viewDate').format('YYYY-MM-DD');
            }

            sToast.confirm('Xác nhận cập nhật ' + ten.toLowerCase() + '?','',
                (result) => {
                    if (result.isConfirmed) {
                        sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/danh-muc/nhan-vien/cap-nhat',
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
                                    if (field === 'ten' || field === 'ngaysinh') {
                                        $('#modalXem .col-thongtin[data-field=taikhoan] span').text(result.data.model.taikhoan);
                                    }
                                }
                            }
                        });
                    }
                });
        }
        if (['ten','dienthoai'].indexOf(field) !== -1) {
            mInput(data.ten,value,true).text(ten,ten + '...',onSubmit);
        }
        if (field === 'ghichu' || field === 'diachi') {
            mInput(data.ten,value).textarea(ten,ten + '...',onSubmit);
        }
        if (field === 'chucvu' || field === 'chinhanh_id') {
            mInput(data.ten,value).select2(ten,'',field === 'chucvu' ? chucvus : chinhanhs,true,onSubmit);
        }
        if (field === 'ngaysinh') {
            mInput(data.ten,value,true).date(ten,ten + '...',onSubmit,'Ngày sinh không được bỏ trống!');
        }
    }
    @endif

    @if(in_array('danh-muc.nhan-vien.action',$info->phanquyen) !== false)
    function clickXoaThongTin(cell) {
        sToast.confirm('Xác nhận xóa thông tin nhân viên?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/danh-muc/nhan-vien/xoa',
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
        sToast.confirm('Xác nhận phục hồi thông tin nhân viên?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang phục hồi dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/danh-muc/nhan-vien/phuc-hoi',
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

    @if(in_array('danh-muc.nhan-vien.reset-matkhau',$info->phanquyen) !== false)
    function resetMatKhau(id) {
        mInput('Reset mật khẩu nhân viên','',true).password('Mật khẩu mới','Nhập mật khẩu mới...',
            () => {
                let matkhau = $('#modalInput .value').val().trim();
                if (matkhau === '') {
                    $('#modalInput .value').addClass('is-invalid');
                    return false;
                }
                sToast.loading('Đang xử lý dữ liệu. Vui lòng chờ...')
                $.ajax({
                    url: '/api/quan-ly/danh-muc/nhan-vien/reset-mat-khau',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        id, matkhau
                    }
                }).done((result) => {
                    if (result.succ) {
                        $('#modalInput').modal('hide');
                    }
                    else {
                        if (!isUndefined(result.erro)) {
                            $('#modalInput .value').addClass('is-invalid');
                        }
                    }
                });
            },'Mật khẩu mới không được bỏ trống!');
    }
    @endif

    @if(in_array('danh-muc.nhan-vien.phan-quyen',$info->phanquyen) !== false)
    function initDanhSachPhanQuyen(data, id, isChinhSua = false) {
        let _data = JSON.parse(JSON.stringify(data));
        if (isChinhSua) {
            $('#modalPhanQuyen .btnChinhSua').off('click').addClass('d-none');
            $('#modalPhanQuyen .btnCancel').off('click').removeClass('d-none').click(() => {
                initDanhSachPhanQuyen(data,id);
            });
            $('#modalPhanQuyen .btnSubmit').off('click').removeClass('d-none').click(() => {
                actionPhanQuyen(id);
            })
        }
        else {
            $('#modalPhanQuyen .btnChinhSua').off('click').removeClass('d-none').click(() => {
                initDanhSachPhanQuyen(_data,id,true);
            })
            $('#modalPhanQuyen .btnCancel, #modalPhanQuyen .btnSubmit').off('click').addClass('d-none');
        }
        let contextMenu = (cell) => {
            let menus;
            if (isChinhSua) {
                let isTatCa = true;
                let isEmpty = true;
                tblPhanQuyen.getData().forEach((value) => {
                    if (!value.checked) {
                        isTatCa = false;
                    }
                    else {
                        isEmpty = false;
                    }
                })
                menus = [
                    {
                        label: '<i class="fa fa-edit text-primary"></i> Xác nhận',
                        action: () => {
                            actionPhanQuyen(id);
                        }
                    },
                    {
                        label: '<i class="fa fa-times text-danger"></i> Hủy bỏ',
                        action: () => {
                            initDanhSachPhanQuyen(data,id);
                        }
                    },
                    {
                        label: '<i class="fa fa-check-square-o text-success"></i> Chọn tất cả',
                        action: () => {
                            let dataUpdate = [];
                            $.each(tblPhanQuyen.getRows(), function (key, row) {
                                if (!row.getData().checked) {
                                    dataUpdate.push({id: row.getIndex(), checked: true});
                                }
                            })
                            tblPhanQuyen.updateData(dataUpdate);
                            $('#modalPhanQuyen .lblSoQuyen').text(data.length);
                        },
                        disabled: isTatCa
                    },
                    {
                        label: '<i class="fa fa-square-o"></i> Bỏ chọn tất cả',
                        action: () => {
                            let dataUpdate = [];
                            $.each(tblPhanQuyen.getRows(), function (key, row) {
                                if (row.getData().checked) {
                                    dataUpdate.push({id: row.getIndex(), checked: false});
                                }
                            })
                            tblPhanQuyen.updateData(dataUpdate);
                            $('#modalPhanQuyen .lblSoQuyen').text(0);
                        },
                        disabled: isEmpty
                    }
                ];
            }
            else {
                menus = [
                    {
                        label: cell.getData().checked ? '<i class="fa fa-times text-danger"></i> Hủy quyền' : '<i class="fa fa-check text-success"></i> Chọn quyền',
                        action: () => {
                            sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                            $.ajax({
                                url: '/api/quan-ly/danh-muc/nhan-vien/chon-quyen',
                                type: 'get',
                                dataType: 'json',
                                data: {
                                    id,
                                    quyen: cell.getData().id
                                }
                            }).done((result) => {
                                if (result.succ) {
                                    cell.getTable().updateData([{
                                        id: cell.getData().id,
                                        checked: !cell.getData().checked
                                    }])
                                    initSoQuyen();
                                }
                            });
                        }
                    },
                    {
                        label: '<i class="fa fa-edit text-primary"></i> Chỉnh sửa',
                        action: () => {
                            initDanhSachPhanQuyen(_data,id,true);
                        }
                    }
                ];
            }

            return menus;
        }

        tblPhanQuyen = new Tabulator("#tblPhanQuyen", {
            columns: [
                {title: "Chọn", headerHozAlign: 'center', vertAlign: 'middle', field: "checked", contextMenu,
                    headerSort: false, hozAlign: 'center', formatter: (cell) => {
                        return cell.getValue() ? '<i class="fa fa-check text-success"></i>' : '';
                    }},
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", contextMenu,
                    headerSort: false, hozAlign: 'center'},
                {title: "Tên", field: "ten", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Loại", field: "loai", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu},
            ],
            height: '450px',
            movableColumns: false,
            groupBy: 'chucnang',
            groupHeader: function(value, count){
                return value + '<span class="text-danger ml-3">(' + count + ' quyền)</span>';
            },
            rowClick: (e, row) => {
                if (isChinhSua) {
                    let checked = row.getData().checked;
                    tblPhanQuyen.updateData([{id: row.getIndex(), checked: !checked}]);
                    let soQuyen = parseInt($('#modalPhanQuyen .lblSoQuyen').text());
                    $('#modalPhanQuyen .lblSoQuyen').text(checked ? --soQuyen : ++soQuyen);
                }
            },
            pageLoaded: () => {
                if (isNull(tblPhanQuyen) || isUndefined(tblPhanQuyen)) {
                    return false;
                }
                setTimeout(() => {tblPhanQuyen.getColumns()[0].updateDefinition()},10);
            },
        });
        initSearchTable(tblPhanQuyen,['ten','chucnang']);

        tblPhanQuyen.setData(_data);
        initSoQuyen();
    }

    function initSoQuyen() {
        let soQuyen = 0;
        tblPhanQuyen.getData().forEach((value) => {
            if (value.checked) {
                soQuyen++;
            }
        });
        $('#modalPhanQuyen .lblSoQuyen').text(soQuyen);
    }

    function actionPhanQuyen(id) {
        let phanquyens = [];
        tblPhanQuyen.getData().forEach((value) => {
            if (value.checked) {
                phanquyens.push(value.id);
            }
        })
        sToast.confirm('Xác nhận cập nhật phân quyền nhân viên?','',
            (confirmed) => {
                if (confirmed.isConfirmed) {
                    sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/danh-muc/nhan-vien/phan-quyen',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            id, phanquyens: JSON.stringify(phanquyens)
                        }
                    }).done((result) => {
                        if (result.succ) {
                            initDanhSachPhanQuyen(tblPhanQuyen.getData(),id);
                        }
                    });
                }
            })
    }
    @endif

    function showError(type, erro = '') {
        let inputs = {
            ten: $('#modalThemMoi .inpTen'),
            dienthoai: $('#modalThemMoi .inpDienThoai'),
        }
        if (erro !== '') {
            $(inputs[type].parent()).find('span.error').text(erro);
        }
        if (type === 'ngaysinh') {
            $('#modalThemMoi .errorNgaySinh').addClass('d-block');
        }
        else {
            inputs[type].addClass('is-invalid');
            inputs[type].focus();
        }
    }

    function getChucVu(chucvu) {
        let result = 'Không có';
        chucvus.forEach((value) => {
            if (value.id == chucvu) {
                result = value.text;
                return;
            }
        });

        return result;
    }

    function getChiNhanh(chinhanh_id) {
        let result = 'Không có';
        chinhanhs.forEach((value) => {
            if (value.id == chinhanh_id) {
                result = value.text;
                return;
            }
        });

        return result;
    }
</script>
@stop
