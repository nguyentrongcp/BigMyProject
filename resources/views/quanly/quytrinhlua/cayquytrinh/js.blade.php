<script>
    init();

    function init() {
        $('#btnLamMoi').click(() => {
            initData()
        })
        $('#selMuaVu').select2({
            data: JSON.parse('{!! str_replace("'","\'",$muavus) !!}'),
            minimumResultsForSearch: -1,
            placeholder: 'Bạn chưa chọn mùa vụ'
        }).change(function () {
            if (!isNull($(this).val())) {
                $('#btnLamMoi').click();
            }
        }).trigger('change');
    }

    function initData() {
        sToast.loading('Đang load dữ liệu. Vui lòng chờ...');
        $.ajax({
            url: '/api/quan-ly/quy-trinh-lua/cay-quy-trinh/danh-sach',
            type: 'get',
            dataType: 'json',
            data: {
                muavu_id: $('#selMuaVu').val(),
            }
        }).done((result) => {
            $('#lblSoViTri').text(result.data.toados.length);
            $('#lblSoNongDan').text(result.data.sonongdan);
            $('#lblSoThuaRuong').text(result.data.sothuaruong);
            $($('#lblSoThuaRuong').parent()).off('click').click(() => {
                initThuaRuong(result.data.muavu_id)
            })
            $($('#lblSoNongDan').parent()).off('click').click(() => {
                initNongDan(result.data.muavu_id)
            })
            $($('#lblSoViTri').parent()).off('click').click(() => {
                initMap(result.data.toados);
            })
            initTimeline(result.data.danhsach);
        });
    }

    function initTimeline(data) {
        let boxTimeline = {
            'Phân bón': $('<div class="timeline"></div>'),
            'Thuốc': $('<div class="timeline"></div>')
        };
        data.forEach((giaidoan) => {
            let color = giaidoan.tu < 0 ? 'bg-secondary' : 'bg-purple';
            let boxGiaiDoan = $('' +
                '<div class="time-label">' +
                '   <span class="' + color + '">' + giaidoan.ten + '</span>' +
                '   <span class="float-right text-muted">' +
                '       <span class="c-pointer lblPhanHoiMoi"><i class="fa fa-comment mr-1 text-warning"></i><strong class="text-info">' + giaidoan.phanhoi_moi + '</strong> phản hồi mới</span>' +
                '       <span class="mx-2">/</span>' +
                '       <span><i class="fa fa-comments mr-1 text-primary"></i><strong class="text-info">' + giaidoan.tongso_phanhoi + '</strong> phản hồi</span>' +
                '       <span class="mx-2">/</span>' +
                '       <span class="c-pointer lblSoHoanThanh"><i class="fa fa-check-square mr-1 text-success"></i><strong class="text-info">'
                + giaidoan.sohoanthanh + '</strong> hoàn thành</span>' +
                '   </span>' +
                '</div>');
            if (giaidoan.sohoanthanh > 0) {
                boxGiaiDoan.find('.lblSoHoanThanh').click(() => {
                    initHoanThanh(data.muavu_id,giaidoan.id);
                })
            }
            if (giaidoan.phanhoi_moi > 0) {
                boxGiaiDoan.find('.lblPhanHoiMoi').click(() => {
                    initModalPhanHoi('Danh Sách Phản Hồi Mới',giaidoan);
                })
            }
            boxTimeline[giaidoan.phanloai].append(boxGiaiDoan);
            giaidoan.quytrinhs.forEach((quytrinh) => {
                let boxItem = $('' +
                    '<div>' +
                    '   <div class="timeline-item">' +
                    '       <span class="time">' +
                    '           <span><i class="fa fa-check-square-o mr-1 text-success"></i><strong class="text-info">'
                    + quytrinh.dacheck + '</strong> đã check</span>' +
                    '       </span>' +
                    '       <h3 class="timeline-header font-weight-bolder">' + quytrinh.sanpham + '</h3>' +
                    '       <div class="timeline-body">' + quytrinh.congdung + '</div>' +
                    '       <div class="timeline-footer">' +
                    '           <a class="btn btn-primary btn-sm font-weight-bolder">' + numeral(quytrinh.dongia).format('0,0') + '</a>' +
                    '           <strong> X </strong>' +
                    '           <a class="btn btn-info btn-sm font-weight-bolder">' + quytrinh.soluong + '</a>' +
                    '           <strong> = </strong>' +
                    '           <a class="btn btn-danger btn-sm font-weight-bolder">' + numeral(quytrinh.thanhtien).format('0,0') + ' VNĐ</a>' +
                    '       </div>' +
                    '   </div>' +
                    '</div>');
                boxTimeline[quytrinh.phanloai].append(boxItem);
            })
        })
        $('#boxPhanBon').empty().append(boxTimeline['Phân bón'])
        $('#boxThuoc').empty().append(boxTimeline['Thuốc'])
    }

    function initModalPhanHoi(modal_title, giaidoan) {
        sToast.loading('Đang load dữ liệu. Vui lòng chờ...');
        $.ajax({
            url: '/api/quan-ly/quy-trinh-lua/cay-quy-trinh/danhsach-phanhoimoi',
            type: 'get',
            dataType: 'json',
            data: {
                giaidoan_id: giaidoan.id,
            }
        }).done((results) => {
            let modal = $('' +
                '<div class="modal fade">' +
                '   <div class="modal-dialog modal-dialog-scrollable">' +
                '       <div class="modal-content">' +
                '           <div class="modal-header"><h5 class="modal-title w-100 text-center">' + modal_title + '</h5></div>' +
                '           <div class="modal-body">' +
                // '               <button class="btn btn-primary btn-block mt-3">Xem Thêm</button>' +
                '           </div>' +
                '       </div>' +
                '   </div>' +
                '</div>')
            results.forEach((thuaruong) => {
                let phanHoiContainer = $('' +
                    '<div class="card card-outline card-primary mb-0">' +
                    '   <div class="card-header">' + thuaruong.tennongdan + ' - ' + thuaruong.dienthoai + ' (' + thuaruong.ten + ')</div>' +
                    '   <div class="card-body">' +
                    '       <div class="boxPhanHoi"></div>' +
                    '   </div>' +
                    '   <div class="card-footer bg-white" style="border-top: 1px solid rgba(0,0,0,.125)">' +
                    '       <textarea class="form-control" rows="1" placeholder="Nhập nội dung trả lời... Nhấn enter để gửi!"></textarea>' +
                    '   </div>' +
                    '</div>');
                thuaruong.phanhois.forEach((phanhoi) => {
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
                        boxPhanHoi.addClass('reply');
                    }
                    phanHoiContainer.find('.boxPhanHoi').append(boxPhanHoi);
                })
                autosize(phanHoiContainer.find('textarea'));
                offEnterTextarea(phanHoiContainer.find('textarea'),() => {
                    actionTraLoiPhanHoi(giaidoan,thuaruong.id,phanHoiContainer,phanHoiContainer.find('textarea'))
                });
                modal.find('.modal-body').append(phanHoiContainer);
            })
            $('body').append(modal);

            modal.on('hidden.bs.modal', function() {
                modal.remove();
            }).modal('show');
        });
    }

    function actionTraLoiPhanHoi(giaidoan, thuaruong_id, boxHeader, textarea) {
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
                            giaidoan_id: giaidoan.id, thuaruong_id, noidung
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
                            socket.emit('send-notification-nongdan',result.data.thongbaos)
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

    function initModalTable(table_name, modal_title) {
        let modal = $('' +
            '<div class="modal fade">' +
            '   <div class="modal-dialog modal-xl">' +
            '       <div class="modal-content">' +
            '           <div class="modal-header"><h5 class="modal-title w-100 text-center">' + modal_title + '</h5></div>' +
            '           <div class="modal-body">' +
            '               <div class="d-flex">' +
            '                   <div class="d-flex box-search-table flex-grow-1" data-target="' + table_name + '">' +
            '                       <div class="input-search input-with-icon">' +
            '                           <input class="form-control non-border" type="text" placeholder="Nhập từ khóa tìm kiếm...">' +
            '                           <span class="icon"><i class="fa fa-times"></i></span>' +
            '                       </div>' +
            '                       <button class="btn bg-gradient-secondary excel font-weight-bolder">' +
            '                           <i class="fas fa-download mr-1"></i> Xuất Excel' +
            '                       </button>' +
            '                   </div>' +
            '               </div>' +
            '               <div id="' + table_name + '" class="mt-1"></div>' +
            '           </div>' +
            '       </div>' +
            '   </div>' +
            '</div>')
        $('body').append(modal);

        modal.on('hidden.bs.modal', function() {
            modal.remove();
        });

        return modal;
    }

    function initThuaRuong(muavu_id) {
        let modal = initModalTable('tblDanhSachThuaRuong','Danh Sách Thửa Ruộng');

        let tblDanhSachThuaRuong = null;
        let views = localStorage.getItem('quytrinhlua.cayquytrinh.hoanthanh.views');
        views = isNull(views) ? {} : JSON.parse(views);
        let contextMenu = (cell) => {
            let subMenus = [];
            for (let field of ['tennongdan','dienthoai','ten','dientich','ngaysa','toado','tinhtrang_hoanthanh','ghichu']) {
                let column = tblDanhSachThuaRuong.getColumn(field);
                let visible = column.isVisible();
                subMenus.push({
                    label: '<i class="fa '
                        + (visible ? 'fa-check-square-o' : 'fa-square-o')
                        + '"></i> ' + column.getDefinition().title,
                    action: () => {
                        if (visible) {
                            column.hide();
                            views[field] = false;
                        }
                        else {
                            column.show();
                            views[field] = true;
                        }
                        localStorage.setItem('quytrinhlua.cayquytrinh.nongdan.views', JSON.stringify(views))
                    }
                })
            }
            return [
                {
                    label: '<i class="fas fa-eye text-info"></i> Xem cây quy trình',
                    action: () => {
                        window.open('/quan-ly/quy-trinh-lua/viewer/cay-quy-trinh/' + cell.getData().id);
                    }
                },
                {
                    label: '<i class="fa fa-eye"></i> Hiển thị',
                    menu: subMenus
                }
            ];
        }
        tblDanhSachThuaRuong = new Tabulator("#tblDanhSachThuaRuong", {
            columns: [
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", formatter: "rownum",
                    width: 40, headerSort: false, hozAlign: 'center', contextMenu},
                {title: "Tên nông dân", field: "tennongdan", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Điện thoại", field: "dienthoai", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Tên thửa ruộng", field: "ten", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Diện tích (ha)", field: "dientich", vertAlign: 'middle', headerSort: false, contextMenu, hozAlign: 'right'},
                {title: "Ngày sạ", field: "ngaysa", vertAlign: 'middle', headerSort: false, contextMenu,
                    formatter: (cell) => {
                        return doi_ngay(cell.getValue());
                    }},
                {title: "Tọa độ", field: "toado", vertAlign: 'middle', headerSort: false, contextMenu,
                    formatter: (cell) => {
                        return cell.getValue() == null ? '' : '<span class="text-success">Đã cập nhật</span>'
                    }},
                {title: "Tình trạng hoàn thành", field: "tinhtrang_hoanthanh", vertAlign: 'middle', headerSort: false,
                    contextMenu, hozAlign: 'right', formatter: (cell) => {
                        return '<span class="text-info font-weight-bolder">' + cell.getValue() + '</span>'
                            + '/' + '<span class="font-weight-bolder mr-1">' + cell.getData().tongquytrinh + '</span>' + 'quy trình'
                    }},
                {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu},
            ],
            height: '465px',
            pagination: 'local',
            paginationSize: 10,
            // ajaxResponse: function (url, params, response) {
            //     return response.danhsach;
            // },
            pageLoaded: function () {
                if (isNull(tblDanhSachThuaRuong) || isUndefined(tblDanhSachThuaRuong)) {
                    return false;
                }
                setTimeout(() => {tblDanhSachThuaRuong.getColumns()[0].updateDefinition()},10);
            },
            dataFiltered: function () {
                if (isNull(tblDanhSachThuaRuong) || isUndefined(tblDanhSachThuaRuong)) {
                    return false;
                }
                setTimeout(() => {tblDanhSachThuaRuong.getColumns()[0].updateDefinition()},10);
            },
            dataChanged: () => {
                tblDanhSachThuaRuong.getColumns()[0].updateDefinition();
            }
        });
        initSearchTable(tblDanhSachThuaRuong,['tennongdan','ten','dienthoai']);

        modal.on('shown.bs.modal', function() {
            tblDanhSachThuaRuong.setData('/api/quan-ly/quy-trinh-lua/cay-quy-trinh/danhsach-thuaruong', {
                muavu_id
            })
        }).modal('show');
    }

    function initNongDan(muavu_id) {
        let modal = initModalTable('tblDanhSachNongDan','Danh Sách Nông Dân');

        let views = localStorage.getItem('quytrinhlua.cayquytrinh.nongdan.views');
        views = isNull(views) ? {} : JSON.parse(views);

        let tblDanhSachNongDan = null;
        let contextMenu = () => {
            let subMenus = [];
            for (let field of ['ma','ten','dienthoai','dienthoai2','danhxung','diachi','ghichu']) {
                let column = tblDanhSachNongDan.getColumn(field);
                let visible = column.isVisible();
                subMenus.push({
                    label: '<i class="fa '
                        + (visible ? 'fa-check-square-o' : 'fa-square-o')
                        + '"></i> ' + column.getDefinition().title,
                    action: () => {
                        if (visible) {
                            column.hide();
                            views[field] = false;
                        }
                        else {
                            column.show();
                            views[field] = true;
                        }
                        localStorage.setItem('quytrinhlua.cayquytrinh.nongdan.views', JSON.stringify(views))
                    }
                })
            }
            return [
                {
                    label: '<i class="fa fa-eye"></i> Hiển thị',
                    menu: subMenus
                }
            ];
        }

        tblDanhSachNongDan = new Tabulator("#tblDanhSachNongDan", {
            columns: [
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", formatter: "rownum",
                    width: 40, headerSort: false, hozAlign: 'center', contextMenu},
                {title: "Mã", field: "ma", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.ma},
                {title: "Tên", field: "ten", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.ten},
                {title: "Danh xưng", field: "danhxung", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.danhxung},
                {title: "Điện thoại", field: "dienthoai", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.dienthoai},
                {title: "Điện thoại 2", field: "dienthoai2", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.dienthoai2},
                {title: "Địa chỉ", field: "diachi", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.diachi},
                {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.ghichu},
            ],
            height: '465px',
            pagination: 'local',
            paginationSize: 10,
            layoutColumnsOnNewData:true,
            pageLoaded: function () {
                if (isNull(tblDanhSachNongDan) || isUndefined(tblDanhSachNongDan)) {
                    return false;
                }
                setTimeout(() => {tblDanhSachNongDan.getColumns()[0].updateDefinition()},10);
            },
            dataFiltered: function () {
                if (isNull(tblDanhSachNongDan) || isUndefined(tblDanhSachNongDan)) {
                    return false;
                }
                setTimeout(() => {tblDanhSachNongDan.getColumns()[0].updateDefinition()},10);
            },
            dataChanged: () => {
                tblDanhSachNongDan.getColumns()[0].updateDefinition();
            }
        });
        initSearchTable(tblDanhSachNongDan,['ma','dienthoai','dienthoai2','ten']);

        modal.on('shown.bs.modal', function() {
            tblDanhSachNongDan.setData('/api/quan-ly/quy-trinh-lua/cay-quy-trinh/danhsach-nongdan', {
                muavu_id
            })
        }).modal('show');
    }

    function initHoanThanh(muavu_id, giaidoan_id) {
        let modal = initModalTable('tblDanhSachHoanThanh','Danh Sách Thửa Ruộng Hoàn Thành');

        let tblDanhSachHoanThanh = null;
        let views = localStorage.getItem('quytrinhlua.cayquytrinh.hoanthanh.views');
        views = isNull(views) ? {} : JSON.parse(views);
        let contextMenu = (cell) => {
            let subMenus = [];
            for (let field of ['tennongdan','dienthoai','ten','dientich','ngaysa','toado','tinhtrang_hoanthanh','ghichu']) {
                let column = tblDanhSachHoanThanh.getColumn(field);
                let visible = column.isVisible();
                subMenus.push({
                    label: '<i class="fa '
                        + (visible ? 'fa-check-square-o' : 'fa-square-o')
                        + '"></i> ' + column.getDefinition().title,
                    action: () => {
                        if (visible) {
                            column.hide();
                            views[field] = false;
                        }
                        else {
                            column.show();
                            views[field] = true;
                        }
                        localStorage.setItem('quytrinhlua.cayquytrinh.hoanthanh.views', JSON.stringify(views))
                    }
                })
            }
            return [
                {
                    label: '<i class="fas fa-eye text-info"></i> Xem cây quy trình',
                    action: () => {
                        window.open('/quan-ly/quy-trinh-lua/viewer/cay-quy-trinh/' + cell.getData().id);
                    }
                },
                {
                    label: '<i class="fa fa-eye"></i> Hiển thị',
                    menu: subMenus
                }
            ];
        }
        tblDanhSachHoanThanh = new Tabulator("#tblDanhSachHoanThanh", {
            columns: [
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", formatter: "rownum",
                    width: 40, headerSort: false, hozAlign: 'center', contextMenu},
                {title: "Tên nông dân", field: "tennongdan", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Điện thoại", field: "dienthoai", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Tên thửa ruộng", field: "ten", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Diện tích (ha)", field: "dientich", vertAlign: 'middle', headerSort: false, contextMenu, hozAlign: 'right'},
                {title: "Ngày sạ", field: "ngaysa", vertAlign: 'middle', headerSort: false, contextMenu,
                    formatter: (cell) => {
                        return doi_ngay(cell.getValue());
                    }},
                {title: "Tọa độ", field: "toado", vertAlign: 'middle', headerSort: false, contextMenu,
                    formatter: (cell) => {
                        return cell.getValue() == null ? '' : '<span class="text-success">Đã cập nhật</span>'
                    }},
                {title: "Tình trạng hoàn thành", field: "tinhtrang_hoanthanh", vertAlign: 'middle', headerSort: false,
                    contextMenu, hozAlign: 'right', formatter: (cell) => {
                        return '<span class="text-info font-weight-bolder">' + cell.getValue() + '</span>'
                            + '/' + '<span class="font-weight-bolder mr-1">' + cell.getData().tongquytrinh + '</span>' + 'quy trình'
                    }},
                {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu},
            ],
            height: '465px',
            pagination: 'local',
            paginationSize: 10,
            pageLoaded: function () {
                if (isNull(tblDanhSachHoanThanh) || isUndefined(tblDanhSachHoanThanh)) {
                    return false;
                }
                setTimeout(() => {tblDanhSachHoanThanh.getColumns()[0].updateDefinition()},10);
            },
            dataFiltered: function () {
                if (isNull(tblDanhSachHoanThanh) || isUndefined(tblDanhSachHoanThanh)) {
                    return false;
                }
                setTimeout(() => {tblDanhSachHoanThanh.getColumns()[0].updateDefinition()},10);
            },
            dataChanged: () => {
                tblDanhSachHoanThanh.getColumns()[0].updateDefinition();
            }
        });
        initSearchTable(tblDanhSachHoanThanh,['tennongdan','ten','dienthoai']);

        modal.on('shown.bs.modal', function() {
            tblDanhSachHoanThanh.setData('/api/quan-ly/quy-trinh-lua/cay-quy-trinh/danhsach-hoanthanh', {
                muavu_id, giaidoan_id
            })
        }).modal('show');
    }

    function initMap(toados) {
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 9,
            center: { lat: 9.767852, lng: 105.061801 },
        });
        // Set LatLng and title text for the markers. The first marker (Boynton Pass)
        // receives the initial focus when tab is pressed. Use arrow keys to
        // move between markers; press tab again to cycle through the map controls.
        // const tourStops = [
        //     [{ lat: 9.754884, lng: 105.032571 }, "Test 1"],
        //     [{ lat: 9.762922, lng: 105.041569 }, "Test 2"],
        //     [{ lat: 9.791672, lng: 105.093349 }, "Test 3"],
        //     [{ lat: 9.783410, lng: 105.109078 }, "Test 4"],
        //     [{ lat: 9.743147, lng: 105.057599 }, "Test 5"],
        // ];
        // Create an info window to share between markers.
        const infoWindow = new google.maps.InfoWindow();

        // Create the markers.
        toados.forEach(([position, title], i) => {
            const marker = new google.maps.Marker({
                position,
                map,
                title: `${i + 1}. ${title}`,
                label: `${i + 1}`,
                optimized: false,
            });

            // Add a click listener for each marker, and set up the info window.
            marker.addListener("click", () => {
                infoWindow.close();
                infoWindow.setContent(marker.getTitle());
                infoWindow.open(marker.getMap(), marker);
            });
        });
        $('#modalToaDo').modal('show');
    }
</script>
