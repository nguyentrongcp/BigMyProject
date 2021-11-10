<script>
    init();
    actionThemMoi();

    function init() {
        $('#modalThemMoi input, #modalThemMoi textarea').on('input', function () {
            if ($(this).hasClass('is-invalid')) {
                $(this).removeClass('is-invalid');
            }
        }).keypress(function(e) {
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
        });

        $('#modalThemMoi').on('shown.bs.modal', function () {
            $(this).find('.inpTen').focus();
        }).on('hidden.bs.modal', function() {
            $(this).find('.is-invalid').removeClass('is-invalid');
        })

        initDiaChi($('#modalThemMoi .diachi-container'));
    }

    function actionThemMoi() {
        $('#modalThemMoi .btnSubmit').click(() => {
            let ten = $('#modalThemMoi .inpTen').val().trim();
            let dienthoai = $('#modalThemMoi .inpDienThoai').val().trim();
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
                showError('ten', 'Tên khách hàng không được bỏ trống!');
            }

            if (!checked) {
                return false;
            }

            sToast.loading('Đang kiểm tra dữ liệu. Vui lòng chờ...');
            $.ajax({
                url: '/api/quan-ly/danh-muc/doi-tuong/them-moi',
                type: 'get',
                dataType: 'json',
                data: {
                    ten, dienthoai, tinh, huyen, xa, _diachi, ghichu
                }
            }).done((result) => {
                if (result.succ) {
                    $('#modalThemMoi input, #modalThemMoi textarea').val('');
                    $('#modalThemMoi .diachi-container select').val(null).trigger('change');
                    lientuc ? $('#modalThemMoi .inpTen').focus() : $('#modalThemMoi').modal('hide');
                    autosize.update($('#modalThemMoi textarea'));
                    @if(url()->current() == route('danh-muc.doi-tuong'))
                    tblDanhSach.addData(result.data.model,true);
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
