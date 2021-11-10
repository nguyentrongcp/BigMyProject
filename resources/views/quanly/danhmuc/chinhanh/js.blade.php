@section('js-custom')
<script>
    let tblDanhSach;
    let views = localStorage.getItem('danhmuc.chinhanh.views');
    views = isNull(views) ? {} : JSON.parse(views);
    let loais = {
        cuahang: 'Cửa hàng',
        congty: 'Công ty',
        khohanghong: 'Kho hàng hỏng'
    }
    init();
    initLoai();
    initDanhSach();
    @if($info->id == '1000000000')
    actionThemMoi();
    @endif

    function init() {
        $('#btnLamMoi').click(() => {
            tblDanhSach.setData('/api/quan-ly/danh-muc/chi-nhanh/danh-sach');
        })

        $('input, textarea').keypress(function (e) {
            let keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                $('#modalThemMoi .btnSubmit').click();
                e.preventDefault();
                return false;
            }
        });

        $('input, textarea').on('input', function () {
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
            }
        });

        $('#modalThemMoi').on('shown.bs.modal', function () {
            $(this).find('.inpTen').focus();
            $('#modalThemMoi .selCopyGia').html(null).trigger('change').select2({
                data: getChiNhanhs(),
                minimumResultsForSearch: -1
            })
        }).on('hidden.bs.modal', function() {
            $(this).find('.is-invalid').removeClass('is-invalid');
        })

        autosize($('#modalThemMoi .inpGhiChu'));

        @if($info->id !== '1000000000')
        $('#modalXem .col-thongtin i').remove();
        @endif
    }

    function initLoai() {
        $('#modalThemMoi .selLoai').select2({
            minimumResultsForSearch: -1
        });
    }

    @if($info->id == '1000000000')
    function actionThemMoi() {
        $('#modalThemMoi .btnSubmit').click(() => {
            let ten = $('#modalThemMoi .inpTen').val().trim();
            let dienthoai = $('#modalThemMoi .inpDienThoai').val().trim();
            let dienthoai2 = $('#modalThemMoi .inpDienThoai2').val().trim();
            let diachi = $('#modalThemMoi .inpDiaChi').val().trim();
            let ghichu = $('#modalThemMoi .inpGhiChu').val().trim();
            let loai = $('#modalThemMoi .selLoai').val();
            let chinhanh_id = $('#modalThemMoi .selCopyGia').val();
            let lientuc = $('#chkLienTuc')[0].checked;
            let checked = true;

            if (diachi === '')  {
                checked = false;
                showError('diachi');
            }
            if (dienthoai === '')  {
                checked = false;
                showError('dienthoai');
            }
            if (ten === '') {
                checked = false;
                showError('ten', 'Tên cửa hàng không được bỏ trống!');
            }

            if (!checked) {
                return false;
            }

            sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
            $.ajax({
                url: '/api/quan-ly/danh-muc/chi-nhanh/them-moi',
                type: 'get',
                dataType: 'json',
                data: {
                    ten, dienthoai, dienthoai2, loai, diachi, ghichu, chinhanh_id
                }
            }).done((result) => {
                if (result.succ) {
                    $('#modalThemMoi input, #modalThemMoi textarea').val('');
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
                        localStorage.setItem('danhmuc.chinhanh.views', JSON.stringify(views))
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
            @if($info->id == '1000000000')
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
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", formatter: "rownum", contextMenu,
                    width: 40, headerSort: false, hozAlign: 'center'},
                {title: "Tên", field: "ten", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.ten},
                {title: "Điện thoại cửa hàng", field: "dienthoai", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.dienthoai},
                {title: "Điện thoại tổng đài", field: "dienthoai2", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.dienthoai2},
                {title: "Loại", field: "loai", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.loai,
                    formatter: (cell) => {
                        return loais[cell.getValue()];
                    }},
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
            ajaxURL: '/api/quan-ly/danh-muc/chi-nhanh/danh-sach',
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
        initSearchTable(tblDanhSach,['ten','dienthoai','diachi']);
    }

    function clickXemThongTin(data, col) {
        let field = $(col).attr('data-field');
        let ten = $(col).attr('data-title');
        let value = data[field];
        $(col).find('span').text(field === 'loai' ? loais[value] : value);
        @if($info->id == '1000000000')
        let edit = $(col).find('i.edit');
        if (edit.length > 0) {
            edit.off('click').click(() => {
                clickSuaThongTin(field,value,ten,data,col);
            })
        }
        @endif
    }

    @if($info->id == '1000000000')
    function clickSuaThongTin(field, value, ten, data, col = null) {
        let onSubmit = () => {
            let value = $('#modalInput .value').val().trim();
            if (['ten','dienthoai','diachi'].indexOf(field) !== -1 && value === '') {
                showErrorModalInput(ten + ' không được bỏ trống!');
                return false;
            }
            sToast.confirm('Xác nhận cập nhật ' + ten.toLowerCase() + '?','',
                (result) => {
                    if (result.isConfirmed) {
                        sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/danh-muc/chi-nhanh/cap-nhat',
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
        if (['ten','dienthoai','dienthoai2'].indexOf(field) !== -1) {
            mInput(data.ten,value,field !== 'dienthoai2').text(ten,ten + '...',onSubmit);
        }
        if (field === 'ghichu' || field === 'diachi') {
            mInput(data.ten,value,field === 'diachi').textarea(ten,ten + '...',onSubmit);
        }
        if (field === 'loai') {
            let _loais = [
                {id: 'cuahang', text: 'Cửa hàng'},
                {id: 'congty', text: 'Công ty'},
                {id: 'khohanghong', text: 'kho hàng hỏng'}
            ];
            mInput(data.ten,value).select2(ten,'',_loais,true,onSubmit);
        }
    }

    function clickXoaThongTin(cell) {
        sToast.confirm('Xác nhận xóa thông tin cửa hàng?',
            '<div>' + cell.getData().ten + '</div>' +
            '<div class="font-weight-bolder text-danger">Lưu ý: Sau khi xóa cửa hàng. Toàn bộ thông tin giá bán sẽ bị xóa và không thể phục hồi.</div>'
            ,
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/danh-muc/chi-nhanh/xoa',
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
        mInput(cell.getData().ten).select2('Sao chép giá từ cửa hàng','',getChiNhanhs(),true,
            () => {
                let value = $('#modalInput .value').val();
                sToast.confirm('Xác nhận phục hồi thông tin cửa hàng?','',
                    (result) => {
                        if (result.isConfirmed) {
                            sToast.loading('Đang phục hồi dữ liệu. Vui lòng chờ...')
                            $.ajax({
                                url: '/api/quan-ly/danh-muc/chi-nhanh/phuc-hoi',
                                type: 'get',
                                dataType: 'json',
                                data: {
                                    id: cell.getData().id,
                                    chinhanh_id: value
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

    function showError(type, erro = '') {
        let inputs = {
            ten: $('#modalThemMoi .inpTen'),
            dienthoai: $('#modalThemMoi .inpDienThoai'),
            diachi: $('#modalThemMoi .inpDiaChi'),
            chinhanh_id: $('#modalThemMoi .selCopyGia')
        }
        if (erro !== '') {
            $(inputs[type].parent()).find('span.error').text(erro);
        }
        inputs[type].addClass('is-invalid');
        inputs[type].focus();
    }
    @endif

    function getChiNhanhs() {
        let chinhanhs = [];
        tblDanhSach.getData().forEach((value) => {
            if (isNull(value.deleted_at)) {
                chinhanhs.push({
                    id: value.id,
                    text: value.ten
                })
            }
        });
        chinhanhs.push({
            id: 'none',
            text: 'Không sao chép'
        })

        return chinhanhs;
    }
</script>
@stop
