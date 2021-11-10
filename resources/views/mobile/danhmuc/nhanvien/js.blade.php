<script>
    initDanhMuc();
    let contents = [];
    {{--let chinhanhs = JSON.parse('{!! json_encode($chinhanhs) !!}');--}}
    let chucvus = JSON.parse('{!! json_encode($chucvus) !!}');

    function initDanhMuc() {
        showLoaderMobile();
        $.ajax({
            url: '/api/mobile/danh-muc/nhan-vien/danh-sach',
            type: 'get',
            dataType: 'json'
        }).done((results) => {
            $('#boxDanhSach .box-content').empty();
            results.forEach((value) => {
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
                            convertToSlug(value.find('.chucvu').text()).indexOf(input) !== -1) {
                            value.removeClass('d-none');
                        }
                    })
                }, $(this).val() !== '' ? 300 : 0);
            }).val('');
            $('#boxDanhSach .box-search .ui.input i').click(() => {
                $('#boxDanhSach .input-search').val('').trigger('input');
            });
        });
    }

    function addItem(value, isPrepend = false) {
        // let avatar = isNull(value.avatar) ? '' : JSON.parse(value.avatar).url;
        let avatar = '/logo.jpg';
        let temp = $(
            '<li class="item">' +
            '   <div class="product-img">' +
            '       <img src="' + avatar + '" alt="Product Image" class="img-size-50">' +
            '   </div>' +
            '   <div class="product-info">' +
            '       <div class="product-title text-muted ten">' + value.ten + '</div>' +
            '       <div class="product-description">' +
            '           <span class="text-info dienthoai">' + value.dienthoai + '</span>' +
            '           <span class="px-2">|</span>' +
            '           <span class="chucvu">' + (isNull(value.chucvu) ? 'Chưa có' : chucvus[value.chucvu]) + '</span>' +
            '       </div>' +
            '   </div>' +
            '</li>'
        );
        temp.find('img').on('error', function () {
            $(this).attr('src','/logo.jpg');
        });
        temp.find('.dienthoai').click(() => {
            window.ReactNativeWebView.postMessage(JSON.stringify({type: 'call', sodienthoai: value.dienthoai}));
        })
        // clickViewerImage(temp.find('img'));
        if (isPrepend) {
            $('#boxDanhSach .box-content').prepend(temp);
        }
        else {
            $('#boxDanhSach .box-content').append(temp);
        }
        contents.push(temp);

{{--        @if($quyen->thongtin)--}}
{{--        temp.find('.ten').click(() => {--}}
{{--            layThongTin(value,temp);--}}
{{--        })--}}
{{--        @endif--}}
    }

{{--    @if($quyen->chinhsua)--}}
{{--    $('#modalThemMoi').on('shown.bs.modal', function() {--}}
{{--        $(this).find('.ten').select().focus();--}}
{{--    });--}}

{{--    $('#modalThemMoi .submit').click(() => {--}}
{{--        let ten = $('#modalThemMoi .ten').val().trim().toUpperCase();--}}
{{--        let dienthoai = $('#modalThemMoi .dienthoai').val().trim();--}}
{{--        let chinhanh_id = $('#modalThemMoi .chinhanh').val();--}}
{{--        let loai = $('#modalThemMoi .chucvu').val();--}}
{{--        let ngaysinh = $('#modalThemMoi .ngaysinh').val();--}}
{{--        let email = $('#modalThemMoi .email').val();--}}
{{--        let diachi = $('#modalThemMoi .diachi').val().trim();--}}
{{--        let ghichu = $('#modalThemMoi .ghichu').val().trim();--}}
{{--        let cmnd = $('#modalThemMoi .cmnd').val();--}}
{{--        let chuyennganh = $('#modalThemMoi .chuyennganh').val();--}}

{{--        if (ten === '' || dienthoai === '') {--}}
{{--            toastr.error((ten === '' ? 'Tên nhân viên' : 'Số điện thoại') + ' không được bỏ trống!');--}}
{{--            $('#modalThemMoi .' + (ten === '' ? 'ten' : 'dienthoai')).focus();--}}
{{--            return false;--}}
{{--        }--}}

{{--        sToast.confirm('Xác nhận thêm thông tin nhân viên mới?').fire()--}}
{{--            .then((confirmed) => {--}}
{{--                if (confirmed.isConfirmed) {--}}
{{--                    sToast.loading();--}}
{{--                    $.ajax({--}}
{{--                        url: '/api/danh-muc/nhan-vien/them-moi',--}}
{{--                        type: 'get',--}}
{{--                        dataType: 'json',--}}
{{--                        data: {--}}
{{--                            token: _token,--}}
{{--                            ten,dienthoai,ngaysinh,chinhanh_id,diachi,ghichu,loai,chuyennganh,email,cmnd--}}
{{--                        }--}}
{{--                    }).done((result) => {--}}
{{--                        if (result.tt.s === 1) {--}}
{{--                            $('#modalThemMoi input, #modalThemMoi textarea').val('');--}}
{{--                            $('#modalThemMoi').modal('hide');--}}
{{--                            addItem(result.dl.item,true);--}}
{{--                        }--}}
{{--                        else {--}}
{{--                            if (!isUndefined(result.dl)) {--}}
{{--                                if (!isUndefined(result.dl.field)) {--}}
{{--                                    $('#modalThemMoi .' + result.dl.field).select().focus();--}}
{{--                                }--}}
{{--                            }--}}
{{--                        }--}}
{{--                    });--}}
{{--                }--}}
{{--            })--}}
{{--    });--}}
{{--    @endif--}}

{{--    @if($quyen->thongtin)--}}
{{--    function layThongTin(data, container) {--}}
{{--        $('#modalThongTin').modal('show');--}}
{{--        $('#modalThongTin .ten p').text(data.ten);--}}
{{--        $('#modalThongTin .ma p').text(data.ma);--}}
{{--        $('#modalThongTin .dienthoai p').text(!isNull(data.dienthoai) && !isUndefined(data.dienthoai) ? data.dienthoai : '----------');--}}
{{--        $('#modalThongTin .chinhanh p').text(!isNull(data.chinhanh_id) ? chinhanhs[data.chinhanh_id] : '----------');--}}
{{--        $('#modalThongTin .diachi p').text(!isNull(data.diachi) ? data.diachi : '----------');--}}
{{--        $('#modalThongTin .ngaysinh p').text(!isNull(data.ngaysinh) ? doi_ngay(data.ngaysinh) : '----------');--}}
{{--        $('#modalThongTin .chuyennganh p').text(!isNull(data.chuyennganh) ? data.chuyennganh : '----------');--}}
{{--        $('#modalThongTin .chucvu p').text(!isNull(data.loai) ? chucvus[data.loai] : '----------');--}}
{{--        $('#modalThongTin .email p').text(!isNull(data.email) ? data.email : '----------');--}}
{{--        $('#modalThongTin .cmnd p').text(!isNull(data.cmnd) ? data.cmnd : '----------');--}}
{{--        $('#modalThongTin .ghichu p').text(!isNull(data.ghichu) ? data.ghichu : '----------');--}}
{{--        $('#modalThongTin .ngaycap p').text(isNull(data.ngaycap) ? '----------' : doi_ngay(data.ngaycap));--}}
{{--        $('#modalThongTin .noicap p').text(isNull(data.noicap) ? '----------' : data.noicap);--}}
{{--        $('#modalThongTin .anh_mattruoc p').html(isNull(data.anh_mattruoc) ? '----------' :--}}
{{--            ('<img src="' + data.anh_mattruoc + '" class="c-pointer" style="max-width: 100px; max-height: 100px">'));--}}
{{--        $('#modalThongTin .anh_matsau p').html(isNull(data.anh_matsau) ? '----------' :--}}
{{--            ('<img src="' + data.anh_matsau + '" class="c-pointer" style="max-width: 100px; max-height: 100px">'));--}}

{{--        @if($quyen->xoakhoiphuc)--}}
{{--        $('#modalThongTin .delete').off('click').click(() => {--}}
{{--            sToast.confirm('Xác nhận xóa thông tin nhân viên?').fire()--}}
{{--                .then((confirmed) => {--}}
{{--                    if (confirmed.isConfirmed) {--}}
{{--                        sToast.loading();--}}
{{--                        $.ajax({--}}
{{--                            url: '/api/danh-muc/nhan-vien/xoa',--}}
{{--                            type: 'get',--}}
{{--                            dataType: 'json',--}}
{{--                            data: {--}}
{{--                                token: _token,--}}
{{--                                id: data.id--}}
{{--                            }--}}
{{--                        }).done((result) => {--}}
{{--                            if (result.tt.s === 1) {--}}
{{--                                $('#modalThongTin').modal('hide');--}}
{{--                                initDanhMuc();--}}
{{--                            }--}}
{{--                        });--}}
{{--                    }--}}
{{--                })--}}
{{--        })--}}
{{--        @endif--}}

{{--        @if($quyen->chinhsua)--}}
{{--        $.each($('#modalThongTin .field'), function(key, value) {--}}
{{--            let field = $(value).attr('data-field');--}}
{{--            $(value).find('i').off('click').click(() => {--}}
{{--                let onSubmit = () => {--}}
{{--                    let input = $('#modalInput .value').val().trim();--}}
{{--                    if (['ten','dienthoai'].indexOf(field) !== -1 && input === '') {--}}
{{--                        toastr.error($(value).find('strong').text() + ' không được bỏ trống!')--}}
{{--                        $('#modalInput .value').focus();--}}
{{--                    }--}}
{{--                    if (['ten'].indexOf(field) !== -1) {--}}
{{--                        input = input.toUpperCase();--}}
{{--                    }--}}

{{--                    sToast.confirm('Xác nhận cập nhật thông tin nhân viên?').fire()--}}
{{--                        .then((confirmed) => {--}}
{{--                            if (confirmed.isConfirmed) {--}}
{{--                                sToast.loading();--}}
{{--                                $.ajax({--}}
{{--                                    url: '/api/danh-muc/nhan-vien/cap-nhat',--}}
{{--                                    type: 'get',--}}
{{--                                    dataType: 'json',--}}
{{--                                    data: {--}}
{{--                                        token: _token,--}}
{{--                                        id: data.id,--}}
{{--                                        field,--}}
{{--                                        value: input--}}
{{--                                    }--}}
{{--                                }).done((result) => {--}}
{{--                                    if (result.tt.s === 1) {--}}
{{--                                        $('#modalInput').modal('hide');--}}
{{--                                        data[field] = result.dl.item[field];--}}
{{--                                        let _value = result.dl.item[field];--}}
{{--                                        if (field === 'loai') {--}}
{{--                                            _value = chucvus[_value];--}}
{{--                                        }--}}
{{--                                        if (field === 'chinhanh_id') {--}}
{{--                                            _value = chinhanhs[_value];--}}
{{--                                        }--}}
{{--                                        $(value).find('p').text(_value);--}}
{{--                                        if (['ten','dienthoai','loai'].indexOf(field) !== -1) {--}}
{{--                                            container.find('.' + (field === 'loai' ? 'chucvu' : field)).text(_value);--}}
{{--                                        }--}}
{{--                                        layThongTin(data);--}}
{{--                                    }--}}
{{--                                    else {--}}
{{--                                        $('#modalInput .value').select().focus();--}}
{{--                                    }--}}
{{--                                });--}}
{{--                            }--}}
{{--                        })--}}
{{--                }--}}
{{--                if (field === 'ghichu') {--}}
{{--                    mInput(data.ten,data[field])--}}
{{--                        .textarea($(value).find('strong').text(),'Nhập '--}}
{{--                            + $(value).find('strong').text().trim().toLowerCase() + '...',onSubmit);--}}
{{--                }--}}
{{--                else if (field === 'ten' || field === 'dienthoai') {--}}
{{--                    mInput(data.ten,data[field])--}}
{{--                        .text($(value).find('strong').text(),'Nhập '--}}
{{--                            + $(value).find('strong').text().trim().toLowerCase() + '...',onSubmit);--}}
{{--                }--}}
{{--                else {--}}
{{--                    let options = [];--}}
{{--                    $.each(field === 'loai' ? chucvus : chinhanhs, function(id, text) {--}}
{{--                        options.push({id,text});--}}
{{--                    })--}}
{{--                    mInput(data.ten,data[field]).select(options,$(value).find('strong').text(),'',onSubmit);--}}
{{--                }--}}
{{--            })--}}
{{--        });--}}
{{--        @endif--}}
{{--    }--}}
{{--    @endif--}}
</script>
