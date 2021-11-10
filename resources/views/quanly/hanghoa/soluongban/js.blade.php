<script>
    let tblDanhSach;
    let chinhanhs = JSON.parse('{!! $chinhanhs !!}');
    @if($info->id == '1000000000')
    chinhanhs.unshift({id: 'all', text: 'Toàn hệ thống'});
    @endif
    init();
    initChiNhanh();
    initSelHangHoa();
    initDanhSach();
    initActionLoc();

    function init() {
        $('#fromToDate').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            },
            autoApply: true,
            startDate: '{{ '01-'.date('m-Y') }}'
        });
    }

    function initChiNhanh() {
        $('#selChiNhanh').select2({
            data: chinhanhs,
            minimumResultsForSearch: -1
        })
    }

    function initSelHangHoa() {
        $('#selHangHoa').select2({
            ajax: {
                url: '/api/quan-ly/danh-muc/hang-hoa/tim-kiem',
                data: function (params) {
                    let query = {
                        q: params.term
                    };

                    // Query parameters will be ?search=[term]&type=public
                    return query;
                }
            },
            allowClear: true,
            placeholder: 'Tất cả hàng hóa'
        })
    }

    function initDanhSach() {
        tblDanhSach = new Tabulator("#tblDanhSach", {
            columns: [
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", formatter: "rownum",
                    width: 40, headerSort: false, hozAlign: 'center'},
                {title: "Mã", field: "ma", vertAlign: 'middle', headerSort: false},
                {title: "Tên", field: "ten", vertAlign: 'middle', headerSort: false},
                {title: "ĐVT", field: "donvitinh", vertAlign: 'middle', headerSort: false},
                {title: "Quy cách", field: "quycach", vertAlign: 'middle', headerSort: false, hozAlign: 'right'},
                {title: "SL bán", field: "slban", vertAlign: 'middle', hozAlign: 'right', sorter: 'number',
                    formatter: (cell) => {
                        return '<span class="font-weight-bolder">' + parseFloat(cell.getValue()) + '</span>';
                    }},
                {title: "SL trả", field: "sltra", vertAlign: 'middle', hozAlign: 'right', sorter: 'number',
                    formatter: (cell) => {
                        return '<span class="font-weight-bolder">' + parseFloat(cell.getValue()) + '</span>';
                    }},
                {title: "SL bán thực", field: "slbanthuc", vertAlign: 'middle', hozAlign: 'right', sorter: 'number',
                    formatter: (cell) => {
                        return '<span class="font-weight-bolder">' + parseFloat(cell.getValue()) + '</span>';
                    }},
            ],
            height: '450px',
            movableColumns: false,
            pagination: 'local',
            paginationSize: 10,
            pageLoaded: () => {
                if (isNull(tblDanhSach) || isUndefined(tblDanhSach)) {
                    return false;
                }
                tblDanhSach.getColumns()[0].updateDefinition();
            },
            dataFiltered: function () {
                if (isNull(tblDanhSach) || isUndefined(tblDanhSach)) {
                    return false;
                }
                setTimeout(() => {tblDanhSach.getColumns()[0].updateDefinition()},10);
            }
        });
        initSearchTable(tblDanhSach,['ma','ten']);
    }

    function initActionLoc() {
        $('#btnLoc').click(() => {
            let hanghoas = $('#selHangHoa').val();
            let chinhanh_id = $('#selChiNhanh').val();
            tblDanhSach.setData('/api/quan-ly/hang-hoa/so-luong-ban/danh-sach', {
                begin: getDateRangePicker($('#fromToDate')),
                end: getDateRangePicker($('#fromToDate'),false),
                chinhanh_id, hanghoas: JSON.stringify(hanghoas)
            });
        })
    }
</script>
