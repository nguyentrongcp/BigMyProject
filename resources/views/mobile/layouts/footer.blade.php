<!-- jQuery -->
<script src="/giaodien/plugins/jquery/jquery.min.js"></script>

<script src="/giaodien/my_plugins/viewer/viewer.min.js"></script>
<!-- Bootstrap -->
<script src="/giaodien/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="/giaodien/dist/js/adminlte.js"></script>
<script src="/giaodien/my_plugins/fontawesome/full.min.js"></script>

<script src="/giaodien/my_plugins/select2/select2.min.js"></script>
<script src="/giaodien/my_plugins/tabulator/tabulator.min.js"></script>
<script src="/giaodien/my_plugins/tabulator/xlsx.full.min.js"></script>
<script src="/giaodien/my_plugins/numeral-js/numeral.min.js"></script>
<script src="/giaodien/my_plugins/sweet-alert2/SweetAlert2.min.js"></script>
<script src="/giaodien/my_plugins/cookie/cookie.min.js"></script>
<script src="/giaodien/my_plugins/pusher/pusher7-0-3.min.js"></script>

<script src="/giaodien/my_plugins/daterangepicker/moment.min.js"></script>
<script src="/giaodien/my_plugins/daterangepicker/daterangepicker.js"></script>

<script src="/giaodien/my_plugins/tempusdominus/5-4.min.js"></script>

<script src="/giaodien/my_plugins/autosize/autosize.min.js"></script>

<script src="/giaodien/dist/js/function.js?version=1.6.2"></script>

@yield('js-include')

<script>
    initInputNumeral($('input.numeral'));

    let info = JSON.parse('{!! json_encode($info) !!}');

    let _location = null;

    // if ('geolocation' in navigator) {
    //     navigator.geolocation.getCurrentPosition(function (position) {
    //         _location = position.coords.latitude + ',' + position.coords.longitude;
    //     });
    // }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (result) {
            if (Swal.isVisible()) {
                if (Swal.isLoading()) {
                    Swal.close();
                }
            }
            if (!isUndefined(result.sess)) {
                location.href = '{{ route('dang-nhap') }}';
            }
            if (!isUndefined(result.noti)) {
                sToast.toast(result.succ,result.noti);
            }
            if (result.mess !== '' && !isUndefined(result.mess)) {
                console.log(result.mess);
            }
            if (result.succ === 0 && $('#modalInput').hasClass('show') && !isUndefined(result.erro)) {
                $('#modalInput span.error').text(result.erro);
                $('#modalInput .value').addClass('is-invalid');
                $('#modalInput .value').focus();
            }
        },
        error: function (e) {
            if (e.status !== 0) {
                console.log(e);
            }
        }
    });

    $(window).on('message', (event) => {
        let data = event.originalEvent.data;
        let type = data.type;
        if (type === 'autoHeight') {
            $('#modalXemPhieu iframe').css('height', data.height);
        }
    })

    $('#lblTenChiNhanh').text(info.chinhanh_ten);
    $('#modalMenu .title').text(info.ten);

    $('#btnShowQrcode').click(() => {
        if (!isUndefined(window.ReactNativeWebView)) {
            window.ReactNativeWebView.postMessage(JSON.stringify({type: 'qrcode'}));
        }
    });

    $('#lblTitle').text($('#modalMenu .item > div.active').attr('data-title'));
    $('#btnMenu').click(() => {
        $('#modalMenu').modal('show');
    })
    $('#modalMenu .box-menu .item > div:not(.none-url)').click(function () {
        sToast.loading('Đang chuyển trang...');
        location.href = $(this).attr('data-href');
    });
    $('#modalMenu .box-menu .item > div.doi-matkhau').click(function () {
        initDoiMatKhau();
    });

    $('#btnDangXuat').click(() => {
        sToast.confirm('Xác nhận đăng xuất khỏi tài khoản "' + info.ten + '"?','',
            (confirmed) => {
                if (confirmed.isConfirmed) {
                    Cookies.set('token','');
                    localStorage.removeItem('token');
                    if (!isUndefined(window.ReactNativeWebView)) {
                        window.ReactNativeWebView.postMessage(JSON.stringify({
                            type: 'thongbao-remove'
                        }));
                    }
                    location.href = '{{ route('mobile.dang-nhap') }}';
                }
            })
    })

    @if(in_array('role.chi-nhanh.tat-ca',$info->phanquyen) !== false)
    $('#lblTenChiNhanh').click(() => {
        initChuyenCuaHang();
    });
    @endif

    function triggerBackHandle() {
        if ($('.viewer-container.viewer-in').length > 0) {
            $('.viewer-container.viewer-in .viewer-close').click();
        }
        else if ($('.lg-outer.lg-visible').length > 0) {
            $('.lg-outer.lg-visible span.lg-close').click();
        }
        else if (Swal.isVisible()) {
            if (!Swal.isLoading()) {
                Swal.close();
            }
        }
        else if ($('.modal.show').length > 0) {
            $($('.modal.show')[$('.modal.show').length - 1]).modal('hide');
        }
        else {
            if (!isUndefined(window.ReactNativeWebView)) {
                window.ReactNativeWebView.postMessage(JSON.stringify({type: 'exit'}));
            }
        }
    }

    function setIsMobile() {
        return false;
    }

    function setLocation(location) {
        _location = location;
    }

    function triggerQrCode(data) {
        data = data.split('|');
        let key = data[0];
        data = JSON.parse(data[1]);
        switch (key) {
            // case 'xemphieu':
            //     mPhieu('/xem-phieu/' + data).xemphieu();
            //     break;
            case 'hanghoa':
                initTonKhoGiaBan(data.ma,data.chinhanh_id);
                break;
            case 'diemdanh':
                if (_location == null || _location === '') {
                    if ('geolocation' in navigator) {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            _location = position.coords.latitude + ',' + position.coords.longitude;
                        });
                    }
                }
                initDiemDanh(data.chinhanh_id);
                break;
            // case 'dangnhap':
            //     socket.emit('dang-nhap-qrcode',_token,data)
            //     break;
        }
    }

    function testAppMobile() {
        alert(Cookies.get('token'));
        // window.ReactNativeWebView.postMessage(JSON.stringify({type: 'download', url: 'https://hailua.center/qrcode-diemdanh/LaiVung.png'}));
    }
</script>

@yield('js-custom')
