<script>
    let thuaruong = JSON.parse('{!! str_replace("'","\'",$thuaruong) !!}');
    initDanhMuc(thuaruong.id);
    init();
    let isFirst = {
        muavu: true,
        phanhoi: true
    };

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
            if (isFirst.phanhoi) {

            }
            Swal.close();
            if (isFirst.muavu) {
                isFirst.muavu = false;
                setTimeout(() => {
                    $('#selThuaRuong').tooltip('show');
                    setTimeout(() => {$('#selThuaRuong').tooltip('hide')}, 5000)
                }, 5500)
            }
        });
    }

    function addItems(results) {
        let giaidoan_id = '';
        let currentItem = null;
        let currenPhanHoi = null;
        results.forEach((item, stt) => {
            let tungay = moment(thuaruong.ngaysa).add(item.tu,'days').format('X');
            let denngay = moment(thuaruong.ngaysa).add(item.den,'days').format('X');
            let ngayhientai = moment('{{ date('Y-m-d') }}').format('X');
            let giaidoan = (tungay <= ngayhientai && denngay >= ngayhientai) ? 0 : (ngayhientai > denngay ? -1 : 1)
            if (item.giaidoan_id !== giaidoan_id) {
                let boxThongTin = $('' +
                    '<div>' +
                    '   <i class="fas fa-info bg-info"></i>' +
                    '   <div class="timeline-item">' +
                    '       <span class="time"><i class="fa fa-comments-o"></i> 0</span>' +
                    '       <div class="timeline-header text-' + (item.phanloai === 'Phân bón' ? 'green' : 'cyan') + '">' +
                    item.phanloai + '</div>' +
                    '       <div class="timeline-footer d-flex justify-content-between">' +
                    // '           <a class="btn btn-success btn-sm font-size-btn-sm-mobile">Hoàn Thành Tất Cả</a>' +
                    '           <a class="btn btn-primary btn-sm font-size-btn-sm-mobile ml-auto btnPhanHoi">Gửi Phản Hồi</a>' +
                    '       </div>' +
                    '   </div>' +
                    '</div>');
                let label = $('' +
                    '<div class="time-label">' +
                    '   <span class="bg-' + (giaidoan < 0 ? 'secondary' : (giaidoan === 0 ? 'primary' : 'purple')) + '">' +
                    item.giaidoan+ '</span>' +
                    '</div>');
                $('#boxMain').append(label).append(boxThongTin);
                giaidoan_id = item.giaidoan_id;
                if (stt === 0) {
                    currenPhanHoi = boxThongTin.find('.btnPhanHoi');
                }
                if (tungay <= ngayhientai && denngay >= ngayhientai && currentItem == null) {
                    currentItem = label;
                    currenPhanHoi = boxThongTin.find('.btnPhanHoi');
                }
            }

            let trangthai = item.trangthai === 0 ? 'fa fa-clock-o bg-warning' : 'fa fa-check bg-success';
            let btnAction = null;
            let textTrangThai = '<span class="trangthai text-warning">Chưa phản hồi</span>';
            if (ngayhientai < tungay) {
                trangthai = 'fa fa-clock-o bg-danger';
                textTrangThai = '<span class="trangthai text-danger">Chưa đến ngày</span>';
            }
            else {
                let textAction = item.trangthai === 0 ? 'Nhấn Để Hoàn Thành' : 'Nhấn Hủy Hoàn Thành';
                let styleAction = item.trangthai === 0 ? 'primary' : 'danger';
                btnAction = $('<span class="btnAction btn btn-sm font-size-btn-sm-mobile btn-' + styleAction +'">' + textAction + '</span>');
                if (item.trangthai === 1) {
                    textTrangThai = '<span class="trangthai text-success">Đã hoàn thành</span>';
                }
            }
            let element = $('' +
                '<div>' +
                '   <i class="' + trangthai + '"></i>' +
                '   <div class="timeline-item">' +
                '       <div class="timeline-header font-size-mobile font-weight-bolder text-primary">' + item.sanpham + '</div>' +
                '       <div class="timeline-body">' +
                '           <p>' + item.congdung + '</p>' +
                '           <div class="text-right">Số lượng/ha: <span class="font-weight-bolder text-info">' +
                parseFloat(item.soluong) + '</span> ' + item.donvitinh + '</div>' +
                '           <div class="timeline-trangthai py-1 my-2">Trạng thái: ' +
                textTrangThai +
                '           </div>' +
                '           <div class="timeline-action text-right"></div>' +
                '       </div>' +
                '   </div>' +
                '</div>');
            if (btnAction != null) {
                element.find('.timeline-action').append(btnAction);
                btnAction.click(() => {
                    if (item.trangthai === 0) {
                        actionHoanThanh(item.id,element);
                    }
                    if (item.trangthai === 1) {
                        actionHuyHoanThanh(item.id,element);
                    }
                })
            }
            $('#boxMain').append(element);
        });

        if (currentItem != null) {
            $('#container').animate({scrollTop: currentItem.position().top}, 500);
        }
        if (currenPhanHoi != null) {
            currenPhanHoi.tooltip({
                trigger: 'manual',
                title: 'Nhấn vào đây để gửi phản hồi trong giai đoạn này!!!',
            });
            if (isFirst.phanhoi) {
                isFirst.phanhoi = false;
                setTimeout(() => {
                    currenPhanHoi.tooltip('show');
                    setTimeout(() => {currenPhanHoi.tooltip('hide')}, 5000)
                }, 500)
            }
        }
    }

    function actionHuyHoanThanh(quytrinh_id, element) {
        sToast.confirm('Xác nhận hủy hoàn thành quy trình này?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/nong-dan/quy-trinh/huy',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            quytrinh_id, thuaruong_id: thuaruong.id
                        }
                    }).done((result) => {
                        if (result.succ) {
                            element.find('> i').attr('class','fa fa-clock-o bg-warning');
                            element.find('.trangthai').attr('class','trangthai text-warning').text('Chưa phản hồi');
                            element.find('.btnAction').removeClass('btn-danger').addClass('btn-primary').text('Nhấn để hoàn thành')
                            .off('click').click(() => {
                                actionHoanThanh(quytrinh_id,element);
                            });
                        }
                    });
                }
            });
    }

    function actionHoanThanh(quytrinh_id, element) {
        mInput('Xác nhận hoàn thành quy trình','').textarea('Nhập ghi chú','Bạn có thể để trống phần này...',
            () => {
                sToast.confirm('Xác nhận hoàn thành quy trình này?','',
                    (result) => {
                        if (result.isConfirmed) {
                            sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                            $.ajax({
                                url: '/api/nong-dan/quy-trinh/hoan-thanh',
                                type: 'get',
                                dataType: 'json',
                                data: {
                                    quytrinh_id, thuaruong_id: thuaruong.id
                                }
                            }).done((result) => {
                                if (result.succ) {
                                    element.find('> i').attr('class','fa fa-check bg-success');
                                    element.find('.trangthai').attr('class','trangthai text-success').text('Đã hoàn thành');
                                    element.find('.btnAction').removeClass('btn-primary').addClass('btn-danger').text('Nhấn Hủy Hoàn Thành')
                                        .off('click').click(() => {
                                            actionHuyHoanThanh(quytrinh_id,element);
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
