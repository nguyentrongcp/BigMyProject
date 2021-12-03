<script>
    let tblDanhSach;
    let chinhanhs = JSON.parse('{!! str_replace("'","\'",json_encode($chinhanhs)) !!}');
    init();
    initChiNhanh();
    initSelHangHoa();
    initDanhSach();
    initActionLoc();

    function init() {
        @if(in_array('hang-hoa.phat-sinh-ton.dau-ky',$info->phanquyen) !== false)
        autosize($('#modalDauKy .inpGhiChu'));
        $('#modalDauKy').on('shown.bs.modal', function () {
            $(this).find('.inpSoLuong').focus();
        });
        $('#modalDauKy .inpSoLuong').on('input', function() {
            $(this).removeClass('is-invalid');
        })
        $('#modalDauKy .inpSoLuong, #modalDauKy .inpGhiChu').keypress((e) => {
            let keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                $('#modalDauKy .btnSubmit').click();
                e.preventDefault();
                return false;
            }
        })
        @endif
        $('#fromToDate').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true,
            startDate: '{{ '01-'.date('m-Y') }}'
        });
    }

    function initChiNhanh() {
        $('#selChiNhanh').select2({
            data: chinhanhs,
            minimumResultsForSearch: -1
        }).val(info.chinhanh_id).trigger('change').change(() => {
            setThongTin();
            if (!isUndefined(tblDanhSach)) {
                tblDanhSach.clearData();
            }
            $('#btnDauKy').off('click').attr('disabled','');
        });
    }

    function initSelHangHoa() {
        $('#selHangHoa').html(null).select2({
            ajax: {
                url: '/api/quan-ly/danh-muc/hang-hoa/tim-kiem',
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
            placeholder: 'Chọn hàng hóa...'
        }).change(function() {
            if ($(this).val() == null) {
                setThongTin();
                if (!isUndefined(tblDanhSach)) {
                    tblDanhSach.clearData();
                }
                @if(in_array('hang-hoa.phat-sinh-ton.dau-ky',$info->phanquyen) !== false)
                $('#btnDauKy').off('click').attr('disabled','');
                @endif
            }
        }).val(null).trigger('change');
    }

    function initDanhSach() {
        tblDanhSach = new Tabulator("#tblDanhSach", {
            columns: [
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", formatter: "rownum",
                    width: 40, headerSort: false, hozAlign: 'center'},
                {title: "Thời gian", field: "created_at", vertAlign: 'middle', headerSort: false, formatter: (cell) => {
                        if (cell.getValue() === 'ĐẦU KỲ') {
                            return '<span class="font-weight-bolder">' + cell.getValue() + '</span>';
                        }
                        return doi_ngay(cell.getValue());
                    }},
                {title: "Mã phiếu", field: "maphieu", vertAlign: 'middle', headerSort: false,
                    formatter: (cell) => {
                        if (isNull(cell.getValue()) || isUndefined(cell.getValue())) {
                            return '';
                        }
                        return '<span class="text-primary">' + cell.getValue() + '</span>';
                    }, cellClick: (e, cell) => {
                        if (isNull(cell.getValue()) || isUndefined(cell.getValue())) {
                            return false;
                        }
                        mPhieu('/quan-ly/xem-phieu/' + cell.getValue()).xemphieu();
                    }},
                {title: "Loại phiếu", field: "loaiphieu", vertAlign: 'middle', headerSort: false, hozAlign: 'center'},
                // {title: "Tên phiếu", field: "tenphieu", vertAlign: 'middle', headerSort: false},
                {title: "Số lượng", field: "soluong", vertAlign: 'middle', headerSort: false, hozAlign: 'right',
                    formatter: (cell) => {
                        if (isNull(cell.getValue()) || isUndefined(cell.getValue())) {
                            return '';
                        }
                        let value = parseFloat(cell.getValue());
                        return '<span class="text-' + (value < 0 ? 'danger' : 'success') + '">' + cell.getValue() + '</span>';
                    }},
                {title: "Tồn kho", field: "tonkho", vertAlign: 'middle', hozAlign: 'right', sorter: 'number',
                    formatter: (cell) => {
                        return '<span class="text-info font-weight-bolder">' + parseFloat(cell.getValue()) + '</span>';
                    }},
            ],
            ajaxResponse: function(url, params, response) {
                let hanghoa = $('#selHangHoa').select2('data')[0];
                setThongTin(hanghoa,response.thongtin);
                return response.results;
            },
            height: '450px',
            movableColumns: false
        });
        initSearchTable(tblDanhSach,['maphieu','loaiphieu']);
    }

    function initActionLoc() {
        $('#btnLoc').click(() => {
            let hanghoa_id = $('#selHangHoa').val();
            if (hanghoa_id == null) {
                sToast.toast(0,'Bạn chưa chọn hàng hóa!');
                return false;
            }
            let chinhanh_id = $('#selChiNhanh').val();
            tblDanhSach.setData('/api/quan-ly/hang-hoa/phat-sinh-ton/danh-sach', {
                begin: getDateRangePicker($('#fromToDate')),
                end: getDateRangePicker($('#fromToDate'),false),
                chinhanh_id, hanghoa_id
            }).then(() => {tblDanhSach.getColumns()[0].updateDefinition()});
        })
    }

    function setThongTin(hanghoa = null, result = null) {
        @if(in_array('hang-hoa.phat-sinh-ton.dau-ky',$info->phanquyen) !== false)
        if (!isNull(result)) {
            $('#btnDauKy').attr('disabled',null).off('click').click(() => {
                let modal = $('#modalDauKy');
                modal.find('.inpMa').val(hanghoa.ma);
                modal.find('.inpTen').val(hanghoa.ten);
                modal.find('.inpDonViTinh').val(hanghoa.donvitinh);
                modal.find('.inpQuyCach').val(hanghoa.quycach);
                modal.find('.inpTonKho').val(result.tonkho);
                modal.find('.btnSubmit').off('click').click(() => {
                    let soluong = parseFloat($('#modalDauKy .inpSoLuong').val());
                    let ghichu = $('#modalDauKy .inpGhiChu').val();
                    if (isNaN(soluong)) {
                        $('#modalDauKy .inpSoLuong').addClass('is-invalid');
                        return false;
                    }
                    sToast.confirm('Xác nhận đầu kỳ hàng hóa!','',
                        (confirmed) => {
                            if (confirmed.isConfirmed) {
                                sToast.loading('Đang xử lý dữ liệu. Vui lòng chờ...');
                                $.ajax({
                                    url: '/api/quan-ly/hang-hoa/phat-sinh-ton/dau-ky',
                                    type: 'get',
                                    dataType: 'json',
                                    data: {
                                        hanghoa_id: hanghoa.id,
                                        hanghoa_ma: hanghoa.ma,
                                        soluong: soluong - result.tonkho,
                                        ghichu,
                                        chinhanh_id: $('#selChiNhanh').val()
                                    }
                                }).done((result) => {
                                    if (result.succ) {
                                        $('#btnLoc').click();
                                        $('#modalDauKy .inpGhiChu, #modalDauKy .inpSoLuong').val('');
                                        autosize.update($('#modalDauKy .inpGhiChu'));
                                        $('#modalDauKy').modal('hide');
                                        mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu + '?deletable=1').xemphieu();
                                    }
                                    else if (!isUndefined(result.erro)) {
                                        $('#modalDauKy .inpSoLuong').addClass('is-invalid');
                                    }
                                });
                            }
                        })
                });
                modal.modal('show');
            })
        }
        @endif
        let empty = '---------------';
        let thongtins = ['ma','ten','donvitinh','quycach','nhom','tonkho','tangtk','giamtk','cuoiky','dauky'];
        hanghoa = hanghoa == null ? {
                ma: empty,
                ten: empty,
                donvitinh: empty,
                quycach: empty,
                nhom: empty
            } : hanghoa;
        result = result == null ? {
                tonkho: empty,
                tangtk: '',
                giamtk: '',
                cuoiky: '',
                dauky: ''
            } : result;
        $.each(thongtins, (key, value) => {
            let thongtin = isUndefined(hanghoa[value]) ? result[value] : hanghoa[value];
            $('#boxThongTin .' + value).text(thongtin);
        })
    }
</script>
