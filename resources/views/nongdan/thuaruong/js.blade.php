<script>
    initDanhMuc();
    initActionThemMoi();
    let contents = [];

    function initDanhMuc() {
        showLoaderMobile();
        $.ajax({
            url: '/api/nong-dan/thua-ruong/danh-sach',
            type: 'get',
            dataType: 'json'
        }).done((results) => {
            $('#boxDanhSach .box-content').empty();
            results.forEach((value) => {
                addItem(value);
            });
        });
    }

    function addItem(value, isPrepend = false) {
        let catnhat_toado = isNull(value.toado) ? '<span class="text-primary ml-3 capnhat-toado">(cập nhật ngay)</span>' : '';
        let temp = $(
            '<li class="item">' +
            '   <div class="product-info">' +
            '       <div class="product-title text-primary ten">' + value.ten + '</div>' +
            '       <div class="product-description d-flex">' +
            '           <div class="d-flex product-label" style="min-width: 77px">' +
            '               <span>Mùa vụ</span>' +
            '               <span class="ml-auto">:</span>' +
            '           </div>' +
            '           <span class="ml-1 muavu">' + value.muavu + '</span>' +
            '       </div>' +
            '       <div class="product-description d-flex">' +
            '           <div class="d-flex product-label" style="min-width: 77px">' +
            '               <span>Diện tích</span>' +
            '               <span class="ml-auto">:</span>' +
            '           </div>' +
            '           <span class="ml-1 dientich">' + value.dientich + ' ha</span>' +
            '       </div>' +
            '       <div class="product-description d-flex">' +
            '           <div class="d-flex product-label" style="min-width: 77px">' +
            '               <span>Ngày sạ</span>' +
            '               <span class="ml-auto">:</span>' +
            '           </div>' +
            '           <span class="ml-1 ngaysa">' + doi_ngay(value.ngaysa) + '</span>' +
            '       </div>' +
            '       <div class="product-description d-flex">' +
            '           <div class="d-flex product-label" style="min-width: 77px">' +
            '               <span>Tọa độ</span>' +
            '               <span class="ml-auto">:</span>' +
            '           </div>' +
            '           <span class="ml-1 toado ' + (isNull(value.toado) ? 'text-danger' : 'text-success') + '">'
            + (isNull(value.toado) ? 'Chưa cập nhật' : 'Đã cập nhật') + '</span>' +
            catnhat_toado +
            '       </div>' +
            '       <div class="product-description d-flex">' +
            '           <div class="d-flex product-label" style="min-width: 77px">' +
            '               <span>Trạng thái</span>' +
            '               <span class="ml-auto">:</span>' +
            '           </div>' +
            '           <span class="ml-1 status ' + (value.status ? 'text-success' : 'text-danger') + '">'
            + (value.status ? 'Đang hoạt động' : 'Đã kết thúc') + '</span>' +
            '       </div>' +
            '       <div class="product-description">' +
            '           <span class="mr-1">Tình trạng hoàn thành:</span>' +
            '           <span class="text-info font-weight-bolder tinhtrang_hoanthanh">' + value.tinhtrang_hoanthanh + '</span>' +
            '           <span>/</span>' +
            '           <span class="font-weight-bolder mr-1 tongquytrinh">' + value.tongquytrinh + '</span>' +
            '           <span> quy trình</span>' +
            '       </div>' +
            '   </div>' +
            '</li>'
        );
        if (isPrepend) {
            $('#boxDanhSach .box-content').prepend(temp);
        }
        else {
            $('#boxDanhSach .box-content').append(temp);
        }
        if (isNull(value.toado)) {
            temp.find('.capnhat-toado').click(() => {
                capNhatToaDo(value,temp);
            })
        }
        contents.push(temp);

        temp.find('.ten').click(() => {
            layThongTin(value,temp);
        })
    }

    function initActionThemMoi() {
        $('#modalThemMoi input, #modalThemMoi textarea').on('input', function() {
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
            }
        })

        offEnterTextarea($('#modalThemMoi input, #modalThemMoi textarea'), () => {
            $('#modalThemMoi .btnSubmit').click();
        })

        $('#modalThemMoi .btnLayToaDo').click(() => {
            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    let _location = position.coords.latitude + ',' + position.coords.longitude;
                    $('#modalThemMoi .inpToaDo').val(_location);
                }, () => {
                    setTimeout(() => {sToast.toast(0,'Không lấy được tọa độ!')}, 10)
                });
            }
        })

        $('#modalThemMoi').on('shown.bs.modal', function() {
            $(this).find('.ten').focus();
        }).on('hidden.bs.modal', function () {
            $(this).find('.is-invalid').removeClass('is-invalid');
        });

        $('#modalThemMoi .btnSubmit').off('click').click(() => {
            let ten = $('#modalThemMoi .inpTen').val().trim();
            let dientich = parseFloat($('#modalThemMoi .inpDienTich').val());
            let ngaysa = $('#modalThemMoi .inpNgaySa').val();
            let muavu_id = $('#modalThemMoi .selMuaVu').val();
            let toado = $('#modalThemMoi .inpToaDo').val();
            let checked = true;
            if (ngaysa === '') {
                showError('ngaysa');
                checked = false;
            }
            if (isNaN(dientich) || dientich <= 0) {
                showError('dientich');
                checked = false;
            }
            if (ten === '') {
                showError('ten');
                checked = false;
            }
            if (muavu_id == null) {
                showError('muavu_id');
                checked = false;
            }
            if (!checked) {
                return false;
            }

            let ghichu = $('#modalThemMoi .inpGhiChu').val().trim();

            sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
            $.ajax({
                url: '/api/nong-dan/thua-ruong/them-moi',
                type: 'get',
                dataType: 'json',
                data: {
                    dientich,ngaysa,muavu_id,ghichu,ten,toado,nongdan_id: info.id
                }
            }).done((result) => {
                if (result.succ) {
                    $('#modalThemMoi input:not([type=date]), #modalThemThuaRuong textarea').val('').trigger('input');
                    autosize.update($('#modalThemThuaRuong textarea'));
                    addItem(result.data.model,true);
                    $('#modalThemMoi').modal('hide');
                }
                else if (!isUndefined(result.type)) {
                    showError(result.type);
                }
            });
        });

        function showError(type, erro = '') {
            let inputs = {
                ten: $('#modalThemMoi .inpTen'),
                dientich: $('#modalThemMoi .inpDienTich'),
                ngaysa: $('#modalThemMoi .inpNgaySa'),
                muavu_id: $('#modalThemMoi .selMuaVu'),
            }
            if (erro !== '') {
                $(inputs[type].parent()).find('span.error').text(erro);
            }
            inputs[type].addClass('is-invalid');
            inputs[type].focus();
        }
    }

    function layThongTin(data, container) {
        $('#modalThongTin .ten p').text(data.ten);
        $('#modalThongTin .muavu p').text(data.muavu);
        $('#modalThongTin .dientich p').text(isNull(data.dientich) ? 'Chưa cập nhật' : (data.dientich + ' ha'));
        $('#modalThongTin .ngaysa p').text(doi_ngay(data.ngaysa));
        $('#modalThongTin .toado p').html(isNull(data.toado) ? '<span class="text-danger">Chưa cập nhật</span>' : '<span class="text-success">Đã cập nhật</span>');
        $('#modalThongTin .status p').html(data.status ? '<span class="text-success">Đang hoạt động</span>' : '<span class="text-danger">Đã kết thúc</span>');
        $('#modalThongTin .tinhtrang_hoanthanh p').html('<span class="text-info font-weight-bolder">' + data.tinhtrang_hoanthanh
            + '</span>/' + '<span class="font-weight-bolder">' + data.tongquytrinh + '</span>' + ' quy trình');

        $('#modalThongTin .delete').off('click').click(() => {
            sToast.confirm('Xác nhận xóa thông tin thửa ruộng?','',
                (confirmed) => {
                    if (confirmed.isConfirmed) {
                        sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...');
                        $.ajax({
                            url: '/api/nong-dan/thua-ruong/xoa',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                id: data.id
                            }
                        }).done((result) => {
                            if (result.succ) {
                                $('#modalThongTin').modal('hide');
                                initDanhMuc();
                            }
                        });
                    }
            })
        })

        $.each($('#modalThongTin .field'), function(key, value) {
            let field = $(value).attr('data-field');
            let ten = $(value).attr('data-title');
            $(value).find('i:not(.action-toado)').off('click').click(() => {
                let onSubmit = () => {
                    let value = $('#modalInput .value').val();
                    if (field === 'ngaysa') {
                        value = $('#boxInputDate').datetimepicker('viewDate').format('YYYY-MM-DD');
                        if (isNull(value)) {
                            showErrorModalInput('Ngày sạ không được bỏ trống!');
                            return false;
                        }
                    }
                    if (field === 'dientich') {
                        value = parseFloat(value);
                        if (isNaN(value) || value <= 0) {
                            showErrorModalInput('Diện tích không hợp lệ!');
                            return false;
                        }
                    }
                    if (field === 'muavu' && value == null) {
                        showErrorModalInput('Bạn chưa chọn mùa vụ!');
                        return false;
                    }
                    if (field === 'ten') {
                        value = value.trim();
                        if (value === '') {
                            showErrorModalInput('Tên thửa ruộng không được bỏ trống!');
                            return false;
                        }
                    }
                    sToast.confirm('Xác nhận cập nhật ' + ten + '?','',
                        (result) => {
                            if (result.isConfirmed) {
                                sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                                $.ajax({
                                    url: '/api/nong-dan/thua-ruong/cap-nhat',
                                    type: 'get',
                                    dataType: 'json',
                                    data: {
                                        id: data.id,
                                        field: field === 'muavu' ? 'muavu_id' : field, value
                                    }
                                }).done((result) => {
                                    if (result.succ) {
                                        $('#modalInput').modal('hide');
                                        let textChanged = result.data.model[field];
                                        data[field] = textChanged;
                                        if (field === 'dientich') {
                                            textChanged += ' ha';
                                        }
                                        if (field === 'ngaysa') {
                                            textChanged = doi_ngay(textChanged);
                                        }
                                        container.find('.' + field).text(textChanged);
                                        if (field === 'muavu') {
                                            data.muavu_id = result.data.model.muavu_id;
                                            data.tongquytrinh = result.data.model.tongquytrinh;
                                            data.tinhtrang_hoanthanh = result.data.model.tinhtrang_hoanthanh;
                                            container.find('.tongquytrinh').text(result.data.model.tongquytrinh);
                                            container.find('.tinhtrang_hoanthanh').text(result.data.model.tinhtrang_hoanthanh);
                                        }
                                        layThongTin(data,container);
                                    }
                                    else if (!isUndefined(result.erro)) {
                                        showErrorModalInput(result.erro);
                                    }
                                });
                            }
                        });
                }
                if (field === 'ten') {
                    mInput(data.ten,data.ten,true).text(ten,ten + '...',onSubmit);
                }
                if (field === 'dientich') {
                    mInput(data.ten,data.dientich,true).number(ten,ten + '...',onSubmit);
                }
                if (field === 'ngaysa') {
                    mInput(data.ten,data.ngaysa,true).date(ten,'Nhập ' + ten.toLowerCase() + '...',onSubmit);
                }
                if (field === 'muavu') {
                    mInput(data.ten,data.muavu_id,true).select2(ten,'',JSON.parse('{!! str_replace("'","\'",$muavus) !!}'),true,onSubmit);
                }
            })
            $(value).find('i.action-toado').off('click').click(() => {
                capNhatToaDo(data,container);
            })
        });

        $('#modalThongTin').modal('show');
    }

    function capNhatToaDo(data, container) {
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(function (position) {
                let _location = position.coords.latitude + ',' + position.coords.longitude;
                sToast.confirm('Xác nhận cập nhật tọa độ thửa ruộng?','',
                    (result) => {
                        if (result.isConfirmed) {
                            sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                            $.ajax({
                                url: '/api/nong-dan/thua-ruong/cap-nhat',
                                type: 'get',
                                dataType: 'json',
                                data: {
                                    id: data.id,
                                    field: 'toado', value: _location
                                }
                            }).done((result) => {
                                if (result.succ) {
                                    data.toado = result.data.model.toado;
                                    container.find('.toado').removeClass('text-danger').addClass('text-success').text('Đã cập nhật')
                                    if ($('#modalThongTin').hasClass('show')) {
                                        layThongTin(data,container);
                                    }
                                }
                            });
                        }
                    });
            }, () => {
                setTimeout(() => {sToast.toast(0,'Không lấy được tọa độ. Không thể cập nhật vị trí!')}, 10)
            });
        }
    }
</script>
