<script>
    let tblDanhSach;
    let chinhanhs = JSON.parse('{!! str_replace("'","\'",json_encode($chinhanhs)) !!}');
    chinhanhs.unshift({id: 'all', text: 'Toàn hệ thống'});
    initChiNhanh();
    // initHangHoa();
    initLoai();
    initDanhSach();
    @if($info->id == '1000000000')
    initActionLoc();
    initHangHoa();
    @endif

    function initChiNhanh() {
        $('#selChiNhanh').select2({
            data: chinhanhs,
            minimumResultsForSearch: -1
        })
        @if($info->id != '1000000000')
        $('#selChiNhanh').change(function () {
            if ($(this).val() === 'all') {
                initActionLoc('all');
                initHangHoa('all');
            }
            else {
                initActionLoc();
                initHangHoa();
            }
        }).val('all').trigger('change');
        @endif
    }

    function initHangHoa(type = null) {
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
            placeholder: type === 'all' ? 'Chọn hàng hóa...' : 'Tất cả hàng hóa'
        })
    }

    function initLoai() {
        $('#selLoai').select2({
            minimumResultsForSearch: -1
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
                {title: "Tồn kho", field: "tonkho", vertAlign: 'middle', hozAlign: 'right', sorter: 'number',
                    formatter: (cell) => {
                        return '<span class="text-info font-weight-bolder">' + parseFloat(cell.getValue()) + '</span>';
                    }},
                {title: "Cửa hàng", field: "chinhanh", vertAlign: 'middle'},
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

    function initActionLoc(type = null) {
        $('#btnLoc').off('click').click(() => {
            let chinhanh_id = $('#selChiNhanh').val();
            let hanghoas = $('#selHangHoa').val();
            @if($info->id != '1000000000')
            if (type === 'all' && hanghoas.length === 0) {
                sToast.toast(0,'Để xem tồn kho tất cả cửa hàng. Bạn phải chọn hàng hóa cần tra!');
                return false;
            }
            @endif
            let is_tonkho = $('#selLoai').val() === 'only' ? 1 : '';
            tblDanhSach.setData('/api/quan-ly/hang-hoa/ton-kho/danh-sach', {
                chinhanh_id, is_tonkho,
                hanghoas: JSON.stringify(hanghoas)
            });
        })
    }
</script>
