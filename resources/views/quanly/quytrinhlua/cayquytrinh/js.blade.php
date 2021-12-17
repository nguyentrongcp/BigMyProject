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
            }).done((results) => {
                initTimeline(results);
            });
        }

        function initTimeline(data) {
            let boxTimeline = {
                'Phân bón': $('<div class="timeline"></div>'),
                'Thuốc': $('<div class="timeline"></div>')
            };
            let giaidoan_id = '';
            data.forEach((quytrinh) => {
                if (quytrinh.giaidoan_id !== giaidoan_id) {
                    let color = quytrinh.tu < 0 ? 'bg-secondary' : 'bg-purple';
                    let boxGiaiDoan = $('' +
                        '<div class="time-label">' +
                        '   <span class="' + color + '">' + quytrinh.giaidoan + '</span>' +
                        '   <span class="float-right text-muted">' +
                        '       <span><i class="fa fa-commenting mr-1 text-primary"></i><strong class="text-info">10000</strong> phản hồi</span>' +
                        '       <span class="mx-2">/</span>' +
                        '       <span><i class="fa fa-check-square mr-1 text-success"></i><strong class="text-info">1004</strong> hoàn thành</span>' +
                        '       <span class="mx-2">/</span>' +
                        '       <span><i class="fas fa-users mr-1"></i><strong class="text-info">5359</strong> thửa ruộng</span>' +
                        '   </span>' +
                        '</div>');
                    boxTimeline[quytrinh.phanloai].append(boxGiaiDoan);
                    giaidoan_id = quytrinh.giaidoan_id;
                }
                let boxItem = $('' +
                    '<div>' +
                    '   <div class="timeline-item">' +
                    '       <span class="time">' +
                    '           <span><i class="fa fa-check-square-o mr-1 text-success"></i><strong class="text-info">1004</strong> đã check</span>' +
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
            $('#boxPhanBon').empty().append(boxTimeline['Phân bón'])
            $('#boxThuoc').empty().append(boxTimeline['Thuốc'])
        }
    </script>
@stop
