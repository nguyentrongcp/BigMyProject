<script>
    let tblHangHoa;
    let nhacungcaps = JSON.parse('{!! $nhacungcaps !!}');
    init();
    initSelHangHoa();
    initSelNhaCungCap();
    initTblHangHoa();
    initActionThemHangHoa();
    initActionXemPhieu();
    initTblDanhSachPhieu();

    function init() {
        $('#fromToDate').daterangepicker({
            startDate: '{{ date('d-m-Y',strtotime('-1 months',time())) }}',
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true
        });
        offEnterTextarea($('#inpGhiChu'),() => {$('#btnXemPhieu').click()});
        $('#hansudung').datetimepicker({
            format: 'DD/MM/YYYY',
            keepOpen: false,
        });
        $('#boxHangHoa .hansudung').focus(() => {
            $('#hansudung').datetimepicker('show');
        })
        $('#boxHangHoa input').keypress((e) => {
            if (e.keyCode === 13) {
                $('#btnThemHangHoa').click();
            }
        });
        autosize($('#inpGhiChu'));
    }

    function initSelHangHoa() {
        $('#selHangHoa').select2({
            ajax: {
                url: '/api/quan-ly/nhap-hang/tim-kiem',
                data: function (params) {
                    let query = {
                        q: params.term
                    };

                    // Query parameters will be ?search=[term]&type=public
                    return query;
                }
            },
            allowClear: true,
            placeholder: 'Chọn hàng hóa...'
        }).change(() => {
            if ($('#selHangHoa').val() != null) {
                setTimeout(() =>
                {
                    $('#boxHangHoa .soluong').focus()
                },10)
            }
        }).val(null).trigger('change').on('select2:unselect',function(){
            $(this).html(null);
        });
    }

    function initSelNhaCungCap() {
        $('#selNhaCungCap').select2({
            data: nhacungcaps,
            allowClear: true,
            placeholder: 'Chọn nhà cung cấp...'
        }).val(null).trigger('change');
    }

    function initTblHangHoa() {
        tblHangHoa = new Tabulator("#tblHangHoa", {
            columns: [
                {
                    title: "",
                    formatter: function () {
                        return '<i class="far fa-trash-alt text-danger"></i>';
                    },
                    width: 30,
                    hozAlign: "center",
                    vertAlign: 'middle',
                    headerSort: false,
                    cellClick: function (e, cell) {
                        cell.getRow().delete();
                    }
                },
                {title: "Mã", field: "ma", headerSort: false, vertAlign: 'middle'},
                {title: "Tên", field: "ten", headerSort: false, vertAlign: 'middle'},
                {title: "ĐVT", field: "donvitinh", headerSort: false, vertAlign: 'middle'},
                {title: "Q.cách", field: "quycach", hozAlign: 'right', headerSort: false, vertAlign: 'middle'},
                {title: "Số lượng", field: "soluong", headerSort: false, hozAlign: 'right', vertAlign: 'middle',
                    formatter: (cell) => {
                        return '<span class="text-info font-weight-bolder">' + cell.getValue() + '</span>';
                    }, cellClick: (e, cell) => {
                        let data = cell.getData();
                        mInput(data.ten,data.soluong).number('Nhập số lượng mới','Nhập số lượng mới...',
                            () => {
                                let value = parseFloat($('#modalInput .value').val());
                                if (value <= 0 || isNaN(value)) {
                                    $('#modalInput .value').addClass('is-invalid');
                                    return false;
                                }
                                cell.getTable().updateData([{
                                    id: data.id,
                                    soluong: value
                                }]);
                                $('#modalInput').modal('hide');
                            }, 'Số lượng không hợp lệ!');
                    }},
                {title: "Hạn sử dụng", field: "hansudung", headerSort: false, vertAlign: 'middle',
                    formatter: (cell) => {
                        return doi_ngay(cell.getValue());
                    }, cellClick: function(e, cell) {
                        sToast.datepicker('Chọn thời hạn sử dụng',{minDate: new Date(), date: cell.getValue()},
                            (confirm) => {
                            if (confirm.isConfirmed) {
                                tblHangHoa.updateData([{
                                    id: cell.getData().id,
                                    hansudung: $('#swalDatetimepicker').datetimepicker('viewDate').format('YYYY-MM-DD')
                                }])
                            }
                        })
                    }},
            ],
            height: '450px',
            movableColumns: false,
            pagination: 'local',
            paginationSize: 10,
            dataFiltered: function () {
                if (isNull(tblHangHoa) || isUndefined(tblHangHoa)) {
                    return false;
                }
                setTimeout(() => {tblHangHoa.getColumns()[0].updateDefinition()},10);
            }
        });
    }

    function initActionThemHangHoa() {
        $('#btnThemHangHoa').click(() => {
            if (isNull($('#selHangHoa').val())) {
                sToast.toast(0,'Bạn chưa chọn hàng hóa!');
                return false;
            }
            let hanghoa = $('#selHangHoa').select2('data')[0];
            let soluong = parseFloat($('#boxHangHoa .soluong').val());
            if ($('#boxHangHoa .hansudung').val() === '') {
                sToast.toast(0,'Bạn chưa chọn hạn sử dụng!');
                return false;
            }
            let hansudung = $('#hansudung').datetimepicker('viewDate').format('YYYY-MM-DD');
            if (isNaN(soluong) || soluong <= 0) {
                sToast.toast(0,'Số lượng hàng hóa không hợp lệ!');
                return false;
            }

            let dataTable = {
                id: renderID(),
                hanghoa_id: hanghoa.id,
                ma: hanghoa.ma,
                ten: hanghoa.ten,
                donvitinh: hanghoa.donvitinh,
                quycach: hanghoa.quycach,
                soluong,
                hansudung
            }
            tblHangHoa.addData(dataTable, true);

            $('#boxHangHoa input').val('').trigger('change');
            $('#selHangHoa').val(null).trigger('change').focus().select2('open');
        })
    }

    function initActionXemPhieu() {
        $('#btnXemPhieu').off('click').click(() => {
            let dshanghoa = [];
            tblHangHoa.getData().forEach((value) => {
                dshanghoa.push({
                    hanghoa: {
                        id: value.hanghoa_id,
                        ma: value.ma,
                        ten: value.ten,
                        donvitinh: value.donvitinh,
                        quycach: value.quycach
                    },
                    soluong: value.soluong,
                    hansudung: value.hansudung
                })
            })
            if (dshanghoa.length === 0) {
                sToast.toast(0,'Bạn chưa chọn hàng hóa!');
                return false;
            }
            let doituong = $('#selNhaCungCap').select2('data');
            if (doituong.length === 0) {
                sToast.toast(0,'Bạn chưa chọn nhà cung cấp!');
                return false;
            }
            doituong = {
                id: doituong[0].id,
                ma: doituong[0].ma,
                ten: doituong[0].ten
            }

            sToast.loading('Đang lấy thông tin phiếu. Vui lòng chờ...');

            let data = {
                dshanghoa, doituong,
                ghichu: $('#inpGhiChu').val().trim()
            }
            $.ajax({
                url: '/api/quan-ly/phieu/tao-phieu/NH',
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
                            url: '/api/quan-ly/nhap-hang/luu-phieu',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                phieu: JSON.stringify(data)
                            }
                        }).done((result) => {
                            if (result.succ) {
                                mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu + '?deletable=1').xemphieu();
                                $('#selNhaCungCap').val(null).trigger('change');
                                $('#inpGhiChu').val('').css('height','unset');
                                tblHangHoa.clearData();
                                socket.emit('send-notification',result.data.thongbaos);
                            }
                        });
                    })
                }
            });
        })
    }

    function initTblDanhSachPhieu() {
        $('#modalDanhSachPhieu .danhsachphieu-title').text('Nhập Hàng');

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
                @if($info->id == '1000000000')
                {title: 'Cửa hàng', field: 'chinhanh', headerSort: false},
                @endif
                {title: 'Nhà cung cấp', field: 'doituong', headerSort: false},
                @if($info->id == '1000000000')
                {title: 'Tiền thanh toán', field: 'tienthanhtoan', headerHozAlign: 'right',
                    hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                    formatter: function(cell) {
                        return '<span class="text-tienthanhtoan">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }, bottomCalc: (values, data) => {
                        let tong = 0;
                        data.forEach((value) => {
                            if (value.deleted_at == null && value.status === 1) {
                                tong += value.tienthanhtoan;
                            }
                        });
                        return numeral(tong).format('0,0');
                    }, bottomCalcFormatter: (cell) => {
                        return '<span class="text-tienthanhtoan font-weight-bolder">' + cell.getValue() + '</span>';
                    }},
                @endif
                {title: 'Số phiếu', field: 'sophieu', headerHozAlign: 'right', headerSort: false, hozAlign: 'right', vertAlign: 'middle'},
                {title: 'NV lập phiếu', field: 'nhanvien', headerSort: false}
            ],
            rowFormatter: (row) => {
                if (!isNull(row.getData().deleted_at)) {
                    $(row.getElement()).addClass('phieu-daxoa');
                }
                else {
                    $(row.getElement()).removeClass('phieu-daxoa');
                    if (row.getData().status === 0) {
                        $(row.getElement()).addClass('phieu-chuaduyet');
                    }
                    else {
                        $(row.getElement()).removeClass('phieu-chuaduyet');
                    }
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
                loaiphieu: 'NH',
                begin: getDateRangePicker($('#fromToDate')),
                end: getDateRangePicker($('#fromToDate'),false)
            })
        })
    }
</script>
