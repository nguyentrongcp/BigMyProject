@section('js-custom')
    <script>
        init();
        initData();

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
                $('#lblSoNongDan').text(result.data.sonongdan);
                $('#lblSoThuaRuong').text(result.data.sothuaruong);
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
                    '       <span><i class="fa fa-comment mr-1 text-warning"></i><strong class="text-info">' + giaidoan.phanhoi_moi + '</strong> phản hồi mới</span>' +
                    '       <span class="mx-2">/</span>' +
                    '       <span><i class="fa fa-comments mr-1 text-primary"></i><strong class="text-info">' + giaidoan.tongso_phanhoi + '</strong> phản hồi</span>' +
                    '       <span class="mx-2">/</span>' +
                    '       <span><i class="fa fa-check-square mr-1 text-success"></i><strong class="text-info">'
                    + giaidoan.sohoanthanh + '</strong> hoàn thành</span>' +
                    '   </span>' +
                    '</div>');
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
    </script>
@stop
