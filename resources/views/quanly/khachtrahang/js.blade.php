<script>
    let tblHangHoa = null;
    let tblLichSu;
    let tblDanhSachPhieu;
    let caytrongs = JSON.parse('{!! str_replace("'","\'",json_encode($caytrongs)) !!}');
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
    init();
    initSelKhachHang();
    initTblHangHoa();
    initTblLichSu();
    initActionTraHang();
    initTblDanhSachPhieu();

    function init() {
        $('#fromToDate').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true
        });

        autosize($('#inpGhiChu'));

        @if(in_array('danh-muc.khach-hang.chinh-sua',$info->phanquyen) === false)
        $('#modalXemKH .col-thongtin i').remove();
        @endif

        $('#btnXemPhieu').click(() => {
            sToast.toast(0,'Bạn chưa chọn hàng hóa!');
        })
    }

    function initSelKhachHang() {
        $('#selKhachHang').select2({
            ajax: {
                url: '/api/quan-ly/danh-muc/khach-hang/tim-kiem',
                data: function (params) {
                    let query = {
                        q: params.term
                    };

                    // Query parameters will be ?search=[term]&type=public
                    return query;
                },
                delay: 250
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
            allowClear: true,
            placeholder: 'KH000000 - Khách Hàng Lẻ'
        }).change(() => {
            let khachhang = $('#selKhachHang').select2('data');
            khachhang = khachhang.length > 0 ? khachhang[0] :
                { ma: 'KH000000', ten: 'Khách Hàng Lẻ', dienthoai: '', dienthoai2: '', diachi: '', congno: 0 };
            setThongTin(khachhang);
            $('#lblMaPhieu').text('-----').off('click');
            if (!isNull(tblHangHoa)) {
                tblHangHoa.clearData();
            }
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
                {title: "Số lượng", field: "soluong", headerSort: false, hozAlign: 'right', vertAlign: 'middle',
                    formatter: (cell) => {
                        return '<span class="text-info">' + cell.getValue() + '</span>';
                    }, cellClick: (e, cell) => {
                        let data = cell.getData();
                        mInput(data.ten,parseFloat(data.soluong)).number('Nhập số lượng mới','Nhập số lượng mới...',
                            () => {
                                let value = parseFloat($('#modalInput .value').val());
                                if (value <= 0 || isNaN(value)) {
                                    $('#modalInput .value').addClass('is-invalid');
                                    return false;
                                }
                                if (value > data._soluong) {
                                    sToast.toast(0,'Số lượng trả hàng đã vượt quá số lượng mua!');
                                    return false;
                                }
                                let giamgia = data._giamgia * value;
                                cell.getTable().updateData([{
                                    id: data.id,
                                    soluong: value,
                                    giamgia,
                                    thanhtien: value * (data.dongia - giamgia)
                                }]);
                                $('#modalInput').modal('hide');
                                actionTinhTien();
                            }, 'Số lượng không hợp lệ!');
                    }},
                {title: "Đơn giá", field: "dongia", hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                    formatter: (cell) => {
                        return '<span class="text-danger">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }},
                {title: "Giảm giá", field: "giamgia", hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                    formatter: (cell) => {
                        return '<span class="text-danger">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }},
                {title: "Thành tiền", field: "thanhtien", hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                    formatter: (cell) => {
                        return '<span class="text-tienthanhtoan font-weight-bolder">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }},
            ],
            height: '100%',
            dataChanged: () => {
                tblHangHoa.getColumns()[0].updateDefinition();
                actionTinhTien();
                if (tblHangHoa.getData().length === 0) {
                    $('#lblMaPhieu').text('-----').off('click');
                }
            }
        });
    }

    function initTblLichSu() {
        tblLichSu = new Tabulator("#tblLichSu", {
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
                {title: 'Số lượng', field: 'soluong', headerSort: false, vertAlign: 'middle', hozAlign: 'right'},
                {title: 'Đơn giá', field: 'dongia', headerSort: false, vertAlign: 'middle', hozAlign: 'right',
                    formatter: (cell) => {
                        return numeral(cell.getValue()).format('0,0');
                    }},
                {title: 'Giảm giá', field: 'giamgia', headerSort: false, vertAlign: 'middle', hozAlign: 'right',
                    formatter: (cell) => {
                        return numeral(cell.getValue()).format('0,0');
                    }},
                {title: 'Thành tiền', field: 'thanhtien', headerSort: false, vertAlign: 'middle', hozAlign: 'right',
                    formatter: (cell) => {
                        return numeral(cell.getValue()).format('0,0');
                    }},
                {title: 'SL trả hàng', field: 'soluong_trahang', headerSort: false, vertAlign: 'middle', hozAlign: 'right'},
            ],
            groupBy: (data) => {
                return '<span class="text-primary ml-0 maphieu">' + data.maphieu + '</span> | ' +
                    doi_ngay(data.created_at) + ' | NV bán hàng: ' + data.nhanvien +
                    ' | Tiền thanh toán: <span class="text-danger ml-0">' + numeral(data.tienthanhtoan).format('0,0') + '</span>';
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
                    $('#btnTraHang').attr('disabled',null);
                }
                else {
                    $('#btnTraHang').attr('disabled','');
                }
            },
            rowContextMenu: [
                {
                    label: '<i class="fa fa-check"></i> Chọn hàng',
                    action: () => {
                        $('#btnTraHang').click();
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
                        $('#btnTraHang').click();
                    }
                }
            ],
            height: '465px',
            pagination: 'local',
            paginationSize: 10,
            dataFiltered: function () {
                if (isNull(tblLichSu) || isUndefined(tblLichSu)) {
                    return false;
                }
                setTimeout(() => {tblLichSu.getColumns()[0].updateDefinition()},10);
            }
        });

        initSearchTable(tblLichSu,['maphieu','hanghoa_ten','nhanvien']);

        $('#modalLichSu').on('shown.bs.modal', function() {
            tblLichSu.setData('/api/quan-ly/khach-tra-hang/lichsu-muahang', {
                khachhang_id: isNull($('#selKhachHang').val()) ? '1000000000' : $('#selKhachHang').val()
            });
        }).on('hidden.bs.modal', () => {
            tblLichSu.clearData();
            $('#btnTraHang').attr('disabled','').html('<i class="fas fa-check mr-1"></i> Trả Hàng');
        })
    }

    function initActionTraHang() {
        $('#btnTraHang').click(() => {
            let data = tblLichSu.getSelectedData();
            if (data.length === 0) {
                sToast.toast(0,'Bạn chưa chọn hàng hóa cần trả hàng!');
                return false;
            }
            let tongsoluong = 0;
            tblLichSu.getData().forEach((value) => {
                if (value.maphieu === data[0].maphieu) {
                    tongsoluong += parseFloat(value.soluong);
                }
            });

            let tblData = [];
            let checked = true;
            data.forEach((value) => {
                let soluong = parseFloat(value.soluong) - parseFloat(value.soluong_trahang);
                if (soluong === 0) {
                    checked = false;
                    return;
                }
                value._giamgia = value.giamgia / parseFloat(value.soluong);
                value._soluong = soluong;
                value.soluong = value._soluong;
                value.giamgia = value.soluong * value._giamgia;
                value.thanhtien = value.soluong * (value.dongia - value.giamgia);
                value.__giamgia = value.__giamgia / tongsoluong;
                value.__phuthu = value.__phuthu / tongsoluong;
                tblData.push(value);
            })

            tblHangHoa.setData(tblData).then(() => { actionTinhTien() });
            $('#lblMaPhieu').text(data[0].maphieu).off('click').click(() => {
                mPhieu('/quan-ly/xem-phieu/' + data[0].maphieu).xemphieu();
            })
            $('#modalLichSu').modal('hide');
        })
    }

    function initTblDanhSachPhieu() {
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
                {title: 'Khách hàng', field: 'doituong', headerSort: false},
                {title: 'Giảm giá', field: 'giamgia',
                    hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                    formatter: function(cell) {
                        return '<span class="text-giamgia">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }, bottomCalc: (values, data) => {
                        let tong = 0;
                        data.forEach((value) => {
                            if (isNull(value.deleted_at)) {
                                tong += value.giamgia;
                            }
                        })
                        return numeral(tong).format('0,0');
                    }, bottomCalcFormatter: (cell) => {
                        return '<span class="text-giamgia">' + cell.getValue() + '</span>';
                    }},
                {title: 'Phụ thu', field: 'phuthu',
                    hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                    formatter: function(cell) {
                        return '<span class="text-secondary">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }, bottomCalc: (values, data) => {
                        let tong = 0;
                        data.forEach((value) => {
                            if (isNull(value.deleted_at)) {
                                tong += value.phuthu;
                            }
                        })
                        return numeral(tong).format('0,0');
                    }, bottomCalcFormatter: (cell) => {
                        return '<span class="text-secondary">' + cell.getValue() + '</span>';
                    }},
                {title: 'Tổng thành tiền', field: 'tongthanhtien', headerHozAlign: 'right',
                    hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                    formatter: function(cell) {
                        return numeral(cell.getValue()).format('0,0');
                    }, bottomCalc: (values, data) => {
                        let tong = 0;
                        data.forEach((value) => {
                            if (isNull(value.deleted_at)) {
                                tong += value.tongthanhtien;
                            }
                        });
                        return numeral(tong).format('0,0');
                    }},
                {title: 'Tiền thanh toán', field: 'tienthanhtoan', headerHozAlign: 'right',
                    hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                    formatter: function(cell) {
                        return '<span class="text-tienthanhtoan">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }, bottomCalc: (values, data) => {
                        let tong = 0;
                        data.forEach((value) => {
                            if (isNull(value.deleted_at)) {
                                tong += value.tienthanhtoan;
                            }
                        });
                        return numeral(tong).format('0,0');
                    }, bottomCalcFormatter: (cell) => {
                        return '<span class="text-tienthanhtoan">' + cell.getValue() + '</span>';
                    }},
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

        initSearchTable(tblDanhSachPhieu,['maphieu','doituong','nhanvien']);

        $('#modalDanhSachPhieu').on('shown.bs.modal', function() {
            $(this).find('button.btnXem').click();
        })

        $('#modalDanhSachPhieu button.btnXem').click(() => {
            tblDanhSachPhieu.setData('/api/quan-ly/phieu/danh-sach', {
                loaiphieu: 'KTH',
                begin: getDateRangePicker($('#fromToDate')),
                end: getDateRangePicker($('#fromToDate'),false)
            })
        })
    }

    function setThongTin(khachhang) {
        if (khachhang.ma === 'KH000000') {
            $('#boxThongTin .btnThongTin').attr('disabled','').off('click');
        }
        else {
            $('#boxThongTin .btnThongTin').attr('disabled',null).off('click').click(() => {
                clickXemThongTinKH(khachhang);
            });
        }
        $('#boxThongTin .ma').text(khachhang.ma);
        $('#boxThongTin .ten').text(khachhang.ten);
        $('#boxThongTin .dienthoai').text(khachhang.dienthoai);
        $('#boxThongTin .dienthoai2').text(khachhang.dienthoai2);
        $('#boxThongTin .congno').text(numeral(khachhang.congno).format('0,0'));
        $('#boxThongTin .diachi').text(khachhang.diachi);
    }

    function actionTinhTien() {
        let tongthanhtien = 0;
        let phuthu = 0;
        let giamgia = 0;
        let dshanghoa = [];
        tblHangHoa.getData().forEach((value) => {
            tongthanhtien += value.thanhtien;
            phuthu += value.__phuthu * value.soluong;
            giamgia += value.__giamgia * value.soluong;
            dshanghoa.push({
                id: value.id,
                hanghoa: {
                    id: value.hanghoa.id,
                    ma: value.hanghoa.ma,
                    ten: value.hanghoa.ten,
                    donvitinh: value.hanghoa.donvitinh
                },
                dongia: value.dongia,
                giamgia: value.giamgia,
                soluong: value.soluong,
                thanhtien: value.thanhtien
            })
        });
        $('#lblPhuThu').text(numeral(phuthu).format('0,0'));
        $('#lblTongThanhTien').text(numeral(tongthanhtien).format('0,0'));
        let tienthanhtoan = tongthanhtien + phuthu - giamgia;
        giamgia += tienthanhtoan % 1000;
        tienthanhtoan = tongthanhtien + phuthu - giamgia;
        $('#lblGiamGia').text(numeral(giamgia).format('0,0'));
        $('#lblTienThanhToan').text(numeral(tienthanhtoan).format('0,0'));

        $('#btnXemPhieu').off('click').click(() => {
            if (dshanghoa.length === 0) {
                sToast.toast(0,'Bạn chưa chọn hàng hóa!');
                return false;
            }
            let doituong = $('#selKhachHang').select2('data');
            doituong = doituong.length > 0 ? doituong[0] : {
                id: '1000000000',
                ma: 'KH000000',
                ten: 'Khách Hàng Lẻ',
                dienthoai: '',
                diachi: ''
            };

            sToast.loading('Đang lấy thông tin phiếu. Vui lòng chờ...');

            let data = {
                dshanghoa, doituong, phuthu, giamgia, tienthanhtoan, tongthanhtien,
                ghichu: $('#inpGhiChu').val().trim()
            }
            $.ajax({
                url: '/api/quan-ly/phieu/tao-phieu/KTH',
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
                            url: '/api/quan-ly/khach-tra-hang/luu-phieu',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                phieu: JSON.stringify(data)
                            }
                        }).done((result) => {
                            if (result.succ) {
                                mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu).xemphieu();
                                $('#selKhachHang').val(null).trigger('change');
                                $('#inpGhiChu').val('');
                                autosize.update($('#inpGhiChu'));
                            }
                        });
                    })
                }
            });
        });
    }

    function clickXemThongTinKH(data) {
        for(let col of $('#modalXemKH .col-thongtin')) {
            let field = $(col).attr('data-field');
            let ten = $(col).find('strong').text().trim();
            let value = data[field];
            let _value = value;
            if (field === 'congno') {
                _value = numeral(value).format(0,0);
            }
            if (field === 'lancuoi_muahang') {
                _value = doi_ngay(value);
            }
            $(col).find('span').text(_value);
            @if(in_array('danh-muc.khach-hang.chinh-sua',$info->phanquyen) !== false)
            let edit = $(col).find('i.edit');
            if (edit.length > 0) {
                edit.off('click').click(() => {
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
                        sToast.confirm('Xác nhận cập nhật ' + ten.toLowerCase() + '?','',
                            (result) => {
                                if (result.isConfirmed) {
                                    sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                                    $.ajax({
                                        url: '/api/quan-ly/danh-muc/khach-hang/cap-nhat',
                                        type: 'get',
                                        dataType: 'json',
                                        data: {
                                            id: data.id,
                                            field, value
                                        }
                                    }).done((result) => {
                                        if (result.succ) {
                                            $('#modalInput').modal('hide');
                                            data = result.data.model;
                                            if (field === 'dienthoai' || field === 'ten') {
                                                $('#boxKhachHang .select2-selection__rendered').text(data.dienthoai + ' - ' + data.ten);
                                            }
                                            clickXemThongTinKH(result.data.model);
                                            setThongTin(data);
                                        }
                                        else {
                                            if (!isUndefined(result.erro)) {
                                                $('#modalInput span.error').text(result.erro);
                                                $('#modalInput .value').addClass('is-invalid');
                                                $('#modalInput .value').focus();
                                            }
                                        }
                                    });
                                }
                            });
                    }
                    if (['ten','dienthoai','dienthoai2','dientich'].indexOf(field) !== -1) {
                        mInput(data.ten,value).text(ten,'Nhập ' + ten.toLowerCase() + '...',onSubmit);
                    }
                    if (field === 'ghichu') {
                        mInput(data.ten,value).textarea(ten,'Nhập ' + ten.toLowerCase() + '...',onSubmit);
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
                })
            }
            @endif
        }

        $('#modalXemKH').modal('show');
    }
</script>
