@section('js-custom')
    <script>
        initData();

        function initData() {
            sToast.loading('Đang load dữ liệu. Vui lòng chờ...');
            $.ajax({
                url: '/api/quan-ly/quy-trinh-lua/thua-ruong/cay-quy-trinh',
                type: 'get',
                dataType: 'json',
                data: {
                    thuaruong_id: '{{ $thuaruong->id }}',
                }
            }).done((result) => {
                initTimeline(result.data);
            });
        }

        function initTimeline(data) {
            let boxTimeline = {
                'Phân bón': $('<div class="timeline"></div>'),
                'Thuốc': $('<div class="timeline"></div>')
            };
            data.danhsach.forEach((giaidoan) => {
                let tungay = moment('{{ $thuaruong->ngaysa }}').add(giaidoan.tu,'days').format('X');
                let denngay = moment('{{ $thuaruong->ngaysa }}').add(giaidoan.den,'days').format('X');
                let ngayhientai = moment('{{ date('Y-m-d') }}').format('X');
                let _giaidoan = (tungay <= ngayhientai && denngay >= ngayhientai) ? 0 : (ngayhientai > denngay ? -1 : 1);
                let boxGiaiDoan = $('' +
                    '<div class="time-label">' +
                    '   <span class="">' + giaidoan.ten + '</span>' +
                    '</div>');
                boxTimeline[giaidoan.phanloai].append(boxGiaiDoan);
                boxGiaiDoan.find('span').addClass(_giaidoan < 0 ? 'bg-secondary' : (_giaidoan === 0 ? 'bg-primary' : 'bg-purple'));

                if (giaidoan.phanhois.length > 0) {
                    let phanHoiContainer = $('' +
                        '<div>' +
                        '   <i class="fa fa-comments bg-primary"></i>' +
                        '   <div class="timeline-item">' +
                        '       <h3 class="timeline-header font-weight-bolder">Danh sách phản hồi</h3>' +
                        '       <div class="timeline-body">' +
                        '           <div class="boxPhanHoi"></div>' +
                        '       </div>' +
                        '       <div class="timeline-footer" style="border-top: 1px solid rgba(0,0,0,.125)">' +
                        '           <textarea class="form-control" rows="1" placeholder="Nhập nội dung trả lời... Nhấn enter để gửi!"></textarea>' +
                        '       </div>' +
                        '   </div>' +
                        '</div>');
                    giaidoan.phanhois.forEach((phanhoi) => {
                        let ten = !isUndefined(phanhoi.nhanvien) ? phanhoi.nhanvien : 'Nông dân';
                        ten = phanhoi.nhanvien_id === info.id ? 'Bạn' : ten;
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
                        if (phanhoi.nhanvien_id != null) {
                            let btnXoa = $('<span class="text-danger btnXoa c-pointer">Xóa phản hồi</span>');
                            btnXoa.click(() => {
                                actionXoaPhanHoi(phanhoi.id,boxPhanHoi);
                            });
                            boxPhanHoi.find('.box-action').prepend(btnXoa);
                            boxPhanHoi.addClass('reply');
                        }
                        phanHoiContainer.find('.boxPhanHoi').append(boxPhanHoi);
                    })
                    autosize(phanHoiContainer.find('textarea'));
                    offEnterTextarea(phanHoiContainer.find('textarea'),() => {
                        actionTraLoiPhanHoi(giaidoan,phanHoiContainer,phanHoiContainer.find('textarea'))
                    });
                    boxTimeline[giaidoan.phanloai].append(phanHoiContainer);
                }

                giaidoan.quytrinhs.forEach((quytrinh) => {
                    let ghichu = quytrinh.quytrinh_thuaruong.ghichu;
                    ghichu = isNull(ghichu) || isUndefined(ghichu) ? '' : ghichu;
                    let boxItem = $('' +
                        '<div>' +
                        '   <i class=""></i>' +
                        '   <div class="timeline-item">' +
                        '       <h3 class="timeline-header font-weight-bolder">' + quytrinh.sanpham + '</h3>' +
                        '       <div class="timeline-body">' +
                        '           <div>' + quytrinh.congdung + '' + '</div>' +
                        '           <div class="mt-2">' +
                        '               <a class="btn btn-primary btn-sm font-weight-bolder">' + numeral(quytrinh.dongia).format('0,0') + '</a>' +
                        '               <strong> X </strong>' +
                        '               <a class="btn btn-info btn-sm font-weight-bolder">' + quytrinh.soluong + '</a>' +
                        '               <strong> = </strong>' +
                        '               <a class="btn btn-danger btn-sm font-weight-bolder">' + numeral(quytrinh.thanhtien).format('0,0') + ' VNĐ</a>' +
                        '           </div>' +
                        '       </div>' +
                        '   </div>' +
                        '</div>');
                    if (ghichu !== '') {
                        boxItem.find('.timeline-item').append('' +
                            '<div class="timeline-footer" style="border-top: 1px solid rgba(0,0,0,.125)">' +
                            '   <span class="font-weight-bolder">Ghi chú: </span>' + ghichu +
                            '</div>');
                    }
                    if (ngayhientai < tungay) {
                        boxItem.find('>i').addClass('fa fa-clock-o bg-danger');
                    }
                    else {
                        if (!isUndefined(quytrinh.quytrinh_thuaruong.status)) {
                            if (quytrinh.quytrinh_thuaruong.status === 1) {
                                boxItem.find('>i').addClass('fa fa-check bg-success');
                            }
                            else {
                                boxItem.find('>i').addClass('fa fa-clock-o bg-danger');
                            }
                        }
                        else {
                            boxItem.find('>i').addClass('fa fa-clock-o bg-warning');
                        }
                    }
                    boxTimeline[quytrinh.phanloai].append(boxItem);
                })
            })
            $('#boxPhanBon').empty().append(boxTimeline['Phân bón'])
            $('#boxThuoc').empty().append(boxTimeline['Thuốc'])
        }

        function actionTraLoiPhanHoi(giaidoan, boxHeader, textarea) {
            let noidung = textarea.val().trim();
            sToast.confirm('Xác nhận trả lời phản hồi?','Giai đoạn ' + giaidoan.ten,
                (result) => {
                    if (result.isConfirmed) {
                        sToast.loading('Đang xử lý dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/quy-trinh-lua/thua-ruong/traloi-phanhoi',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                giaidoan_id: giaidoan.id, thuaruong_id: '{{ $thuaruong->id }}', noidung
                            }
                        }).done((result) => {
                            if (result.succ) {
                                let boxPhanHoi = $('' +
                                    '<div class="item-phanhoi reply">' +
                                    '   <div>' +
                                    '       <span class="font-weight-bolder ten">Bạn: </span>' +
                                    '       <span>' + noidung + '</span>' +
                                    '   </div>' +
                                    '   <div class="d-flex align-items-baseline box-action">' +
                                    '       <span class="text-danger btnXoa c-pointer">Xóa phản hồi</span>' +
                                    '       <span class="text-muted thoigian">' + doi_ngay(result.data.model.created_at,true,false) + '</span>' +
                                    '   </div>' +
                                    '</div>')
                                boxHeader.find('.boxPhanHoi').append(boxPhanHoi);
                                boxPhanHoi.find('.btnXoa').click(() => {
                                    actionXoaPhanHoi(result.data.model.id,boxPhanHoi);
                                });
                                textarea.val('');
                                autosize.update(textarea);
                            }
                        });
                    }
                });
        }

        function actionXoaPhanHoi(phanhoi_id, boxPhanHoi) {
            sToast.confirm('Xác nhận xóa phản hồi?','',
                (result) => {
                    if (result.isConfirmed) {
                        sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/quy-trinh-lua/thua-ruong/xoa-phan-hoi',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                phanhoi_id
                            }
                        }).done((result) => {
                            if (result.succ) {
                                boxPhanHoi.remove();
                            }
                        });
                    }
                });
        }
    </script>
@stop
