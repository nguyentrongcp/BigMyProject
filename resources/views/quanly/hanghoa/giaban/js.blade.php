<script>
    let tblDanhSach;
    initSelHangHoa();
    initDanhSach();
    initActionLoc();

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
                },
                delay: 250
            },
            allowClear: true,
            placeholder: 'Chọn hàng hóa...'
        }).change(function() {
            if ($(this).val() == null) {
                setThongTin();
                if (!isUndefined(tblDanhSach)) {
                    tblDanhSach.clearData();
                }
                $('#btnDongBo').off('click').attr('disabled','');
            }
        }).val(null).trigger('change');
    }

    function initDanhSach() {
        tblDanhSach = new Tabulator("#tblDanhSach", {
            columns: [
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", formatter: "rownum",
                    width: 40, headerSort: false, hozAlign: 'center'},
                {title: "Tên cửa hàng", field: "ten", vertAlign: 'middle', headerSort: false},
                {title: "Tồn kho", field: "tonkho", vertAlign: 'middle', hozAlign: 'right', sorter: 'number',
                    formatter: (cell) => {
                        return '<span class="text-info font-weight-bolder">' + parseFloat(cell.getValue()) + '</span>';
                    }},
                {title: "Giá bán", field: "dongia", vertAlign: 'middle', hozAlign: 'right', sorter: 'number',
                    formatter: (cell) => {
                        return '<span class="text-danger font-weight-bolder">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    },
                    @if(in_array('hang-hoa.gia-ban.dieu-chinh',$info->phanquyen) !== false)
                    cellClick: (e, cell) => {
                        mInput(cell.getData().ten,'',true).numeral('Nhập đơn giá mới','Nhập đơn giá mới...',
                            () => {
                                let value = parseFloat($('#modalInput .value').attr('data-value'));
                                if (value < 0 || isNaN(value)) {
                                    $('#modalInput .value').addClass('is-invalid');
                                    return false;
                                }
                                sToast.confirm('Xác nhận cập nhật đơn giá mới!','',
                                    (confirmed) => {
                                        if (confirmed.isConfirmed) {
                                            sToast.loading('Đang xử lý dữ liệu. Vui lòng chờ...')
                                            $.ajax({
                                                url: '/api/quan-ly/hang-hoa/gia-ban/cap-nhat',
                                                type: 'get',
                                                dataType: 'json',
                                                data: {
                                                    id: cell.getData().id,
                                                    dongia: value
                                                }
                                            }).done((result) => {
                                                if (result.succ) {
                                                    cell.setValue(value);
                                                    $('#modalInput').modal('hide');
                                                    if (result.data.data_socket.length > 0) {
                                                        socket.emit('send-notification',result.data.data_socket);
                                                        socket.emit('send-notification-appcu',result.data.data_socket);
                                                    }
                                                }
                                                else if (!isUndefined(result.erro)) {
                                                    $('#modalInput .value').addClass('is-invalid');
                                                }
                                            });
                                        }
                                    })
                            },'Đơn giá không hợp lệ!')
                    }
                    @endif
                },

            ],
            height: '450px',
            movableColumns: false,
            // dataChanged: () => {
            //     tblDanhSach.getColumns()[0].updateDefinition();
            // }
        });
        initSearchTable(tblDanhSach,['ten']);
    }

    function initActionLoc() {
        $('#btnLoc').click(() => {
            let hanghoa_id = $('#selHangHoa').val();
            if (hanghoa_id == null) {
                sToast.toast(0,'Bạn chưa chọn hàng hóa!');
                return false;
            }
            let hanghoa = $('#selHangHoa').select2('data')[0];
            setThongTin(hanghoa);
            tblDanhSach.setData('/api/quan-ly/hang-hoa/gia-ban/danh-sach', {
                hanghoa_id
            }).then(() => {tblDanhSach.getColumns()[0].updateDefinition()});
        })
    }

    function setThongTin(hanghoa = null) {
        hanghoa = hanghoa == null ? {
                ma: '---------------',
                ten: '---------------',
                donvitinh: '---------------',
                quycach: '---------------',
                nhom: '---------------'
            } : hanghoa;
        $('#boxThongTin .ma').text(hanghoa.ma);
        $('#boxThongTin .ten').text(hanghoa.ten);
        $('#boxThongTin .donvitinh').text(hanghoa.donvitinh);
        $('#boxThongTin .quycach').text(hanghoa.quycach);
        $('#boxThongTin .nhom').text(hanghoa.nhom);

        @if(in_array('hang-hoa.gia-ban.dieu-chinh',$info->phanquyen) !== false)
        $('#btnDongBo').attr('disabled',null).off('click').click(() => {
            mInput('Đồng Bộ Giá Bán','',true).numeral('Nhập đơn giá','Nhập đơn giá...',
                () => {
                    let value = parseFloat($('#modalInput .value').attr('data-value'));
                    if (value < 0 || isNaN(value)) {
                        $('#modalInput .value').addClass('is-invalid');
                        return false;
                    }
                    sToast.confirm('Xác nhận đồng bộ giá bán!','',
                        (confirmed) => {
                            if (confirmed.isConfirmed) {
                                sToast.loading('Đang xử lý dữ liệu. Vui lòng chờ...')
                                $.ajax({
                                    url: '/api/quan-ly/hang-hoa/gia-ban/dong-bo',
                                    type: 'get',
                                    dataType: 'json',
                                    data: {
                                        hanghoa_id: hanghoa.id,
                                        dongia: value
                                    }
                                }).done((result) => {
                                    if (result.succ) {
                                        tblDanhSach.getRows().forEach((row) => {
                                            row.getCell('dongia').setValue(value);
                                        })
                                        $('#modalInput').modal('hide');
                                        if (result.data.data_socket.length > 0) {
                                            socket.emit('send-notification',result.data.data_socket);
                                            socket.emit('send-notification-appcu',result.data.data_socket);
                                        }
                                    }
                                    else if (!isUndefined(result.erro)) {
                                        $('#modalInput .value').addClass('is-invalid')
                                    }
                                });
                            }
                        })
                },'Đơn giá không hợp lệ!')
        })
        @endif
    }
</script>
