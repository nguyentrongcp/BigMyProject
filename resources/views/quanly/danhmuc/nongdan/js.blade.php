<script>
    let tblDanhSach;
    let caytrongs = JSON.parse('{!! str_replace("'","\'",json_encode($caytrongs)) !!}');
    caytrongs.forEach((value) => {
        value.id = value.text;
    })
    let danhxungs = [
        { id: 'Anh', text: 'Anh' },
        { id: 'Chị', text: 'Chị' },
        { id: 'Chú', text: 'Chú' },
        { id: 'Bác', text: 'Bác' },
        { id: 'Cô', text: 'Cô' },
        { id: 'Dì', text: 'Dì' },
        { id: 'Em', text: 'Em' },
        { id: 'Ông', text: 'Ông' },
        { id: 'Bà', text: 'Bà' },
    ];
    let views = localStorage.getItem('danhmuc.nongdan.views');
    views = isNull(views) ? {} : JSON.parse(views);
    init();
    initDanhSach();

    function init() {
        $('#btnLamMoi').click(() => {
            tblDanhSach.setData('/api/quan-ly/danh-muc/nong-dan/danh-sach');
        })

        @if(in_array('danh-muc.nong-dan.chinh-sua',$info->phanquyen) === false)
        $('#modalXem .col-thongtin i').remove();
        @endif
    }

    @if(in_array('danh-muc.nong-dan.them-moi',$info->phanquyen) !== false)
    initActionThemMoi();
    function initActionThemMoi() {
        $('#modalThemMoi input, #modalThemMoi textarea').keypress(function(e) {
            let keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                $('#modalThemMoi .btnSubmit').click();
                e.preventDefault();
                return false;
            }
        }).on('input', function () {
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
            }
        });

        $('#modalThemMoi .selCayTrong').select2({
            data: caytrongs,
            placeholder: 'Chọn cây trồng...',
            allowClear: true
        }).val(null).trigger('change');

        $('#modalThemMoi .selDanhXung').select2({
            data: danhxungs,
            minimumResultsForSearch: -1
        });

        $('#modalThemMoi').on('shown.bs.modal', function () {
            $(this).find('.inpTen').focus();
        }).on('hidden.bs.modal', function() {
            $(this).find('.is-invalid').removeClass('is-invalid');
        })

        initDiaChi($('#modalThemMoi .diachi-container'));

        $('#modalThemMoi .btnSubmit').click(() => {
            let ten = $('#modalThemMoi .inpTen').val().trim();
            let dienthoai = $('#modalThemMoi .inpDienThoai').val().trim();
            let dienthoai2 = $('#modalThemMoi .inpDienThoai2').val().trim();
            let danhxung = $('#modalThemMoi .selDanhXung').val();
            let tinh = $('#modalThemMoi .selTinh').val();
            let huyen = $('#modalThemMoi .selHuyen').val();
            let xa = $('#modalThemMoi .selXa').val();
            let _diachi = $('#modalThemMoi .inpDiaChi').val().trim();
            let caytrong = $('#modalThemMoi .selCayTrong').val();
            let dientich = $('#modalThemMoi .inpDienTich').val().trim();
            let ghichu = $('#modalThemMoi .inpGhiChu').val().trim();
            let lientuc = $('#chkLienTuc')[0].checked;
            let checked = true;

            if (dienthoai === '')  {
                checked = false;
                showError('dienthoai');
            }
            if (ten === '') {
                checked = false;
                showError('ten', 'Tên nông dân không được bỏ trống!');
            }

            if (!checked) {
                return false;
            }

            caytrong = caytrong.join(', ');

            sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
            $.ajax({
                url: '/api/quan-ly/danh-muc/nong-dan/them-moi',
                type: 'get',
                dataType: 'json',
                data: {
                    ten, dienthoai, dienthoai2, tinh, huyen, xa, _diachi, caytrong, dientich, ghichu, danhxung
                }
            }).done((result) => {
                if (result.succ) {
                    $('#modalThemMoi input, #modalThemMoi textarea:not(.select2-search__field)').val('').trigger('input');
                    $('#modalThemMoi .diachi-container select, #modalThemMoi .selCayTrong').val(null).trigger('change');
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
            @if(in_array('danh-muc.nong-dan.action',$info->phanquyen) !== false)
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
                        localStorage.setItem('danhmuc.nongdan.views', JSON.stringify(views))
                    }
                })
            }
            let menus = [
                {
                    label: '<i class="fa fa-info-circle text-info"></i> Chi tiết',
                    action: xemThongTin
                },
                    @if(in_array('danh-muc.nong-dan.action',$info->phanquyen) !== false)
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
            @if(in_array('danh-muc.nong-dan.chinh-sua',$info->phanquyen) !== false)
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
                // {title: "Công nợ", field: "congno", vertAlign: 'middle', hozAlign: 'right', headerSort: false, contextMenu,
                //     visible: isNull(views) ? true : views.congno,
                //     formatter: (cell) => {
                //         return '<span class="text-danger font-weight-bolder">' + numeral(cell.getValue()).format('0,0') + '</span>';
                //     }},
                {title: "Địa chỉ", field: "diachi", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.diachi},
                {title: "Cây trồng", field: "caytrong", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.caytrong},
                {title: "Diện tích", field: "dientich", vertAlign: 'middle', hozAlign: 'right', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.dientich},
                // {title: "Lần cuối mua hàng", field: "lancuoi_muahang", vertAlign: 'middle', headerSort: false, contextMenu,
                //     visible: isNull(views) ? true : views.lancuoi_muahang,
                //     formatter: (cell) => {
                //         return doi_ngay(cell.getValue());
                //     }},
                {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.ghichu},
            ],
            @if(in_array('danh-muc.nong-dan.action',$info->phanquyen) !== false)
            rowFormatter: (row) => {
                if (!isNull(row.getData().deleted_at)) {
                    $(row.getElement()).addClass('text-danger');
                }
                else {
                    $(row.getElement()).removeClass('text-danger');
                }
            },
            @endif
            ajaxURL: '/api/quan-ly/danh-muc/nong-dan/danh-sach',
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
            },
            dataChanged: () => {
                tblDanhSach.getColumns()[0].updateDefinition();
            }
        });
        initSearchTable(tblDanhSach,['ma','dienthoai','dienthoai2','ten']);
    }

    function clickXemThongTin(data, col) {
        let field = $(col).attr('data-field');
        let ten = $(col).attr('data-title');
        let value = data[field];
        $(col).find('span').text(value);
        @if(in_array('danh-muc.nong-dan.chinh-sua',$info->phanquyen) !== false)
        let edit = $(col).find('i.edit');
        if (edit.length > 0) {
            edit.off('click').click(() => {
                clickSuaThongTin(field,data[field],ten,data,col);
            })
        }
        @endif
    }

    @if(in_array('danh-muc.nong-dan.chinh-sua',$info->phanquyen) !== false)
    function clickSuaThongTin(field, value, ten, data, col = null) {
        let onSubmit = () => {
            let value = $('#modalInput .value').val();
            if (field !== 'caytrong') {
                value = value.trim();
            }
            if (field === 'diachi') {
                let _diachi = value;
                let xa = $('#modalInput .xa').val();
                let huyen = $('#modalInput .huyen').val();
                let tinh = $('#modalInput .tinh').val();
                value = JSON.stringify({
                    _diachi, xa, huyen, tinh
                })
            }
            if (field === 'caytrong') {
                value = value.join(', ');
            }
            if ((field === 'ten' || field === 'dienthoai') && value === '') {
                showErrorModalInput(ten + ' không được bỏ trống!');
                return false;
            }
            sToast.confirm('Xác nhận cập nhật ' + ten.toLowerCase() + '?','',
                (result) => {
                    if (result.isConfirmed) {
                        sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/danh-muc/nong-dan/cap-nhat',
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
        if (['ten','dienthoai','dienthoai2','dientich'].indexOf(field) !== -1) {
            mInput(data.ten,value,field === 'ten' || field === 'dienthoai').text(ten,ten + '...',onSubmit);
        }
        if (field === 'ghichu') {
            mInput(data.ten,value).textarea(ten,ten + '...',onSubmit);
        }
        if (field === 'danhxung') {
            mInput(data.ten,value).select2(ten,'',danhxungs,true,onSubmit);
        }
        if (field === 'diachi') {
            mInput(data.ten,data._diachi).diachi(onSubmit);
        }
        if (field === 'caytrong') {
            mInput(data.ten,value).select2(ten,'',caytrongs,true,onSubmit,'',true);
        }
    }
    @endif

    @if(in_array('danh-muc.nong-dan.action',$info->phanquyen) !== false)
    function clickXoaThongTin(cell) {
        sToast.confirm('Xác nhận xóa thông tin nông dân?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/danh-muc/nong-dan/xoa',
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
        sToast.confirm('Xác nhận phục hồi thông tin nông dân?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang phục hồi dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/danh-muc/nong-dan/phuc-hoi',
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
