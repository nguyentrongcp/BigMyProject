@section('js-custom')
<script>
    let tblDanhSach;
    init();
    initLoai();
    initDanhSach();
    actionThemMoi();

    function init() {
        $('#btnLamMoi').click(() => {
            tblDanhSach.setData('/api/quan-ly/danh-muc/phan-quyen/danh-sach');
        })

        $('input, textarea').keypress(function (e) {
            let keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                $('#modalThemMoi .btnSubmit').click();
                e.preventDefault();
                return false;
            }
        });

        $('input').on('input', function () {
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
            }
        });

        $('#modalThemMoi').on('shown.bs.modal', function () {
            $(this).find('.inpSTT').focus();
        }).on('hidden.bs.modal', function() {
            $(this).find('.is-invalid').removeClass('is-invalid');
        })

        autosize($('#modalThemMoi .inpGhiChu'));
    }

    function initLoai() {
        $('#modalThemMoi .selLoai').select2({
            minimumResultsForSearch: -1
        })
    }

    function actionThemMoi() {
        $('#modalThemMoi .btnSubmit').click(() => {
            let stt = $('#modalThemMoi .inpSTT').val();
            let ma = $('#modalThemMoi .inpMa').val().trim();
            let ten = $('#modalThemMoi .inpTen').val().trim();
            let chucnang = $('#modalThemMoi .inpChucNang').val().trim();
            let ghichu = $('#modalThemMoi .inpGhiChu').val().trim();
            let url = $('#modalThemMoi .inpUrl').val().trim();
            let loai = $('#modalThemMoi .selLoai').val();
            let lientuc = $('#chkLienTuc')[0].checked;
            let checked = true;

            if (chucnang === '')  {
                checked = false;
                showError('chucnang');
            }
            if (ten === '')  {
                checked = false;
                showError('ten','Tên quyền không được bỏ trống!');
            }
            if (ma === '') {
                checked = false;
                showError('ma', 'Mã quyền không được bỏ trống!');
            }
            if (stt === '') {
                checked = false;
                showError('stt', 'Số thứ tự không được bỏ trống!');
            }

            if (!checked) {
                return false;
            }

            sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
            $.ajax({
                url: '/api/quan-ly/danh-muc/phan-quyen/them-moi',
                type: 'get',
                dataType: 'json',
                data: {
                    stt, ma, ten, chucnang, ghichu, loai, url
                }
            }).done((result) => {
                if (result.succ) {
                    $('#modalThemMoi input:not(.inpChucNang), #modalThemMoi textarea').val('').trigger('input');
                    lientuc ? $('#modalThemMoi .inpSTT').focus() : $('#modalThemMoi').modal('hide');
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

    function initDanhSach() {
        let contextMenu = () => {
            return [
                {
                    label: '<i class="fa fa-edit text-primary"></i> Chỉnh sửa',
                    action: (e, cell) => {
                        let field = cell.getField();
                        let value = cell.getValue();
                        let data = cell.getData();
                        let ten = cell.getColumn().getDefinition().title;
                        clickSuaThongTin(field,value,ten,data);
                    }
                },
                {
                    label: '<i class="fa fa-edit text-primary"></i> Chỉnh sửa chức năng',
                    action: (e, cell) => {
                        let data = cell.getData();
                        clickSuaThongTin('chucnang',data.chucnang,'Chức năng',data);
                    }
                }
            ];
        }

        tblDanhSach = new Tabulator("#tblDanhSach", {
            columns: [
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", contextMenu,
                    headerSort: false, hozAlign: 'center'},
                {title: "Mã", field: "ma", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Tên", field: "ten", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Loại", field: "loai", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "URL", field: "url", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu},
            ],
            ajaxURL: '/api/quan-ly/danh-muc/phan-quyen/danh-sach',
            height: '450px',
            movableColumns: false,
            groupBy: 'chucnang',
            groupHeader: function(value, count){
                return value + '<span class="text-danger ml-3">(' + count + ' quyền)</span>';
            },
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
        initSearchTable(tblDanhSach,['ma','ten','chucnang']);
    }

    function clickSuaThongTin(field, value, ten, data) {
        let onSubmit = () => {
            let value = $('#modalInput .value').val().trim();
            if (['stt','ma','ten'].indexOf(field) !== -1 && value === '') {
                showErrorModalInput(ten + ' không được bỏ trống!');
                return false;
            }
            sToast.confirm('Xác nhận cập nhật ' + ten.toLowerCase() + '?','',
                (result) => {
                    if (result.isConfirmed) {
                        sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/danh-muc/phan-quyen/cap-nhat',
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
                                setTimeout(() => {
                                    if (field === 'chucnang') {
                                        tblDanhSach.setData(tblDanhSach.getData());
                                    }
                                    else {
                                        tblDanhSach.getColumns()[0].updateDefinition()
                                    }
                                },10);
                            }
                            else if (!isUndefined(result.erro)) {
                                showErrorModalInput(result.erro);
                            }
                        });
                    }
                });
        }
        if (['ma','ten','chucnang','url'].indexOf(field) !== -1) {
            mInput(data.ten,value,field !== 'url').text(ten,ten + '...',onSubmit);
        }
        if (field === 'ghichu') {
            mInput(data.ten,value).textarea(ten,ten + '...',onSubmit);
        }
        if (field === 'stt') {
            mInput(data.ten,value).number(ten,ten + '...',onSubmit);
        }
    }

    // function clickXoaThongTin(cell) {
    //     sToast.confirm('Xác nhận xóa thông tin phân quyền?','',
    //         (result) => {
    //             if (result.isConfirmed) {
    //                 sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
    //                 $.ajax({
    //                     url: '/api/quan-ly/danh-muc/phan-quyen/xoa',
    //                     type: 'get',
    //                     dataType: 'json',
    //                     data: {
    //                         id: cell.getData().id
    //                     }
    //                 }).done((result) => {
    //                     if (result.succ) {
    //                         cell.getTable().updateData([{
    //                             id: cell.getData().id,
    //                             deleted_at: result.data.deleted_at
    //                         }])
    //                     }
    //                 });
    //             }
    //         });
    // }

    function showError(type, erro = '') {
        let inputs = {
            stt: $('#modalThemMoi .inpSTT'),
            ma: $('#modalThemMoi .inpMa'),
            ten: $('#modalThemMoi .inpTen'),
            chucnang: $('#modalThemMoi .inpChucNang')
        }
        if (erro !== '') {
            $(inputs[type].parent()).find('span.error').text(erro);
        }
        inputs[type].addClass('is-invalid');
        inputs[type].focus();
    }
</script>
@stop
