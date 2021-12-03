<script>
    let tblDanhSach;
    let chinhanhs = JSON.parse('{!! str_replace("'","\'",json_encode($chinhanhs)) !!}');
    let loaiphieus = [
        { id: 'BH', text: 'PHIẾU BÁN HÀNG' },
        { id: 'KTH', text: 'PHIẾU KHÁCH TRẢ HÀNG' },
        { id: 'NH', text: 'PHIẾU NHẬP HÀNG' },
        { id: 'THNCC', text: 'PHIẾU TRẢ HÀNG NHÀ CUNG CẤP' },
        { id: 'XKNB', text: 'PHIẾU XUẤT KHO NỘI BỘ' },
        { id: 'NKNB', text: 'PHIẾU NHẬP KHO NỘI BỘ' },
        { id: 'TCNKH', text: 'PHIẾU THU CÔNG NỢ KHÁCH HÀNG' },
        { id: 'DCCNKH', text: 'PHIẾU ĐIỀU CHỈNH CÔNG NỢ KHÁCH HÀNG' },
        { id: 'CCNNCC', text: 'PHIẾU CHI CÔNG NỢ NHÀ CUNG CẤP' },
        { id: 'DCCNNCC', text: 'PHIẾU ĐIỀU CHỈNH CÔNG NỢ NHÀ CUNG CẤP' },
        { id: 'DKHH', text: 'PHIẾU ĐẦU KỲ HÀNG HÓA' },
        { id: 'PT', text: 'PHIẾU THU' },
        { id: 'PC', text: 'PHIẾU CHI' },
        { id: 'KSCN', text: 'PHIẾU KẾT SỔ CUỐI NGÀY' },
    ];
    init();
    initSelLoaiPhieu();
    initChiNhanh();
    initActionLoc();
    initTblDanhSach('BH');

    function init() {
        $('#fromToDate').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true
        });
    }

    function initSelLoaiPhieu() {
        $('#selLoaiPhieu').select2({
            data: loaiphieus
        })
    }

    function initChiNhanh() {
        $('#selChiNhanh').select2({
            data: chinhanhs,
            minimumResultsForSearch: -1
        }).val(info.chinhanh_id).trigger('change');
    }

    function initActionLoc() {
        $('#btnLoc').click(() => {
            let chinhanh_id = $('#selChiNhanh').val();
            let loaiphieu = $('#selLoaiPhieu').val();
            initTblDanhSach(loaiphieu);
            tblDanhSach.setData('/api/quan-ly/phieu/danh-sach', {
                begin: getDateRangePicker($('#fromToDate')),
                end: getDateRangePicker($('#fromToDate'),false),
                chinhanh_id, loaiphieu
            });
        })
    }

    function initTblDanhSach(loaiphieu) {
        let columns = [
            {title: 'STT', field: 'stt', headerHozAlign: 'center', formatter: 'rownum', width: 30,
                hozAlign: 'center', headerSort: false, vertAlign: 'middle'},
            {title: 'Thời gian', field: 'created_at', headerSort: false, vertAlign: 'middle',
                formatter: function(cell) {
                    return doi_ngay(cell.getValue());
                }},
            {title: 'Mã phiếu', field: 'maphieu', headerSort: false, vertAlign: 'middle',
                formatter: (cell) => {
                    return '<span class="text-primary">' + cell.getValue() + '</span>';
                }, cellClick: (e, cell) => {
                    mPhieu('/quan-ly/xem-phieu/' + cell.getValue() + '?deletable=1').xemphieu(cell.getTable());
                }}
        ];
        if (['XKNB','NKNB','DKHH','KSCN'].indexOf(loaiphieu) === -1) {
            columns.push({title: 'Đối tượng', field: 'doituong', headerSort: false});
        }
        if (['PT','PC','DCCNKH','TCNKH','KTH','BH','CCNNCC','DCCNNCC'].indexOf(loaiphieu) !== -1) {
            columns.push({title: 'Tiền thanh toán', field: 'tienthanhtoan', headerHozAlign: 'right',
                hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                formatter: function(cell) {
                    return '<span class="text-tienthanhtoan">' + numeral(cell.getValue()).format('0,0') + '</span>';
                }});
            if (loaiphieu === 'PC' || loaiphieu === 'PT') {
                columns.push({title: 'Nội dung', field: 'noidung', headerSort: false});
            }
        }
        if (loaiphieu === 'NH' || loaiphieu === 'THNCC') {
            columns.push({title: 'Tiền thanh toán', field: 'tienthanhtoan', headerHozAlign: 'right',
                hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                formatter: function(cell) {
                    return '<span class="text-tienthanhtoan">' + numeral(cell.getValue()).format('0,0') + '</span>';
                }});
        }
        if (loaiphieu === 'XKNB' || loaiphieu === 'NKNB') {
            columns.push({title: loaiphieu === 'XKNB' ? 'Cửa hàng nhận' : 'Cửa hàng chuyển', field: 'doituong', headerSort: false});
        }
        if (loaiphieu === 'KSCN') {
            columns.push({title: 'Đầu kỳ', field: 'tongthanhtien', headerHozAlign: 'right',
                hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                formatter: function(cell) {
                    return '<span>' + numeral(cell.getValue()).format('0,0') + '</span>';
                }});
            columns.push({title: 'Tổng thu', field: 'phuthu', headerHozAlign: 'right',
                hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                formatter: function(cell) {
                    return '<span class="text-success">' + numeral(cell.getValue()).format('0,0') + '</span>';
                }});
            columns.push({title: 'Tổng chi', field: 'giamgia', headerHozAlign: 'right',
                hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                formatter: function(cell) {
                    return '<span class="text-danger">' + numeral(cell.getValue()).format('0,0') + '</span>';
                }});
            columns.push({title: 'Tổng cuối', field: 'tienthanhtoan', headerHozAlign: 'right',
                hozAlign: 'right', headerSort: false, vertAlign: 'middle',
                formatter: function(cell) {
                    return '<span class="text-info">' + numeral(cell.getValue()).format('0,0') + '</span>';
                }});
        }

        columns.push({title: 'Số phiếu', field: 'sophieu', headerHozAlign: 'right', headerSort: false, hozAlign: 'right', vertAlign: 'middle'});
        columns.push({title: 'NV lập phiếu', field: 'nhanvien', headerSort: false});
        tblDanhSach = new Tabulator("#tblDanhSach", {
            columns,
            rowFormatter: (row) => {
                if (!isNull(row.getData().deleted_at)) {
                    $(row.getElement()).addClass('phieu-daxoa');
                }
                else {
                    $(row.getElement()).removeClass('phieu-daxoa');
                }
            },
            height: '450px',
            pagination: 'local',
            paginationSize: 10,
            dataFiltered: function () {
                if (isNull(tblDanhSach) || isUndefined(tblDanhSach)) {
                    return false;
                }
                setTimeout(() => {tblDanhSach.getColumns()[0].updateDefinition()},10);
            }
        });

        if (loaiphieu === 'DKHH') {
            initSearchTable(tblDanhSach,['maphieu']);
        }
        else {
            initSearchTable(tblDanhSach,['maphieu','doituong']);
        }
    }
</script>
