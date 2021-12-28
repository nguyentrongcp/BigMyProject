<script>
    let thuaruong = JSON.parse('{!! str_replace("'","\'",$thuaruong) !!}');
    initDanhMuc(thuaruong.id);
    init();
    let isFirst = true;

    function init() {
        $('#selThuaRuong').tooltip({
            trigger: 'manual',
            title: 'Nhấn vào đây để đổi sang thửa ruộng khác!!!',
        });

        $('#selThuaRuong').click(() => {
            let thuaruongs = JSON.parse('{!! str_replace("'","\'",$thuaruongs) !!}');
            thuaruongs.forEach((value) => {
                value.text = value.ten;
            })
            mInput('Chuyển đổi thửa ruộng',thuaruong.id)
                .select2('Chọn thửa ruộng','',thuaruongs,true,() => {
                    $('#modalInput').modal('hide');
                    if ($('#modalInput .value').val() != thuaruong.id) {
                        thuaruong = $('#modalInput .value').select2('data')[0];
                        initDanhMuc(thuaruong.id);
                        $('#selThuaRuong .ten').text(thuaruong.ten);
                        $("#selThuaRuong .ngaysa").text(doi_ngay(thuaruong.ngaysa));
                    }
                })
        })
    }

    function initDanhMuc(thuaruong_id) {
        $('#boxMain').empty();
        sToast.loading('Đang tải dữ liệu. Vui lòng chờ...')
        $.ajax({
            url: '/api/nong-dan/quy-trinh/danh-sach',
            type: 'get',
            dataType: 'json',
            data: {
                thuaruong_id
            }
        }).done((results) => {
            addItems(results);
            Swal.close();
        });
    }

    function addItems(results) {
        let giaidoan_id = '';
        let currentHeader = null;
        let currenTitle = null;
        let currentTitle = null;
        let currentStt = 0;
        results.forEach((item, stt) => {
            let tungay = moment(thuaruong.ngaysa).add(item.tu,'days').format('X');
            let denngay = moment(thuaruong.ngaysa).add(item.den,'days').format('X');
            let ngayhientai = moment('{{ date('Y-m-d') }}').format('X');
            let giaidoan = (tungay <= ngayhientai && denngay >= ngayhientai) ? 0 : (ngayhientai > denngay ? -1 : 1);
            if (item.giaidoan_id !== giaidoan_id) {
                giaidoan_id = item.giaidoan_id;
                let boxHeader = $('' +
                    '<div>' +
                    '   <i class="fas fa-info bg-info"></i>' +
                    '   <div class="timeline-item">' +
                    '       <span class="time"><i class="fa fa-comments-o"></i> 0</span>' +
                    '       <div class="timeline-header">' + item.phanloai + '</div>' +
                    '       <div class="timeline-footer d-flex justify-content-between">' +
                    // '           <a class="btn btn-success btn-sm font-size-btn-sm-mobile">Hoàn Thành Tất Cả</a>' +
                    '           <a class="btn btn-primary btn-sm font-size-btn-sm-mobile ml-auto btnPhanHoi">Gửi Phản Hồi</a>' +
                    '       </div>' +
                    '   </div>' +
                    '</div>');
                let boxTitle = $('' +
                    '<div class="time-label">' +
                    '   <span class="">' + item.giaidoan+ '</span>' +
                    '</div>');
                boxHeader.find('.timeline-header').addClass(item.phanloai === 'Thuốc' ? 'text-green' : 'text-cyan');
                boxTitle.find('span').addClass(giaidoan < 0 ? 'bg-secondary' : (giaidoan === 0 ? 'bg-primary' : 'bg-purple'));
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
            }

            let element = $('' +
                '<div>' +
                '   <i class=""></i>' +
                '   <div class="timeline-item">' +
                '       <div class="timeline-header font-size-mobile font-weight-bolder text-primary">' + item.sanpham + '</div>' +
                '       <div class="timeline-body">' +
                '           <p>' + item.congdung + '</p>' +
                '           <div class="text-right">Số lượng/ha: <span class="font-weight-bolder text-info">' +
                parseFloat(item.soluong) + '</span> ' + item.donvitinh + '</div>' +
                '           <div class="timeline-trangthai py-1 my-2">Trạng thái: ' +
                '               <span class="trangthai"></span>' +
                '           </div>' +
                '           <div class="timeline-action text-right"></div>' +
                '       </div>' +
                '   </div>' +
                '</div>');
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
                        actionHoanThanh(item,element);
                    }
                    if (item.trangthai === 1) {
                        actionHuyHoanThanh(item,element);
                    }
                })
            }
            $('#boxMain').append(element);
        });

        $('#container').animate({scrollTop: currentTitle.position().top}, 500);
        currentTitle.find('span').tooltip({
            trigger: 'manual',
            title: 'Tên giai đoạn',
        });
        if (isFirst) {
            isFirst = false;
            setTimeout(() => {
                currentTitle.find('span').tooltip('show');
                setTimeout(() => {currentTitle.find('span').tooltip('hide')}, 3000)
            }, 500)
            setTimeout(() => {
                $('#selThuaRuong').tooltip('show');
                setTimeout(() => {$('#selThuaRuong').tooltip('hide')}, 3000)
            }, 3500)
        }
    }

    function actionHuyHoanThanh(item, element) {
        sToast.confirm('Xác nhận hủy sử dụng?',item.sanpham,
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/nong-dan/quy-trinh/huy',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            quytrinh_id: item.id, thuaruong_id: thuaruong.id
                        }
                    }).done((result) => {
                        if (result.succ) {
                            element.find('> i').attr('class','fa fa-clock-o bg-warning');
                            element.find('.trangthai').attr('class','trangthai text-warning').text('Chưa phản hồi');
                            element.find('.btnAction').removeClass('btn-danger').addClass('btn-primary').text('Nhấn để hoàn thành')
                            .off('click').click(() => {
                                actionHoanThanh(item,element);
                            });
                        }
                    });
                }
            });
    }

    function actionHoanThanh(item, element) {
        mInput('Xác nhận đã sử dụng "' + item.sanpham + '"','').textarea('Nhập ghi chú','Bạn có thể để trống phần này...',
            () => {
                sToast.confirm('Xác nhận đã sử dụng?',item.sanpham,
                    (result) => {
                        if (result.isConfirmed) {
                            sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                            $.ajax({
                                url: '/api/nong-dan/quy-trinh/hoan-thanh',
                                type: 'get',
                                dataType: 'json',
                                data: {
                                    quytrinh_id: item.id, thuaruong_id: thuaruong.id
                                }
                            }).done((result) => {
                                if (result.succ) {
                                    element.find('> i').attr('class','fa fa-check bg-success');
                                    element.find('.trangthai').attr('class','trangthai text-success').text('Đã hoàn thành');
                                    element.find('.btnAction').removeClass('btn-primary').addClass('btn-danger').text('Nhấn Hủy Hoàn Thành')
                                        .off('click').click(() => {
                                            actionHuyHoanThanh(item,element);
                                    });
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
