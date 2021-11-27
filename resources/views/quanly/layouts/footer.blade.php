<!-- jQuery -->
<script src="/giaodien/plugins/jquery/jquery.min.js"></script>
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

<script src="/giaodien/dist/js/function.js?version=1.5"></script>

<script src="/giaodien/dist/js/socket.io.js"></script>

@yield('js-include')

<script>
    initInputNumeral($('input.numeral'));

    $('input[type=text]').attr('autocomplete','off');

    let socket = io.connect("https://realtime-1.hailua.center:8282");

    let info = JSON.parse('{!! json_encode($info) !!}');

    Pusher.logToConsole = true;
    let pusher = new Pusher('740b3484c72a47373ce7', {
        cluster: 'ap1'
    });
    let channel = pusher.subscribe('my-project');

    $(document).on('select2:open', () => {
        setTimeout(() => {
            if ($('.select2-search.select2-search--dropdown .select2-search__field').length > 0) {
                document.querySelector('.select2-search.select2-search--dropdown .select2-search__field').focus();
            }
        },100)
    });

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

    // initSoDonHang();

    $.each($('.nav-sidebar .nav-item.has-treeview'), function(key, value) {
        if ($(value).find('ul .nav-link.active').length > 0) {
            $(value).addClass('menu-open menu-is-opening').find('> .nav-link').addClass('active');
            $(value).find('ul').css('display','block');
        }
    });

    // offEnterTextarea($('textarea'));

    initThongBaoGia();

{{--    @if(in_array('nhapkho-noibo',$dsquyen) !== false)--}}
    initSoPhieuXuat();
{{--    @endif--}}

{{--    @if(in_array('nhap-hang.danh-sach',$dsquyen) !== false)--}}
    initSoPhieuNhap();
{{--    @endif--}}

{{--    @if(in_array('role.chi-nhanh.tat-ca',$dsquyen) !== false)--}}
    $('#btnChuyenCuaHang').click(() => {
        mInput('Chuyển cửa hàng','').select2('Chọn cửa hàng','Vui lòng chọn cửa hàng cần chuyển',
            '/api/quan-ly/danh-muc/chi-nhanh/tim-kiem?selectAll=0',true,
            () => {
                let chinhanh_id = $('#modalInput .value').val();
                let chinhanh_ten = $('#modalInput .value option:selected').text();
                if (isNull(chinhanh_id)) {
                    $('#modalInput .value').addClass('is-invalid');
                    return false;
                }
                sToast.confirm('Chuyển cửa hàng?',
                    'Xác nhận chuyển sang cửa hàng <span class="text-info">' + chinhanh_ten + '</span>',
                    (result) => {
                        if (result.isConfirmed) {
                            sToast.loading('Đang chuyển cửa hàng. Vui lòng chờ...')
                            $.ajax({
                                url: '/api/quan-ly/danh-muc/nhan-vien/chuyen-cua-hang',
                                type: 'get',
                                dataType: 'json',
                                data: {
                                    id: info.id,
                                    chinhanh_id
                                }
                            }).done((result) => {
                                if (result.succ) {
                                    sToast.notification(1,'Chuyển cửa hàng thành công.',() => {
                                        location.reload();
                                    })
                                }
                            });
                        }
                    });
            }, 'Bạn chưa chọn cửa hàng cần chuyển!');
    });
{{--    @endif--}}

    checkMatKhauMacDinh();

    $('#btnDoiMatKhau').click(() => {
        initDoiMatKhau();
    })
    $('#btnInfo').click(() => {
        initInfo(info);
    })

    $('#btnLogout').click(() => {
        sToast.confirm('Xác nhận đăng xuất khỏi tài khoản {{ $info->ten }}?','',
            (confirmed) => {
                if (confirmed.isConfirmed) {
                    sToast.loading('Đang đăng xuất khỏi hệ thống...');
                    setTimeout(() => {
                        Cookies.remove('token');
                        location.href = '{{ route('dang-nhap') }}';
                    }, 1000);
                }
            })
    })

    $(window).on('message', (event) => {
        let data = event.originalEvent.data;
        let type = data.type;
        if (type === 'autoHeight') {
            $('#modalXemPhieu iframe').css('height', data.height);
        }
    })

    channel.bind('thongbaogia', function() {
        initThongBaoGia();
    })

    channel.bind('reload-info-' + info.id, function() {
        location.reload();
    })

    channel.bind('reload-chucvu-' + info.chucvu, function() {
        location.reload();
    })

{{--    @if(in_array('nhap-hang.danh-sach',$dsquyen) !== false)--}}
    channel.bind('so-phieunhap', function() {
        initSoPhieuNhap();
    })
{{--    @endif--}}

    channel.bind('change-token-' + info.id, function() {
        sToast.confirm('Trở về trang đăng nhập?',
            '<div class="text-danger font-weight-bolder">Tài khoản của bạn đang đăng nhập từ một thiết bị khác!</div>' +
            '<div>Bấm <span style="color: rgb(48, 133, 214)">Xác Nhận</span> để trở về trang đăng nhập</div>' +
            '<div>Hoặc <span style="color: rgb(221, 51, 51)">Hủy Bỏ</span> để giữ nguyên trang hiện tại.</div>',
            (result) => {
                if (result.isConfirmed) {
                    location.href = '{{ route('dang-nhap') }}';
                }
            });
    })

    channel.bind('doiten-chinhanh', function(data) {
        info.chinhanh_ten = data.message;
        $('#lblTenChiNhanh').text(data.message);
    });
</script>

@yield('js-custom')
