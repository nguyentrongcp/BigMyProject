<script>
    let contents = [];
    let chinhanhs = JSON.parse('{!! $chinhanhs !!}');
    initDanhMuc();

    function initDanhMuc() {
        showLoaderMobile();
        $('#boxDanhSach .box-content').empty();
        chinhanhs.forEach((value) => {
            addItem(value);
        });

        let timeout = null;
        $('#boxDanhSach .input-search').off('input').on('input', function () {
            if (timeout != null) {
                clearTimeout(timeout);
            }
            timeout = setTimeout(() => {
                let input = convertToSlug($(this).val().trim());
                $('#boxDanhSach .box-content > .item').addClass('d-none');
                contents.forEach((value) => {
                    if (convertToSlug(value.find('.ten').text()).indexOf(input) !== -1 ||
                        value.find('.dienthoai').text().indexOf(input) !== -1 ||
                        value.find('.dienthoai2').text().indexOf(input) !== -1) {
                        value.removeClass('d-none');
                    }
                })
            }, $(this).val() !== '' ? 300 : 0);
        }).val('');
        $('#boxDanhSach .box-search .ui.input i').click(() => {
            $('#boxDanhSach .input-search').val('').trigger('input');
        });
    }

    function addItem(value, isPrepend = false) {
        let temp = $(
            '<li class="item">' +
            '   <div class="product-info">' +
            '       <div class="product-title text-primary ten">' + value.ten + '</div>' +
            '       <div class="product-description">' +
            '           <span class="text-info dienthoai">' + value.dienthoai + '</span>' +
            '           <span class="px-2">|</span>' +
            '           <span class="text-success dienthoai2 font-weight-bolder">' + value.dienthoai2 + '</span>' +
            '       </div>' +
            '       <div><strong>Địa chỉ: </strong>' + value.diachi + '</div>' +
            '   </div>' +
            '</li>'
        );
        temp.find('.dienthoai').click(() => {
            window.ReactNativeWebView.postMessage(JSON.stringify({type: 'call', sodienthoai: value.dienthoai}));
        })
        temp.find('.dienthoai2').click(() => {
            window.ReactNativeWebView.postMessage(JSON.stringify({type: 'call', sodienthoai: value.dienthoai2}));
        })
        if (isPrepend) {
            $('#boxDanhSach .box-content').prepend(temp);
        }
        else {
            $('#boxDanhSach .box-content').append(temp);
        }
        contents.push(temp);
    }
</script>
