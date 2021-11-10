<script>
    function clickXemThongTin{{ url()->current() == route('danh-muc.khach-hang') ? '' : 'KH' }}(data, col) {
        let field = $(col).attr('data-field');
        let ten = $(col).attr('data-title');
        let value = data[field];
        if (field === 'congno') {
            value = numeral(value).format(0,0);
        }
        if (field === 'lancuoi_muahang') {
            value = doi_ngay(value);
        }
        $(col).find('span').text(value);
        let edit = $(col).find('i.edit');
        if (edit.length > 0) {
            edit.off('click').click(() => {
                clickSuaThongTin(field,data[field],ten,data,col);
            })
        }
    }

    function clickSuaThongTin(field, value, ten, data, col = null) {
        let onSubmit = () => {
            let value = $('#modalInput .value').val();
            if (field !== 'caytrong') {
                value = value.trim();
            }
            if (field === 'diachi') {
                let _diachi = value;
                let xa = $('#modalInput .xa').val();
                let huyen = $('#modalInput .huyen').val();
                let tinh = $('#modalInput .tinh').val();
                value = JSON.stringify({
                    _diachi, xa, huyen, tinh
                })
            }
            if (field === 'caytrong') {
                value = value.join(', ');
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
                            url: '/api/quan-ly/danh-muc/khach-hang/cap-nhat',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                id: data.id,
                                field, value
                            }
                        }).done((result) => {
                            if (result.succ) {
                                $('#modalInput').modal('hide');
                                @if(url()->current() == route('danh-muc.khach-hang'))
                                tblDanhSach.updateData([{...result.data.model}]);
                                setTimeout(() => {tblDanhSach.getColumns()[0].updateDefinition()},10);
                                if (!isNull(col)) {
                                    clickXemThongTin(data,col);
                                }
                                @else
                                data = result.data.model;
                                if (field === 'dienthoai' || field === 'ten') {
                                    $('#boxKhachHang .select2-selection__rendered').text(data.dienthoai + ' - ' + data.ten);
                                }
                                clickXemThongTinKH(data,col);
                                setThongTinKH(data);
                                @endif
                            }
                        });
                    }
                });
        }
        if (['ten','dienthoai','dienthoai2','dientich'].indexOf(field) !== -1) {
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
        if (field === 'caytrong') {
            mInput(data.ten,value).select2(ten,'',caytrongs,true,onSubmit,'',true);
        }
    }
</script>
