<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/select2.min.css">
    <link rel="stylesheet" href="css/tabulator.min.css">
    <link rel="stylesheet" href="css/toastr.min.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/tabulator.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/autosize.min.js"></script>
    <script src="js/toastr.min.js"></script>
    <script src="js/swal2.min.js"></script>
    <script src="js/xlsx.min.js"></script>

    <title>Tool Admin</title>

    <style>
        .container {
            width: 100vw !important;
            max-width: unset !important;
            padding: 0.5rem !important;
        }
        #textError {
            display: none;
        }
        #textError.active {
            display: block;
        }

        .select2-container--default .select2-selection--single {
            outline: unset;
            height: 38px !important;
        }
        .select2-container .select2-selection--single .select2-selection__rendered {
            /*padding-left: unset;*/
            /*margin-top: -.5rem !important;*/
            line-height: 38px;
        }
        .select2-container {
            width: 100% !important;
        }
        .select2-dropdown.select2-dropdown--below {
            z-index: 1061;
        }
        .modal-footer button {
            font-weight: bolder;
        }
        .select2-search input {
            outline: unset;
        }
    </style>
</head>
<body>
<div class="container">
    <textarea id="query" min-rows="2" rows="2" class="form-control mt-2" style="font-size: 12px"></textarea>
    <div class="my-2 d-flex">
        <button class="btn btn-secondary" id="btnSelect">Select</button>
        <button class="btn btn-success ms-1" id="btnDownload">Download</button>
        <button class="btn btn-dark ms-1" id="btnCopy">Copy</button>
        <div class="ms-auto d-flex">
            <button type="button" id="btnDiemDanh" class="btn btn-secondary ms-1" data-bs-toggle="modal" data-bs-target="#modalDiemDanh">Điểm Danh</button>
            <button type="button" id="btnReset" class="btn btn-secondary ms-1" data-bs-toggle="modal" data-bs-target="#modalReset">Reset Điểm Danh</button>
        </div>
    </div>
    <textarea style="font-size: 12px" rows="1" id="txtCopy" class="form-control my-2" readonly></textarea>
    <div id="table"></div>
</div>

<div class="modal fade" id="modalDiemDanh">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Điểm danh</h5>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label mb-0 fw-bolder">Nhân viên</label>
                    <select class="nhanvien form-control"></select>
                </div>
                <div class="mb-2">
                    <label class="form-label mb-0 fw-bolder">Loại</label>
                    <select class="loai form-control">
                        <option value="batdau">Bắt đầu</option>
                        <option value="ketthuc">Kết thúc</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label mb-0 fw-bolder">Chức vụ</label>
                    <select class="chucvu form-control">
                        <option value="banhang">Bán hàng</option>
                        <option value="vanphong">Văn phòng</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label mb-0 fw-bolder">Ngày</label>
                    <input type="date" class="form-control ngay" value="<?= date('Y-m-d') ?>">
                </div>
                <div>
                    <label class="form-label mb-0 fw-bolder">Thời gian</label>
                    <input type="text" class="form-control thoigian" placeholder="Nhập thời gian...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Thoát</button>
                <button type="button" class="btn btn-primary submit">Điểm Danh</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalReset">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Reset/Xóa điểm danh</h5>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label mb-0 fw-bolder">Nhân viên</label>
                    <select class="nhanvien form-control"></select>
                </div>
                <div class="mb-2">
                    <label class="form-label mb-0 fw-bolder">Ngày</label>
                    <input type="date" class="form-control ngay" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="mb-2">
                    <label class="form-label mb-0 fw-bolder">Thời gian bắt đầu</label>
                    <input type="text" class="form-control batdau" placeholder="Thời gian bắt đầu..." readonly>
                </div>
                <div class="mb-2">
                    <label class="form-label mb-0 fw-bolder">Thời gian kết thúc</label>
                    <input type="text" class="form-control ketthuc" placeholder="Thời gian kết thúc..." readonly>
                </div>
                <div>
                    <label class="form-label mb-0 fw-bolder">Ngày công</label>
                    <input type="text" class="form-control ngaycong" placeholder="Ngày công..." readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Thoát</button>
                <button type="button" class="btn btn-warning reset">Reset</button>
                <button type="button" class="btn btn-danger xoa">Xóa</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCapNhat">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cập nhật dữ liệu</h5>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <label class="form-label mb-0 fw-bolder">Chọn bảng</label>
                    <select class="table form-control"></select>
                </div>
                <div class="mb-2">
                    <label class="form-label mb-0 fw-bolder">ID</label>
                    <input type="text" class="form-control id" readonly>
                </div>
                <div class="form-capnhat">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Thoát</button>
                <button type="button" class="btn btn-primary reset">Xác Nhận</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDangNhap" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Đăng nhập</h5>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label mb-0 fw-bolder">Số điện thoại</label>
                    <input type="text" class="form-control" id="dienthoai" placeholder="Nhập số điện thoại...">
                </div>
                <div>
                    <label class="form-label mb-0 fw-bolder">Mật khẩu</label>
                    <input type="password" class="form-control" id="matkhau" placeholder="Nhập mật khẩu...">
                    <div id="textError" class="form-text text-danger">Tài khoản không tồn tại!</div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btnDangNhap" type="button" class="btn btn-primary">Đăng Nhập</button>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    let mbm = localStorage.getItem('mbm');

    let table = '';

    $('input').attr('autocomplete','off');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (result) {
            if (result.tt.b == 0) {
                $('#modalDangNhap').modal('show');
            }
        },
        error: function (e) {
            if (e.status !== 0) {
                console.log(e);
            }
        }
    });

    $('#modalDiemDanh, #modalReset').on('show.bs.modal', function () {
        $(this).find('.nhanvien').val(null).trigger('change');
    })

    autosize($('#select'));

    $('#modalDiemDanh .loai, #modalDiemDanh .chucvu').change(() => {
        let gio = $('#modalDiemDanh .chucvu').val() === 'vanphong' ? '07' : '06';
        let batdau = gio + ':2' + Math.floor(Math.random() * 10) + ':' + Math.floor(Math.random() * 5) + Math.floor(Math.random() * 10);
        let ketthuc = '17:' + Math.floor(Math.random() * 3) + Math.floor(Math.random() * 10) + ':' + Math.floor(Math.random() * 5) + Math.floor(Math.random() * 10);
        if ($('#modalDiemDanh .loai').val() === 'batdau') {
            $('#modalDiemDanh .thoigian').val(batdau);
        }
        else {
            $('#modalDiemDanh .thoigian').val(ketthuc);
        }
    });
    $('#modalDiemDanh .loai').change();

    $('#modalDiemDanh .submit').click(function() {
        let nhanvien_id = $('#modalDiemDanh .nhanvien').val();
        if (nhanvien_id == null) {
            toastr.error('Bạn chưa chọn nhân viên!');
            return false;
        }
        let ngay = $('#modalDiemDanh .ngay').val();
        let thoigian = ngay + ' ' + $('#modalDiemDanh .thoigian').val();
        let data = {
            mbm,
            ngay,
            nhanvien_id
        }
        let url = "https://api-banhang.hailua.center/api/bpbh-diemdanh-";
        if ($('#modalDiemDanh .loai').val() === 'batdau') {
            url += 'batdau';
            data.tg_batdau = thoigian;
        }
        else {
            url += 'ketthuc';
            data.tg_ketthuc = thoigian;
        }
        $(this).attr('disabled','');
        $.ajax({
            url,
            dataType: 'json',
            type: 'get',
            data
        }).done((result) => {
            $('#modalDiemDanh .submit').attr('disabled',null);
            if (result.tt.s === 'success') {
                toastr.success(result.tn);
                $('#modalDiemDanh .loai').change();
            }
            else {
                toastr.error(result.tn);
            }
        });
    });

    $('#modalReset .nhanvien').change(() => {init_info()});
    $('#modalReset .ngay').focusout(() => {
        init_info();
    })

    $('#modalReset .reset').click(function () {
        let nhanvien_id = $('#modalReset .nhanvien').val();
        if (nhanvien_id == null) {
            toastr.error('Bạn chưa chọn nhân viên!');
            return false;
        }
        let ngay = $('#modalReset .ngay').val();
        let tg_batdau = $('#modalReset .batdau').val();
        let tg_ketthuc = $('#modalReset .ketthuc').val();
        if (tg_batdau === '') {
            toastr.error('Dữ liệu rỗng!');
            return false;
        }
        Swal.fire({
            title: 'Xác nhận reset điểm danh?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xác Nhận',
            cancelButtonText: 'Thoát'
        }).then((confirmed) => {
            if (confirmed.isConfirmed) {
                $(this).attr('disabled','');
                $.ajax({
                    url: "https://api-banhang.hailua.center/api/bpbh-diemdanh-reset-confirm",
                    dataType: 'json',
                    type: 'get',
                    data: {
                        mbm: mbm,
                        ngay,
                        nhanvien_id,
                        tg_ketthuc,
                        tg_batdau
                    },
                }).done((result) => {
                    $('#modalReset .reset').attr('disabled',null);
                    if (result.tt.s === 'success') {
                        toastr.success(result.tn);
                        $('#modalReset .nhanvien').val(null).trigger('change');
                    }
                    else {
                        toastr.error(result.tn);
                    }
                });
            }
        })
    });

    $('#modalReset .xoa').click(function () {
        let nhanvien_id = $('#modalReset .nhanvien').val();
        if (nhanvien_id == null) {
            toastr.error('Bạn chưa chọn nhân viên!');
            return false;
        }
        let ngay = $('#modalReset .ngay').val();
        Swal.fire({
            title: 'Xác nhận xóa điểm danh?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xác Nhận',
            cancelButtonText: 'Thoát'
        }).then((confirmed) => {
            if (confirmed.isConfirmed) {
                $(this).attr('disabled','');
                $.ajax({
                    url: "https://api-banhang.hailua.center/api/bpbh-diemdanh-huy-test",
                    dataType: 'json',
                    type: 'get',
                    data: {
                        mbm: mbm,
                        ngay,
                        nhanvien_id
                    },
                }).done((result) => {
                    $('#modalReset .xoa').attr('disabled',null);
                    if (result.tt.s === 'success') {
                        toastr.success(result.tn);
                        $('#modalReset .nhanvien').val(null).trigger('change');
                    }
                    else {
                        toastr.error(result.tn);
                    }
                });
            }
        })
    });

    $('#btnSelect').click(function () {
        $(this).attr('disabled','');
        if (table !== '') {
            table.setColumns([]);
            table.setData([]);
        }
        $.ajax({
            url: 'https://api-cskh.hailua.center/api/dev-tool-lay-dulieu',
            type: 'post',
            dataType: 'json',
            data: {
                mbm: mbm,
                query: $('#query').val().trim()
            },
            error: function (e) {
                toastr.error('Câu lệnh không hợp lệ!');
                $('#btnSelect').attr('disabled',null);
            }
        }).done((result) => {
            $('#btnSelect').attr('disabled',null);
            if (result.tt.s === 'error') {
                toastr.error(result.tn);
                return false;
            }
            let columns = [];
            columns.push({
                title: 'STT',
                field: 'stt',
                headerSort: false,
                width: 40
            });
            let data = [];
            $.each(result.dl, function (stt, value) {
                data[stt] = [];
                data[stt]['stt'] = stt + 1;
                $.each(value, function (key, value2) {
                    if (stt === 0) {
                        let _data = {
                            title: key,
                            field: key,
                            headerSort: false,
                            width: 120
                        };
                        columns.push(_data);
                    }
                    data[stt][key] = value2;
                });
            });
            table = new Tabulator('#table', {
                columns: columns,
                pagination:"local",
                paginationSize:10,
                autoResize: false,
                cellClick: function (e, cell) {
                    $('#txtCopy').val(cell.getValue());
                    let offset = $('#txtCopy')[0].offsetHeight - $('#txtCopy')[0].clientHeight;
                    $('#txtCopy').css('height', 'auto').css('height', $('#txtCopy')[0].scrollHeight + offset);
                }
            });
            table.setData(data);
        });
    });

    $('#btnDownload').click(() => {
        if (table !== '') {
            Swal.fire({
                title: 'Vui lòng nhập tên bảng.',
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Xác Nhận',
                cancelButtonText: 'Thoát'
            }).then((confirmed) => {
                if (confirmed.isConfirmed) {
                    table.download('xlsx',confirmed.value.trim() + '.xlsx');
                }
            })
        }
    });

    $('#btnCopy').click(() => {
        if ($('#txtCopy').val().trim() !== '') {
            copyToClipboard($('#txtCopy').val().trim());
        }
    })

    $(function() {
        if (mbm == null) {
            $('#modalDangNhap').modal('show');
        }
        else {
            $.ajax({
                url: 'https://api-cskh.hailua.center/api/dev-tool-check-mbm',
                type: 'get',
                dataType: 'json',
                data: {
                    mbm
                }
            });
        }

        layDSNhanVien();
        layDSBang();
    });

    function layDSNhanVien() {
        $.ajax({
            url: 'https://api-cskh.hailua.center/api/dev-tool-danh-sach-nhan-vien',
            type: 'get',
            dataType: 'json',
            data: {
                mbm
            }
        }).done((result) => {
            $('#modalDiemDanh .nhanvien, #modalReset .nhanvien').select2({
                data: result.dl,
                allowClear: true,
                placeholder: 'Chọn nhân viên...'
            }).val(null).trigger('change').on('select2:open', () => {
                $('.select2-search input').get(0).focus();
            });
        });
    }

    function layDSBang() {
        $.ajax({
            url: 'https://api-cskh.hailua.center/api/dev-tool-danh-sach-bang',
            type: 'get',
            dataType: 'json',
            data: {
                mbm
            }
        }).done((result) => {
            $('#modalCapNhat .table').select2({
                data: result.dl,
                allowClear: true,
                placeholder: 'Chọn bảng...'
            }).val(null).trigger('change').on('select2:open', () => {
                $('.select2-search input').get(0).focus();
            });
        });
    }

    $('#btnDangNhap').click(function () {
        let dienthoai = $('#dienthoai').val().trim();
        let matkhau = $('#matkhau').val();

        if (dienthoai === '') {
            $('#textError').addClass('active').text('Bạn chưa nhập tài khoản!');
        }
        if (matkhau === '') {
            $('#textError').addClass('active').text('Bạn chưa nhập mật khẩu!');
        }

        $('#textError').removeClass('active');
        $(this).attr('disabled','');

        $.ajax({
            url: 'https://automatic.hailua.center/xac_thuc/tai_khoan',
            type: 'get',
            dataType: 'json',
            data: {
                a: dienthoai,
                b: matkhau
            }
        }).done((result) => {
            $('#btnDangNhap').attr('disabled',null);
            if (result.tt.s === 'success') {
                localStorage.setItem('mbm',result.dl.thongtin.mbm);
                mbm = result.dl.thongtin.mbm;
                $('#modalDangNhap').modal('hide');
                layDSNhanVien();
                layDSBang();
            }
            else {
                $('#textError').addClass('active').text(result.tn);
            }
        });
    })

    function init_info() {
        $('#modalReset .batdau').val('');
        $('#modalReset .ketthuc').val('');
        $('#modalReset .ngaycong').val('');
        let ngay = $('#modalReset .ngay').val();
        let nhanvien_id = $('#modalReset .nhanvien').val();
        if (nhanvien_id == null) {
            return false;
        }
        $.ajax({
            url: "https://api-cskh.hailua.center/api/tool-xem-diemdanh",
            dataType: 'json',
            type: 'get',
            data: {
                mbm, ngay, nhanvien_id
            }
        }).done((result) => {
            if (result.dl.length > 0) {
                let data = result.dl[0];
                $('#modalReset .batdau').val(data.tg_batdau);
                $('#modalReset .ketthuc').val(data.tg_ketthuc);
                $('#modalReset .ngaycong').val(data.diem);
            }
            else {
                toastr.error('Không tìm thấy dữ liệu!');
            }
        });
    }

    function copyToClipboard(text) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(text).select();
        document.execCommand("copy");
        $temp.remove();
        toastr.success('Đã copy vào clipboard.');
    }
</script>
</html>