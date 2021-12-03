<script>
    let tblHangHoa;
    init();
    initSelHangHoa();
    initTblHangHoa();
    initActionThemHangHoa();
    initActionIn();

    function init() {
        $('#boxHangHoa input').keypress((e) => {
            if (e.keyCode === 13) {
                $('#btnThemHangHoa').click();
            }
        });
    }

    function initSelHangHoa() {
        $('#selHangHoa').select2({
            ajax: {
                url: '/api/quan-ly/hang-hoa/qrcode/tim-kiem',
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
                {title: "Đơn giá", field: "dongia", headerSort: false, hozAlign: 'right', vertAlign: 'middle',
                    formatter: (cell) => {
                        return '<span class="text-danger font-weight-bolder">' + numeral(cell.getValue()).format('0,0') + '</span>';
                    }},
            ],
            height: '450px',
            movableColumns: false,
            dataChanged: () => {
                tblHangHoa.getColumns()[0].updateDefinition();
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

            if (tblHangHoa.getRow(hanghoa.id) !== false) {
                let _soluong = tblHangHoa.getRow(hanghoa.id).getData().soluong;
                tblHangHoa.updateData([{
                    id: hanghoa.id,
                    soluong: _soluong + soluong
                }]);
            }
            else {
                let dataTable = {
                    id: hanghoa.id,
                    ma: hanghoa.ma,
                    ten: hanghoa.ten,
                    donvitinh: hanghoa.donvitinh,
                    quycach: hanghoa.quycach,
                    soluong,
                    dongia: hanghoa.dongia
                }
                tblHangHoa.addData(dataTable,true).then(() => {tblHangHoa.getColumns()[0].updateDefinition()});
            }

            $('#boxHangHoa input').val('').trigger('change');
            $('#selHangHoa').val(null).trigger('change').focus().select2('open');
        })
    }

    function initActionIn() {
        $('#btnIn').off('click').click(() => {
            let data = tblHangHoa.getData();
            if (data.length === 0) {
                sToast.toast(0,'Bạn chưa chọn hàng hóa!');
                return false;
            }

            sToast.loading('Đang xử lý dữ liệu. Vui lòng chờ...');

            $.ajax({
                url: '/api/quan-ly/hang-hoa/qrcode/tao-ma',
                type: 'post',
                dataType: 'json',
                data: {
                    data: JSON.stringify(data)
                }
            }).done((result) => {
                if (result.succ) {
                    window.open('/quan-ly/hang-hoa/in-qrcode?matam=' + result.data.matam);
                    tblHangHoa.clearData();
                }
            });
        })
    }
</script>
