<script>
    $('#inpImport').off('input').on('input', function () {
        ExportToTable(this);
        $(this).val('');
    })

    function ExportToTable(input) {
        sToast.loading('Đang đọc file. Vui lòng chờ...');
        let name = $(input).val().replace(/C:\\fakepath\\/i, '').split('.');
        name = name[name.length - 1].toLowerCase();
        /*Checks whether the file is a valid excel file*/
        if (name === 'xlsx' || name === 'xls') {
            let xlsxflag = false; /*Flag for checking whether excel is .xls format or .xlsx format*/
            if ($(input).val().toLowerCase().indexOf(".xlsx") > 0) {
                xlsxflag = true;
            }
            /*Checks whether the browser supports HTML5*/
            if (typeof (FileReader) != "undefined") {
                let reader = new FileReader();
                reader.onload = function (e) {
                    let data = {};
                    let binary = "";
                    let bytes = new Uint8Array(e.target.result);
                    let length = bytes.byteLength;
                    for (let i = 0; i < length; i++) {
                        binary += String.fromCharCode(bytes[i]);
                    }
                    /*Converts the excel data in to object*/
                    let workbook = null;
                    if (xlsxflag) {
                        workbook = XLSX.read(binary, { type: 'binary' });
                    }
                    else {
                        workbook = XLS.read(binary, { type: 'binary' });
                    }
                    /*Gets all the sheetnames of excel in to a variable*/
                    let sheet_name_list = workbook.SheetNames;

                    let dataSheet = [];
                    sheet_name_list.forEach(function (y) { /*Iterate through all sheets*/
                        /*Convert the cell value to Json*/
                        if (xlsxflag) {
                            data[y] = XLSX.utils.sheet_to_json(workbook.Sheets[y]);
                        }
                        else {
                            data[y] = XLS.utils.sheet_to_row_object_array(workbook.Sheets[y]);
                        }
                        dataSheet.push({id: y, text: y});
                    });
                    let _data = [];
                    $('#modalImport .selSheet').off('change').html(null).select2({
                        minimumResultsForSearch: -1,
                        data: dataSheet
                    }).change(function () {
                        if ($(this).val() == null) {
                            return false;
                        }
                        _data = [];
                        let title = data[$(this).val()];
                        if (title.length > 0) {
                            let data_title = [];
                            let check_title = {};
                            title.forEach((item) => {
                                let _object = {};
                                $.each(item, function (key, _item) {
                                    _object[key.toLowerCase()] = _item;
                                    if (isUndefined(check_title[key])) {
                                        data_title.push({id: key.toLowerCase(), text: key});
                                        check_title[key] = 1;
                                    }
                                });
                                _data.push(_object);
                            })
                            $('#modalImport select:not(.selSheet)').off('change').html(null).select2({
                                data: data_title,
                                minimumResultsForSearch: -1
                            }).val(function() {
                                return $(this).attr('data-title');
                            }).trigger('change');
                        }
                    }).trigger('change');
                    Swal.close();
                    $('#modalImport').modal('show').find('.btnSubmit').off('click').click(() => {
                        actionImport(_data);
                    });
                }
                if (xlsxflag) {/*If excel file is .xlsx extension than creates a Array Buffer from excel*/
                    reader.readAsArrayBuffer($(input)[0].files[0]);
                }
                else {
                    reader.readAsBinaryString($(input)[0].files[0]);
                }
            }
            else {
                sToast.toast(0,'Trình duyệt web của bạn không hỗ trợ HTML 5!')
            }
        }
        else {
            sToast.toast(0,'Định dạng file excel không hợp lệ!');
        }
    }

    function actionImport(_data) {
        sToast.confirm('Xác nhận kết nhập dữ liệu nông dân?','',
            (confirmed) => {
                if (confirmed.isConfirmed) {
                    let data = [];
                    _data.forEach((value) => {
                        let _object = {
                            ten: value[$('#modalImport .selTen').val()],
                            danhxung: value[$('#modalImport .selDanhXung').val()],
                            dienthoai: value[$('#modalImport .selDienThoai').val()],
                            dienthoai2: value[$('#modalImport .selDienThoai2').val()],
                            tinh: value[$('#modalImport .selTinh').val()],
                            huyen: value[$('#modalImport .selHuyen').val()],
                            xa: value[$('#modalImport .selXa').val()],
                            diachi: value[$('#modalImport .selDiaChi').val()],
                            ghichu: value[$('#modalImport .selGhiChu').val()],
                            dientich1: value[$('#modalImport .selDienTich1').val()],
                            ngaysa1: value[$('#modalImport .selNgaySa1').val()],
                            ghichu1: value[$('#modalImport .selGhiChu1').val()],
                            dientich2: value[$('#modalImport .selDienTich2').val()],
                            ngaysa2: value[$('#modalImport .selNgaySa2').val()],
                            ghichu2: value[$('#modalImport .selGhiChu2').val()],
                            dientich3: value[$('#modalImport .selDienTich3').val()],
                            ngaysa3: value[$('#modalImport .selNgaySa3').val()],
                            ghichu3: value[$('#modalImport .selGhiChu3').val()]
                        }
                        let item = {};
                        let count = 0;
                        $.each(_object, function (key, _item) {
                            if (!isUndefined(_item) && !isNull(_item)) {
                                item[key] = _item;
                                count++;
                            }
                        });
                        if (count > 0) {
                            data.push(item);
                        }
                    });
                    Swal.fire({
                        title: 'Đang xử lý. Vui lòng chờ...',
                        html: 'Đã kết nhập được <b class="text-info"><span class="percent">50</span>%</b> (còn <b class="time">5 phút</b> nữa)',
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        allowEnterKey: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                            let ajx = $.ajax({
                                url: '/api/quan-ly/quy-trinh-lua/nong-dan/import',
                                type: 'post',
                                dataType: 'json',
                                data: {
                                    data: JSON.stringify(data)
                                }
                            }).done((result) => {

                            });
                            channel.unbind('progress-import-' + info.id);
                            channel.bind('progress-import-' + info.id, function(data) {
                                $(Swal.getHtmlContainer()).find('.percent').text(data.message.percent);
                                $(Swal.getHtmlContainer()).find('.time').text(data.message.thoigian);
                            })
                        }
                    });
                }
            })
    }
</script>
