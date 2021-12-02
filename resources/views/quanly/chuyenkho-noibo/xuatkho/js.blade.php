<script>
    let tblHangHoa;
    let chinhanhs = JSON.parse('{!! str_replace("'","\'",json_encode($chinhanhs)) !!}');
    let nhanviens = JSON.parse('{!! str_replace("'","\'",json_encode($nhanviens)) !!}');
    init();
    initSelHangHoa();
    initSelChiNhanh();
    initSelNhanVien();
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
                url: '/api/quan-ly/danh-muc/hang-hoa/tim-kiem',
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

    function initSelChiNhanh() {
        $('#selChiNhanh').select2({
            data: chinhanhs,
            minimumResultsForSearch: -1
        });
    }

    function initSelNhanVien() {
        $('#selNhanVien').select2({
            data: nhanviens,
            allowClear: true,
            placeholder: info.dienthoai + ' - ' + info.ten
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
                {title: 'STT', field: 'stt', headerHozAlign: 'center', formatter: 'rownum', width: 30,
                    hozAlign: 'center', headerSort: false, vertAlign: 'middle'},
                {title: "Mã", field: "ma", headerSort: false, vertAlign: 'middle'},
                {title: "Tên", field: "ten", headerSort: false, vertAlign: 'middle'},
                {title: "ĐVT", field: "donvitinh", headerSort: false, vertAlign: 'middle'},
                {title: "Q.cách", field: "quycach", hozAlign: 'right', headerSort: false, vertAlign: 'middle'},
                {title: "Số lượng", field: "soluong", headerSort: false, hozAlign: 'right', vertAlign: 'middle',
                    formatter: (cell) => {
                        return '<span class="text-info">' + cell.getValue() + '</span>';
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
            if (isNaN(soluong) || soluong <= 0) {
                sToast.toast(0,'Số lượng hàng hóa không hợp lệ!');
                return false;
            }

            let row = tblHangHoa.getRow(hanghoa.id);

            if (row !== false) {
                tblHangHoa.updateData([{
                    id: hanghoa.id,
                    soluong: row.getData().soluong + soluong
                }])
            }
            else {
                let dataTable = {
                    id: hanghoa.id,
                    ma: hanghoa.ma,
                    ten: hanghoa.ten,
                    donvitinh: hanghoa.donvitinh,
                    quycach: hanghoa.quycach,
                    soluong,
                }
                tblHangHoa.addData(dataTable, true);
            }

            $('#boxHangHoa .soluong').val('');
            $('#selHangHoa').val(null).trigger('change').focus().select2('open');
        })
    }

    function initActionXemPhieu() {
        $('#btnXemPhieu').off('click').click(() => {
            let dshanghoa = [];
            tblHangHoa.getData().forEach((value) => {
                dshanghoa.push({
                    hanghoa: {
                        id: value.id,
                        ma: value.ma,
                        ten: value.ten,
                        donvitinh: value.donvitinh,
                        quycach: value.quycach
                    },
                    soluong: value.soluong,
                    status: 0
                })
            })
            if (dshanghoa.length === 0) {
                sToast.toast(0,'Bạn chưa chọn hàng hóa!');
                return false;
            }
            let doituong = $('#selChiNhanh').select2('data');
            if (doituong.length === 0) {
                sToast.toast(0,'Bạn chưa chọn cửa hàng nhận!');
                return false;
            }
            doituong = {
                id: doituong[0].id,
                ten: doituong[0].ten
            }
            let nhanvien_soanhang = isNull($('#selNhanVien').val()) ? {
                id: info.id,
                ten: info.ten,
                dienthoai: info.dienthoai
            } : $('#selNhanVien').select2('data')[0];

            sToast.loading('Đang lấy thông tin phiếu. Vui lòng chờ...');

            let data = {
                dshanghoa, doituong, nhanvien_soanhang,
                ghichu: $('#inpGhiChu').val().trim()
            }
            $.ajax({
                url: '/api/quan-ly/phieu/tao-phieu/XKNB',
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
                            url: '/api/quan-ly/chuyenkho-noibo/xuat-kho/luu-phieu',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                phieu: JSON.stringify(data)
                            }
                        }).done((result) => {
                            if (result.succ) {
                                mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu + '?deletable=1').xemphieu();
                                $('#selNhanVien').val(null).trigger('change');
                                $('#inpGhiChu').val('');
                                autosize.update($('#inpGhiChu'));
                                tblHangHoa.clearData();
                            }
                        });
                    })
                }
            });
        })
    }

    function initTblDanhSachPhieu() {
        $('#modalDanhSachPhieu .danhsachphieu-title').text('Xuất Kho Nội Bộ');

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
                {title: 'Cửa hàng nhận', field: 'doituong', headerSort: false},
                {title: 'Số phiếu', field: 'sophieu', headerHozAlign: 'right', headerSort: false, hozAlign: 'right', vertAlign: 'middle'},
                {title: 'NV lập phiếu', field: 'nhanvien', headerSort: false},
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
                loaiphieu: 'XKNB',
                begin: getDateRangePicker($('#fromToDate')),
                end: getDateRangePicker($('#fromToDate'),false)
            })
        })
    }
</script>
