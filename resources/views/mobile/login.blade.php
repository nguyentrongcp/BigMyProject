<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>MyProject By Nguyễn Đình Trọng</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/giaodien/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="/giaodien/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/giaodien/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="/giaodien/my_plugins/sweet-alert2/custom.css">
    <style>

    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <span class="h1">VTNN HAI LÚA</span>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Đăng nhập để bắt đầu phiên làm việc</p>

            <div>
                <div class="input-group mb-3">
                    <input id="inpDienThoai" type="text" class="form-control" placeholder="Số điện thoại hoặc tài khoản">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fa fa-phone"></span>
                        </div>
                    </div>
                    <span id="lblErrorDienThoai" class="error invalid-feedback"></span>
                </div>
                <div class="input-group mb-3">
                    <input id="inpMatKhau" type="password" class="form-control" placeholder="Mật khẩu">
                    <div class="input-group-append">
                        <div class="input-group-text" style="width: 42px">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    <span id="lblErrorMatKhau" class="error invalid-feedback"></span>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="chkRemember">
                        <label class="custom-control-label" for="chkRemember">
                            Đăng xuất khỏi tất cả các thiết bị khác
                        </label>
                    </div>
                </div>
                <button id="btnSubmit" type="submit" class="btn btn-primary btn-block font-weight-bolder">Đăng Nhập</button>
            </div>

{{--            <div class="social-auth-links text-center mt-2 mb-3">--}}
{{--                <a href="#" class="btn btn-block btn-primary">--}}
{{--                    <i class="fab fa-facebook mr-2"></i> Sign in using Facebook--}}
{{--                </a>--}}
{{--                <a href="#" class="btn btn-block btn-danger">--}}
{{--                    <i class="fab fa-google-plus mr-2"></i> Sign in using Google+--}}
{{--                </a>--}}
{{--            </div>--}}
            <!-- /.social-auth-links -->

{{--            <p class="mb-1">--}}
{{--                <a href="forgot-password.html">I forgot my password</a>--}}
{{--            </p>--}}
{{--            <p class="mb-0">--}}
{{--                <a href="register.html" class="text-center">Register a new membership</a>--}}
{{--            </p>--}}
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="/giaodien/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="/giaodien/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="/giaodien/dist/js/adminlte.min.js"></script>
<script src="/giaodien/my_plugins/sweet-alert2/SweetAlert2.min.js"></script>
<script src="/giaodien/my_plugins/cookie/cookie.min.js"></script>
<script src="/giaodien/dist/js/function.js"></script>

<script>
    $('#inpDienThoai, #inpMatKhau').keypress(function(e) {
        if ($(this).hasClass('is-invalid')) {
            $(this).removeClass('is-invalid');
        }
        if (e.keyCode === 13) {
            $('#btnSubmit').click();
        }
    })

    $('#btnSubmit').click(() => {
        let dienthoai = $('#inpDienThoai').val().trim();
        let matkhau = $('#inpMatKhau').val().trim();
        let remember = $('#chkRemember')[0].checked;

        if (dienthoai === '') {
            showError('dienthoai','Tài khoản hoặc số điện thoại không được bỏ trống!');
            return false;
        }
        if (matkhau === '') {
            showError('matkhau','Mật khẩu không được bỏ trống!');
            return false;
        }

        sToast.loading('Đang kiểm tra thông tin tài khoản...');

        $.ajax({
            url: '/api/quan-ly/xac-thuc/dang-nhap',
            type: 'get',
            dataType: 'json',
            data: {
                dienthoai,
                matkhau,
                is_dangxuat: remember ? 1 : 0
            },
            success: function (result) {
                if (!result.succ) {
                    showError(result.type,result.noti);
                    Swal.close();
                }
                else {
                    Cookies.set('token', result.data.token, { expires: 7 });
                    localStorage.setItem('token',result.data.token);
                    if (!isUndefined(window.ReactNativeWebView)) {
                        window.ReactNativeWebView.postMessage(JSON.stringify({
                            type: 'thongbao-init',
                            topics: ['hailua_' + result.data.token,'hailua_' + result.data.chinhanh_id]
                        }));
                    }
                    Swal.update({title: 'Đang đăng nhập vào hệ thống...'});
                    setTimeout(() => {location.href = "{{ route('mobile.lichsu-diemdanh') }}"},300);
                }
            }
        });
    })

    function testAppMobile() {
        alert(localStorage.getItem('token'));
        // window.ReactNativeWebView.postMessage(JSON.stringify({type: 'download', url: 'https://hailua.center/qrcode-diemdanh/LaiVung.png'}));
    }

    function showError (type, noti) {
        if (type === 'dienthoai') {
            $('#lblErrorDienThoai').text(noti);
            $('#inpDienThoai').addClass('is-invalid').focus();
        }
        else {
            $('#lblErrorMatKhau').text(noti);
            $('#inpMatKhau').addClass('is-invalid').focus();
        }
    }
</script>
</body>
</html>
