<script>
    let tblHangHoa;
    let tblPhieuXuat;
    let tblDanhSachPhieu;
    let nhanviens = JSON.parse('{!! str_replace("'","\'",json_encode($nhanviens)) !!}');
    init();
    initSelNhanVien();
    initTblHangHoa();
    initTblPhieuXuat();
    initActionChonHang();
    initActionXemPhieu();
    initActionHuyPhieu();
    initTblDanhSachPhieu();

    function init() {
        $('#fromToDate').daterangepicker({
            startDate: '{{ date('d-m-Y',strtotime('-1 months',time())) }}',
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true
        });
        autosize($('#inpGhiChu'));
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
                {title: "Mã", field: "ma", headerSort: false, vertAlign: 'middle', formatter: (cell) => {
                        return cell.getData().hanghoa.ma
                    }},
                {title: "Tên", field: "ten", headerSort: false, vertAlign: 'middle', formatter: (cell) => {
                        return cell.getData().hanghoa.ten
                    }},
                {title: "ĐVT", field: "donvitinh", headerSort: false, vertAlign: 'middle', formatter: (cell) => {
                        return cell.getData().hanghoa.donvitinh
                    }},
                {title: "Quy cách", field: "quycach", headerSort: false, vertAlign: 'middle', hozAlign: 'right',
                    formatter: (cell) => {
                        return cell.getData().hanghoa.quycach
                    }},
                {title: "Số lượng", field: "soluong", headerSort: false, hozAlign: 'right', vertAlign: 'middle',
                    formatter: (cell) => {
                        return '<span class="text-info">' + cell.getValue() + '</span>';
                    }},
            ],
            height: '465px',
            movableColumns: false,
            pagination: 'local',
            paginationSize: 10,
            dataFiltered: function () {
                if (isNull(tblHangHoa) || isUndefined(tblHangHoa)) {
                    return false;
                }
                setTimeout(() => {tblHangHoa.getColumns()[0].updateDefinition()},10);
                if (tblHangHoa.getData().length === 0) {
                    setThongTin();
                }
            }
        });
    }

    function initTblPhieuXuat() {
        tblPhieuXuat = new Tabulator("#tblPhieuXuat", {
            columns: [
                {title: 'STT', field: 'stt', headerHozAlign: 'center', formatter: 'rownum', width: 30,
                    hozAlign: 'center', headerSort: false, vertAlign: 'middle'},
                {title: 'Mã', field: 'ma', headerSort: false, vertAlign: 'middle',
                    formatter: (cell) => {
                        return cell.getData().hanghoa.ma;
                    }},
                {title: 'Tên', field: 'hanghoa_ten', headerSort: false, vertAlign: 'middle'},
                {title: 'ĐVT', field: 'donvitinh', headerSort: false, vertAlign: 'middle',
                    formatter: (cell) => {
                        return cell.getData().hanghoa.donvitinh;
                    }},
                {title: 'Quy cách', field: 'quycach', headerSort: false, vertAlign: 'middle', hozAlign: 'right',
                    formatter: (cell) => {
                        return cell.getData().hanghoa.quycach;
                    }},
                {title: 'Số lượng', field: 'soluong', headerSort: false, vertAlign: 'middle', hozAlign: 'right'}
            ],
            groupBy: (data) => {
                return '<span class="text-primary ml-0 maphieu">' + data.maphieu + '</span> | ' +
                    doi_ngay(data.created_at) + ' | ' + data.chinhanh_chuyen +
                    ' | Người soạn hàng: ' + data.nhanvien + '</span>';
            },
            groupHeader: (value) => {
                return value;
            },
            groupClick: (e, component) => {
                mPhieu('/quan-ly/xem-phieu/' + $(component.getElement()).find('span.maphieu').text()).xemphieu();
            },
            rowClick: (e, row) => {
                if (row.isSelected()) {
                    row.deselect();
                }
                else {
                    let rows = row.getTable().getSelectedRows();
                    row.select();
                    if (rows.length > 0) {
                        if (rows[0].getData().maphieu !== row.getData().maphieu) {
                            rows.forEach((value) => {
                                value.deselect();
                            })
                        }
                    }
                }
                let length = row.getTable().getSelectedData().length;
                if (length > 0) {
                    $('#btnChonHang').attr('disabled',null);
                }
                else {
                    $('#btnChonHang').attr('disabled','');
                }
            },
            rowContextMenu: [
                {
                    label: '<i class="fa fa-check"></i> Chọn phiếu',
                    action: () => {
                        $('#btnChonHang').click();
                    }
                },
                {
                    label: '<i class="fa fa-check-square"></i> Chọn tất cả',
                    action: (e, row) => {
                        row.getTable().getRows().forEach((value) => {
                            if (value.getData().maphieu === row.getData().maphieu) {
                                value.select();
                            }
                        })
                        $('#btnChonHang').click();
                    }
                }
            ],
            height: '465px',
            pagination: 'local',
            paginationSize: 10,
            dataFiltered: function () {
                if (isNull(tblPhieuXuat) || isUndefined(tblPhieuXuat)) {
                    return false;
                }
                setTimeout(() => {tblPhieuXuat.getColumns()[0].updateDefinition()},10);
            }
        });

        initSearchTable(tblPhieuXuat,['maphieu','hanghoa_ten','nhanvien']);

        $('#modalDanhSachPhieuXuat').on('shown.bs.modal', function() {
            tblPhieuXuat.setData('/api/quan-ly/chuyenkho-noibo/nhap-kho/danhsach-phieuxuat');
        }).on('hidden.bs.modal', () => {
            tblPhieuXuat.clearData();
            $('#btnChonHang').attr('disabled','');
        })
    }

    function initActionChonHang() {
        $('#btnChonHang').click(() => {
            let data = tblPhieuXuat.getSelectedData();
            if (data.length === 0) {
                sToast.toast(0,'Bạn chưa chọn hàng hóa!');
                return false;
            }

            tblHangHoa.setData(data);
            setThongTin(data[0]);
            $('#modalDanhSachPhieuXuat').modal('hide');
        })
    }

    function setThongTin(data = null) {
        if (isNull(data)) {
            data = {
                maphieu: '-----',
                chinhanh_chuyen: '---',
                nhanvien: '',
                ghichu: ''
            }
            $('#lblMaPhieu').off('click').text(data.maphieu);
            $('#btnHuyPhieu').addClass('d-none');
        }
        else {
            $('#lblMaPhieu').text(data.maphieu).off('click').click(() => {
                mPhieu('/quan-ly/xem-phieu/' + data.maphieu).xemphieu();
            })
            $('#btnHuyPhieu').removeClass('d-none');
        }
        $('#lblChiNhanh').text(data.chinhanh_chuyen);
        $('#boxThongTin .ten').text(data.nhanvien);
        $('#boxThongTin .ghichu').text(data.ghichu);
    }

    function initActionXemPhieu() {
        $('#btnXemPhieu').off('click').click(() => {
            let dshanghoa = [];
            let doituong = '';
            let phieuxuat = '';
            tblHangHoa.getData().forEach((value) => {
                if (doituong === '') {
                    doituong = {
                        id: value.phieu_id,
                        ten: value.chinhanh_chuyen
                    }
                }
                if (phieuxuat === '') {
                    phieuxuat = value.maphieu;
                }
                dshanghoa.push({
                    id: value.id,
                    hanghoa: {
                        id: value.hanghoa.id,
                        ma: value.hanghoa.ma,
                        ten: value.hanghoa.ten,
                        donvitinh: value.hanghoa.donvitinh,
                        quycach: value.hanghoa.quycach
                    },
                    soluong: value.soluong
                })
            });
            if (dshanghoa.length === 0) {
                sToast.toast(0,'Bạn chưa chọn hàng hóa!');
                return false;
            }
            let nhanvien_nhanhang = isNull($('#selNhanVien').val()) ? {
                id: info.id,
                ten: info.ten,
                dienthoai: info.dienthoai
            } : $('#selNhanVien').select2('data')[0];

            sToast.loading('Đang lấy thông tin phiếu. Vui lòng chờ...');

            let data = {
                dshanghoa, doituong, phieuxuat, nhanvien_nhanhang,
                ghichu: $('#inpGhiChu').val().trim()
            }
            $.ajax({
                url: '/api/quan-ly/phieu/tao-phieu/NKNB',
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
                            url: '/api/quan-ly/chuyenkho-noibo/nhap-kho/luu-phieu',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                phieu: JSON.stringify(data)
                            }
                        }).done((result) => {
                            if (result.succ) {
                                mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu).xemphieu();
                                $('#inpGhiChu').val('');
                                autosize.update($('#inpGhiChu'));
                                setThongTin();
                                tblHangHoa.clearData();
                            }
                        });
                    })
                }
            });
        });
    }

    function initActionHuyPhieu() {
        $('#btnHuyPhieu').off('click').click(() => {
            let chitiets = [];
            tblHangHoa.getData().forEach((value) => {
                chitiets.push(value.id);
            });
            if (chitiets.length === 0) {
                sToast.toast(0,'Bạn chưa chọn hàng hóa!');
                return false;
            }

            sToast.confirm('Xác nhận trả lại hàng chuyển kho nội bộ?','',
                (confirmed) => {
                    if (confirmed.isConfirmed) {
                        sToast.loading('Đang xử lý. Vui lòng chờ...');
                        $.ajax({
                            url: '/api/quan-ly/chuyenkho-noibo/nhap-kho/huy-phieu',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                chitiets: JSON.stringify(chitiets)
                            }
                        }).done((result) => {
                            if (result.succ) {
                                setThongTin();
                                tblHangHoa.clearData();
                            }
                        });
                    }
                })
        });
    }

    function initTblDanhSachPhieu() {
        $('#modalDanhSachPhieu .danhsachphieu-title').text('Nhập Kho Nội Bộ');

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
                {title: 'Cửa hàng chuyển', field: 'doituong', headerSort: false},
                {title: 'Phiếu xuất kho', field: 'phieuxuat', headerSort: false, vertAlign: 'middle',
                    formatter: (cell) => {
                        return '<span class="text-primary">' + cell.getValue() + '</span>';
                    }, cellClick: (e, cell) => {
                        mPhieu('/quan-ly/xem-phieu/' + cell.getValue()).xemphieu(cell.getTable());
                    }},
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

        initSearchTable(tblDanhSachPhieu,['phieuxuat','maphieu','doituong']);

        $('#modalDanhSachPhieu').on('shown.bs.modal', function() {
            $(this).find('button.btnXem').click();
        })

        $('#modalDanhSachPhieu button.btnXem').click(() => {
            tblDanhSachPhieu.setData('/api/quan-ly/phieu/danh-sach', {
                loaiphieu: 'NKNB',
                begin: getDateRangePicker($('#fromToDate')),
                end: getDateRangePicker($('#fromToDate'),false)
            })
        })
    }
</script>
