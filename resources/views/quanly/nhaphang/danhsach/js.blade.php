<script>
    let tblHangHoa;
    let tblDanhSachPhieuNhap;
    let tblDanhSachPhieu;
    init();
    initTblHangHoa();
    initActionChonPhieu();
    // initActionXemPhieu();
    initTblDanhSachPhieuNhap();
    initTblDanhSachPhieu();

    function init() {
        $('#fromToDate').daterangepicker({
            startDate: '{{ date('d-m-Y',strtotime('-1 months',time())) }}',
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true
        });
        $('#inpPhuThu, #inpGiamGia').on('input', function() {
            actionTinhTien();
        })
        autosize($('#inpGhiChu'));
    }

    function initTblHangHoa() {
        tblHangHoa = new Tabulator("#tblHangHoa", {
            columns: [
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
                {title: "Q.cách", field: "quycach", hozAlign: 'right', headerSort: false, vertAlign: 'middle', formatter: (cell) => {
                        return cell.getData().hanghoa.quycach
                    }},
                {title: "Đơn giá", field: "dongia", hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                    formatter: (cell) => {
                        return numeral(cell.getValue()).format('0,0');
                    }, cellClick: (e, cell) => {
                        let data = cell.getData();
                        mInput(data.ten,data.dongia).numeral('Nhập đơn giá mới','Nhập đơn giá mới...',
                            () => {
                                let value = parseFloat($('#modalInput .value').attr('data-value'));
                                if (isNaN(value) || value <= 0) {
                                    $('#modalInput .value').addClass('is-invalid');
                                    return false;
                                }
                                cell.getTable().updateData([{
                                    id: data.id,
                                    dongia: value,
                                    thanhtien: data.soluong * value
                                }]);
                                $('#modalInput').modal('hide');
                                actionTinhTien();
                            }, 'Đơn giá không hợp lệ!');
                    }},
                {title: "Số lượng", field: "soluong", headerSort: false, hozAlign: 'right', vertAlign: 'middle',
                    formatter: (cell) => {
                        return '<span class="text-info">' + cell.getValue() + '</span>';
                    }},
                {title: "Thành tiền", field: "thanhtien", hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                    formatter: (cell) => {
                        return numeral(cell.getValue()).format('0,0');
                    }},
                {title: "Hạn sử dụng", field: "hansudung", headerSort: false, vertAlign: 'middle',
                    formatter: (cell) => {
                        return doi_ngay(cell.getValue());
                    }},
            ],
            height: '100%',
            movableColumns: false,
            pagination: 'local',
            paginationSize: 10,
            dataFiltered: function () {
                if (isNull(tblHangHoa) || isUndefined(tblHangHoa)) {
                    return false;
                }
                setTimeout(() => {tblHangHoa.getColumns()[0].updateDefinition()},10);
                actionTinhTien();
            }
        });
    }

    // function initActionXemPhieu() {
    //     $('#btnXemPhieu').off('click').click(() => {
    //         let dshanghoa = [];
    //         tblHangHoa.getData().forEach((value) => {
    //             dshanghoa.push({
    //                 hanghoa: {
    //                     id: value.hanghoa_id,
    //                     ma: value.ma,
    //                     ten: value.ten,
    //                     donvitinh: value.donvitinh,
    //                     quycach: value.quycach
    //                 },
    //                 soluong: value.soluong,
    //                 hansudung: value.hansudung
    //             })
    //         })
    //         if (dshanghoa.length === 0) {
    //             sToast.toast(0,'Bạn chưa chọn hàng hóa!');
    //             return false;
    //         }
    //         let doituong = $('#selNhaCungCap').select2('data');
    //         if (doituong.length === 0) {
    //             sToast.toast(0,'Bạn chưa chọn nhà cung cấp!');
    //             return false;
    //         }
    //         doituong = {
    //             id: doituong[0].id,
    //             ma: doituong[0].ma,
    //             ten: doituong[0].ten
    //         }
    //
    //         sToast.loading('Đang lấy thông tin phiếu. Vui lòng chờ...');
    //
    //         let data = {
    //             dshanghoa, doituong,
    //             ghichu: $('#inpGhiChu').val().trim()
    //         }
    //         $.ajax({
    //             url: '/api/quan-ly/phieu/tao-phieu/NH',
    //             type: 'get',
    //             dataType: 'json',
    //             data: {
    //                 phieu: JSON.stringify(data)
    //             }
    //         }).done((result) => {
    //             if (result.succ) {
    //                 mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu).taophieu(() => {
    //                     sToast.confirm('Xác nhận tạo phiếu nhập hàng?','',
    //                         (confirmed) => {
    //                             if (confirmed.isConfirmed) {
    //                                 sToast.loading('Đang tạo phiếu. Vui lòng chờ...');
    //                                 $.ajax({
    //                                     url: '/api/quan-ly/nhap-hang/luu-phieu',
    //                                     type: 'post',
    //                                     dataType: 'json',
    //                                     data: {
    //                                         phieu: JSON.stringify(data)
    //                                     }
    //                                 }).done((result) => {
    //                                     if (result.succ) {
    //                                         mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu + '?deletable=1').xemphieu();
    //                                         $('#selNhaCungCap').val(null).trigger('change');
    //                                         $('#inpGhiChu').val('').css('height','unset');
    //                                         tblHangHoa.clearData();
    //                                     }
    //                                 });
    //                             }
    //                         })
    //                 })
    //             }
    //         });
    //     })
    // }

    function initTblDanhSachPhieuNhap() {
        let cellClick = (e, cell) => {
            if (cell.getField() === 'maphieu') {
                return false;
            }
            let row = cell.getRow();
            if (row.isSelected()) {
                row.deselect();
            }
            else {
                row.getTable().getRows().forEach((row) => {
                    if (row.isSelected()) {
                        row.deselect();
                    }
                });
                row.select();
            }
        }

        tblDanhSachPhieuNhap = new Tabulator("#tblDanhSachPhieuNhap", {
            columns: [
                {title: 'STT', field: 'stt', headerHozAlign: 'center', formatter: 'rownum', width: 30,
                    hozAlign: 'center', headerSort: false, vertAlign: 'middle', cellClick},
                {title: 'Mã phiếu', field: 'maphieu', headerSort: false, vertAlign: 'middle',
                    formatter: (cell) => {
                        return '<span class="text-primary">' + cell.getValue() + '</span>';
                    }, cellClick: (e, cell) => {
                        mPhieu('/quan-ly/xem-phieu/' + cell.getValue()).xemphieu(cell.getTable());
                    }},
                {title: 'Cửa hàng', field: 'chinhanh', headerSort: false, cellClick, formatter: (cell) => {
                        if (!isNull(cell.getValue())) {
                            return cell.getValue().ten;
                        }
                    }},
                {title: 'Nhà cung cấp', field: 'doituong', headerSort: false, cellClick, formatter: (cell) => {
                        if (!isNull(cell.getValue())) {
                            return cell.getValue().ten;
                        }
                    }},
                {title: 'NV lập phiếu', field: 'nhanvien', headerSort: false, cellClick, formatter: (cell) => {
                        if (!isNull(cell.getValue())) {
                            return cell.getValue().ten;
                        }
                    }},
                {title: 'Thời gian', field: 'created_at', headerSort: false, width: 127, vertAlign: 'middle', cellClick,
                    formatter: function(cell) {
                        return doi_ngay(cell.getValue());
                    }}
            ],
            rowContextMenu: [
                {
                    label: '<i class="fa fa-check"></i> Chọn phiếu',
                    action: (e, row) => {
                        actionChonPhieu(row.getData());
                    }
                }
            ],
            height: '465px',
            pagination: 'local',
            paginationSize: 10,
            dataFiltered: function () {
                if (isNull(tblDanhSachPhieuNhap) || isUndefined(tblDanhSachPhieuNhap)) {
                    return false;
                }
                setTimeout(() => {tblDanhSachPhieuNhap.getColumns()[0].updateDefinition()},10);
            }
        });

        initSearchTable(tblDanhSachPhieuNhap,['maphieu','doituong']);

        $('#modalDanhSachPhieuNhap').on('shown.bs.modal', function() {
            tblDanhSachPhieuNhap.setData('/api/quan-ly/nhap-hang/danh-sach');
        })
    }

    function initActionChonPhieu() {
        $('#btnChonPhieu').click(() => {
            let row = tblDanhSachPhieuNhap.getSelectedRows();
            if (row.length === 0) {
                sToast.toast(0,'Bạn chưa chọn phiếu nhập hàng!');
                return false;
            }
            let phieu = row[0].getData();
            actionChonPhieu(phieu);
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

    function actionChonPhieu(phieu) {
        setThongTin(phieu);
        $("#btnHuyPhieu").removeClass('d-none').off('click').click(() => {
            sToast.confirm('Xác nhận hủy phiếu nhập hàng?','',
                (confirmed) => {
                    if (confirmed.isConfirmed) {
                        sToast.loading('Đang hủy phiếu. Vui lòng chờ...');
                        $.ajax({
                            url: '/api/quan-ly/nhap-hang/huy-phieu',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                maphieu: phieu.maphieu
                            }
                        }).done((result) => {
                            if (result.succ) {
                                setThongTin();
                            }
                        });
                    }
                })
        })
        $('#btnXemPhieu').off('click').click(() => {
            let tongthanhtien = 0;
            let phuthu = parseFloat($('#inpPhuThu').attr('data-value'));
            phuthu = isNaN(phuthu) ? 0 : phuthu;
            let giamgia = parseFloat($('#inpGiamGia').attr('data-value'));
            giamgia = isNaN(giamgia) ? 0 : giamgia;
            let dshanghoa = [];
            tblHangHoa.getData().forEach((value) => {
                tongthanhtien += value.thanhtien;
                dshanghoa.push({
                    id: value.id,
                    hanghoa: value.hanghoa,
                    soluong: value.soluong,
                    hansudung: value.hansudung,
                    dongia: value.dongia,
                    thanhtien: value.thanhtien
                })
            });
            if (dshanghoa.length === 0) {
                sToast.toast(0,'Bạn chưa chọn phiếu nhập hàng!');
                return false;
            }
            let tienthanhtoan = tongthanhtien + phuthu - giamgia;
            phieu.phuthu = phuthu;
            phieu.giamgia = giamgia;
            phieu.tienthanhtoan = tienthanhtoan;
            phieu.tongthanhtien = tongthanhtien;
            phieu.dshanghoa = dshanghoa;

            sToast.loading('Đang lấy thông tin phiếu. Vui lòng chờ...');
            $.ajax({
                url: '/api/quan-ly/phieu/tao-phieu/NH',
                type: 'post',
                dataType: 'json',
                data: {
                    phieu: JSON.stringify(phieu)
                }
            }).done((result) => {
                if (result.succ) {
                    mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu).taophieu(() => {
                        sToast.confirm('Xác nhận duyệt phiếu nhập hàng?','',
                            (confirmed) => {
                                if (confirmed.isConfirmed) {
                                    sToast.loading('Đang duyệt phiếu. Vui lòng chờ...');
                                    $.ajax({
                                        url: '/api/quan-ly/nhap-hang/duyet-phieu',
                                        type: 'post',
                                        dataType: 'json',
                                        data: {
                                            phieu: JSON.stringify(phieu)
                                        }
                                    }).done((result) => {
                                        if (result.succ) {
                                            mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu + '?deletable=1').xemphieu();
                                            setThongTin();
                                            initSoPhieuNhap();
                                        }
                                    });
                                }
                            })
                    },false)
                }
            });
        })

        $('#modalDanhSachPhieuNhap').modal('hide');
    }

    function setThongTin(phieu = null) {
        if (isNull(phieu)) {
            phieu = {
                maphieu: '-----',
                chinhanh: { ten: '---' },
                doituong: { ma: '', ten: '', dienthoai: '', dienthoai2: '', congno: '', diachi: '' },
                ghichu: '',
                chitiets: []
            }
            $('#btnHuyPhieu').off('click').addClass('d-none');
            $('#lblMaPhieu').off('click');
        }
        else {
            $('#lblMaPhieu').off('click').click(() => {
                mPhieu('/quan-ly/xem-phieu/' + phieu.maphieu).xemphieu();
            })
        }
        $('#lblMaPhieu').text(phieu.maphieu);
        $('#lblChiNhanh').text(phieu.chinhanh.ten);
        $('#boxThongTin .ten').text(phieu.doituong.ten);
        $('#boxThongTin .ma').text(phieu.doituong.ma);
        $('#boxThongTin .dienthoai').text(phieu.doituong.dienthoai);
        $('#boxThongTin .dienthoai2').text(phieu.doituong.dienthoai2);
        if (phieu.doituong.congno !== '') {
            $('#boxThongTin .congno').text(numeral(phieu.doituong.congno).format('0,0'));
        }
        $('#boxThongTin .diachi').text(phieu.doituong.diachi);
        $('#inpGhiChu').val(phieu.ghichu);
        autosize.update($('#inpGhiChu'));
        tblHangHoa.setData(phieu.chitiets);
    }

    function actionTinhTien() {
        let tongthanhtien = 0;
        let phuthu = parseFloat($('#inpPhuThu').attr('data-value'));
        phuthu = isNaN(phuthu) ? 0 : phuthu;
        let giamgia = parseFloat($('#inpGiamGia').attr('data-value'));
        giamgia = isNaN(giamgia) ? 0 : giamgia;
        tblHangHoa.getData().forEach((value) => {
            tongthanhtien += value.thanhtien;
        });
        $('#lblTongThanhTien').text(numeral(tongthanhtien).format('0,0'));
        let tienthanhtoan = tongthanhtien + phuthu - giamgia;
        $('#lblTienThanhToan').text(numeral(tienthanhtoan).format('0,0'));
    }
</script>
