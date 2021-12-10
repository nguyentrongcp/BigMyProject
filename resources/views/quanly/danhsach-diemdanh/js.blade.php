<script>
    let tblDanhSach;
    let tblChiTiet;
    init();
    initTblDanhSach();
    initTblChiTiet();
    initActionLoc();
    @if(in_array('diem-danh.tool',$info->phanquyen) !== false)
    initActionDiemDanh();
    @endif

    function init() {
        $('#selThang').select2({
            minimumResultsForSearch: -1
        });
        $('#selNam').select2({
            minimumResultsForSearch: -1
        });
        $('#selChucVu').select2({
            minimumResultsForSearch: -1,
            allowClear: true,
            placeholder: 'Tất cả chức vụ'
        }).val(null).trigger('change');
        $('#selChiNhanh').select2({
            minimumResultsForSearch: -1,
            allowClear: true,
            placeholder: 'Toàn hệ thống'
        }).val(null).trigger('change');

        $('#modalChiTiet').on('hidden.bs.modal', () => {
            tblChiTiet.clearData();
        })
    }

    function initTblDanhSach() {
        tblDanhSach = new Tabulator("#tblDanhSach", {
            columns: [
                {title: 'STT', field: 'stt', headerHozAlign: 'center', width: 30,
                    hozAlign: 'center', headerSort: false, vertAlign: 'middle'},
                {title: 'Tên', field: 'tennhanvien', headerSort: false, vertAlign: 'middle'},
                {title: 'Chức vụ', field: 'tenchucvu', headerSort: false, vertAlign: 'middle'},
                {title: 'Ngày công', field: 'ngaycong', hozAlign: 'right', headerSort: false, vertAlign: 'middle'},
            ],
            groupBy: 'tenchinhanh',
            groupHeader: function(value, count){
                return value + '<span class="text-danger ml-3">(' + count + ' nhân viên)</span>';
            },
            rowContextMenu: [
                {
                    label: '<i class="fa fa-info-circle text-info"></i> Chi tiết',
                    action: (e, row) => {
                        let data = row.getData();
                        $('#modalChiTiet').off('shown.bs.modal').on('shown.bs.modal', () => {
                            tblChiTiet.setData('/api/quan-ly/diem-danh/lich-su', {
                                nhanvien_id: data.nhanvien_id,
                                thang: data.thang,
                                nam: data.nam
                            })
                        }).modal('show').find('.title').text(data.tennhanvien);
                    }
                }
            ],
            height: '450px',
            dataGrouped: () => {
                if (isNull(tblDanhSach) || isUndefined(tblDanhSach)) {
                    return false;
                }
                tblDanhSach.getData().forEach((value, key) => {
                    tblDanhSach.updateData([{
                        id: value.id,
                        stt: key+1
                    }])
                })
            }
        });

        initSearchTable(tblDanhSach,['tenchinhanh','tennhanvien','tenchucvu']);
    }

    function initTblChiTiet() {
        let contextMenu = (cell) => {
            @if(in_array('diem-danh.tool',$info->phanquyen) !== false)
            let data = cell.getData();
            let menus = [
                {
                    label: '<i class="fa fa-trash-alt text-danger"></i> Xóa',
                    action: (e, cell) => {
                        sToast.loading('Đang xóa dữ liệu. Vui lòng chờ...');
                        $.ajax({
                            url: '/api/quan-ly/diem-danh/xoa',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                id: data.id
                            }
                        }).done((result) => {
                            if (result.succ) {
                                cell.getRow().delete();
                            }
                        });
                    }
                },
                {
                    label: '<i class="fa fa-history text-warning"></i> Reset điểm danh',
                    action: (e, cell) => {
                        sToast.loading('Đang xử lý dữ liệu. Vui lòng chờ...');
                        $.ajax({
                            url: '/api/quan-ly/diem-danh/reset',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                id: data.id
                            }
                        }).done((result) => {
                            if (result.succ) {
                                cell.getTable().updateData([{
                                    id: cell.getData().id,
                                    tg_ketthuc: null,
                                    ngaycong: 0
                                }])
                            }
                        });
                    }
                }
            ]
            return menus;
            @else
            return [];
            @endif
        };
        tblChiTiet = new Tabulator("#tblChiTiet", {
            columns: [
                {title: 'STT', field: 'stt', headerHozAlign: 'center', formatter: 'rownum', width: 30, contextMenu,
                    hozAlign: 'center', headerSort: false, vertAlign: 'middle', },
                {title: 'Ngày', field: 'ngay', headerSort: false, hozAlign: 'center', vertAlign: 'middle', contextMenu,
                    formatter: (cell) => {
                        return doi_ngay(cell.getValue());
                    }},
                {title: 'TG bắt đầu', field: 'tg_batdau', hozAlign: 'center', headerSort: false, contextMenu, vertAlign: 'middle'},
                {title: 'TG kết thúc', field: 'tg_ketthuc', hozAlign: 'center', headerSort: false, contextMenu, vertAlign: 'middle'},
                {title: 'Ngày công', field: 'ngaycong', hozAlign: 'right', headerSort: false, contextMenu, vertAlign: 'middle',
                    formatter: (cell) => {
                        return '<span class="font-weight-bolder ' + (cell.getValue() > 0 ? 'text-info' : 'text-danger') + '">' + cell.getValue() + '</span>';
                    }, bottomCalc: 'sum'},
            ],
            height: '465px',
            dataFiltered: function () {
                if (isNull(tblChiTiet) || isUndefined(tblChiTiet)) {
                    return false;
                }
                setTimeout(() => {tblChiTiet.getColumns()[0].updateDefinition()},10);
            }
        });

        initSearchTable(tblChiTiet,['ngay']);
    }

    function initActionLoc() {
        $('#btnLoc').click(() => {
            let thang = $('#selThang').val();
            let nam = $('#selNam').val();
            let chucvus = $('#selChucVu').val();
            let chinhanhs = $('#selChiNhanh').val();

            tblDanhSach.setData('/api/quan-ly/diem-danh/danh-sach', {
                thang, nam, chucvus: JSON.stringify(chucvus), chinhanhs: JSON.stringify(chinhanhs)
            }).then(() => {tblDanhSach.getColumns()[0].updateDefinition()})
        })
    }

    @if(in_array('diem-danh.tool',$info->phanquyen) !== false)
    function initActionDiemDanh() {
        $('#modalDiemDanh .selChucVu, #modalDiemDanh .selLoai').change(() => {
            initThoiGian();
        }).trigger('change');
        let nhanviens = JSON.parse('{!! str_replace("'","\'",$nhanviens) !!}');
        $('#modalDiemDanh .selNhanVien').select2({
            data: nhanviens
        });

        $('#modalDiemDanh .btnSubmit').click(() => {
            let is_batdau = $('#modalDiemDanh .selLoai').val() === 'begin';
            let nhanvien_id = $('#modalDiemDanh .selNhanVien').val();
            let ngay = $('#modalDiemDanh .inpNgay').val();
            let thoigian = $('#modalDiemDanh .inpThoiGian').val();
            sToast.loading('Đang xử lý dữ liệu. Vui lòng chờ...');
            $.ajax({
                url: '/api/quan-ly/diem-danh/' + (is_batdau ? 'bat-dau' : 'ket-thuc'),
                type: 'get',
                data: {
                    nhanvien_id, ngay, thoigian
                },
                dataType: 'json'
            }).done((result) => {
                if (result.succ) {
                    initThoiGian();
                }
            });
        })
    }

    function initThoiGian() {
        let chucvu = $('#modalDiemDanh .selChucVu').val();
        let gio = chucvu === 'vanphong' ? '07' : '06';
        let batdau = gio + (chucvu === 'vanphong' ? ':5' : ':2') + Math.floor(Math.random() * 10) + ':' + Math.floor(Math.random() * 5) + Math.floor(Math.random() * 10);
        let ketthuc = '17:' + Math.floor(Math.random() * 3) + Math.floor(Math.random() * 10) + ':' + Math.floor(Math.random() * 5) + Math.floor(Math.random() * 10);
        if ($('#modalDiemDanh .selLoai').val() === 'begin') {
            $('#modalDiemDanh .inpThoiGian').val(batdau);
        }
        else {
            $('#modalDiemDanh .inpThoiGian').val(ketthuc);
        }
    }
    @endif
</script>
