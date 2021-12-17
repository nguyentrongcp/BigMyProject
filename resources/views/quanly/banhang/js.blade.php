<script>
    let tblHangHoa;
    let tblDanhSachPhieu;
    let tblLichSu;
    let nhanviens = JSON.parse('{!! str_replace("'","\'",json_encode($nhanviens)) !!}');
    let views = localStorage.getItem('banhang.views');
    views = isNull(views) ? {} : JSON.parse(views);
    init();
    initSelHangHoa();
    initActionThemHangHoa();
    initSelKhachHang();
    initSelNhanVien();
    initTblHangHoa();
    initTblDanhSachPhieu();
    initTblLichSu();

    function init() {
        channel.bind('reload-danhsach-hanghoa', function() {
            initSelHangHoa();
        });
        $('#selHangHoa').change(() => {
            if ($('#selHangHoa').val() != null) {
                setTimeout(() =>
                {
                    $('#boxHangHoa .soluong').focus()
                },10)
            }
        }).on('select2:unselect',function(){
            $(this).html(null);
        });
        $('#fromToDate').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true
        });

        autosize($('#inpGhiChu'));

        $('#inpPhuThu, #inpGiamGia, #inpTienKhachDua').on('input', () => {
            actionTinhTien();
        })

        $('#boxHangHoa input').keypress((e) => {
            if (e.keyCode === 13) {
                $('#btnThemHangHoa').click();
            }
        })

        $('#btnXemPhieu').click(() => {
            sToast.toast(0,'Bạn chưa chọn hàng hóa!');
        })

        $('#modalXemHH .col-thongtin i').remove();

        @if(in_array('danh-muc.khach-hang.chinh-sua',$info->phanquyen) === false)
        $('#modalXemKH .col-thongtin i').remove();
        @endif

        @if(in_array('danh-muc.khach-hang.thu-cong-no',$info->phanquyen) === false)
        $('#modalThuCongNo').on('shown.bs.modal', function() {
            $(this).find('.inpSoTien').focus();
        }).on('hidden.bs.modal', function() {
            $(this).find('input.is-invalid').removeClass('is-invalid');
            $(this).find('.inpSoTien').val('').trigger('input');
            $(this).find('.inpGhiChu').val('');
            if ($('.modal.show').length > 0) {
                $('body').addClass('modal-open');
            }
        })
        $('#modalThuCongNo input, #modalThuCongNo textarea').keypress(function(e) {
            let keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                $('#modalThuCongNo button.btnSubmit').click();
                e.preventDefault();
                return false;
            }
        }).on('input', function() {
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
            }
        });
        @endif
    }

    function initSelHangHoa() {
        $('#selHangHoa').html(null).select2({
            ajax: {
                url: '/api/quan-ly/ban-hang/tim-kiem',
                data: function (params) {
                    let query = {
                        q: params.term
                    };

                    // Query parameters will be ?search=[term]&type=public
                    return query;
                },
                delay: 300
            },
            dropdownCssClass : 'select2-hanghoa',
            templateResult: (value) => {
                if (!isUndefined(value.ma)) {
                    return $('' +
                        '<div>' + value.ma + ' - ' + value.ten + '</div>' +
                        '<div class="form-row">' +
                        '   <div class="col-6">' +
                        '       <div>Tồn kho: <strong class="float-right tonkho text-info">' + parseFloat(value.tonkho) + '</strong></div>' +
                        '   </div>' +
                        '   <div class="col-6">' +
                        '       <div>Giá bán: <strong class="float-right giaban text-danger">' + numeral(value.dongia).format('0,0') + '</strong></div>' +
                        '   </div>' +
                        '</div>');
                }
                else {
                    return value.text;
                }
            },
            templateSelection: (value) => {
                if (!isUndefined(value.ma)) {
                    return value.ma + ' - ' + value.ten;
                }
                else {
                    return value.text;
                }
            },
            allowClear: true,
            placeholder: 'Chọn hàng hóa...'
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
            let giamgia = parseFloat($('#boxHangHoa .giamgia').attr('data-value'));
            if (isNaN(soluong) || soluong <= 0) {
                soluong = 1;
            }
            giamgia = isNaN(giamgia) ? 0 : giamgia;

            let dataTable = {
                id: renderID(),
                hanghoa_id: hanghoa.id,
                ma: hanghoa.ma,
                ten: hanghoa.ten,
                donvitinh: hanghoa.donvitinh,
                quycach: hanghoa.quycach,
                nhom: hanghoa.nhom,
                hoatchat: hanghoa.hoatchat,
                dang: hanghoa.dang,
                congdung: hanghoa.congdung,
                dongia: hanghoa.dongia,
                giamgia, soluong,
                thanhtien: soluong * (parseFloat(hanghoa.dongia) - giamgia),
                hinhanh: hanghoa.hinhanh,
                lieuluong: hanghoa.lieuluong
            }
            tblHangHoa.addData(dataTable,true).then(() => {
                tblHangHoa.getColumns()[0].updateDefinition()
            });

            $('#boxHangHoa input').val('').trigger('input');
            $('#selHangHoa').val(null).trigger('change').focus().select2('open');
        })
    }

    function initSelKhachHang() {
        $('#selKhachHang').html(null).select2({
            ajax: {
                url: '/api/quan-ly/danh-muc/khach-hang/tim-kiem',
                data: function (params) {
                    let query = {
                        q: params.term
                    };

                    // Query parameters will be ?search=[term]&type=public
                    return query;
                },
                delay: 300
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
        });

        $('#selKhachHang').change(() => {
            let khachhang = $('#selKhachHang').select2('data');
            khachhang = khachhang.length > 0 ? khachhang[0] :
                { ma: 'KH000000', ten: 'Khách Hàng Lẻ', dienthoai: '', diachi: '', congno: 0 };
            if (!$('#boxKhachHang .btnThuCongNo').hasClass('d-none')) {
                $('#boxKhachHang .btnThuCongNo').addClass('d-none').off('click');
            }
            setThongTinKH(khachhang);
        }).val(null).trigger('change').on('select2:unselect',function(){
            $('#selKhachHang').html(null).trigger('change');
        });
    }

    function initSelNhanVien() {
        initSelect2($('#selNhanVienTuVan'),nhanviens,{
            allowClear: true,
            placeholder: info.dienthoai + ' - ' + info.ten,
            defaultText: ['dienthoai','ten']
        });
    }

    function initTblHangHoa() {
        let contextMenu = (cell) => {
            let fields = {
                ma: 'Mã',
                donvitinh: 'Đơn vị tính',
                quycach: 'Quy cách',
                nhom: 'Nhóm'
            };
            let subMenus = [];
            $.each(fields, function(key, value) {
                let col = cell.getTable().getColumn(key);
                let visible = col.isVisible();
                subMenus.push({
                    label: '<i class="fa '
                        + (visible ? 'fa-check-square-o' : 'fa-square-o')
                        + '"></i> ' + value,
                    action: () => {
                        if (visible) {
                            col.hide();
                            views[key] = false;
                        }
                        else {
                            col.show();
                            views[key] = true;
                        }
                        localStorage.setItem('banhang.views', JSON.stringify(views))
                    }
                })
            })
            let menus = [
                {
                    label: '<i class="fa fa-info-circle text-info"></i> Thông tin',
                    action: () => {
                        clickXemThongTinHH(cell.getData());
                    }
                },
                {
                    label: '<i class="fa fa-search text-warning"></i> Tồn kho',
                    action: () => {
                        initTonKhoGiaBan(cell.getData().ma);
                    }
                },
                {
                    label: '<i class="fa fa-trash-alt text-danger"></i> Xóa',
                    action: () => {
                        cell.getRow().delete()
                    }
                },
                {
                    label: '<i class="fa fa-eye"></i> Hiển thị',
                    menu: subMenus
                }
            ];
            if (!isNull(cell.getData().hinhanh)) {
                menus.unshift({
                    label: '<i class="fa fa-image text-dark"></i> Hình ảnh',
                    action: () => {
                        showViewerUrl(JSON.parse(cell.getData().hinhanh).url);
                    }
                })
            }

            return menus;
        }

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
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", contextMenu,
                    formatter: "rownum", width: 40, headerSort: false, hozAlign: 'center'},
                {title: "Mã", field: "ma", headerSort: false, vertAlign: 'middle', contextMenu,
                    visible: isNull(views) ? true : views.ma},
                {title: "Tên", field: "ten", headerSort: false, vertAlign: 'middle', contextMenu},
                {title: "ĐVT", field: "donvitinh", headerSort: false, vertAlign: 'middle', contextMenu,
                    visible: isNull(views) ? true : views.donvitinh},
                {title: "Quy cách", field: "quycach", headerSort: false, hozAlign: 'right', vertAlign: 'middle', contextMenu,
                    visible: isNull(views) ? true : views.quycach},
                {title: "Nhóm", field: "nhom", headerSort: false, vertAlign: 'middle', contextMenu,
                    visible: isNull(views) ? true : views.nhom},
                {title: "Đơn giá", field: "dongia", headerSort: false, hozAlign: 'right', vertAlign: 'middle', contextMenu,
                    formatter: (cell) => {
                        return '<span class="text-danger">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }, cellClick: (e, cell) => {
                        let data = cell.getData();
                        mInput(data.ten,data.dongia).numeral('Nhập giá mới','Nhập giá mới...',
                            () => {
                                let value = parseFloat($('#modalInput .value').attr('data-value'));
                                if (isNaN(value) || value <= 0) {
                                    $('#modalInput .value').addClass('is-invalid');
                                    return false;
                                }
                                cell.getTable().updateData([{
                                    id: data.id,
                                    dongia: value,
                                    thanhtien: data.soluong * (value - data.giamgia)
                                }]);
                                $('#modalInput').modal('hide');
                                actionTinhTien();
                            }, 'Đơn giá không hợp lệ!');
                    }},
                {title: "Giảm giá", field: "giamgia", headerSort: false, hozAlign: 'right', vertAlign: 'middle', contextMenu,
                    formatter: (cell) => {
                        return '<span class="text-giamgia">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }, cellClick: (e, cell) => {
                        let data = cell.getData();
                        mInput(data.ten,data.giamgia).numeral('Nhập giảm giá mới','Nhập giảm giá mới...',
                            () => {
                                let value = parseFloat($('#modalInput .value').attr('data-value'));
                                if (isNaN(value) || value < 0) {
                                    $('#modalInput .value').addClass('is-invalid');
                                    return false;
                                }
                                cell.getTable().updateData([{
                                    id: data.id,
                                    giamgia: value,
                                    thanhtien: data.soluong * (data.dongia - value)
                                }]);
                                $('#modalInput').modal('hide');
                            }, 'Giảm giá không hợp lệ!');
                    }},
                {title: "Số lượng", field: "soluong", headerSort: false, hozAlign: 'right', vertAlign: 'middle', contextMenu,
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
                                    soluong: value,
                                    thanhtien: value * (data.dongia - data.giamgia)
                                }]);
                                $('#modalInput').modal('hide');
                            }, 'Số lượng không hợp lệ!');
                    }},
                {title: "Thành tiền", field: "thanhtien", headerSort: false, hozAlign: 'right', vertAlign: 'middle', contextMenu,
                    formatter: (cell) => {
                        return '<span class="text-tienthanhtoan font-weight-bolder">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }},
            ],
            height: '100%',
            movableColumns: false,
            dataChanged: () => {
                tblHangHoa.getColumns()[0].updateDefinition();
                actionTinhTien();
            }
        });
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
                loaiphieu: 'BH',
                begin: getDateRangePicker($('#fromToDate')),
                end: getDateRangePicker($('#fromToDate'),false)
            })
        })
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
        })
    }

    @if(in_array('danh-muc.khach-hang.thu-cong-no',$info->phanquyen) !== false)
    function actionThuCongNo(khachhang) {
        let modal = $('#modalThuCongNo');
        modal.find('.inpMa').val(khachhang.ma);
        modal.find('.inpTen').val(khachhang.ten);
        modal.find('.inpDienThoai').val(khachhang.dienthoai);
        modal.find('.inpCongNo').val(numeral(khachhang.congno).format('0,0'));
        modal.find('.inpSoTien').val(numeral(khachhang.congno).format('0,0')).trigger('input');

        modal.find('.btnSubmit').off('click').click(() => {
            let sotien = parseFloat(modal.find('.inpSoTien').attr('data-value'));
            if (isNaN(sotien) || sotien <= 0) {
                modal.find('.inpSoTien').addClass('is-invalid');
                return false;
            }
            let ghichu = modal.find('.inpGhiChu').val().trim();

            let data = {
                doituong: {
                    id: khachhang.id,
                    ten: khachhang.ten,
                    dienthoai: khachhang.dienthoai,
                    diachi: khachhang.diachi
                },
                tienthanhtoan: sotien, ghichu
            }

            sToast.loading('Đang lấy thông tin phiếu. Vui lòng chờ...');
            $.ajax({
                url: '/api/quan-ly/phieu/tao-phieu/TCNKH',
                type: 'post',
                dataType: 'json',
                data: {
                    phieu: JSON.stringify(data)
                }
            }).done((result) => {
                if (result.succ) {
                    mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu).taophieu(() => {
                        sToast.confirm('Xác nhận tạo phiếu thu công nợ khách hàng?','',
                            (confirmed) => {
                                if (confirmed.isConfirmed) {
                                    sToast.loading('Đang tạo phiếu. Vui lòng chờ...');
                                    $.ajax({
                                        url: '/api/quan-ly/congno-khachhang/thu-congno',
                                        type: 'get',
                                        dataType: 'json',
                                        data: {
                                            phieu: JSON.stringify(data)
                                        }
                                    }).done((result) => {
                                        if (result.succ) {
                                            mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu).xemphieu();
                                            $('#modalThuCongNo .inpSoTien, #modalThuCongNo .inpGhiChu').val('').trigger('input');
                                            $('#modalThuCongNo').modal('hide');
                                            $('#selKhachHang').html(null).val(null).trigger('change');
                                        }
                                    });
                                }
                            })
                    })
                }
            });
        })

        modal.modal('show');
    }
    @endif

    function actionTinhTien() {
        let tongthanhtien = 0;
        let tienthanhtoan = 0;
        let phuthu = parseFloat($('#inpPhuThu').attr('data-value'));
        phuthu = isNaN(phuthu) ? 0 : phuthu;
        let giamgia = parseFloat($('#inpGiamGia').attr('data-value'));
        giamgia = isNaN(giamgia) ? 0 : giamgia;
        let dshanghoa = [];
        tblHangHoa.getData().forEach((value) => {
            tongthanhtien += value.thanhtien;
            dshanghoa.push({
                hanghoa: {
                    id: value.hanghoa_id,
                    ma: value.ma,
                    ten: value.ten,
                    donvitinh: value.donvitinh
                },
                dongia: value.dongia,
                giamgia: value.giamgia,
                soluong: value.soluong,
                thanhtien: value.thanhtien
            })
        })

        tienthanhtoan = tongthanhtien + phuthu - giamgia;
        let tienkhachdua = parseFloat($('#inpTienKhachDua').attr('data-value'));
        tienkhachdua = isNaN(tienkhachdua) ? 0 : tienkhachdua;
        let tienthua = tienkhachdua - tienthanhtoan;
        if (tienthua >= 0) {
            $('#boxTienThua .label').text('Tiền thừa:');
            $('#boxTienThua').attr('class','text-success');
        }
        else {
            $('#boxTienThua .label').text('Tiền còn nợ:');
            $('#boxTienThua').attr('class','text-danger');
        }
        $('#boxTienThua .value').text(numeral(Math.abs(tienthua)).format('0,0'));

        $('#lblTongThanhTien').text(numeral(tongthanhtien).format('0,0'));
        $('#lblTienThanhToan').text(numeral(tienthanhtoan).format('0,0'));

        $('#btnXemPhieu').off('click').click(() => {
            if (dshanghoa.length === 0) {
                sToast.toast(0,'Bạn chưa chọn hàng hóa!');
                return false;
            }
            if (phuthu < 0 || giamgia < 0) {
                sToast.toast(0,(phuthu < 0 ? 'Phụ thu ': 'Giảm giá ') + 'không hợp lệ!');
                return false;
            }
            let doituong_id = $('#selKhachHang').val();
            doituong_id = doituong_id == null ? '1000000000' : doituong_id;
            if(doituong_id === '1000000000' && tienthua < 0) {
                sToast.toast(0,'Khách hàng lẻ không được bán nợ!');
                return false;
            }

            let nhanvien_tuvan = $('#selNhanVienTuVan').select2('data');
            nhanvien_tuvan = nhanvien_tuvan.length > 0 ? nhanvien_tuvan[0] : {
                id: info.id,
                ten: info.ten,
                dienthoai: info.dienthoai
            };

            sToast.loading('Đang lấy thông tin phiếu. Vui lòng chờ...');

            let data = {
                dshanghoa, doituong_id, phuthu, giamgia, tienthanhtoan, tongthanhtien, tienkhachdua, tienthua, nhanvien_tuvan,
                ghichu: $('#inpGhiChu').val().trim()
            }
            $.ajax({
                url: '/api/quan-ly/phieu/tao-phieu/BH',
                type: 'post',
                dataType: 'json',
                data: {
                    phieu: JSON.stringify(data)
                }
            }).done((result) => {
                if (result.succ) {
                    let confirm = () => {
                        sToast.loading('Đang tạo phiếu. Vui lòng chờ...');
                        $.ajax({
                            url: '/api/quan-ly/ban-hang/luu-phieu',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                phieu: JSON.stringify(data)
                            }
                        }).done((result) => {
                            if (result.succ) {
                                mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu).xemphieu();
                                $('#selKhachHang').val(null).trigger('change');
                                $('#inpGiamGia, #inpPhuThu, #inpTienKhachDua').val('').trigger('input');
                                $('#inpGhiChu').val('');
                                $('#selNhanVienTuVan').val(null).trigger('change');
                                tblHangHoa.clearData();
                            }
                        });
                    }
                    mPhieu('/quan-ly/xem-phieu/' + result.data.maphieu).taophieu(() => {
                        if (tienthua < 0) {
                            sToast.confirm('Xác nhận tạo phiếu bán hàng?',
                                tienthua < 0 ? '<span class="text-danger font-weight-bolder">Lưu ý: Bạn đang bán nợ!!!</span>' : '',
                                (confirmed) => {
                                    if (confirmed.isConfirmed) {
                                        confirm();
                                    }
                                })
                        }
                        else {
                            confirm();
                        }
                    })
                }
            });
        })
    }

    function clickXemThongTinHH(data) {
        for (let col of $('#modalXemHH .col-thongtin')) {
            let field = $(col).attr('data-field');
            let value = data[field];
            $(col).find('span').text(value);
        }
        $('#modalXemHH').modal('show');
    }

    function initClickXemThongTinKH(id) {
        sToast.loading('Đang lấy thông tin. Vui lòng chờ...');
        $.ajax({
            url: '/api/quan-ly/danh-muc/khach-hang/thong-tin',
            type: 'get',
            dataType: 'json',
            data: {
                id
            }
        }).done((result) => {
            if (result.succ) {
                for(let col of $('#modalXemKH .col-thongtin')) {
                    clickXemThongTinKH(result.data,col);
                }

                $('#modalXemKH').modal('show');
            }
        });
    }

    function setThongTinKH(khachhang) {
        // $('#boxKhachHang .lblTenKH').text(khachhang.ten);
        $('#boxKhachHang .lblMaKH').text(khachhang.ma);
        $('#boxKhachHang .lblCongNoKH').text(numeral(khachhang.congno).format('0,0'));
        $('#boxKhachHang .lblDiaChiKH').text(khachhang.diachi);
        $('#boxKhachHang .btnThongTin').off('click');
        if (isNull($('#selKhachHang').val())) {
            $('#boxKhachHang .btnThongTin').attr('disabled','');
        }
        else {
            $('#boxKhachHang .btnThongTin').attr('disabled',null).click(() => {
                initClickXemThongTinKH(khachhang.id);
            })
        }
        @if(in_array('danh-muc.khach-hang.thu-cong-no',$info->phanquyen) !== false)
        if (khachhang.congno > 0) {
            $('#boxKhachHang .btnThuCongNo').removeClass('d-none').off('click').click(() => {
                actionThuCongNo(khachhang);
            });
        }
        @endif
    }
</script>
