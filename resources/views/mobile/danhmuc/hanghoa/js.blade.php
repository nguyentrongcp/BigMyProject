<script>
    initDanhMucHangHoa();

    function initDanhMucHangHoa() {
        let timeout = null;
        $('#boxDanhSach .input-search').off('input').on('input', function () {
            showLoaderMobile();
            if (timeout != null) {
                clearTimeout(timeout);
            }
            timeout = setTimeout(() => {
                let input = $(this).val().trim();
                $.ajax({
                    url: '/api/mobile/danh-muc/hang-hoa/danh-sach',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        q: input
                    }
                }).done((result) => {
                    $('#boxDanhSach .box-content').empty();
                    result.forEach((value) => {
                        addItem(value);
                    })
                });
            }, $(this).val() !== '' ? 300 : 0);
        }).trigger('input');
        $('#boxDanhSach .box-search .ui.input i').click(() => {
            $('#boxDanhSach .input-search').val('').trigger('input');
        });
    }

    function addItem(value, isPrepend = false) {
        let hinhanh = isNull(value.hinhanh) ? '' : JSON.parse(value.hinhanh);
        // if (avatar.length > 0) {
        //     avatar = avatar[0].url;
        // }
        // else {
        //     avatar = '';
        // }
        let ten = value.ten + ' (' + value.donvitinh + ')';
        let temp = $(
            '<li class="item">' +
            '   <div class="product-img">' +
            '       <img src="' + hinhanh.url + '" alt="Product Image" class="img-size-50">' +
            '   </div>' +
            '   <div class="product-info">' +
            '       <div class="product-title text-muted ten">' + ten + '</div>' +
            '       <div class="product-description">' +
            '           <span class="mr-1">Mã:</span>' +
            '           <span>' + value.ma + '</span>' +
            '           <span class="ml-3 mr-1 font-weight-bolder">Q.Cách:</span>' +
            '           <span class="quycach">' + value.quycach + '</span>' +
            '       </div>' +
            '       <div class="product-description">' +
            '           <span class="mr-1">Nhóm:</span>' +
            '           <span class="nhom">' + value.nhom + '</span>' +
            '       </div>' +
            '       <div class="product-description tonkho">' +
            '           <span class="mr-1">Tồn kho:</span>' +
            '           <span class="font-weight-bolder text-info">' + value.tonkho + '</span>' +
            '       </div>' +
            '       <div class="product-description giaban">' +
            '           <span class="mr-1">Đơn giá:</span>' +
            '           <span class="font-weight-bolder text-danger">' + (isNull(value.dongia) ? 'Chưa có' : numeral(value.dongia).format('0,0')) + '</span>' +
            '       </div>' +
            '   </div>' +
            '</li>'
        );
        clickViewerImage(temp.find('img'));
        temp.find('img').on('error', function () {
            $(this).attr('src','/logo.jpg');
            temp.find('img').off('click');
        });
        temp.find('.tonkho').click(() => {
            initTonKhoGiaBan(value.ma);
        });
        // temp.find('.giaban').click(() => {
        //     $('#modalTonKho .modal-title').text('Danh Sách Giá Bán');
        //     $('#modalTonKho .modal-body').empty()
        //         .append('<p class="mb-0 text-muted font-weight-bolder">' + ten + '</p>');
        //     value.giabans.forEach((value) => {
        //         $('#modalTonKho .modal-body').append('<hr>')
        //             .append(
        //                 '<p class="mb-0 d-flex align-items-center">' +
        //                 '   <span class="text-muted mr-3">' + value.chinhanh + '</span>' +
        //                 '   <span class="ml-auto font-weight-bolder text-danger">' + numeral(value.giaban).format('0,0') + '</span>' +
        //                 '</p>');
        //     });
        //     $('#modalTonKho').modal('show');
        // });
        if (isPrepend) {
            $('#boxDanhSach .box-content').prepend(temp);
        }
        else {
            $('#boxDanhSach .box-content').append(temp);
        }
        temp.find('.ten').click(() => {
            clickXemThongTin(value);
        })
    }

    function clickXemThongTin(data) {
        $.each($('#modalXem .col-thongtin'), function(key, col) {
            let field = $(col).attr('data-field');
            let value = data[field];
            if (['gianhap'].indexOf(field) !== -1) {
                value = numeral(value).format('0,0');
            }
            $(col).find('span').text(value);
        });
        $('#modalXem').modal('show');
    }
</script>
