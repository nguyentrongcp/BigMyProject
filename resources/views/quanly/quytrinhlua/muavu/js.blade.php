<script>
    let tblDanhSach;
    let muavus = JSON.parse('{!! str_replace("'","\'",$muavus) !!}');
    init();
    initDanhSach();

    function init() {
        $('#btnLamMoi').click(() => {
            tblDanhSach.setData('/api/quan-ly/quy-trinh-lua/mua-vu/danh-sach');
        })
    }

    @if(in_array('quy-trinh-lua.mua-vu.them-moi',$info->phanquyen) !== false)
    initActionThemMoi();
    function initActionThemMoi() {
        offEnterTextarea($('#modalThemMoi input, #modalThemMoi textarea'),() => {$('#modalThemMoi .btnSubmit').click()})
        $('#modalThemMoi input, #modalThemMoi textarea').on('input', function () {
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
            }
        });

        $('#modalThemMoi .selMuaVu').select2({
            data: muavus,
            minimumResultsForSearch: -1,
            allowClear: true,
            placeholder: 'Không sao chép'
        }).val(null).trigger('change');

        $('#modalThemMoi').on('shown.bs.modal', function () {
            $(this).find('.inpMa').focus();
        }).on('hidden.bs.modal', function() {
            $(this).find('.is-invalid').removeClass('is-invalid');
        })

        $('#modalThemMoi .btnSubmit').click(() => {
            let ma = $('#modalThemMoi .inpMa').val().trim();
            let ten = $('#modalThemMoi .inpTen').val().trim();
            let muavu_id = $('#modalThemMoi .selMuaVu').val();
            let ghichu = $('#modalThemMoi .inpGhiChu').val().trim();
            let lientuc = $('#chkLienTuc')[0].checked;

            if (ma === '')  {
                showError('ma','Mã mùa vụ không được bỏ trống!');
                return false;
            }
            if (ten === '') {
                showError('ten', 'Tên mùa vụ không được bỏ trống!');
                return false;
            }

            sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
            $.ajax({
                url: '/api/quan-ly/quy-trinh-lua/mua-vu/them-moi',
                type: 'get',
                dataType: 'json',
                data: {
                    ten, ma, ghichu, muavu_id
                }
            }).done((result) => {
                if (result.succ) {
                    $('#modalThemMoi input, #modalThemMoi textarea').val('').trigger('input');
                    lientuc ? $('#modalThemMoi .inpMa').focus() : $('#modalThemMoi').modal('hide');
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
        let contextMenu = (cell) => {
            let data = cell.getData();
            let menus = [];
            @if(in_array('quy-trinh-lua.mua-vu.chinh-sua',$info->phanquyen) !== false)
            if (['ma','ten','ghichu'].indexOf(cell.getField()) > -1) {
                menus.unshift({
                    label: '<i class="fa fa-edit text-primary"></i> Chỉnh sửa',
                    action: (e, cell) => {
                        let field = cell.getField();
                        let value = cell.getValue();
                        let data = cell.getData();
                        let ten = cell.getColumn().getDefinition().title;
                        clickSuaThongTin(field,value,ten,data);
                    }
                });
            }
            @endif
            @if(in_array('quy-trinh-lua.mua-vu.action',$info->phanquyen) !== false)
            menus.push({
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
            })
            @endif

            return menus;
        }

        tblDanhSach = new Tabulator("#tblDanhSach", {
            columns: [
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", formatter: "rownum",
                    width: 40, headerSort: false, hozAlign: 'center', contextMenu},
                {title: "Mã mùa vụ", field: "ma", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Tên mùa vụ", field: "ten", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Trạng thái", field: "status", vertAlign: 'middle', headerSort: false, contextMenu,
                    formatter: (cell) => {
                        if (cell.getValue()) {
                            return '<span class="text-success">Đang hoạt động</span>';
                        }
                        else {
                            return '<span class="text-danger">Đã kết thúc</span>';
                        }
                    }},
                {title: "Số nông dân tham gia", field: "soluong_nongdan", vertAlign: 'middle', hozAlign: 'right', headerSort: false, contextMenu},
                {title: "Số thửa ruộng", field: "soluong_thuaruong", vertAlign: 'middle', hozAlign: 'right', headerSort: false, contextMenu},
                {title: "Ngày tạo", field: "ngaytao", vertAlign: 'middle', hozAlign: 'center', headerSort: false, contextMenu,
                    formatter: (cell) => {
                        return doi_ngay(cell.getValue());
                    }},
                {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu},
            ],
            @if(in_array('quy-trinh-lua.mua-vu.action',$info->phanquyen) !== false)
            rowFormatter: (row) => {
                if (!isNull(row.getData().deleted_at)) {
                    $(row.getElement()).addClass('text-danger');
                }
                else {
                    $(row.getElement()).removeClass('text-danger');
                }
            },
            @endif
            ajaxURL: '/api/quan-ly/quy-trinh-lua/mua-vu/danh-sach',
            height: '450px',
            layoutColumnsOnNewData:true,
            pageLoaded: () => {
                tblDanhSach.getColumns()[0].updateDefinition();
            },
            dataChanged: () => {
                tblDanhSach.getColumns()[0].updateDefinition();
            }
        });
        initSearchTable(tblDanhSach,['ma','ten']);
    }

    @if(in_array('quy-trinh-lua.mua-vu.chinh-sua',$info->phanquyen) !== false)
    function clickSuaThongTin(field, value, ten, data, col = null) {
        let onSubmit = () => {
            let value = $('#modalInput .value').val().trim();
            if ((field === 'ma' || field === 'ten') && value === '') {
                showErrorModalInput(ten + ' không được bỏ trống!');
                return false;
            }
            sToast.confirm('Xác nhận cập nhật ' + ten.toLowerCase() + '?','',
                (result) => {
                    if (result.isConfirmed) {
                        sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/quy-trinh-lua/mua-vu/cap-nhat',
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
                            }
                        });
                    }
                });
        }
        if (['ten','ma'].indexOf(field) !== -1) {
            mInput(data.ten,value,true).text(ten,ten + '...',onSubmit);
        }
        if (field === 'ghichu') {
            mInput(data.ten,value).textarea(ten,ten + '...',onSubmit);
        }
    }
    @endif

    @if(in_array('quy-trinh-lua.mua-vu.action',$info->phanquyen) !== false)
    function clickXoaThongTin(cell) {
        sToast.confirm('Xác nhận xóa thông tin mùa vụ?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/quy-trinh-lua/mua-vu/xoa',
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
                        }
                    });
                }
            });
    }

    function clickPhucHoiThongTin(cell) {
        sToast.confirm('Xác nhận phục hồi thông tin mùa vụ?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang phục hồi dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/quy-trinh-lua/mua-vu/phuc-hoi',
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
                        }
                    });
                }
            });
    }
    @endif

    function showError(type, erro = '') {
        let inputs = {
            ma: $('#modalThemMoi .inpMa'),
            ten: $('#modalThemMoi .inpTen'),
        }
        if (erro !== '') {
            $(inputs[type].parent()).find('span.error').text(erro);
        }
        inputs[type].addClass('is-invalid');
        inputs[type].focus();
    }
</script>
