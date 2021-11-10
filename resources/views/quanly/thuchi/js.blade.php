<script>
    let tblDanhSach;
    let tblDanhSachPhieu;
    let tblDanhSachPhieu2;
    let chinhanhs = JSON.parse('{!! $chinhanhs !!}');
    let khoanmucs = JSON.parse('{!! $khoanmucs !!}');
    init();
    initSelDoiTuong();
    initChiNhanh();
    initDanhSach();
    initTblDanhSachPhieu();
    actionLoc();

    function init() {
        $(window).on('message', (event) => {
            let data = event.originalEvent.data;
            let type = data.type;

            if ((type === 'xoa' || type === 'phuc-hoi') && $('#lblNgay').attr('data-value') === '{{ date('Y-m-d') }}') {
                actionLoc();
            }
        });
        $('#btnLoc').click(() => {
            actionLoc();
        })
        $('#inpNgay').datetimepicker({
            format: 'DD/MM/YYYY',
            keepOpen: false,
            date: '{{ date('Y-m-d') }}',
            maxDate: '{{ date('Y-m-d') }}'
        });
        $('#inpNgay').on('change.datetimepicker', () => {
            actionLoc();
        });
        $('#inpNgay input').focus(() => {
            $('#inpNgay').datetimepicker('show');
        });
        $('#modalThemMoi').on('hidden.bs.modal', () => {
            $('body').addClass('modal-open');
        });
        $('#modalLapPhieu').on('hidden.bs.modal', function () {
            $(this).find('.is-invalid').removeClass('is-invalid');
        });
        $('#modalLapPhieu input, #modalLapPhieu textarea').on('input', function () {
            $(this).removeClass('is-invalid');
        })
        autosize($('#modalLapPhieu textarea'));
        $('#btnLapPhieuThu').click(() => {
            $('#modalLapPhieu').modal('show').find('.modal-title').text('Lập Phiếu Thu');
            initKhoanMuc();
            actionXemPhieu();
        });
        $('#btnLapPhieuChi').click(() => {
            $('#modalLapPhieu').modal('show').find('.modal-title').text('Lập Phiếu Chi');
            initKhoanMuc(false);
            actionXemPhieu(false);
        });
        $('#fromToDate').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true
        });
    }

    function initKhoanMuc(is_khoanthu = true) {
        let _khoanmucs = [];
        khoanmucs.forEach((value) => {
            if (is_khoanthu) {
                if (value.is_khoanthu) {
                    _khoanmucs.push(value);
                }
            }
            else {
                if (!value.is_khoanthu) {
                    _khoanmucs.push(value);
                }
            }
        })
        $('#modalLapPhieu .selKhoanMuc').html(null).select2({
            data: _khoanmucs
        })
    }

    function initSelDoiTuong() {
        $('#modalLapPhieu .selDoiTuong').select2({
            ajax: {
                url: '/api/quan-ly/danh-muc/doi-tuong/tim-kiem',
                data: function (params) {
                    let query = {
                        q: params.term
                    };

                    // Query parameters will be ?search=[term]&type=public
                    return query;
                }
            },
            templateResult: (value) => {
                if (!isUndefined(value.id)) {
                    return $('' +
                        '<span class="dienthoai text-info">' + value.dienthoai + '</span> - ' +
                        '<span>' + value.ten + '</span>' +
                        '');
                }
                else {
                    return value.text;
                }
            },
            placeholder: 'Chọn đối tượng...'
        }).on('select2:select', () => {
            $('#modalLapPhieu .inpSoTien').focus();
            $('#modalLapPhieu .selDoiTuong').removeClass('is-invalid');
        }).val(null).trigger('change');
    }

    function initChiNhanh() {
        $('#selChiNhanh').select2({
            data: chinhanhs,
            minimumResultsForSearch: -1
        }).val(info.chinhanh_id).trigger('change');

        $('#selChiNhanh').change(() => {
            actionLoc();
        });
    }

    function initDanhSach() {
        tblDanhSach = new Tabulator("#tblDanhSach", {
            columns: [
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", formatter: "rownum",
                    width: 40, headerSort: false, hozAlign: 'center'},
                {title: "Tên phiếu/khoản mục", field: "tenphieu", vertAlign: 'middle', headerSort: false,
                    cellClick: (e, cell) => {
                        if (cell.getValue() !== 'ĐẦU KỲ') {
                            let data = cell.getData();
                            initTblDanhSachPhieu2(data.loaiphieu,cell.getValue(),data.dsphieu);
                        }
                    }},
                {title: "Tổng công nợ", field: "congno", vertAlign: 'middle', headerSort: false, hozAlign: 'right',
                    formatter: (cell) => {
                        if (isNull(cell.getValue()) || isUndefined(cell.getValue())) {
                            return '';
                        }
                        return '<span class="text-secondary font-weight-bolder">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }},
                {title: "Tổng thu", field: "tongthu", vertAlign: 'middle', headerSort: false, hozAlign: 'right',
                    formatter: (cell) => {
                        if (isNull(cell.getValue()) || isUndefined(cell.getValue())) {
                            return '';
                        }
                        return '<span class="text-success font-weight-bolder">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }},
                {title: "Tổng chi", field: "tongchi", vertAlign: 'middle', headerSort: false, hozAlign: 'right',
                    formatter: (cell) => {
                        if (isNull(cell.getValue()) || isUndefined(cell.getValue())) {
                            return '';
                        }
                        return '<span class="text-danger font-weight-bolder">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }},
                {title: "Tổng cuối", field: "tongcuoi", vertAlign: 'middle', headerSort: false, hozAlign: 'right',
                    formatter: (cell) => {
                        return '<span class="font-weight-bolder text-info">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }},
            ],
            ajaxResponse: function(url, params, response) {
                $('#boxThongTin .dauky').text(numeral(response.dauky).format('0,0'));
                $('#boxThongTin .tongthu').text(numeral(response.tongthu).format('0,0'));
                $('#boxThongTin .tongchi').text(numeral(response.tongchi).format('0,0'));
                $('#boxThongTin .cuoiky').text(numeral(response.cuoiky).format('0,0'));
                if (response.is_ketso) {
                    if ($('#lblNgay').attr('data-value') === '{{ date('Y-m-d') }}') {
                        initActionMoSo();
                    }
                    else {
                        $('#btnKetSo').removeClass('bg-gradient-danger').attr('disabled','').html('<i class="fa fa-book mr-1"></i> ĐÃ KẾT SỔ');
                    }
                }
                else {
                    initActionKetSo();
                }

                return response.data; //return the tableData property of a response json object
            },
            height: '450px',
            movableColumns: false,
            pagination: 'local',
            paginationSize: 10,
            pageLoaded: () => {
                if (isNull(tblDanhSach) || isUndefined(tblDanhSach)) {
                    return false;
                }
                tblDanhSach.getColumns()[0].updateDefinition();
            },
            dataFiltered: function () {
                if (isNull(tblDanhSach) || isUndefined(tblDanhSach)) {
                    return false;
                }
                setTimeout(() => {tblDanhSach.getColumns()[0].updateDefinition()},10);
            }
        });
        initSearchTable(tblDanhSach,['loaiphieu','tenphieu']);
    }

    function actionLoc() {
        let chinhanh_id = $('#selChiNhanh').val();
        let ngay = $('#inpNgay').datetimepicker('viewDate').format('YYYY-MM-DD');
        tblDanhSach.setData('/api/quan-ly/thu-chi/tra-cuu', {
            chinhanh_id, ngay
        });
        $('#lblNgay').text($('#inpNgay').datetimepicker('viewDate').format('DD/MM/YYYY'))
            .attr('data-value', $('#inpNgay').datetimepicker('viewDate').format('YYYY-MM-DD'))
    }

    function initTblDanhSachPhieu() {
        $('#modalDanhSachPhieu .danhsachphieu-title').text('Thu Chi');

        tblDanhSachPhieu = new Tabulator("#tblDanhSachPhieu", {
            columns: [
                {title: 'STT', field: 'stt', headerHozAlign: 'center', formatter: 'rownum', width: 30,
                    hozAlign: 'center', headerSort: false, vertAlign: 'middle'},
                {title: 'Thời gian', field: 'created_at', headerSort: false, vertAlign: 'middle',
                    formatter: function(cell) {
                        return doi_ngay(cell.getValue());
                    }},
                {title: 'Mã phiếu', field: 'maphieu', headerSort: false, vertAlign: 'middle',
                    formatter: (cell) => {
                        return '<span class="text-primary">' + cell.getValue() + '</span>';
                    }, cellClick: (e, cell) => {
                        mPhieu('/quan-ly/xem-phieu/' + cell.getValue() + '?deletable=1').xemphieu(cell.getTable());
                    }},
                {title: 'Đối tượng', field: 'doituong', headerSort: false},
                {title: 'Tiền thanh toán', field: 'tienthanhtoan', headerHozAlign: 'right',
                    hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                    formatter: function(cell) {
                        return '<span class="text-tienthanhtoan">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }},
                {title: 'Nội dung', field: 'noidung', headerSort: false},
                {title: 'Số phiếu', field: 'sophieu', headerHozAlign: 'right', headerSort: false, hozAlign: 'right', vertAlign: 'middle'},
                {title: 'NV lập phiếu', field: 'nhanvien', headerSort: false}
            ],
            rowFormatter: (row) => {
                if (!isNull(row.getData().deleted_at)) {
                    $(row.getElement()).addClass('phieu-daxoa');
                }
                else {
                    $(row.getElement()).removeClass('phieu-daxoa');
                }
            },
            height: '465px',
            pagination: 'local',
            paginationSize: 10,
            dataFiltered: function () {
                if (isNull(tblDanhSachPhieu) || isUndefined(tblDanhSachPhieu)) {
                    return false;
                }
                setTimeout(() => {tblDanhSachPhieu.getColumns()[0].updateDefinition()},10);
            }
        });

        initSearchTable(tblDanhSachPhieu,['maphieu','doituong']);

        $('#modalDanhSachPhieu').on('shown.bs.modal', function() {
            $(this).find('button.btnXem').click();
        })

        $('#modalDanhSachPhieu button.btnXem').click(() => {
            tblDanhSachPhieu.setData('/api/quan-ly/phieu/danh-sach', {
                loaiphieu: 'TC',
                begin: getDateRangePicker($('#fromToDate')),
                end: getDateRangePicker($('#fromToDate'),false)
            })
        })
    }

    function actionXemPhieu(is_phieuthu = true) {
        $('#modalLapPhieu .btnSubmit').off('click').click(() => {
            let khoanmuc_id = $('#modalLapPhieu .selKhoanMuc').val();
            let khoanmuc = $('#modalLapPhieu .selKhoanMuc option:selected').text();
            let doituong = $('#modalLapPhieu .selDoiTuong').select2('data');
            let sotien = parseFloat($('#modalLapPhieu .inpSoTien').attr('data-value'));
            let noidung = $('#modalLapPhieu .inpNoiDung').val().trim();
            let ghichu = $('#modalLapPhieu .inpGhiChu').val().trim();
            let checked = true;
            if (noidung === '') {
                showError2('noidung');
                $('#modalLapPhieu .inpNoiDung').focus();
                checked = false;
            }
            if (sotien <= 0 || isNaN(sotien)) {
                showError2('sotien');
                $('#modalLapPhieu .inpSoTien').focus();
                checked = false;
            }
            if (doituong.length === 0) {
                showError2('doituong');
                $('#modalLapPhieu .selDoiTuong').focus();
                checked = false;
            }
            else {
                doituong = doituong[0];
            }

            if (!checked) {
                return false;
            }

            sToast.loading('Đang lấy thông tin phiếu. Vui lòng chờ...');

            let data = {
                doituong, noidung, tienthanhtoan: sotien, khoanmuc_id, khoanmuc, ghichu,
                loaiphieu: is_phieuthu ? 'PT' : 'PC'
            }
            $.ajax({
                url: '/api/quan-ly/phieu/tao-phieu/' + (is_phieuthu ? 'PT' : 'PC'),
                type: 'post',
                dataType: 'json',
                data: {
                    phieu: JSON.stringify(data)
                }
            }).done((result) => {
                if (result.succ) {
                    mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu).taophieu(() => {
                        sToast.loading('Đang tạo phiếu. Vui lòng chờ...');
                        $.ajax({
                            url: '/api/quan-ly/thu-chi/luu-phieu',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                phieu: JSON.stringify(data)
                            }
                        }).done((result) => {
                            if (result.succ) {
                                mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu).xemphieu();
                                $('#modalLapPhieu .selDoiTuong').val(null).trigger('change');
                                $('#modalLapPhieu .inpSoTien, #modalLapPhieu .inpNoiDung, #modalLapPhieu .inpGhiChu').val('').trigger('input');
                                autosize.update($('#modalLapPhieu textarea'));
                                if ($('#lblNgay').attr('data-value') === '{{ date('Y-m-d') }}') {
                                    actionLoc();
                                }
                            }
                        });
                    })
                }
            });
        })
    }

    function initActionKetSo() {
        $('#btnKetSo').attr('disabled',null).html('<i class="fa fa-book mr-1"></i> KẾT SỔ').off('click').click(() => {
            mInput('Kết Sổ Ngày ' + $('#lblNgay').text()).textarea('Ghi chú','Nhập ghi chú...',
                () => {
                    sToast.confirm('Xác nhận kết sổ ngày ' + $('#lblNgay').text() + '?','',
                        (confirmed) => {
                            if (confirmed.isConfirmed) {
                                sToast.loading('Đang thực hiện. Vui lòng chờ...');
                                $.ajax({
                                    url: '/api/quan-ly/thu-chi/ket-so',
                                    type: 'get',
                                    dataType: 'json',
                                    data: {
                                        ngay: $('#lblNgay').attr('data-value'),
                                        ghichu: $('#modalInput .value').val().trim(),
                                        chinhanh_id: $('#selChiNhanh').val()
                                    }
                                }).done((result) => {
                                    $('#modalInput').modal('hide');
                                    if (result.succ) {
                                        mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu).xemphieu();
                                        if ($('#lblNgay').attr('data-value') === '{{ date('Y-m-d') }}') {
                                            initActionMoSo();
                                        }
                                        else {
                                            $('#btnKetSo').removeClass('bg-gradient-danger').attr('disabled','').html('<i class="fa fa-book mr-1"></i> ĐÃ KẾT SỔ')
                                        }
                                    }
                                });
                            }
                        })
                })
        });
        $('#btnKetSo').removeClass('bg-gradient-danger');
    }

    function initActionMoSo() {
        $('#btnKetSo').attr('disabled',null).html('<i class="fa fa-book mr-1"></i> MỞ SỔ').off('click').click(() => {
            sToast.confirm('Xác nhận mở sổ ngày ' + $('#lblNgay').text() + '?','',
                (confirmed) => {
                    if (confirmed.isConfirmed) {
                        sToast.loading('Đang thực hiện. Vui lòng chờ...');
                        $.ajax({
                            url: '/api/quan-ly/thu-chi/mo-so',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                chinhanh_id: $('#selChiNhanh').val()
                            }
                        }).done((result) => {
                            if (result.succ) {
                                initActionKetSo();
                            }
                        });
                    }
                })
        });
        $('#btnKetSo').addClass('bg-gradient-danger');
    }

    function initTblDanhSachPhieu2(loaiphieu, tenphieu, data) {
        $('#modalDanhSachPhieu2').off('hidden.bs.modal').off('shown.bs.modal').on('shown.bs.modal', () => {
            tblDanhSachPhieu2 = new Tabulator("#tblDanhSachPhieu2", {
                columns: [
                    {title: 'STT', field: 'stt', headerHozAlign: 'center', formatter: 'rownum', width: 30,
                        hozAlign: 'center', headerSort: false, vertAlign: 'middle'},
                    {title: 'Thời gian', field: 'created_at', headerSort: false, vertAlign: 'middle',
                        formatter: function(cell) {
                            return doi_ngay(cell.getValue());
                        }},
                    {title: 'Mã phiếu', field: 'maphieu', headerSort: false, vertAlign: 'middle',
                        formatter: (cell) => {
                            return '<span class="text-primary">' + cell.getValue() + '</span>';
                        }, cellClick: (e, cell) => {
                            mPhieu('/quan-ly/xem-phieu/' + cell.getValue() + '?deletable=1').xemphieu(cell.getTable());
                        }},
                    {title: 'Đối tượng', field: 'doituong', headerSort: false},
                    {title: 'Tiền thanh toán', field: 'tienthanhtoan', headerHozAlign: 'right',
                        hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                        formatter: function(cell) {
                            return '<span class="text-tienthanhtoan">' + numeral(cell.getValue()).format('0,0') + '</span>';
                        }},
                    loaiphieu === 'BHM' || loaiphieu === 'BHN' ?
                        {title: 'Tiền còn nợ', field: 'tienthua', headerHozAlign: 'right',
                            hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                            formatter: function(cell) {
                                if (cell.getValue() < 0) {
                                    return '<span class="text-danger">' + numeral(-cell.getValue()).format('0,0') + '</span>';
                                }
                                return '0';
                            }} :
                        {title: 'Nội dung', field: 'noidung', headerSort: false},
                    {title: 'Số phiếu', field: 'sophieu', headerHozAlign: 'right', headerSort: false, hozAlign: 'right', vertAlign: 'middle'},
                    {title: 'NV lập phiếu', field: 'nhanvien', headerSort: false}
                ],
                height: '465px',
                pagination: 'local',
                paginationSize: 10,
                dataFiltered: function () {
                    if (isNull(tblDanhSachPhieu2) || isUndefined(tblDanhSachPhieu2)) {
                        return false;
                    }
                    setTimeout(() => {tblDanhSachPhieu2.getColumns()[0].updateDefinition()},10);
                }
            });

            initSearchTable(tblDanhSachPhieu2,['maphieu','doituong']);

            tblDanhSachPhieu2.setData(data);
        }).on('hidden.bs.modal', () => {
            tblDanhSachPhieu2.clearData();
        }).modal('show').find('.modal-title').text('Danh Sách Phiếu ' + tenphieu);
    }

    function showError2(type, erro = '') {
        let inputs = {
            doituong: $('#modalLapPhieu .selDoiTuong'),
            sotien: $('#modalLapPhieu .inpSoTien'),
            noidung: $('#modalLapPhieu .inpNoiDung')
        }
        if (erro !== '') {
            $(inputs[type].parent()).find('span.error').text(erro);
        }
        inputs[type].addClass('is-invalid');
        inputs[type].focus();
    }
</script>
