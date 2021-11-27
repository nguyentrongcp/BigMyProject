<script>
    init();
    initDanhMuc();

    function init() {
        $('#boxDanhSach .thang, #boxDanhSach .nam').change(() => {
            initDanhMuc();
        })
    }

    function initDanhMuc() {
        $('#boxDanhSach .box-content').empty();
        $.ajax({
            url: '/api/quan-ly/diem-danh/lich-su',
            type: 'get',
            dataType: 'json',
            data: {
                thang: $('#boxDanhSach .thang').val(),
                nam: $('#boxDanhSach .nam').val()
            }
        }).done((result) => {
            let tong = 0;
            result.forEach((value) => {
                addItem(value);
                tong += value.ngaycong;
            });

            $('#boxDanhSach .box-content').prepend(
                '<li class="item">' +
                '   <div class="product-info">' +
                '       <div class="product-title d-flex">' +
                '           <span class="ngay pr-2 text-nowrap">Tổng ngày công</span>' +
                '           <span class="text-info ml-auto text-right">' + tong + '</span>' +
                '       </div>' +
                '   </div>' +
                '</li>'
            );
        });
    }

    function addItem(value) {
        let style = 'text-info';
        if (('{{ date('Y-m-d') }}' !== value.ngay && value.ngaycong === 0) ||
            ('{{ date('Y-m-d') }}' === value.ngay && value.ngaycong === 0 && value.tg_ketthuc != null)) {
            style = ' text-danger';
        }
        let temp = $(
            '<li class="item">' +
            '   <div class="product-info">' +
            '       <div class="product-title row">' +
            '           <div class="col-4 text-nowrap">' + doi_ngay(value.ngay) + '</div>' +
            '           <div class="col-3 text-primary" style="width: 69px">' + value.tg_batdau + '</div>' +
            '           <div class="col-3 text-danger" style="width: 69px">' +
            (value.tg_ketthuc == null ? '----------' : value.tg_ketthuc) + '</div>' +
            '           <div class="' + style + ' col-2 text-right">' + value.ngaycong + '</div>' +
            '       </div>' +
            '   </div>' +
            '</li>'
        );
        $('#boxDanhSach .box-content').append(temp);
    }
</script>
