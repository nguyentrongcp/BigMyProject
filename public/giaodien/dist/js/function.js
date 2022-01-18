function renderID() {
    let id = "";
    for (let i = 1; i <= 15; i++) {
        id += Math.floor(Math.random() * 10);
    }
    return id;
}
function isUndefined(value) {
    return typeof(value) === 'undefined';
}
function doi_ngay(thoigian, co_gio = true, fullGio = true) {
    if (isUndefined(thoigian)) {
        return '';
    }
    if (thoigian === null || thoigian === '') {
        return thoigian;
    }
    thoigian = thoigian.split(' ');
    let ngay = thoigian[0].split('-');
    let gio = '';
    if (thoigian.length > 1) {
        let _gio = thoigian[1];
        if (!fullGio) {
            _gio = _gio.split(':');
            _gio = [_gio[0],_gio[1]].join(':');
        }
        gio = ' ' + _gio;
    }

    if (co_gio) {
        return [ngay[2],ngay[1],ngay[0]].join('-') + gio;
    }
    else {
        return [ngay[2],ngay[1],ngay[0]].join('-');
    }
}
function new_date(date) {
    let result = date.split(/[- :]/);
    return new Date(result[0], result[1] - 1, result[2], result[3], result[4], result[5]);
}
function isNull(value) {
    return value === null || value === '';
}

const sToast = {
    toast: (succ, title, didClose = null, timer = 3000) => {
        Swal.fire({
            title,
            icon: succ === 0 ? 'error' : 'success',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
                $(toast).click(() => {
                    Swal.close();
                })
            },
            didClose
        })
    },
    notification: (succ, title, didClose = null, timer = 3000) => {
        Swal.fire({
            title,
            icon: succ === 0 ? 'error' : 'success',
            timer,
            timerProgressBar: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Xác Nhận',
            didClose
        })
    },
    loading: (title = 'Đang xử lý. Vui lòng chờ...') => {
        Swal.fire({
            title,
            timer: 60000,
            allowEscapeKey: false,
            timerProgressBar: true,
            toast: true,
            showConfirmButton: false,
            customClass: {
                container: 'swal2-loading'
            },
            didOpen: () => {
                Swal.showLoading()
            }
        })
    },
    confirm: (title, text, onSubmit) => {
        Swal.fire({
            title,
            html: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xác Nhận'
        }).then(onSubmit)
    },
    input: (title, then, inputValue='', inputLabel='') => {
        Swal.fire({
            title,
            input: 'text',
            inputLabel,
            inputValue,
            showCancelButton: true,
        }).then(then);
    },
    datepicker: (title, option, then) => {
        return Swal.fire({
            title,
            html: '<div id="swalDatetimepicker"></div>',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xác Nhận',
            willOpen: function() {
                let datepicker_option = {
                    inline: true,
                    format: 'DD-MM-YYYY'
                }
                if (!isUndefined(option.minDate)) {
                    datepicker_option.minDate = option.minDate;
                }
                if (!isUndefined(option.date)) {
                    datepicker_option.date = new Date(option.date);
                }
                $('#swalDatetimepicker').datetimepicker(datepicker_option);
            },
        }).then(then);
    }
}

const mInput = (title = '', value = '', is_required = false, size = '') => {
    let sttModal = $('.modal.show').length;
    if (sttModal === 1) {
        sttModal = ' modal-secondary';
    }
    else if (sttModal === 2) {
        sttModal = ' modal-third';
    }
    else {
        sttModal = '';
    }
    if (size !== '') {
        size = size.trim() + ' ';
    }
    let modal = $(
        '<div id="modalInput" class="modal fade' + sttModal + '">' +
        '        <div class="modal-dialog ' + size + 'modal-dialog-centered">' +
        '            <div class="modal-content">' +
        '                <div class="modal-body">' +
        '                    <div class="form-group"></div>' +
        '                    <div class="text-right">' +
        '                       <button type="button" class="btn bg-gradient-primary submit font-weight-bolder">Xác Nhận</button>' +
        '                       <button type="button" class="btn bg-gradient-secondary font-weight-bolder ml-1" data-dismiss="modal">Thoát</button>' +
        '                    </div>' +
        '                </div>' +
        '            </div>' +
        '        </div>' +
        '    </div>'
    );
    if (!isNull(title)) {
        modal.find('.modal-content').prepend(
            '<div class="modal-header">' +
            '   <h4 class="w-100 text-center modal-title">' + title + '</h4>' +
            '</div>');
    }
    modal.on('show.bs.modal', () => {
        if (!isNull(value) && !isUndefined(value)) {
            modal.find('.value').val(value).select().trigger('input').trigger('change');
        }
        setTimeout(() => {effectMoreModal()},10)
    })
    $('body').append(modal);
    return {
        number: (label, placeholder, onSubmit, errorText = '') => {
            mInputReturnOptions(modal,'number',label,onSubmit,placeholder,errorText,is_required);
        },
        text: (label, placeholder, onSubmit, errorText = '') => {
            mInputReturnOptions(modal,'text',label,onSubmit,placeholder,errorText,is_required);
        },
        daterangepicker: (label, placeholder, onSubmit) => {
            if (!moment(value,'DD-MM-YYYY',true).isValid()) {
                value = doi_ngay(value);
            }
            mInputReturnOptions(modal,'daterangepicker',label,onSubmit,placeholder,'',is_required);
        },
        date: (label, placeholder, onSubmit, errorText = '') => {
            if (!moment(value,'DD-MM-YYYY',true).isValid()) {
                value = doi_ngay(value);
            }
            mInputReturnOptions(modal,'date',label,onSubmit,placeholder,errorText,is_required);
        },
        select: (label, data, onSubmit, selected = '', errorText='') => {
            if (!isNull(label) && !isUndefined(label)) {
                label = $('<label>' + label + '</label>');
                if (is_required) {
                    label.addClass('required');
                }
                modal.find('.form-group').append(label)
            }
            let select = $('<select class="form-control value"></select>');
            modal.find('.form-group').append(select).append('<span class="error invalid-feedback">' + errorText + '</span>');
            data.forEach((item) => {
                select.append(new Option(item.ten,item.id,item.id === selected));
            })
            modal.find('button.submit').click(onSubmit);
            modal.on('hidden.bs.modal', () => {
                if ($('.modal.show').length > 0) {
                    $('body').addClass('modal-open');
                }
                modal.remove();
            });
            modal.modal('show');
        },
        select2: (label, placeholder, data, hideSearch, onSubmit, errorText='', multiple=false) => {
            if (!isNull(label) && !isUndefined(label)) {
                label = $('<label>' + label + '</label>');
                if (is_required) {
                    label.addClass('required');
                }
                modal.find('.form-group').append(label)
            }
            let select = $('<select' + (multiple ? ' multiple' : '') + ' class="form-control value"></select>');
            modal.find('.form-group').append(select).append('<span class="error invalid-feedback">' + errorText + '</span>');
            let options = {};
            if (typeof(data) === 'string') {
                options.ajax = {
                    url: data
                }
                if (!hideSearch) {
                    options.ajax.delay = 300;
                    options.ajax.data = (params) => {
                        let query = {
                            q: params.term
                        };

                        // Query parameters will be ?search=[term]&type=public
                        return query;
                    }
                }
            }
            else {
                options.data = data;
            }
            if (placeholder !== '') {
                options.placeholder = placeholder;
                options.allowClear = true;
            }
            if (hideSearch) {
                options.minimumResultsForSearch = -1
            }
            $(select).select2(options);
            modal.find('button.submit').click(onSubmit);
            modal.on('hidden.bs.modal', () => {
                if ($('.modal.show').length > 0) {
                    $('body').addClass('modal-open');
                }
                modal.remove();
            });
            modal.css('height','100vh').modal('show');
        },
        textarea: (label, placeholder, onSubmit, errorText = '') => {
            mInputReturnOptions(modal,'textarea',label,onSubmit,placeholder,errorText,is_required);
        },
        numeral: (label, placeholder, onSubmit, errorText = '') => {
            mInputReturnOptions(modal,'numeral',label,onSubmit,placeholder,errorText,is_required);
        },
        password: (label, placeholder, onSubmit, errorText = '') => {
            mInputReturnOptions(modal,'password',label,onSubmit,placeholder,errorText,is_required);
        },
        diachi: (onSubmit) => {
            modal.find('.form-group').remove();
            let container = $('' +
                '<div class="diachi-container">' +
                '   <div class="form-group">' +
                '       <label>Chọn tỉnh/thành phố</label>' +
                '       <select class="form-group tinh"></select>' +
                '   </div>' +
                '   <div class="form-group">' +
                '       <label>Chọn quận/huyện/thị xã</label>' +
                '       <select class="form-group huyen"></select>' +
                '   </div>' +
                '   <div class="form-group">' +
                '       <label>Chọn xã/phường/thị trấn</label>' +
                '       <select class="form-group xa"></select>' +
                '   </div>' +
                '   <div class="form-group">' +
                '       <label>Địa chỉ cụ thể</label>' +
                '       <textarea rows="2" class="form-control diachi value" placeholder="Nhập địa chỉ cụ thể..."></textarea>' +
                '   </div>' +
                '</div>')
            modal.find('.modal-body').prepend(container);
            initDiaChi(container);
            container.find('.value').keypress((e) => {
                if (e.keyCode === 13) {
                    e.preventDefault();
                    modal.find('.submit').click();
                }
            })
            modal.find('button.submit').click(onSubmit);
            modal.on('hidden.bs.modal', () => {
                if ($('.modal.show').length > 0) {
                    $('body').addClass('modal-open');
                }
                modal.remove();
            });
            modal.modal('show');
        }
    }
}

function mInputReturnOptions(modal, type, label, onSubmit, placeholder='', errorText = '', is_required = false) {
    if (!isNull(label) && !isUndefined(label)) {
        label = $('<label>' + label + '</label>');
        if (is_required) {
            label.addClass('required');
        }
        modal.find('.form-group').append(label)
    }
    let input;
    if (type !== 'textarea' && type !== 'password') {
        if (type === 'date') {
            input = $('' +
                '<div class="input-group date" data-target-input="nearest" id="boxInputDate">' +
                '   <input type="text" class="form-control value datetimepicker-input" data-target="#boxInputDate" placeholder="Ngày sinh...">' +
                '   <div class="input-group-append" data-target="#boxInputDate" data-toggle="datetimepicker">' +
                '       <div class="input-group-text"><i class="fa fa-calendar"></i></div>' +
                '   </div>' +
                '</div>');
            input.datetimepicker({
                format: 'DD/MM/YYYY',
                keepOpen: false
            });
        }
        else {
            let inputType = type === 'number' ? 'number' : 'text';
            input = $('<input type="' + inputType + '" class="form-control value" placeholder="' + placeholder + '">')
        }
    }
    else if (type === 'password') {
        input = $('<input type="password" class="form-control value" placeholder="' + placeholder + '">');
    }
    else {
        input = $('<textarea rows="2" class="form-control value" placeholder="' + placeholder + '"></textarea>')
        autosize(input);
    }
    modal.find('.form-group').append(input).append('<span class="error invalid-feedback">' + errorText + '</span>');
    input.on('input', function () {
        if ($(this).hasClass('is-invalid')) {
            $(this).removeClass('is-invalid');
        }
    });
    input.keypress((e) => {
        if (e.keyCode === 13) {
            e.preventDefault();
            modal.find('.submit').click();
        }
    })
    if (type === 'numeral') {
        initInputNumeral(input);
    }
    modal.find('button.submit').click(onSubmit);
    modal.on('hidden.bs.modal', () => {
        if ($('.modal.show').length > 0) {
            $('body').addClass('modal-open');
        }
        modal.remove();
    });
    modal.on('shown.bs.modal', () => {
        modal.find('.value').focus();
    }).modal('show');
}

const mPhieu = (src) => {
    let modal = $('#modalXemPhieu');
    let onMessage = null;
    if (modal.length === 0) {
        modal = $(
            '<div class="modal fade without-footer" id="modalXemPhieu">' +
            '        <div class="modal-dialog">' +
            '            <div class="modal-content">' +
            // '                <span class="c-pointer d-flex" data-dismiss="modal" style="position: absolute; top: 1rem; right: 1rem; z-index: 1; padding: 10px">' +
            // '                    <i class="fa fa-times text-secondary" data-disiss="modal" style="font-size: 16px;"></i>' +
            // '                </span>' +
            '                <div class="modal-body" style="padding: unset; border-radius: unset">' +
            '                    <iframe style="border-radius: 0.3rem" src="' + src + '"></iframe>' +
            '                </div>' +
            '            </div>' +
            '        </div>' +
            '    </div>'
        );
        modal.on('hidden.bs.modal', () => {
            if (onMessage !== null) {
                $(window).off('message','**',onMessage);
            }
            modal.remove();
        }).on('shown.bs.modal', function() {
            $(this).find('.submit').focus();
        });
        $('body').append(modal);
    }
    else {
        modal.find('iframe').attr('src',src);
    }
    return {
        xemphieu: (table = null, onLoad = null) => {
            if (onLoad != null) {
                modal.find('iframe').on('load', onLoad);
            }
            if (table != null) {
                onMessage = (event) => {
                    let data = event.originalEvent.data;
                    let type = data.type;
                    let maphieu = data.maphieu;

                    let id = '';
                    table.getRows().forEach((row) => {
                        if (row.getData().maphieu === maphieu) {
                            id = row.getIndex();
                            return;
                        }
                    });
                    if ((type === 'xoa' || type === 'phuc-hoi') && id !== '') {
                        table.updateData([{
                            id,
                            deleted_at: type === 'xoa' ? data.deleted_at : null
                        }]);
                    }

                    if (type === 'huy') {
                        table.getRow(id).delete();
                        $('#modalXemPhieu').modal('hide');
                    }
                }
                $(window).on('message', onMessage);
            }
            if (!modal.hasClass('show')) {
                modal.modal('show');
            }
            if (!modal.hasClass('without-footer')) {
                modal.addClass('without-footer').remove('.modal-footer');
            }
        },
        taophieu: (onSubmit, is_taophieu = true) => {
            let footer = $(
                '<div class="modal-footer">' +
                '   <button type="button" class="btn bg-gradient-primary submit">' + (is_taophieu ? 'Tạo Phiếu' : 'Duyệt Phiếu') + '</button>' +
                '   <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>' +
                '</div>'
            );
            footer.find('.submit').click(onSubmit);
            modal.removeClass('without-footer').modal('show').find('.modal-content').append(footer);
        }
    }
}

function initInputNumeral(inputs) {
    $.each(inputs, function(key, input) {
        $(input).attr('data-value', '').on('input', function() {
            if (!$(this).hasClass('non-empty')) {
                if ($(this).val() === '' || $(this).val() === '-') {
                    $(this).attr('data-value', '');
                    return false;
                }
            }
            $(this).attr('data-value', numeral($(this).val()).format('0')).val(numeral($(this).val()).format('0,0'));
        });
    })
}

function offEnterTextarea(textareas, onClick = null) {
    $.each(textareas, function(key, textarea) {
        $(textarea).keypress(function(e) {
            if (e.keyCode === 13 && !e.shiftKey) {
                e.preventDefault();
                if (!isNull(onClick)) {
                    onClick();
                }
            }
        });
    })
}

function convertToSlug(text) {
    if (typeof(text) !== 'string') {
        return '';
    }

    let slug;

    slug = text.toLowerCase();

    slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
    slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
    slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
    slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
    slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
    slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
    slug = slug.replace(/đ/gi, 'd');
    slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|\'|\"|\:|\;|_/gi, '');
    slug = slug.replace(/ /gi, "");
    slug = slug.replace(/\-\-\-\-\-/gi, '');
    slug = slug.replace(/\-\-\-\-/gi, '');
    slug = slug.replace(/\-\-\-/gi, '');
    slug = slug.replace(/\-\-/gi, '');
    slug = slug.replace(/\-/gi, '');
    slug = '@' + slug + '@';
    slug = slug.replace(/\@\-|\-\@|\@/gi, '');

    return slug;
}

function initSearchTable(table, fields, idBoxSearch = null, btnExcel = null) {
    if (table == null) {
        return false;
    }
    idBoxSearch = idBoxSearch == null ? table.element.id : idBoxSearch;
    let boxSearch = $('.box-search-table[data-target=' + idBoxSearch + ']');

    let timeout = null;
    $(boxSearch).find('input').off('input').on('input', function () {
        let input = $(this).val().trim();
        if (timeout !== null) {
            clearTimeout(timeout);
        }
        timeout = setTimeout(() => {
            clearTimeout(timeout);
            if (input === '') {
                table.clearFilter();
            }
            else {
                input = convertToSlug(input);
                table.setFilter(function customFilter(data, input){
                    let checked = false;
                    $.each(fields, function (key, value) {
                        if (convertToSlug(data[value]).indexOf(input) !== -1) {
                            checked = true;
                            return false;
                        }
                    });
                    return checked;
                }, input);
            }
        }, input === '' ? 0 : 300);
    }).val('').trigger('input');
    btnExcel = btnExcel == null ? $(boxSearch).find('button.excel') : btnExcel;
    btnExcel.off('click').click(function () {
        sToast.input('Vui lòng nhập tên bảng!',(result) => {
            if (result.value) {
                if (result.value.trim() !== '') {
                    table.download('xlsx', result.value.trim() + '.xlsx');
                }
            }
        });
    });
    $(boxSearch).find('span.icon').off('click').click(function () {
        $(boxSearch).find('.input-search input').val('').trigger('input');
    });
}

function effectMoreModal() {
    let backdrops = $('.modal-backdrop');
    if (backdrops.length === 2) {
        $(backdrops[1]).css('z-index','1050')
    }
    if (backdrops.length === 3) {
        $(backdrops[1]).css('z-index','1051')
    }
}

function initDiaChi(container,autoOpen = true) {
    // Init search tỉnh
    $(container).find('.tinh').select2({
        ajax: {
            url: '/api/quan-ly/danh-muc/dia-chi/tinh',
            data: function (params) {
                let query = {
                    q: params.term
                };

                // Query parameters will be ?search=[term]&type=public
                return query;
            },
            delay: 300
        },
        allowClear: true,
        placeholder: 'Chọn tỉnh/thành phố...'
    }).change(function() {
        $(container).find('.huyen').val(null).trigger('change');
        if ($(this).val() != null && autoOpen) {
            $(container).find('.huyen').select2('open');
        }
    });

    // Init search huyện
    $(container).find('.huyen').select2({
        ajax: {
            url: '/api/quan-ly/danh-muc/dia-chi/huyen',
            data: function (params) {
                let query = {
                    q: params.term,
                    parent_code: $(container).find('.tinh').val() == null ? '' : $(container).find('.tinh').select2('data')[0].code
                };

                // Query parameters will be ?search=[term]&type=public
                return query;
            }
        },
        allowClear: true,
        placeholder: 'Chọn quận/huyện/thành phố...'
    }).change(function() {
        $(container).find('.xa').val(null).trigger('change');
        if ($(this).val() != null && autoOpen) {
            $(container).find('.xa').select2('open');
        }
    });

    // Init search xã
    $(container).find('.xa').select2({
        ajax: {
            url: '/api/quan-ly/danh-muc/dia-chi/xa',
            data: function (params) {
                let query = {
                    q: params.term,
                    parent_code: $(container).find('.huyen').val() == null ? '' : $(container).find('.huyen').select2('data')[0].code
                };

                // Query parameters will be ?search=[term]&type=public
                return query;
            }
        },
        allowClear: true,
        placeholder: 'Chọn phường/xã/thị trấn...'
    }).change(function() {
        if ($(this).val() != null && autoOpen) {
            setTimeout(() => {$(container).find('.diachi').focus()}, 10)
        }
    });
}

const num2Word2 = function() {
    var t = ["không", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín"],
        r = function(r, n) {
            var o = "",	a = Math.floor(r / 10),	e = r % 10;	return a > 1 ? (o = " " + t[a] + " mươi",
            1 == e && (o += " mốt")) : 1 == a ? (o = " mười",
            1 == e && (o += " một")) : n && e > 0 && (o = " lẻ"),
                5 == e && a >= 1 ? o += " lăm" : 4 == e && a >= 1 ? o += " bốn" : (e > 1 || 1 == e && 0 == a) && (o += " " + t[e]), o
        },
        n = function(n, o) {
            var a = "",	e = Math.floor(n / 100),	n = n % 100;
            return o || e > 0 ? (a = " " + t[e] + " trăm", a += r(n, !0)) : a = r(n, !1), a
        },
        o = function(t, r) {
            var o = "",	a = Math.floor(t / 1e6),	t = t % 1e6;	a > 0 && (o = n(a, r) + " triệu", r = !0);
            var e = Math.floor(t / 1e3),	t = t % 1e3;	return e > 0 && (o += n(e, r) + " ngàn", r = !0),
            t > 0 && (o += n(t, r)), o
        };
    return {
        convert: function(r) {
            if (0 == r) return 'Không đồng';
            let soam = r < 0 ? true : false;
            if (0 === r) return '';
            if (soam) {
                r = Math.abs(r);
            }
            var n = "",	a = "";	do ty = r % 1e9, r = Math.floor(r / 1e9), n = r > 0 ? o(ty, !0) + a + n : o(ty, !1) + a + n,
                a = " tỷ"; while (r > 0);
            n = n.trim();
            if (!soam) {
                let result = '';
                for(let i=0; i<n.length; i++) {
                    if (i === 0) {
                        result += n[i].toUpperCase();
                    }
                    else {
                        result += n[i];
                    }
                }
                return result + ' đồng';
            }
            else {
                return 'Âm ' + n + ' đồng';
            }
        }
    }
}();

function initInfo(_info) {
    let modal = $('' +
        '<div class="modal fade" id="modalInfo">' +
        '    <div class="modal-dialog">' +
        '        <div class="modal-content">' +
        '            <div class="modal-header">' +
        '                <h5 class="modal-title">Thông Tin Cá Nhân</h5>' +
        '            </div>' +
        '            <div class="modal-body row-thongtin">' +
        '                <div class="form-row">' +
        '                    <div class="col-6">' +
        '                        <div class="col-thongtin" data-field="ma">' +
        '                            <strong>Mã</strong>' +
        '                            <span>' + _info.ma + '</span>' +
        '                        </div>' +
        '                    </div>' +
        '                    <div class="col-6">' +
        '                        <div class="col-thongtin" data-field="ten">' +
        '                            <strong>Tên' +
        '                            </strong>' +
        '                            <span>' + _info.ten + '</span>' +
        '                        </div>' +
        '                    </div>' +
        '                </div>' +
        '                <div class="divider my-3"></div>' +
        '                <div class="form-row">' +
        '                    <div class="col-6">' +
        '                        <div class="col-thongtin" data-field="taikhoan">' +
        '                            <strong>Tài khoản</strong>' +
        '                            <span>' + _info.taikhoan + '</span>' +
        '                        </div>' +
        '                    </div>' +
        '                    <div class="col-6">' +
        '                        <div class="col-thongtin" data-field="dienthoai">' +
        '                            <strong>Điện thoại</strong>' +
        '                            <span>' + _info.dienthoai + '</span>' +
        '                        </div>' +
        '                    </div>' +
        '                </div>' +
        '                <div class="divider my-3"></div>' +
        '                <div class="form-row">' +
        '                    <div class="col-6">' +
        '                        <div class="col-thongtin" data-field="ngaysinh">' +
        '                            <strong>Ngày sinh' +
        '                            </strong>' +
        '                            <span>' + (isNull(_info.ngaysinh) ? '' : doi_ngay(_info.ngaysinh)) + '</span>' +
        '                        </div>' +
        '                    </div>' +
        '                    <div class="col-6">' +
        '                        <div class="col-thongtin" data-field="chucvu">' +
        '                            <strong>Chức vụ</strong>' +
        '                            <span>' + _info.chucvu_ten + '</span>' +
        '                        </div>' +
        '                    </div>' +
        '                </div>' +
        '                <div class="divider my-3"></div>' +
        '                <div class="col-thongtin" data-field="chinhanh_id">' +
        '                    <strong>Cửa hàng</strong>' +
        '                    <span>' + _info.chinhanh_ten + '</span>' +
        '                </div>' +
        '                <div class="divider my-3"></div>' +
        '                <div class="col-thongtin" data-field="ghichu">' +
        '                    <strong>Ghi chú' +
        '                    </strong>' +
        '                    <span>' + (isNull(_info.ghichu) ? '' : _info.ghichu) + '</span>' +
        '                </div>' +
        '            </div>' +
        '            <div class="modal-footer">' +
        '                <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>' +
        '            </div>' +
        '        </div>' +
        '    </div>' +
        '</div>');

    modal.on('hidden.bs.modal', () => {
        modal.remove();
    }).modal('show');
}

function initTonKhoGiaBan(hanghoa_ma, chinhanh_id = '') {
    sToast.loading('Đang lấy dữ liệu. Vui lòng chờ...');
    $.ajax({
        url: '/api/quan-ly/hang-hoa/ton-kho/lay-thong-tin',
        type: 'get',
        data: {
            hanghoa_ma,
            chinhanh_id
        },
        dataType: 'json'
    }).done((result) => {
        if (result.succ) {
            let data = result.data;
            let modal = $('' +
                '<div class="modal fade" id="modalTonKhoGiaBan">' +
                '    <div class="modal-dialog">' +
                '        <div class="modal-content">' +
                '            <div class="modal-header">' +
                '                <h5 class="modal-title">Danh Sách Tồn Kho & Giá Bán</h5>' +
                '            </div>' +
                '            <div class="modal-body">' +
                '                <div class="font-weight-bolder text-muted" style="font-size: 15px">' + data.ten + '</div>' +
                '                <div class="divider my-3"></div>' +
                '                <div class="form-row">' +
                '                    <div class="col-6 d-flex justify-content-between">' +
                '                        <span class="text-muted">ĐƠN GIÁ:</span>' +
                '                        <span class="font-weight-bolder text-danger">' + numeral(data.dongia).format('0,0') + '</span>' +
                '                    </div>' +
                '                    <div class="col-6 d-flex justify-content-between">' +
                '                        <span class="text-muted">TỒN KHO:</span>' +
                '                        <span class="font-weight-bolder text-info">' + data.tonkho + '</span>' +
                '                    </div>' +
                '                </div>' +
                '                <div class="boxTonKho"></div>' +
                '            </div>' +
                '            <div class="modal-footer">' +
                '                <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>' +
                '            </div>' +
                '        </div>' +
                '    </div>' +
                '</div>');
            data.danhsach.forEach((value) => {
                modal.find('.boxTonKho').append('<div class="divider my-3"></div>').append('' +
                    '<div class="d-flex justify-content-between">' +
                    '   <span class="text-secondary">' + value.ten + '</span>' +
                    '   <span class="text-info font-weight-bolder ml-2">' + value.tonkho + '</span>' +
                    '</div>');
            })
            modal.on('hidden.bs.modal', () => {
                modal.remove();
            }).modal('show');
        }
    });
}

function initDiemDanh(chinhanh_id = '1000000000') {
    sToast.loading('Đang lấy dữ liệu. Vui lòng chờ...');
    $.ajax({
        url: '/api/quan-ly/diem-danh/check-thong-tin',
        type: 'get',
        dataType: 'json'
    }).done((result) => {
        if (result.succ) {
            let data = result.data;
            let text_diemdanh = data.checked === 0 ? 'text-primary' : (data.checked === 1 ? 'text-danger' : 'text-info');
            let modal = $('' +
                '<div class="modal fade" id="modalDiemDanh">' +
                '    <div class="modal-dialog modal-dialog-centered">' +
                '        <div class="modal-content">' +
                '            <div class="modal-header">' +
                '                <h5 class="modal-title font-weight-bolder w-100">Điểm danh ngày ' + doi_ngay(result.data.today) + '</h5>' +
                '            </div>' +
                '            <div class="modal-body">' +
                '               <h5 class="mb-3 font-weight-bolder text-center ' + text_diemdanh + ' title">' +
                (data.checked === 0 ? 'Bạn chưa điểm danh bắt đầu' :
                    (data.checked === 1 ? 'Bạn chưa điểm danh kết thúc' : 'Bạn đã hoàn thành điểm danh hôm nay'))+
                '               </h5>' +
                '               <div class="box-result"></div>' +
                '            </div>' +
                '            <div class="modal-footer">' +
                (data.checked < 2 ? '<button type="button" class="btn ' + (data.checked === 0 ? 'btn-primary' : 'btn-danger') + ' submit">' +
                (data.checked === 0 ? 'Điểm Danh Bắt Đầu' : 'Điểm Danh Kết Thúc') + '</button>' : '') +
                '                <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>' +
                '            </div>' +
                '        </div>' +
                '    </div>' +
                '</div>');
            data.results.forEach((result, stt) => {
                if (stt > 0) {
                    modal.find('.box-result').append('<div class="divider my-3"></div>');
                }
                let boxDiemDanh = $(
                    '               <div class="d-flex">' +
                    '                   <h6>Thời gian bắt đầu:</h6>' +
                    '                   <h6 class="ml-auto font-weight-bolder text-primary tg_batdau">' +
                    result.tg_batdau +
                    '                   </h6>' +
                    '               </div>' +
                    '               <div class="d-flex">' +
                    '                   <h6>Thời gian kết thúc:</h6>' +
                    '                   <h6 class="ml-auto font-weight-bolder text-danger tg_ketthuc">' +
                    (result.tg_ketthuc != null ? result.tg_ketthuc : '---') +
                    '                   </h6>' +
                    '               </div>' +
                    '               <div class="d-flex">' +
                    '                   <h6>Ngày công:</h6>' +
                    '                   <h6 class="ml-auto font-weight-bolder text-info ngaycong">' +
                    result.ngaycong +
                    '                   </h6>' +
                    '               </div>'
                );
                modal.find('.box-result').append(boxDiemDanh);
                if (data.checked < 2 && stt === 0) {
                    modal.find('.submit').click(() => {
                        initActionDiemDanh(chinhanh_id,boxDiemDanh,data.checked === 0)
                    })
                }
            })
            if (data.checked === 0 && data.results.length === 0) {
                modal.find('.submit').click(() => {
                    initActionDiemDanh(chinhanh_id,null,data.checked === 0)
                })
            }
            modal.on('hidden.bs.modal', () => {
                modal.remove();
            }).modal('show');
        }
    });
}

function initActionDiemDanh(chinhanh_id, boxDiemDanh, is_batdau = true) {
    sToast.confirm('Xác nhận điểm danh ' + (is_batdau ? 'bắt đầu' : 'kết thúc'),'',
        (confirmed) => {
            if (confirmed.isConfirmed) {
                sToast.loading('Đang xử lý dữ liệu. Vui lòng chờ...');
                $.ajax({
                    url: '/api/quan-ly/diem-danh/' + (is_batdau ? 'bat-dau' : 'ket-thuc'),
                    type: 'get',
                    data: {
                        chinhanh_id, toado: _location
                    },
                    dataType: 'json'
                }).done((result) => {
                    if (result.succ) {
                        if (is_batdau) {
                            if ($('#modalDiemDanh .box-result > div').length > 0) {
                                $('#modalDiemDanh .box-result').prepend('<div class="divider my-3"></div>');
                            }
                            $('#modalDiemDanh .box-result').prepend(
                                '               <div class="d-flex">' +
                                '                   <h6>Thời gian bắt đầu:</h6>' +
                                '                   <h6 class="ml-auto font-weight-bolder text-primary tg_batdau">' +
                                result.data.tg_batdau +
                                '                   </h6>' +
                                '               </div>' +
                                '               <div class="d-flex">' +
                                '                   <h6>Thời gian kết thúc:</h6>' +
                                '                   <h6 class="ml-auto font-weight-bolder text-danger tg_ketthuc">---</h6>' +
                                '               </div>' +
                                '               <div class="d-flex">' +
                                '                   <h6>Ngày công:</h6>' +
                                '                   <h6 class="ml-auto font-weight-bolder text-info ngaycong">0</h6>' +
                                '               </div>'
                            );
                            $('#modalDiemDanh .title').removeClass('text-primary').addClass('text-danger').text('Bạn chưa điểm danh kết thúc');
                        }
                        else {
                            boxDiemDanh.find('.tg_ketthuc').text(result.data.tg_ketthuc);
                            boxDiemDanh.find('.ngaycong').text(result.data.ngaycong);
                            $('#modalDiemDanh .title').removeClass('text-danger').addClass('text-info').text('Bạn đã hoàn thành điểm danh hôm nay');
                        }
                        $('#modalDiemDanh .submit').remove();
                    }
                });
            }
        })
}

function initThongBaoGia() {
    $('#boxThongBaoGia').empty();
    $.ajax({
        url: '/api/quan-ly/hang-hoa/gia-ban/thong-bao-gia',
        type: 'get',
        dataType: 'json'
    }).done((results) => {
        let thongbaos = results;
        $('#boxThongBaoGia').empty();
        $('#boxThongBaoGia').append('<span class="dropdown-item dropdown-header font-weight-bolder">Bảng thông báo thay đổi giá hôm nay</span>');
        $('#lblSoThongBao').text(thongbaos.length);
        if (thongbaos.length > 0) {
            thongbaos.forEach((thongbao) => {
                let temp = $(
                    '<div class="dropdown-item c-pointer">' +
                    '<span style="white-space: initial" class="font-weight-bolder">' + thongbao.hanghoa + '</span>' +
                    '<div class="form-row">' +
                    '   <div class="col-6 d-flex justify-content-between">' +
                    '       <span>Giá cũ:</span>' +
                    '       <span>' + numeral(thongbao.giacu).format('0,0') + '</span>' +
                    '   </div>' +
                    '   <div class="col-6 d-flex justify-content-between">' +
                    '       <span>Giá mới:</span>' +
                    '       <span class="text-danger font-weight-bolder">' + numeral(thongbao.giamoi).format('0,0') + '</span>' +
                    '   </div>' +
                    '</div>' +
                    '<div class="text-right text-secondary" style="font-size: 12px">Cập nhật lúc ' +
                    '<span class="font-weight-bolder">' + thongbao.created_at.split(' ')[1] + '</span></div>' +
                    '</div>'
                );
                temp.click(() => {
                    initTonKhoGiaBan(thongbao.hanghoa_ma);
                });
                $('#boxThongBaoGia').append('<div class="dropdown-divider"></div>').append(temp);
            })
        }
    });
}

function initChuyenCuaHang() {
    mInput('Chuyển cửa hàng','').select2('Chọn cửa hàng','Vui lòng chọn cửa hàng cần chuyển',
        '/api/quan-ly/danh-muc/chi-nhanh/tim-kiem?selectAll=0',true,
        () => {
            let chinhanh_id = $('#modalInput .value').val();
            let chinhanh_ten = $('#modalInput .value option:selected').text();
            if (isNull(chinhanh_id)) {
                $('#modalInput .value').addClass('is-invalid');
                return false;
            }
            sToast.confirm('Chuyển cửa hàng?',
                'Xác nhận chuyển sang cửa hàng <span class="text-info">' + chinhanh_ten + '</span>',
                (result) => {
                    if (result.isConfirmed) {
                        sToast.loading('Đang chuyển cửa hàng. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/danh-muc/nhan-vien/chuyen-cua-hang',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                id: info.id,
                                chinhanh_id
                            }
                        }).done((result) => {
                            if (result.succ) {
                                sToast.notification(1,'Chuyển cửa hàng thành công.',() => {
                                    location.reload();
                                })
                            }
                        });
                    }
                });
        }, 'Bạn chưa chọn cửa hàng cần chuyển!');
}

function initSoPhieuXuat() {
    $.ajax({
        url: '/api/quan-ly/chuyenkho-noibo/nhap-kho/so-phieuxuat',
        type: 'get',
        dataType: 'json'
    }).done((result) => {
        if (result.succ) {
            $('#lblSoPhieuXuatKho').text(result.data.sophieu > 0 ? result.data.sophieu : '');
        }
    });
}

function initSoPhieuNhap() {
    $.ajax({
        url: '/api/quan-ly/nhap-hang/so-phieunhap',
        type: 'get',
        dataType: 'json'
    }).done((result) => {
        if (result.succ) {
            $('#lblSoPhieuNhap').text(result.data.sophieu > 0 ? result.data.sophieu : '');
        }
    });
}

function initDateRangePicker(date1, date2, option = {}) {
    if (date1 != null) {
        date1.daterangepicker({
            startDate: isUndefined(option.startDate) ? moment().format('DD-MM-YYYY') : option.startDate,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            autoAplly: true,
            autoUpdateInput: false,
            singleDatePicker: true,
            opens: isUndefined(option.opens) ? 'left' : option.opens,
            locale: {
                format: 'DD-MM-YYYY'
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
            if (date2 != null) {
                setTimeout(() => {date2.focus()},200)
            }
        }).on('hide.daterangepicker', function(ev, picker) {
            if ($(this).val() !== '') {
                $(this).val(picker.startDate.format('DD-MM-YYYY'));
            }
        }).val(isUndefined(option.startDate) ? moment().format('DD-MM-YYYY') : option.startDate);
    }

    if (date2 != null) {
        date2.daterangepicker({
            startDate: isUndefined(option.endDate) ? moment().format('DD-MM-YYYY') : option.endDate,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            autoAplly: true,
            autoUpdateInput: false,
            singleDatePicker: true,
            opens: isUndefined(option.opens) ? 'left' : option.opens,
            locale: {
                format: 'DD-MM-YYYY'
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        }).on('hide.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        }).val(isUndefined(option.endDate) ? moment().format('DD-MM-YYYY') : option.endDate);
    }
}

function getDateRangePicker(input, isBegin = true) {
    return isBegin ? input.val() === '' ? '' : input.data('daterangepicker').startDate.format('YYYY-MM-DD')
                   : input.val() === '' ? '' : input.data('daterangepicker').endDate.format('YYYY-MM-DD');
}

function showErrorModalInput(erro = '') {
    if (erro !== '') {
        $('#modalInput span.error').text(erro);
    }
    if ($('#boxInputDate').length > 0) {
        $('#modalInput span.error').addClass('d-block');
    }
    else {
        $('#modalInput .value').addClass('is-invalid');
        $('#modalInput .value').focus();
    }
}

function showLoaderMobile(content = '#boxDanhSach .box-content') {
    if ($(content).find('.mobile-loading').length > 0) {
        return false;
    }
    $(content).html(
        '<li class="item d-flex justify-content-center">' +
        '   <div class="spinner-border text-primary mobile-loading" role="status">' +
        '       <span class="sr-only">Loading...</span>' +
        '   </div>' +
        '</li>');
}

function checkMatKhauMacDinh() {
    $.ajax({
        url: '/api/quan-ly/danh-muc/nhan-vien/check-matkhau-macdinh',
        type: 'get',
        dataType: 'json'
    }).done((result) => {
        if (result.succ) {
            if (!result.data) {
                sToast.notification(0,'Bạn đang dùng mật khẩu mặc định. Nhấn Xác Nhận để thay đổi!',
                    () => { initDoiMatKhau() }, 10000)
            }
        }
    });
}

function initDoiMatKhau() {
    let item = $('<div class="modal fade" id="modalDoiMatKhau">' +
        '    <div class="modal-dialog modal-dialog-centered">' +
        '        <div class="modal-content">' +
        '            <div class="modal-header">' +
        '                <h5 class="modal-title">Thay Đổi Mật Khẩu</h5>' +
        '            </div>' +
        '            <div class="modal-body">' +
        '                <div class="form-group">' +
        '                    <label>Mật khẩu cũ</label>' +
        '                    <input class="form-control inpMKCu" type="password" placeholder="Nhập mật khẩu cũ...">' +
        '                    <span class="error invalid-feedback"></span>' +
        '                </div>' +
        '                <div class="form-group">' +
        '                    <label>Mật khẩu mới</label>' +
        '                    <input class="form-control inpMKMoi" type="password" placeholder="Nhập mật khẩu mới...">' +
        '                    <span class="error invalid-feedback"></span>' +
        '                </div>' +
        '                <div class="form-group">' +
        '                    <label>Nhập lại mật khẩu</label>' +
        '                    <input class="form-control inpMKNhapLai" type="password" placeholder="Nhập lại mật khẩu...">' +
        '                    <span class="error invalid-feedback"></span>' +
        '                </div>' +
        '            </div>' +
        '            <div class="modal-footer">' +
        '                <button type="button" class="btn bg-gradient-primary btnSubmit">Xác Nhận</button>' +
        '                <button type="button" class="btn bg-gradient-secondary" data-dismiss="modal">Thoát</button>' +
        '            </div>' +
        '        </div>' +
        '    </div>' +
        '</div>');
    $('body').append(item);
    item.find('.btnSubmit').click(() => {
        let matkhau_cu = $('#modalDoiMatKhau .inpMKCu').val().trim();
        let matkhau_moi = $('#modalDoiMatKhau .inpMKMoi').val().trim();
        let matkhau_nhaplai = $('#modalDoiMatKhau .inpMKNhapLai').val().trim();
        let checked = true;
        let _showError = (type, erro = '') => {
            let inputs = {
                matkhau_cu: $('#modalDoiMatKhau .inpMKCu'),
                matkhau_moi: $('#modalDoiMatKhau .inpMKMoi'),
                matkhau_nhaplai: $('#modalDoiMatKhau .inpMKNhapLai'),
            }
            if (erro !== '') {
                $(inputs[type].parent()).find('span.error').text(erro);
            }
            inputs[type].addClass('is-invalid');
            inputs[type].focus();
        }
        if (matkhau_moi === '') {
            _showError('matkhau_moi','Mật khẩu mới không được bỏ trống!');
            checked = false;
        }
        if (matkhau_cu === '') {
            _showError('matkhau_cu','Mật khẩu cũ không được bỏ trống!');
            checked = false;
        }
        if (matkhau_moi !== matkhau_nhaplai) {
            _showError('matkhau_nhaplai','Mật khẩu nhập lại không khớp!');
            checked = false;
        }
        if (!checked) {
            return false;
        }
        sToast.loading('Đang xử lý dữ liệu. Vui lòng chờ...')
        $.ajax({
            url: '/api/quan-ly/danh-muc/nhan-vien/doi-mat-khau',
            type: 'get',
            dataType: 'json',
            data: {
                id: info.id,
                matkhau_cu, matkhau_moi, matkhau_nhaplai
            }
        }).done((result) => {
            if (result.succ) {
                $('#modalDoiMatKhau').modal('hide');
            }
            else {
                if (!isUndefined(result.erro)) {
                    _showError(result.type,result.erro)
                }
            }
        });
    });
    item.find('input').on('input', function() {
        if ($(this).hasClass('is-invalid')) {
            $(this).removeClass('is-invalid');
        }
    });
    item.on('shown.bs.modal', function() {
        $(this).find('.inpMKCu').focus();
    }).on('hidden.bs.modal', function() {
        $(this).find('input').val('').trigger('input');
        item.remove();
        checkMatKhauMacDinh();
    }).modal('show');
}

function initSelect2 (element, data, option = {}) {
    if (!isUndefined(option.defaultText)) {
        data.forEach((value) => {
            value.text = typeof(option.defaultText) === 'string' ? value[option.defaultText] :
                (value[option.defaultText[0]] + ' - ' + value[option.defaultText[1]]);
        })
    }
    let count = 0;
    let length = data.length;
    let _option = {
        data
    }

    if (!isUndefined(option.allowClear)) {
        _option.allowClear = option.allowClear;
    }
    if (!isUndefined(option.placeholder)) {
        _option.placeholder = option.placeholder;
    }
    if (!isUndefined(option.templateResult)) {
        _option.templateResult = option.templateResult;
    }
    if (!isUndefined(option.templateSelection)) {
        _option.templateSelection = option.templateSelection;
    }
    if (!isUndefined(option.minimumResultsForSearch)) {
        _option.minimumResultsForSearch = option.minimumResultsForSearch;
    }
    if (!isUndefined(option.matcher)) {
        _option.matcher = (params, _data) => {
            let result = null;
            if (count < 20) {
                $.each(option.matcher, function (key, value) {
                    if (convertToSlug(_data[value]).indexOf(convertToSlug(params.term)) > -1) {
                        result = _data;
                        count++;
                        return false;
                    }
                })
            }

            if (--length === 0) {
                length = data.length;
                count = 0;
            }
            return result;
        }
    }

    element.html(null).select2(_option);

    if (!isUndefined(option.onChange)) {
        element.change(option.onChange);
    }
    if (!isUndefined(option.allowClear)) {
        element.val(null).trigger('change');
    }
}

function showViewerImage(image) {
    let img = $('<img src="' + $(image).attr('src') + '">')
    let viewer = new Viewer(img[0]);
    img.on('hidden', () => {
        viewer.destroy();
    });
    viewer.show();
}

function showViewerUrl(url) {
    let img = $('<img src="' + url + '">')
    let viewer = new Viewer(img[0]);
    img.on('hidden', () => {
        viewer.destroy();
    });
    viewer.show();
}

function clickViewerImage(images) {
    $.each(images, function(key, image) {
        $(image).click(() => {
            showViewerImage($(image));
        });
    })
}
