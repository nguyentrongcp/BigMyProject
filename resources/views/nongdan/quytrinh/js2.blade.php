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
                }, 2000)
            }
        });
    }

    function addItems(results) {
        let giaidoan_id = '';
        let currentItem = null;
        results.forEach((item, stt) => {
            let tungay = moment(thuaruong.ngaysa).add(item.tu,'days').format('X');
            let denngay = moment(thuaruong.ngaysa).add(item.den,'days').format('X');
            let ngayhientai = moment('{{ date('Y-m-d') }}').format('X');
            let giaidoan = (tungay <= ngayhientai && denngay >= ngayhientai) ? 0 : (ngayhientai > denngay ? 1 : -1)
            if (item.giaidoan_id !== giaidoan_id) {
                let boxThongTin = $('' +
                    '<div>' +
                    '   <i class="fas fa-info bg-info"></i>' +
                    '   <div class="timeline-item">' +
                    '       <span class="time"><i class="fa fa-comments-o"></i> 0</span>' +
                    '       <div class="timeline-header text-' + (item.phanloai === 'Phân bón' ? 'green' : 'cyan') + '">' +
                    item.phanloai + '</div>' +
                    '   </div>' +
                    '       <div class="timeline-footer d-flex justify-content-between">' +
                    '           <a class="btn btn-success btn-sm font-size-btn-sm-mobile">Hoàn Thành Tất Cả</a>' +
                    '           <a class="btn btn-primary btn-sm font-size-btn-sm-mobile">Gửi Phản Hồi</a>' +
                    '       </div>' +
                    '</div>');
                $('#boxMain').append('' +
                    '<div class="time-label">' +
                    '   <span class="bg-' + (giaidoan < 0 ? 'secondary' : (giaidoan === 0 ? 'primary' : 'purple')) + '">' +
                    item.giaidoan+ '</span>' +
                    '</div>').append(boxThongTin);
                giaidoan_id = item.giaidoan_id;
            }

            let trangthai = item.trangthai === 0 ? 'fa fa-clock-o bg-warning' : 'fa fa-check bg-success';
            let btnAction = null;
            if (ngayhientai < tungay) {
                trangthai = 'fa fa-clock-o bg-danger';
            }
            else {
                let textAction = item.trangthai === 0 ? 'Nhấn Để Hoàn Thành' : 'Nhấn Để Hủy';
                let styleAction = item.trangthai === 0 ? ' text-primary' : ' text-danger';
                btnAction = $('<span class="tbtnAction' + styleAction +'">' + textAction + '</span>');
            }
            let element = $('' +
                '<div>' +
                '   <i class="' + trangthai + '"></i>' +
                '   <div class="timeline-item">' +
                '       <div class="timeline-header">' + item.sanpham + '</div>' +
                '       <div class="timeline-body">' +
                '           <p>' + item.congdung + '</p>' +
                '           <div class="text-right">Số lượng/ha: <span class="font-weight-bolder text-info">' +
                parseFloat(item.soluong) + '</span> ' + item.donvitinh + '</div>' +
                '       </div>' +
                '   </div>' +
                '</div>');
            if (btnAction != null) {
                element.find('.boxAction').append(btnAction);
            }
            boxGiaiDoan.find('> .card-body').append(element);
            if (stt + 1 === results.length) {
                boxGiaiDoan.addClass('mr-0')
            }
            if (tungay <= ngayhientai && denngay >= ngayhientai && currentItem == null) {
                currentItem = boxGiaiDoan;
                itemPhanHoi = currentItem;
            }
        });

        if (currentItem != null) {
            $('section').animate({scrollLeft: currentItem.position().left}, 500);
        }
        if (itemPhanHoi != null) {
            itemPhanHoi.find('.btnPhanHoi').tooltip({
                trigger: 'manual',
                title: 'Nhấn vào đây để gửi phản hồi trong giai đoạn này!!!',
            });
            if (isFirst.phanhoi) {
                isFirst.phanhoi = false;
                setTimeout(() => {
                    itemPhanHoi.find('.btnPhanHoi').tooltip('show');
                    setTimeout(() => {itemPhanHoi.find('.btnPhanHoi').tooltip('hide')}, 5000)
                }, 500)
            }
        }
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
