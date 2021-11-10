<script>
    let tblDanhSach;
    let tblDanhSachPhieu;
    let tblDanhSachPhieu2;
    let chinhanhs = JSON.parse('{!! $chinhanhs !!}');
    init();
    initChiNhanh();
    actionLoc();

    function init() {
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
    }

    function initChiNhanh() {
        $('#selChiNhanh').change(() => {
            actionLoc();
        });
    }

    function actionLoc() {
        let chinhanh_id = $('#selChiNhanh').val();
        let ngay = $('#inpNgay').datetimepicker('viewDate').format('YYYY-MM-DD');
        $('#boxTienVao, #boxTienRa, #boxTongCuoi').empty();
        $.ajax({
            url: '/api/quan-ly/thu-chi/tra-cuu',
            type: 'get',
            dataType: 'json',
            data: {
                chinhanh_id, ngay
            }
        }).done((results) => {
            let itemCongNo = null;
            results.data.splice(0,1);
            results.data.forEach((value) => {
                if (value.tongthu > 0) {
                    if ($('#boxTienVao > div').length > 0) {
                        $('#boxTienVao').append('<div class="my-2"></div>');
                    }
                    let item = $('<div class="d-flex justify-content-between">' +
                        '<span>' + value.tenphieu + '</span>' +
                        '<span class="font-weight-bolder ml-1 text-success">' + numeral(value.tongthu).format('0,0') + '</span>' +
                        '</div>');
                    $('#boxTienVao').append(item);
                    item.click(() => {initDanhSachPhieu(value.tenphieu,value.dsphieu)});
                }
                if (value.tongchi > 0) {
                    if ($('#boxTienRa > div').length > 0) {
                        $('#boxTienRa').append('<div class="my-2"></div>');
                    }
                    let item = $('<div class="d-flex justify-content-between">' +
                        '<span>' + value.tenphieu + '</span>' +
                        '<span class="font-weight-bolder ml-1 text-danger">' + numeral(value.tongchi).format('0,0') + '</span>' +
                        '</div>');
                    $('#boxTienRa').append(item);
                    item.click(() => {initDanhSachPhieu(value.tenphieu,value.dsphieu)});
                }
                if (value.congno > 0) {
                    itemCongNo = $('<div class="d-flex justify-content-between">' +
                        '<span>' + value.tenphieu + '</span>' +
                        '<span class="font-weight-bolder ml-1 text-secondary">' + numeral(value.congno).format('0,0') + '</span>' +
                        '</div>');
                    itemCongNo.click(() => {initDanhSachPhieu(value.tenphieu,value.dsphieu)});
                }
            });
            $('#boxTongCuoi').append('' +
                '<div class="d-flex justify-content-between">' +
                '<span>ĐẦU KỲ</span>' +
                '<span class="font-weight-bolder ml-1">' + numeral(results.dauky).format('0,0') + '</span>' +
                '</div>').append('<div class="my-2"></div>');
            $('#boxTongCuoi').append(itemCongNo).append('<div class="my-2"></div>');
            $('#boxTongCuoi').append('' +
                '<div class="d-flex justify-content-between">' +
                '<span>TỔNG THU</span>' +
                '<span class="font-weight-bolder ml-1 text-success">' + numeral(results.tongthu).format('0,0') + '</span>' +
                '</div>').append('<div class="my-2"></div>');
            $('#boxTongCuoi').append('' +
                '<div class="d-flex justify-content-between">' +
                '<span>TỔNG CHI</span>' +
                '<span class="font-weight-bolder ml-1 text-danger">' + numeral(results.tongchi).format('0,0') + '</span>' +
                '</div>').append('<div class="my-2"></div>');
            $('#boxTongCuoi').append('' +
                '<div class="d-flex justify-content-between">' +
                '<span>CUỐI KỲ</span>' +
                '<span class="font-weight-bolder ml-1 text-info">' + numeral(results.cuoiky).format('0,0') + '</span>' +
                '</div>').append('<div class="my-2"></div>');
        });
    }

    function initDanhSachPhieu(tenphieu, data) {
        $('#modalDanhSachPhieu .modal-body').empty();
        data.forEach((value, key) => {
            let item = $('' +
                '<div>' +
                '   <div class="font-weight-bolder text-primary" style="font-size: 20px">' + value.maphieu + '</div>' +
                '   <div class="d-flex">' +
                '       <span class="font-weight-bolder">Đối tượng:</span>' +
                '       <span class="ml-2 text-info font-weight-bolder">' + value.doituong + '</span>' +
                '   </div>' +
                '   <div class="d-flex">' +
                '       <span class="font-weight-bolder">Tiền thanh toán:</span>' +
                '       <span class="ml-2 text-tienthanhtoan font-weight-bolder">' + numeral(value.tienthanhtoan).format('0,0') + '</span>' +
                '   </div>' +
                '   <div class="d-flex">' +
                '       <span class="font-weight-bolder">NV lập phiếu:</span>' +
                '       <span class="ml-2">' + value.nhanvien + '</span>' +
                '   </div>' +
                '   <div class="d-flex">' +
                '       <span class="font-weight-bolder">Số phiếu:</span>' +
                '       <span class="ml-2">' + value.sophieu + '</span>' +
                '   </div>' +
                '   <div class="d-flex">' +
                '       <span class="font-weight-bolder">Thời gian:</span>' +
                '       <span class="ml-2">' + doi_ngay(value.created_at) + '</span>' +
                '   </div>' +
                '</div>');
            if (key > 0) {
                $('#modalDanhSachPhieu .modal-body').append('<div class="divider my-2"></div>');
            }
            $('#modalDanhSachPhieu .modal-body').append(item);
        })
        $('#modalDanhSachPhieu').modal('show').find('.modal-title').text(tenphieu);
    }
</script>
