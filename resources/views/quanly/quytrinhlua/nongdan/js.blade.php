<script>
    let tblDanhSach = null;
    let tblDanhSachMuaVu = null;
    let danhxungs = [
        { id: 'Anh', text: 'Anh' },
        { id: 'Chị', text: 'Chị' },
        { id: 'Chú', text: 'Chú' },
        { id: 'Bác', text: 'Bác' },
        { id: 'Cô', text: 'Cô' },
        { id: 'Dì', text: 'Dì' },
        { id: 'Em', text: 'Em' },
        { id: 'Ông', text: 'Ông' },
        { id: 'Bà', text: 'Bà' },
    ];
    let views = localStorage.getItem('quytrinhlua.nongdan.views');
    views = isNull(views) ? {} : JSON.parse(views);
    init();
    initDanhSach();

    function init() {
        $('#btnLamMoi').click(() => {
            tblDanhSach.setData('/api/quan-ly/quy-trinh-lua/nong-dan/danh-sach');
        })

        @if(in_array('quy-trinh-lua.nong-dan.chinh-sua',$info->phanquyen) === false)
        $('#modalXem .col-thongtin i').remove();
        @endif

        $('#modalDanhSachMuaVu').on('shown.bs.modal', () => {
            if (tblDanhSachMuaVu == null) {
                initTblMuaVu();
            }
        })
    }

    @if(in_array('quy-trinh-lua.nong-dan.them-moi',$info->phanquyen) !== false)
    initActionThemMoi();
    function initActionThemMoi() {
        $('#boxMuaVu .btnThem').click(function() {
            let parent = $($($(this).parent()).parent()).find('.boxMain');
            let boxMuaVu = $('<div class="boxMuaVu card"><div class="card-body"></div></div>');
            // let diachi = $('' +
            //     '<div class="boxDiaChi">' +
            //     '   <div class="form-row">' +
            //     '       <div class="col-4">' +
            //     '           <div class="form-group">' +
            //     '               <label>Chọn tỉnh/thành phố</label>' +
            //     '               <select class="form-group tinh selTinh"></select>' +
            //     '           </div>' +
            //     '       </div>' +
            //     '       <div class="col-4">' +
            //     '           <div class="form-group">' +
            //     '               <label>Chọn quận/huyện/thị xã</label>' +
            //     '               <select class="form-group huyen selHuyen"></select>' +
            //     '           </div>' +
            //     '       </div>' +
            //     '       <div class="col-4">' +
            //     '           <div class="form-group">' +
            //     '               <label>Chọn xã/phường/thị trấn</label>' +
            //     '               <select class="form-group xa selXa"></select>' +
            //     '           </div>' +
            //     '       </div>' +
            //     '   </div>' +
            //     '   <div class="form-group">' +
            //     '       <label>Địa chỉ cụ thể</label>' +
            //     '       <textarea rows="2" class="form-control diachi inpDiaChi" placeholder="Nhập địa chỉ cụ thể..."></textarea>' +
            //     '   </div>' +
            //     '</div>');
            // initDiaChi(diachi);
            let date = "{{ date('Y-m-d') }}";
            boxMuaVu.find('.card-body').append('' +
                '<div class="form-row">' +
                '   <div class="col-6">' +
                '       <div class="form-group">' +
                '           <label>Diện tích (ha)</label>' +
                '           <input type="number" class="form-control inpDienTich" placeholder="Nhập diện tích thửa ruộng...">' +
                '       </div>' +
                '   </div>' +
                '   <div class="col-6">' +
                '       <div class="form-group required">' +
                '           <label>Ngày sạ</label>' +
                '           <input type="date" class="form-control inpNgaySa" value="' + date + '">' +
                '           <span class="error invalid-feedback">Ngày sạ không được bỏ trống!</span>' +
                '       </div>' +
                '   </div>' +
                '</div>' +
                '<div class="form-group">' +
                '   <label>Ghi chú</label>' +
                '   <textarea rows="2" class="form-control inpGhiChu" placeholder="Nhập ghi chú..."></textarea>' +
                '</div>');
            parent.append(boxMuaVu);
            boxMuaVu.find('.inpDienTich').focus();
            boxMuaVu.find('.inpNgaySa').on('input', function() {
                $(this).removeClass('is-invalid');
            });
            autosize(boxMuaVu.find('textarea'));
            $($(this).parent()).find('.btnXoa').removeClass('d-none');
        })

        $('#boxMuaVu .btnXoa').click(function() {
            let boxMuaVu = $($($(this).parent()).parent()).find('.boxMain > .boxMuaVu');
            if (boxMuaVu.length === 1) {
                $(this).addClass('d-none');
            }
            $(boxMuaVu[boxMuaVu.length - 1]).remove();
        })

        offEnterTextarea($('#modalThemMoi input, #modalThemMoi textarea'),() => {$('#modalThemMoi .btnSubmit').click()})
        $('#modalThemMoi input, #modalThemMoi textarea, #modalThemThuaRuong input').on('input', function () {
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
            }
        });

        $('#modalThemMoi .selDanhXung').select2({
            data: danhxungs,
            minimumResultsForSearch: -1
        });

        $('#modalThemMoi').on('shown.bs.modal', function () {
            $(this).find('.inpTen').focus();
        }).on('hidden.bs.modal', function() {
            $(this).find('.is-invalid').removeClass('is-invalid');
        })

        initDiaChi($('#modalThemMoi .diachi-container'));

        $('#modalThemMoi .btnSubmit').click(() => {
            let ten = $('#modalThemMoi .inpTen').val().trim();
            let dienthoai = $('#modalThemMoi .inpDienThoai').val().trim();
            let dienthoai2 = $('#modalThemMoi .inpDienThoai2').val().trim();
            let danhxung = $('#modalThemMoi .selDanhXung').val();
            let tinh = $('#modalThemMoi .selTinh').val();
            let huyen = $('#modalThemMoi .selHuyen').val();
            let xa = $('#modalThemMoi .selXa').val();
            let _diachi = $('#modalThemMoi .inpDiaChi').val().trim();
            let ghichu = $('#modalThemMoi .inpGhiChu').val().trim();
            let lientuc = $('#chkLienTuc')[0].checked;
            let checked = true;

            if (dienthoai === '')  {
                checked = false;
                showError('dienthoai');
            }
            if (ten === '') {
                checked = false;
                showError('ten', 'Tên nông dân không được bỏ trống!');
            }

            let boxMuaVus = $('#boxMuaVu .boxMuaVu');
            let muavus = [];
            $.each(boxMuaVus, function(key, value) {
                let muavu_id = $($(value).parent()).attr('data-value');
                let ten = $($(value).parent()).attr('data-title');
                // let tinh = $(value).find('.selTinh').val();
                // let huyen = $(value).find('.selHuyen').val();
                // let xa = $(value).find('.selXa').val();
                // let _diachi = $(value).find('.inpDiaChi').val().trim();
                let ghichu = $(value).find('.inpGhiChu').val().trim();
                let dientich = $(value).find('.inpDienTich').val();
                let ngaysa = $(value).find('.inpNgaySa').val();
                if (muavu_id === '') {
                    sToast.toast(0,'Mùa vụ không hợp lệ!');
                    checked = false;
                }
                if (ngaysa === '') {
                    $(value).find('.inpNgaySa').addClass('is-invalid');
                    checked = false;
                }
                muavus.push({
                    muavu_id,ghichu,dientich,ngaysa,ten
                })
            })

            if (!checked) {
                return false;
            }

            sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
            $.ajax({
                url: '/api/quan-ly/quy-trinh-lua/nong-dan/them-moi',
                type: 'get',
                dataType: 'json',
                data: {
                    ten, dienthoai, dienthoai2, tinh, huyen, xa, _diachi, ghichu, danhxung, muavus: JSON.stringify(muavus)
                }
            }).done((result) => {
                if (result.succ) {
                    $('#boxMuaVu .boxMuaVu').remove();
                    $('#modalThemMoi input, #modalThemMoi textarea').val('').trigger('input');
                    $('#modalThemMoi .diachi-container select').val(null).trigger('change');
                    lientuc ? $('#modalThemMoi .inpTen').focus() : $('#modalThemMoi').modal('hide');
                    autosize.update($('#modalThemMoi textarea'));
                    tblDanhSach.addData(result.data.model,true);
                }
                else if (!isUndefined(result.type)) {
                    if (!isUndefined(result.erro)) {
                        showError(result.type,result.erro)
                    }
                    else {
                        showError(result.type)
                    }
                }
            });
        });
    }

    // Code thêm mới Thửa Ruộng
    initActionThemThuaRuong();
    $('#modalThemThuaRuong').on('hidden.bs.modal', function () {
        $(this).find('.is-invalid').removeClass('is-invalid');
        if ($('.modal.show').length > 0) {
            $('body').addClass('modal-open');
        }
    }).on('shown.bs.modal', function () {
        $(this).find('.inpDienTich').focus();
    });
    offEnterTextarea($('#modalThemThuaRuong input, #modalThemThuaRuong textarea'),() => {$('#modalThemThuaRuong .btnSubmit').click()});
    $('#modalThemThuaRuong .selMuaVu').select2({
        minimumResultsForSearch: -1
    }).on('select2:select', function () {
        $(this).removeClass('is-invalid')
    })
    function initActionThemThuaRuong(nongdan_id) {
        $('#modalThemThuaRuong .btnSubmit').off('click').click(() => {
            let dientich = parseFloat($('#modalThemThuaRuong .inpDienTich').val());
            let ngaysa = $('#modalThemThuaRuong .inpNgaySa').val();
            let muavu_id = $('#modalThemThuaRuong .selMuaVu').val();
            if (muavu_id == null) {
                $('#modalThemThuaRuong .selMuaVu').addClass('is-invalid');
                return false;
            }
            if (isNaN(dientich) || dientich <= 0) {
                $('#modalThemThuaRuong .inpDienTich').addClass('is-invalid');
                return false;
            }
            if (ngaysa === '') {
                $('#modalThemThuaRuong .inpNgaySa').addClass('is-invalid');
                return false;
            }
            let ghichu = $('#modalThemThuaRuong .inpGhiChu').val().trim();

            sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
            $.ajax({
                url: '/api/quan-ly/quy-trinh-lua/thua-ruong/them-moi',
                type: 'get',
                dataType: 'json',
                data: {
                    dientich,ngaysa,muavu_id,ghichu,nongdan_id
                }
            }).done((result) => {
                if (result.succ) {
                    $('#modalThemThuaRuong input:not([type=date]), #modalThemThuaRuong textarea').val('').trigger('input');
                    autosize.update($('#modalThemThuaRuong textarea'));
                    if ($('#modalDanhSachMuaVu').hasClass('show')) {
                        tblDanhSachMuaVu.addData(result.data.model,true);
                    }
                    $('#modalThemThuaRuong').modal('hide');
                }
                else if (!isUndefined(result.type)) {
                    if (result.type === 'muavu_id') {
                        $('#modalThemThuaRuong .selMuaVu').addClass('is-invalid');
                    }
                    if (result.type === 'dientich') {
                        $('#modalThemThuaRuong .inpDienTich').addClass('is-invalid');
                    }
                    if (result.type === 'ngaysa') {
                        $('#modalThemThuaRuong .inpNgaySa').addClass('is-invalid');
                    }
                }
            });
        });
    }
    // Endcode thêm mới thửa ruộng
    @endif

    function initDanhSach() {
        let xemThongTin = (e, cell) => {
            let data = cell.getData();
            $.each($('#modalXem .col-thongtin'), function(key, col) {
                clickXemThongTin(data,col);
            })
            @if(in_array('quy-trinh-lua.nong-dan.action',$info->phanquyen) !== false)
            if (isNull(data.deleted_at)) {
                $('#modalXem button.delete').attr('class','btn bg-gradient-danger delete')
                    .text('Xóa thông tin').off('click').click(() => {
                    clickXoaThongTin(cell);
                })
            }
            else {
                $('#modalXem button.delete').attr('class','btn bg-gradient-success delete')
                    .text('Phục hồi thông tin').off('click').click(() => {
                    clickPhucHoiThongTin(cell);
                })
            }
            @endif
            $('#modalXem').modal('show');
        }
        let contextMenu = (cell) => {
            let data = cell.getData();
            let subMenus = [];
            for (let col of $('#modalXem .col-thongtin')) {
                let field = $(col).attr('data-field');
                let column = tblDanhSach.getColumn(field);
                let visible = column.isVisible();
                subMenus.push({
                    label: '<i class="fa '
                        + (visible ? 'fa-check-square-o' : 'fa-square-o')
                        + '"></i> ' + $(col).find('strong').text(),
                    action: () => {
                        if (visible) {
                            column.hide();
                            views[field] = false;
                        }
                        else {
                            column.show();
                            views[field] = true;
                        }
                        localStorage.setItem('quytrinhlua.nongdan.views', JSON.stringify(views))
                    }
                })
            }
            let menus = [
                {
                    label: '<i class="fa fa-plus text-primary"></i> Thêm thửa ruộng',
                    action: () => {
                        $('#modalThemThuaRuong').modal('show');
                        initActionThemThuaRuong(data.id);
                    }
                },
                {
                    label: '<i class="fa fa-info-circle text-info"></i> Chi tiết',
                    action: xemThongTin
                },
                    @if(in_array('quy-trinh-lua.nong-dan.action',$info->phanquyen) !== false)
                {
                    label: '<i class="fas ' + (isNull(data.deleted_at) ? 'fa-trash-alt text-danger' : 'fa-trash-restore-alt text-success')
                        + '"></i> ' + (isNull(data.deleted_at) ? 'Xóa' : 'Phục hồi'),
                    action: () => {
                        if (isNull(data.deleted_at)) {
                            clickXoaThongTin(cell);
                        }
                        else {
                            clickPhucHoiThongTin(cell);
                        }
                    }
                },
                    @endif
                {
                    label: '<i class="fa fa-eye"></i> Hiển thị',
                    menu: subMenus
                }
            ];
            if (data.muavu_ketthuc > 0 || data.muavu_hoatdong > 0) {
                menus.unshift({
                    label: '<i class="fa fa-bars text-purple"></i> Danh sách thửa ruộng',
                    action: (e, cell) => {
                        $('#modalDanhSachMuaVu').off('shown.bs.modal').on('shown.bs.modal', () => {
                            if (tblDanhSachMuaVu == null) {
                                initTblMuaVu();
                            }
                            tblDanhSachMuaVu.setData('/api/quan-ly/quy-trinh-lua/thua-ruong/danh-sach?nongdan_id=' + data.id);
                        }).modal('show').find('.title').text(data.ten);
                        initActionThemThuaRuong(data.id);
                    }
                })
            }
            @if(in_array('quy-trinh-lua.nong-dan.chinh-sua',$info->phanquyen) !== false)
            if ($('#modalXem .col-thongtin[data-field=' + cell.getField() + '] i.edit').length > 0) {
                menus.unshift({
                    label: '<i class="fa fa-edit text-primary"></i> Chỉnh sửa',
                    action: (e, cell) => {
                        let field = cell.getField();
                        let value = cell.getValue();
                        let data = cell.getData();
                        let ten = $('#modalXem .col-thongtin[data-field=' + field + ']').attr('data-title');
                        clickSuaThongTin(field,value,ten,data);
                    }
                });
            }
            @endif

            return menus;
        }

        tblDanhSach = new Tabulator("#tblDanhSach", {
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
                // {title: "Công nợ", field: "congno", vertAlign: 'middle', hozAlign: 'right', headerSort: false, contextMenu,
                //     visible: isNull(views) ? true : views.congno,
                //     formatter: (cell) => {
                //         return '<span class="text-danger font-weight-bolder">' + numeral(cell.getValue()).format('0,0') + '</span>';
                //     }},
                {title: "Địa chỉ", field: "diachi", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.diachi},
                {title: "Mùa vụ đã kết thúc", field: "muavu_ketthuc", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.muavu_ketthuc, hozAlign: 'right'},
                {title: "Mùa vụ đang hoạt động", field: "muavu_hoatdong", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.muavu_hoatdong, hozAlign: 'right'},
                {title: "Đăng nhập lần cuối", field: "xacthuc_lancuoi", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.xacthuc_lancuoi,
                    formatter: (cell) => {
                        return doi_ngay(cell.getValue());
                    }},
                {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu,
                    visible: isNull(views) ? true : views.ghichu},
            ],
            @if(in_array('quy-trinh-lua.nong-dan.action',$info->phanquyen) !== false)
            rowFormatter: (row) => {
                if (!isNull(row.getData().deleted_at)) {
                    $(row.getElement()).addClass('row-deleted');
                }
                else {
                    $(row.getElement()).removeClass('row-deleted');
                }
            },
            @endif
            ajaxURL: '/api/quan-ly/quy-trinh-lua/nong-dan/danh-sach',
            height: '450px',
            pagination: 'local',
            paginationSize: 10,
            layoutColumnsOnNewData:true,
            dataFiltered: function () {
                if (isNull(tblDanhSach) || isUndefined(tblDanhSach)) {
                    return false;
                }
                setTimeout(() => {tblDanhSach.getColumns()[0].updateDefinition()},10);
            },
            dataChanged: () => {
                tblDanhSach.getColumns()[0].updateDefinition();
            }
        });
        initSearchTable(tblDanhSach,['ma','dienthoai','dienthoai2','ten']);
    }

    function initTblMuaVu() {
        let contextMenu = (cell) => {
            let data = cell.getData();
            let menus = [
                {
                    label: '<i class="fas fa-eye text-info"></i> Xem cây quy trình',
                    action: () => {
                        window.open('/quan-ly/quy-trinh-lua/viewer/cay-quy-trinh/' + data.id);
                    }
                },
                @if(in_array('quy-trinh-lua.nong-dan.action',$info->phanquyen) !== false)
                {
                    label: '<i class="fas ' + (isNull(data.deleted_at) ? 'fa-trash-alt text-danger' : 'fa-trash-restore-alt text-success')
                        + '"></i> ' + (isNull(data.deleted_at) ? 'Xóa' : 'Phục hồi'),
                    action: () => {
                        if (isNull(data.deleted_at)) {
                            clickXoaThuaRuong(cell);
                        }
                        else {
                            clickPhucHoiThuaRuong(cell);
                        }
                    }
                },
                @endif
            ];
            let field = cell.getField();
            if (['dientich','muavu','ngaysa'].indexOf(field) > -1) {
                menus.unshift({
                    label: '<i class="fa fa-edit text-primary"></i> Chỉnh sửa',
                    action: (e, cell) => {
                        let value = cell.getValue();
                        let data = cell.getData();
                        if (field === 'muavu') {
                            field = 'muavu_id';
                            value = data.muavu_id;
                        }
                        let ten = cell.getColumn().getDefinition().title;
                        clickSuaThuaRuong(field,value,ten,data);
                    }
                });
            }

            return menus;
        }

        tblDanhSachMuaVu = new Tabulator("#tblDanhSachMuaVu", {
            columns: [
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", formatter: "rownum",
                    width: 40, headerSort: false, hozAlign: 'center', contextMenu},
                {title: "Tên mùa vụ", field: "muavu", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Tên gợi nhớ", field: "ten", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Diện tích (ha)", field: "dientich", vertAlign: 'middle', headerSort: false, contextMenu, hozAlign: 'right'},
                {title: "Ngày sạ", field: "ngaysa", vertAlign: 'middle', headerSort: false, contextMenu,
                    formatter: (cell) => {
                        return doi_ngay(cell.getValue());
                    }},
                {title: "Tọa độ", field: "toado", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Trạng thái", field: "status", vertAlign: 'middle', headerSort: false, contextMenu,
                    formatter: (cell) => {
                        return cell.getValue() === 0 ? '<span class="text-danger">Đã kết thúc</span>' : '<span class="text-success">Đang hoạt động</span>';
                    }},
                {title: "Tình trạng hoàn thành", field: "tinhtrang_hoanthanh", vertAlign: 'middle', headerSort: false,
                    contextMenu, hozAlign: 'right', formatter: (cell) => {
                        return '<span class="text-info font-weight-bolder">' + cell.getValue() + '</span>'
                            + '/' + '<span class="font-weight-bolder mr-1">' + cell.getData().tongquytrinh + '</span>' + 'quy trình'
                    }},
                {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu},
            ],
            @if(in_array('quy-trinh-lua.nong-dan.action',$info->phanquyen) !== false)
            rowFormatter: (row) => {
                if (!isNull(row.getData().deleted_at)) {
                    $(row.getElement()).addClass('row-deleted');
                }
                else {
                    $(row.getElement()).removeClass('row-deleted');
                }
            },
            @endif
            height: '400px',
            layoutColumnsOnNewData:true,
            dataFiltered: function () {
                if (isNull(tblDanhSach) || isUndefined(tblDanhSach)) {
                    return false;
                }
                setTimeout(() => {tblDanhSach.getColumns()[0].updateDefinition()},10);
            },
            dataChanged: () => {
                tblDanhSach.getColumns()[0].updateDefinition();
            }
        });
    }

    function clickXemThongTin(data, col) {
        let field = $(col).attr('data-field');
        let ten = $(col).attr('data-title');
        let value = data[field];
        $(col).find('span').text(value);
        @if(in_array('quy-trinh-lua.nong-dan.chinh-sua',$info->phanquyen) !== false)
        let edit = $(col).find('i.edit');
        if (edit.length > 0) {
            edit.off('click').click(() => {
                clickSuaThongTin(field,data[field],ten,data,col);
            })
        }
        @endif
    }

    @if(in_array('quy-trinh-lua.nong-dan.chinh-sua',$info->phanquyen) !== false)
    function clickSuaThongTin(field, value, ten, data, col = null) {
        let onSubmit = () => {
            let value = $('#modalInput .value').val().trim();
            if (field === 'diachi') {
                let _diachi = value;
                let xa = $('#modalInput .xa').val();
                let huyen = $('#modalInput .huyen').val();
                let tinh = $('#modalInput .tinh').val();
                value = JSON.stringify({
                    _diachi, xa, huyen, tinh
                })
            }
            if ((field === 'ten' || field === 'dienthoai') && value === '') {
                showErrorModalInput(ten + ' không được bỏ trống!');
                return false;
            }
            sToast.confirm('Xác nhận cập nhật ' + ten.toLowerCase() + '?','',
                (result) => {
                    if (result.isConfirmed) {
                        sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/quy-trinh-lua/nong-dan/cap-nhat',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                id: data.id,
                                field, value
                            }
                        }).done((result) => {
                            if (result.succ) {
                                $('#modalInput').modal('hide');
                                tblDanhSach.updateData([{...result.data.model}]);
                                if (!isNull(col)) {
                                    clickXemThongTin(data,col);
                                }
                            }
                        });
                    }
                });
        }
        if (['ten','dienthoai','dienthoai2'].indexOf(field) !== -1) {
            mInput(data.ten,value,field === 'ten' || field === 'dienthoai').text(ten,ten + '...',onSubmit);
        }
        if (field === 'ghichu') {
            mInput(data.ten,value).textarea(ten,ten + '...',onSubmit);
        }
        if (field === 'danhxung') {
            mInput(data.ten,value).select2(ten,'',danhxungs,true,onSubmit);
        }
        if (field === 'diachi') {
            mInput(data.ten,data._diachi).diachi(onSubmit);
        }
    }
    function clickSuaThuaRuong(field, value, ten, data) {
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
            if (field === 'muavu_id' && value == null) {
                showErrorModalInput('Bạn chưa chọn mùa vụ!');
                return false;
            }
            sToast.confirm('Xác nhận cập nhật ' + ten.toLowerCase() + '?','',
                (result) => {
                    if (result.isConfirmed) {
                        sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/quy-trinh-lua/thua-ruong/cap-nhat',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                id: data.id,
                                field, value
                            }
                        }).done((result) => {
                            if (result.succ) {
                                $('#modalInput').modal('hide');
                                tblDanhSachMuaVu.updateData([{...result.data.model}]);
                            }
                            else if (!isUndefined(result.erro)) {
                                showErrorModalInput(result.erro);
                            }
                        });
                    }
                });
        }
        if (field === 'dientich') {
            mInput(data.ten,value,true).number(ten,ten + '...',onSubmit);
        }
        if (field === 'ngaysa') {
            mInput(data.ten,value,true).date(ten,'Nhập ' + ten.toLowerCase() + '...',onSubmit);
        }
        if (field === 'muavu_id') {
            let muavus = JSON.parse('{!! str_replace("'","\'",$muavus) !!}');
            muavus.forEach((value) => {
                value.text = value.ten;
            })
            mInput(data.ten,value,true).select2(ten,'',muavus,true,onSubmit);
        }
    }
    @endif

    @if(in_array('quy-trinh-lua.nong-dan.action',$info->phanquyen) !== false)
    function clickXoaThongTin(cell) {
        sToast.confirm('Xác nhận xóa thông tin nông dân?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/quy-trinh-lua/nong-dan/xoa',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            id: cell.getData().id
                        }
                    }).done((result) => {
                        if (result.succ) {
                            cell.getTable().updateData([{
                                id: cell.getData().id,
                                deleted_at: result.data.deleted_at
                            }])
                            if ($('#modalXem').hasClass('show')) {
                                $('#modalXem button.delete').attr('class','btn bg-gradient-success delete')
                                    .text('Phục hồi thông tin').off('click').click(() => {
                                    clickPhucHoiThongTin(cell);
                                })
                            }
                        }
                    });
                }
            });
    }

    function clickPhucHoiThongTin(cell) {
        sToast.confirm('Xác nhận phục hồi thông tin nông dân?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang phục hồi dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/quy-trinh-lua/nong-dan/phuc-hoi',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            id: cell.getData().id
                        }
                    }).done((result) => {
                        if (result.succ) {
                            cell.getTable().updateData([{
                                id: cell.getData().id,
                                deleted_at: null
                            }])
                            if ($('#modalXem').hasClass('show')) {
                                $('#modalXem button.delete').attr('class','btn bg-gradient-danger delete')
                                    .text('Xóa thông tin').off('click').click(() => {
                                    clickXoaThongTin(cell);
                                })
                            }
                        }
                    });
                }
            });
    }

    function clickXoaThuaRuong(cell) {
        sToast.confirm('Xác nhận xóa thông tin thửa ruộng?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/quy-trinh-lua/thua-ruong/xoa',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            id: cell.getData().id
                        }
                    }).done((result) => {
                        if (result.succ) {
                            cell.getTable().updateData([{
                                id: cell.getData().id,
                                deleted_at: result.data.deleted_at
                            }])
                        }
                    });
                }
            });
    }

    function clickPhucHoiThuaRuong(cell) {
        sToast.confirm('Xác nhận phục hồi thông tin thửa ruộng?','',
            (result) => {
                if (result.isConfirmed) {
                    sToast.loading('Đang phục hồi dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/quy-trinh-lua/thua-ruong/phuc-hoi',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            id: cell.getData().id
                        }
                    }).done((result) => {
                        if (result.succ) {
                            cell.getTable().updateData([{
                                id: cell.getData().id,
                                deleted_at: null
                            }])
                        }
                    });
                }
            });
    }
    @endif

    function showError(type, erro = '') {
        let inputs = {
            ten: $('#modalThemMoi .inpTen'),
            dienthoai: $('#modalThemMoi .inpDienThoai'),
        }
        if (erro !== '') {
            $(inputs[type].parent()).find('span.error').text(erro);
        }
        inputs[type].addClass('is-invalid');
        inputs[type].focus();
    }
</script>
