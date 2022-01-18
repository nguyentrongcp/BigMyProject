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

<script src="/giaodien/dist/js/function.js?version=1.6.5"></script>

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
            if (result.succ && $('#modalInput.show').length > 0) {
                $('#modalInput').modal('hide');
            }
        },
        error: function (e) {
            if (e.status !== 0) {
                console.log(e);
            }
        }
    });

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
                    location.href = '{{ route('nong-dan.dang-nhap') }}';
                }
            })
    })

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

    initThongBao();
    function initThongBao() {
        $('#dropdownThongBao').empty();
        $.ajax({
            url: '/api/nong-dan/thong-bao/danh-sach',
            type: 'get',
            dataType: 'json',
        }).done((result) => {
            let thongbaos = result.data.thongbaos;
            let quytrinhs = result.data.quytrinhs;
            if (thongbaos.length === 0 && quytrinhs.length === 0) {
                $('#dropdownThongBao').append('' +
                    '<span class="dropdown-item dropdown-header text-secondary font-weight-bolder">' +
                    'Chưa có thông báo mới' +
                    '</span>');
            }
            else {
                if (thongbaos.length > 0) {
                    $('#dropdownThongBao').append('' +
                        '<span class="dropdown-item dropdown-header">' +
                        thongbaos.length + ' thông báo chưa đọc' +
                        '</span>' +
                        '<div class="dropdown-divider"></div>');
                    thongbaos.forEach((thongbao, stt) => {
                        let item = $('' +
                            '<div class="dropdown-item thongbao">' +
                            '   <div class="font-weight-bolder text-purple">' + thongbao.thuaruong_ten + '</div>' +
                            '   <div class="text-wrap font-size-mobile text-justify">' +
                            '       <span class="tieude">' +
                            '           <strong>' + thongbao.nhanvien + '</strong> ' + thongbao.tieude + ': </span>' +
                            '       </span>' +
                            '       <span class="noidung">' + thongbao.noidung + '</span>' +
                            '   </div>' +
                            '</div>');
                        if (stt > 0) {
                            $('#dropdownThongBao').append('<div class="dropdown-divider"></div>');
                        }
                        $('#dropdownThongBao').append(item);
                        item.click(() => {
                            sToast.loading('Đang chuyển trang. Vui lòng chờ...')
                            $.ajax({
                                url: '/api/nong-dan/thong-bao/xem',
                                type: 'get',
                                dataType: 'json',
                                data: {
                                    id: thongbao.id
                                }
                            }).done(() => {
                                location.href = '/nong-dan/quy-trinh?thuaruong_id=' + thongbao.thuaruong_id + '&giaidoan_id=' + thongbao.giaidoan_id
                            });
                        })
                    })
                }
                if (quytrinhs.length > 0) {
                    if (thongbaos.length > 0) {
                        $('#dropdownThongBao').append('<div class="dropdown-divider"></div>');
                    }
                    let header = $('<span class="dropdown-item dropdown-header"></span>');
                    $('#dropdownThongBao').append(header).append('<div class="dropdown-divider"></div>');
                    let soluong = 0;
                    quytrinhs.forEach((_quytrinh, stt) => {
                        soluong += _quytrinh.danhsach.length;
                        let item = $('' +
                            '<div class="dropdown-item quytrinh">' +
                            '   <div class="text-wrap font-weight-bolder text-purple text-center">' + _quytrinh.thuaruong_ten + '</div>' +
                            '   <div class="dropdown-divider"></div>' +
                            '</div>');
                        _quytrinh.danhsach.forEach((quytrinh, key) => {
                            let element = $('' +
                                '<div class="text-wrap font-size-mobile d-flex flex-column">' +
                                '   <div class="tieude">' + quytrinh.sanpham + '</div>' +
                                '   <div class="noidung">' +
                                '       <div class="congdung">' + quytrinh.congdung + '</div>' +
                                '       <div class="text-right">Số lượng/ha: <strong class="soluong">' + quytrinh.soluong +
                                '</strong> ' + quytrinh.donvitinh + '</div>' +
                                '   </div>' +
                                '</div>');
                            if (key > 0) {
                                item.append('<div class="dropdown-divider"></div>')
                            }
                            item.append(element);
                        })
                        if (stt > 0) {
                            $('#dropdownThongBao').append('<div class="dropdown-divider"></div>')
                        }
                        $('#dropdownThongBao').append(item);
                        item.click(() => {
                            @if(url()->current() == route('nong-dan.quytrinh-hientai'))
                            $('#container').animate({
                                scrollTop: $('#container').scrollTop() +
                                    $('.timeline .time-label[data-id=' + _quytrinh.giaidoan_id + ']').offset().top - 65
                            }, 500);
                            @else
                                location.href = '/nong-dan/quytrinh-hientai?giaidoan_id=' + _quytrinh.giaidoan_id
                            @endif
                        })
                    });
                    header.text(soluong + ' quy trình trong hôm nay');
                    $('#lblSoThongBao').text(soluong + thongbaos.length);
                }
            }
        });
    }

    function initSanPham(sanpham_id) {
        sToast.loading('Đang lấy thông tin sản phẩm. Vui lòng chờ...')
        $.ajax({
            url: '/api/nong-dan/san-pham/thong-tin',
            type: 'get',
            dataType: 'json',
            data: {
                id: sanpham_id
            }
        }).done((result) => {
            if (result.succ) {
                let model = result.data.model;
                let modal = $('' +
                    '<div class="modal fade" id="modalSanPham">' +
                    '    <div class="modal-dialog">' +
                    '        <div class="modal-content">' +
                    '            <div class="modal-header">' +
                    '                <h5 class="modal-title">Thông Tin Sản Phẩm</h5>' +
                    '            </div>' +
                    '            <div class="modal-body row-thongtin">' +
                    '                <div class="col-thongtin">' +
                    '                     <strong>Mã</strong>' +
                    '                     <span>' + model.ma + '</span>' +
                    '                </div>' +
                    '                <div class="divider my-3"></div>' +
                    '                <div class="col-thongtin">' +
                    '                     <strong>Tên</strong>' +
                    '                     <span>' + model.ten + '</span>' +
                    '                </div>' +
                    '                <div class="divider my-3"></div>' +
                    '                <div class="col-thongtin">' +
                    '                     <strong>Đơn vị tính</strong>' +
                    '                     <span>' + model.donvitinh + '</span>' +
                    '                </div>' +
                    '                <div class="divider my-3"></div>' +
                    '                <div class="col-thongtin">' +
                    '                     <strong>Nhóm</strong>' +
                    '                     <span>' + model.nhom + '</span>' +
                    '                </div>' +
                    '                <div class="divider my-3"></div>' +
                    '                <div class="col-thongtin">' +
                    '                     <strong>Đơn giá</strong>' +
                    '                     <span>' + numeral(model.dongia).format('0,0') + '</span>' +
                    '                </div>' +
                    '            </div>' +
                    '            <div class="modal-footer">' +
                    '                <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>' +
                    '            </div>' +
                    '        </div>' +
                    '    </div>' +
                    '</div>');

                modal.on('hidden.bs.modal', () => {
                    modal.remove();
                }).modal('show');
            }
        });
    }
</script>

@yield('js-custom')
