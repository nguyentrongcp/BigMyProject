<script>
    initDanhMuc();

    function initDanhMuc() {
        $('#container').empty();
        sToast.loading('Đang tải dữ liệu. Vui lòng chờ...')
        $.ajax({
            url: '/api/nong-dan/quy-trinh/danhsach-hientai',
            type: 'get',
            dataType: 'json'
        }).done((result) => {
            if (result.data.danhsach.length === 0) {
                $('#container').append('' +
                    '<div class="font-size-mobile card card-outline card-warning">' +
                    '   <div class="card-header">' +
                    '       <div class="card-title text-center w-100">' +
                    '           <h5 class="mb-1 text-secondary">Bạn chưa có quy trình nào trong hôm nay!</h5>' +
                    '           <a href="/nong-dan/quy-trinh">Xem toàn bộ quy trình của bạn.</a>' +
                    '       </div>' +
                    '   </div>' +
                    '</div>');
            }
            else {
                let colors = ['info','purple','primary','success'];
                result.data.danhsach.forEach((thuaruong, stt) => {
                    let timeline = $('<div class="timeline"></div>');
                    if (stt === result.data.danhsach.length - 1) {
                        timeline.addClass('mb-0');
                    }
                    let boxTitle = $('' +
                        '<div class="font-size-mobile card card-outline card-' + colors[stt] + '">' +
                        '   <div class="card-header p-1">' +
                        '       <div class="card-title text-center w-100">' +
                        '           <h5 class="mb-0 text-' + colors[stt] + ' font-weight-bolder">' + thuaruong.ten + '</h5>' +
                        '           <span class="text-secondary">Sạ ngày <strong>' + doi_ngay(thuaruong.ngaysa)
                        + '</strong> (<strong>' + thuaruong.songay + '</strong> ngày)</span>' +
                        '       </div>' +
                        '   </div>' +
                        '</div>');
                    if (thuaruong.toado == null) {
                        let btnCapNhatToaDo = $('' +
                            '<span class="btn btn-sm font-size-btn-sm-mobile btn-danger mt-2">' +
                            'Chưa cập nhật vị trí! Nhấn cập nhật ngay!!!' +
                            '</span>');
                        boxTitle.find('.card-title').append(btnCapNhatToaDo);
                        btnCapNhatToaDo.click(() => {
                            capNhatToaDo(thuaruong,btnCapNhatToaDo);
                        })
                    }
                    $('#container').append(boxTitle).append(timeline);
                    addItems(timeline,thuaruong);
                });
                if ('{{ $giaidoan_id }}' !== '') {
                    $('#container').animate({scrollTop: $('.timeline .time-label[data-id={{ $giaidoan_id }}]').offset().top - 65}, 500);
                }
            }
        });
    }

    function addItems(timeline, thuaruong) {
        thuaruong.giaidoans.forEach((giaidoan) => {
            let boxHeader = $('' +
                '<div>' +
                '   <i class="fas fa-info bg-info"></i>' +
                '   <div class="timeline-item">' +
                '       <div class="timeline-header d-flex">' +
                '           <span class="title d-flex align-items-center">' + giaidoan.phanloai + '</span>' +
                '           <span class="ml-auto text-muted">Danh sách phản hồi</span>' +
                // '           <span class="ml-auto btn btn-primary btn-sm font-size-btn-sm-mobile btnPhanHoi">Gửi phản hồi</span>' +
                '       </div>' +
                '       <div class="timeline-body">' +
                '           <div class="lblDefault">Chưa có phản hồi nào</div>' +
                '           <div class="boxPhanHoi"></div>' +
                '       </div>' +
                '       <div class="timeline-footer" style="border-top: 1px solid rgba(0,0,0,.125)">' +
                '           <textarea class="form-control font-size-mobile" rows="1" placeholder="Nhập nội dung phản hồi..."></textarea>' +
                '           <div class="text-right mt-1">' +
                '               <span class="btn btn-primary btn-sm font-size-btn-sm-mobile btnPhanHoi">Gửi phản hồi</span>' +
                '           </div>' +
                '       </div>' +
                '   </div>' +
                '</div>');
            autosize(boxHeader.find('textarea'));
            giaidoan.phanhois.forEach((phanhoi) => {
                let ten = !isUndefined(phanhoi.nhanvien) ? phanhoi.nhanvien : 'Bạn';
                let boxPhanHoi = $('' +
                    '<div class="item-phanhoi">' +
                    '   <div>' +
                    '       <span class="font-weight-bolder ten">' + ten + ': </span>' +
                    '       <span>' + phanhoi.noidung + '</span>' +
                    '   </div>' +
                    '   <div class="d-flex align-items-baseline box-action">' +
                    '       <span class="text-muted thoigian">' + doi_ngay(phanhoi.created_at,true,false) + '</span>' +
                    '   </div>' +
                    '</div>');
                if (phanhoi.nongdan_id != null) {
                    let btnXoa = $('<span class="text-danger btnXoa">Xóa phản hồi</span>');
                    btnXoa.click(() => {
                        actionXoaPhanHoi(phanhoi.id,boxPhanHoi,boxHeader);
                    });
                    boxPhanHoi.find('.box-action').prepend(btnXoa)
                }
                if (phanhoi.nhanvien_id != null) {
                    boxPhanHoi.addClass('reply');
                }
                boxHeader.find('.boxPhanHoi').append(boxPhanHoi);
            });
            if (giaidoan.phanhois.length > 0) {
                boxHeader.find('.lblDefault').addClass('d-none')
            }
            boxHeader.find('.btnPhanHoi').click(() => {
                actionPhanHoi(giaidoan,boxHeader,thuaruong.id);
            });
            offEnterTextarea(boxHeader.find('textarea'), () => { boxHeader.find('.btnPhanHoi').click() })
            let boxTitle = $('' +
                '<div class="time-label" data-id="' + giaidoan.id + '">' +
                '   <span class="bg-primary">' + giaidoan.ten + '</span>' +
                '</div>');
            boxHeader.find('.timeline-header .title').addClass(giaidoan.phanloai === 'Thuốc' ? 'text-green' : 'text-cyan');
            timeline.append(boxTitle).append(boxHeader);
            let is_hoanthanh = true;
            giaidoan.quytrinhs.forEach((item) => {
                if (item.trangthai !== 1) {
                    is_hoanthanh = false;
                }
                let element = $('' +
                    '<div>' +
                    '   <i class=""></i>' +
                    '   <div class="timeline-item">' +
                    '       <div class="timeline-header font-size-mobile font-weight-bolder text-primary ten">' + item.sanpham + '</div>' +
                    '       <div class="timeline-body">' +
                    '           <p>' + item.congdung + '</p>' +
                    '           <div class="text-right">Số lượng/ha: <span class="font-weight-bolder text-info">' +
                    parseFloat(item.soluong) + '</span> ' + item.donvitinh + '</div>' +
                    '           <div class="timeline-trangthai py-1 mt-2">Trạng thái: ' +
                    '               <span class="trangthai"></span>' +
                    '           </div>' +
                    '           <div class="timeline-ghichu"></div>' +
                    '           <div class="timeline-action text-right mt-2"></div>' +
                    '       </div>' +
                    '   </div>' +
                    '</div>');
                element.find('.ten').click(() => { initSanPham(item.sanpham_id) })
                if (item.nongdan_ghichu !== '') {
                    element.find('.timeline-ghichu').append('' +
                        '<div class="py-1 mb-2">' +
                        '   <span class="font-weight-bolder">Ghi chú:</span>' +
                        '   <span class="ghichu">' + item.nongdan_ghichu + '</span>' +
                        '</div>');
                }
                element.find('>i').addClass(item.trangthai === 0 ? 'fa fa-clock-o bg-warning' : 'fa fa-check bg-success');
                if (item.trangthai === 1) {
                    element.find('.trangthai').addClass('text-success').text('Đã hoàn thành');
                }
                else {
                    element.find('.trangthai').addClass('text-warning').text('Chưa hoàn thành');
                }
                let btnAction = $('<span class="btnAction btn btn-sm font-size-btn-sm-mobile"></span>');
                if (item.trangthai === 1) {
                    btnAction.addClass('btn-danger').text('Nhấn hủy hoàn thành');
                }
                else {
                    btnAction.addClass('btn-primary').text('Nhấn để hoàn thành');
                }
                element.find('.timeline-action').append(btnAction);
                btnAction.click(() => {
                    if (item.trangthai === 0) {
                        actionHoanThanh(item,element,thuaruong.muavu_id,thuaruong.id);
                    }
                    if (item.trangthai === 1) {
                        actionHuyHoanThanh(item,element,thuaruong.muavu_id,thuaruong.id);
                    }
                })
                timeline.append(element);
            })
            if (is_hoanthanh) {
                boxTitle.append('' +
                    '<span class="float-right text-success" style="background-color: unset">' +
                    '   <i class="fa fa-check mr-1"></i>Đã hoàn thành' +
                    '</span>')
                boxTitle.find('span:first').attr('class','bg-success');
            }
        });
    }

    function actionPhanHoi(giaidoan, boxHeader, thuaruong_id) {
        let noidung = boxHeader.find('textarea').val().trim();
        sToast.confirm('Xác nhận gửi phản hồi?','Giai đoạn ' + giaidoan.ten,
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang xử lý dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/nong-dan/quy-trinh/gui-phan-hoi',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            giaidoan_id: giaidoan.id, thuaruong_id, noidung
                        }
                    }).done((result) => {
                        if (result.succ) {
                            let boxPhanHoi = $('' +
                                '<div class="item-phanhoi">' +
                                '   <div>' +
                                '       <span class="font-weight-bolder">Bạn: </span>' +
                                '       <span>' + noidung + '</span>' +
                                '   </div>' +
                                '   <div class="d-flex align-items-baseline box-action">' +
                                '       <span class="text-danger btnXoa">Xóa phản hồi</span>' +
                                '       <span class="text-muted thoigian">' + doi_ngay(result.data.model.created_at,true,false) + '</span>' +
                                '   </div>' +
                                '</div>')
                            boxHeader.find('.boxPhanHoi').append(boxPhanHoi);
                            boxPhanHoi.find('.btnXoa').click(() => {
                                actionXoaPhanHoi(result.data.model.id,boxPhanHoi,boxHeader);
                            });
                            if (!boxHeader.find('.lblDefault').hasClass('d-none')) {
                                boxHeader.find('.lblDefault').addClass('d-none')
                            }
                            boxHeader.find('textarea').val('');
                            autosize.update(boxHeader.find('textarea'))
                        }
                    });
                }
            });
    }

    function actionXoaPhanHoi(phanhoi_id, boxPhanHoi, boxHeader) {
        sToast.confirm('Xác nhận xóa phản hồi?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/nong-dan/quy-trinh/xoa-phan-hoi',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            phanhoi_id
                        }
                    }).done((result) => {
                        if (result.succ) {
                            boxPhanHoi.remove();
                            if (boxHeader.find('.boxPhanHoi .item-phanhoi').length === 0) {
                                boxHeader.find('.lblDefault').removeClass('d-none');
                            }
                        }
                    });
                }
            });
    }

    function actionHuyHoanThanh(item, element, muavu_id, thuaruong_id) {
        sToast.confirm('Xác nhận hủy sử dụng?',item.sanpham,
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/nong-dan/quy-trinh/huy',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            quytrinh_id: item.id, thuaruong_id
                        }
                    }).done((result) => {
                        if (result.succ) {
                            element.find('> i').attr('class','fa fa-clock-o bg-warning');
                            element.find('.trangthai').attr('class','trangthai text-warning').text('Chưa phản hồi');
                            element.find('.btnAction').removeClass('btn-danger').addClass('btn-primary').text('Nhấn để hoàn thành')
                            .off('click').click(() => {
                                actionHoanThanh(item,element,muavu_id);
                            });
                            element.find('.timeline-ghichu > div').remove();
                        }
                    });
                }
            });
    }

    function actionHoanThanh(item, element, muavu_id, thuaruong_id) {
        mInput('Xác nhận đã sử dụng "' + item.sanpham + '"','').textarea('Nhập ghi chú','Bạn có thể để trống phần này...',
            () => {
                let ghichu = $('#modalInput .value').val().trim();
                sToast.confirm('Xác nhận đã sử dụng?',item.sanpham,
                    (result) => {
                        if (result.isConfirmed) {
                            sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                            $.ajax({
                                url: '/api/nong-dan/quy-trinh/hoan-thanh',
                                type: 'get',
                                dataType: 'json',
                                data: {
                                    quytrinh_id: item.id, thuaruong_id, muavu_id,
                                    ghichu
                                }
                            }).done((result) => {
                                if (result.succ) {
                                    element.find('> i').attr('class','fa fa-check bg-success');
                                    element.find('.trangthai').attr('class','trangthai text-success').text('Đã hoàn thành');
                                    element.find('.btnAction').removeClass('btn-primary').addClass('btn-danger').text('Nhấn Hủy Hoàn Thành')
                                        .off('click').click(() => {
                                            actionHuyHoanThanh(item,element,muavu_id);
                                    });
                                    element.find('.timeline-ghichu').append('' +
                                        '<div class="py-1 mb-2">' +
                                        '   <span class="font-weight-bolder">Ghi chú:</span>' +
                                        '   <span class="ghichu">' + ghichu + '</span>' +
                                        '</div>');
                                    $('#modalInput').modal('hide');
                                }
                            });
                        }
                    });
            })
    }

    function capNhatToaDo(data, button) {
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
                                    button.remove();
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
