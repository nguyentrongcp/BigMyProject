<script>
    let caytrongs = JSON.parse('{!! str_replace("'","\'",json_encode($caytrongs)) !!}');
    caytrongs.forEach((value) => {
        value.id = value.text;
    })
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

    @if(in_array('danh-muc.khach-hang.them-moi',$info->phanquyen) !== false)
    initActionThemMoi();
    function initActionThemMoi() {
        $('#modalThemMoi input, #modalThemMoi textarea').keypress(function(e) {
            let keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                if ($('#modalThemMoi').hasClass('show')) {
                    $('#modalThemMoi .btnSubmit').click();
                }
                else {
                    $('#modalThemQuyDoi .btnSubmit').click();
                }
                e.preventDefault();
                return false;
            }
        }).on('input', function () {
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
            }
        });

        $('#modalThemMoi .selCayTrong').select2({
            data: caytrongs,
            placeholder: 'Chọn cây trồng...',
            allowClear: true
        }).val(null).trigger('change');

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
            let caytrong = $('#modalThemMoi .selCayTrong').val();
            let dientich = $('#modalThemMoi .inpDienTich').val().trim();
            let ghichu = $('#modalThemMoi .inpGhiChu').val().trim();
            let lientuc = $('#chkLienTuc')[0].checked;
            let checked = true;

            if (dienthoai === '')  {
                checked = false;
                showError('dienthoai');
            }
            if (ten === '') {
                checked = false;
                showError('ten', 'Tên khách hàng không được bỏ trống!');
            }

            if (!checked) {
                return false;
            }

            caytrong = caytrong.join(', ');

            sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
            $.ajax({
                url: '/api/quan-ly/danh-muc/khach-hang/them-moi',
                type: 'get',
                dataType: 'json',
                data: {
                    ten, dienthoai, dienthoai2, tinh, huyen, xa, _diachi, caytrong, dientich, ghichu, danhxung
                }
            }).done((result) => {
                if (result.succ) {
                    $('#modalThemMoi input, #modalThemMoi textarea:not(.select2-search__field)').val('').trigger('input');
                    $('#modalThemMoi .diachi-container select, #modalThemMoi .selCayTrong').val(null).trigger('change');
                    lientuc ? $('#modalThemMoi .inpTen').focus() : $('#modalThemMoi').modal('hide');
                    autosize.update($('#modalThemMoi textarea'));
                    @if(url()->current() == route('danh-muc.khach-hang'))
                    tblDanhSach.addData(result.data.model,true);
                    @else
                    let khachhang = result.data.model;
                    $('#selKhachHang').append(new Option(khachhang.dienthoai + ' - ' + khachhang.ten, khachhang.id))
                        .trigger('change').val(khachhang.id).trigger('change');
                    setThongTinKH(khachhang);
                    @endif
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
