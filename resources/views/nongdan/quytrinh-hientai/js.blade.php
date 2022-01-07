<script>
    initDanhMuc();

    function initDanhMuc() {
        $('#boxMain').empty();
        sToast.loading('Đang tải dữ liệu. Vui lòng chờ...')
        $.ajax({
            url: '/api/nong-dan/quy-trinh/danhsach-hientai',
            type: 'get',
            dataType: 'json'
        }).done((result) => {

        });
    }

    function addItems(result,giaidoan_id) {
        let currentHeader = null;
        let currentTitle = null;
        let currentStt = 0;
        result.danhsach.forEach((giaidoan, stt) => {
            let tungay = moment(thuaruong.ngaysa).add(giaidoan.tu,'days').format('X');
            let denngay = moment(thuaruong.ngaysa).add(giaidoan.den,'days').format('X');
            let ngayhientai = moment('{{ date('Y-m-d') }}').format('X');
            let _giaidoan = (tungay <= ngayhientai && denngay >= ngayhientai) ? 0 : (ngayhientai > denngay ? -1 : 1);
            let boxHeader = $('' +
                '<div>' +
                '   <i class="fas fa-info bg-info"></i>' +
                '   <div class="timeline-item">' +
                '       <div class="timeline-header d-flex">' +
                '           <span class="title d-flex align-items-center">' + giaidoan.phanloai + '</span>' +
                '           <span class="ml-auto btn btn-primary btn-sm font-size-btn-sm-mobile btnPhanHoi">Gửi phản hồi</span>' +
                '       </div>' +
                '       <div class="timeline-body">' +
                '           <div class="lblDefault">Chưa có phản hồi nào</div>' +
                '           <div class="boxPhanHoi"></div>' +
                '       </div>' +
                // '       <div class="timeline-footer d-flex justify-content-between">' +
                // '           <a class="btn btn-primary btn-sm font-size-btn-sm-mobile ml-auto btnPhanHoi">Gửi Phản Hồi</a>' +
                // '       </div>' +
                '   </div>' +
                '</div>');
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
                actionPhanHoi(giaidoan,boxHeader,result.thuaruong.id);
            });
            let boxTitle = $('' +
                '<div class="time-label" data-id="' + giaidoan.id + '">' +
                '   <span class="">' + giaidoan.ten + '</span>' +
                '</div>');
            boxHeader.find('.timeline-header .title').addClass(giaidoan.phanloai === 'Thuốc' ? 'text-green' : 'text-cyan');
            boxTitle.find('span').addClass(_giaidoan < 0 ? 'bg-secondary' : (_giaidoan === 0 ? 'bg-primary' : 'bg-purple'));
            $('#boxMain').append(boxTitle).append(boxHeader);
            if (stt === 0) {
                currentTitle = boxTitle;
                currentHeader = boxHeader;
            }
            if (tungay <= ngayhientai && denngay >= ngayhientai && currentStt === 0) {
                currentTitle = boxTitle;
                currentHeader = boxHeader;
                currentStt = stt;
            }
            giaidoan.quytrinhs.forEach((item) => {
                let element = $('' +
                    '<div>' +
                    '   <i class=""></i>' +
                    '   <div class="timeline-item">' +
                    '       <div class="timeline-header font-size-mobile font-weight-bolder text-primary">' + item.sanpham + '</div>' +
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
                if (item.nongdan_ghichu !== '') {
                    element.find('.timeline-ghichu').append('' +
                        '<div class="py-1 mb-2">' +
                        '   <span class="font-weight-bolder">Ghi chú:</span>' +
                        '   <span class="ghichu">' + item.nongdan_ghichu + '</span>' +
                        '</div>');
                }
                if (ngayhientai < tungay) {
                    element.find('>i').addClass('fa fa-clock-o bg-danger');
                    element.find('.trangthai').addClass('text-danger').text('Chưa đến ngày');
                }
                else {
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
                            actionHoanThanh(item,element,result.muavu_id,result.thuaruong.id);
                        }
                        if (item.trangthai === 1) {
                            actionHuyHoanThanh(item,element,result.muavu_id,result.thuaruong.id);
                        }
                    })
                }
                $('#boxMain').append(element);
            })
        });

        if (giaidoan_id !== '') {
            $('#container').animate({scrollTop: $('#boxMain .time-label[data-id={{ $giaidoan_id }}]').position().top}, 500);
        }
        else {
            $('#container').animate({scrollTop: currentTitle.position().top}, 500);
        }
        // currentTitle.find('span').tooltip({
        //     trigger: 'manual',
        //     title: 'Tên giai đoạn',
        // });
        if (isFirst) {
            isFirst = false;
            setTimeout(() => {
                $('#selThuaRuong').tooltip('show');
                setTimeout(() => {$('#selThuaRuong').tooltip('hide')}, 3000)
            }, 500)
        }
    }

    function actionPhanHoi(giaidoan, boxHeader, thuaruong_id) {
        mInput('Gửi phản hồi').textarea('Nhập nội dung','Nhập nội dung phản hồi...',
            () => {
                let noidung = $('#modalInput .value').val().trim();
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
                                }
                            });
                        }
                    });
            })
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
                                    container.addClass('d-none').find('.btnCapNhatViTri').off('click');
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
