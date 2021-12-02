<script>
    let tblDanhSach;
    let tblPhanQuyen;
    initDanhSach();

    function initDanhSach() {
        let contextMenu = (cell) => {
            let data = cell.getData();
            let menus = [
                @if(in_array('danh-muc.chuc-vu.phan-quyen',$info->phanquyen) !== false)
                {
                    label: '<i class="fa fa-user text-dark"></i> Phân quyền',
                    action: () => {
                        sToast.loading('Đang lấy dữ liệu. Vui lòng chờ...')
                        $.ajax({
                            url: '/api/quan-ly/danh-muc/chuc-vu/danhsach-phanquyen',
                            type: 'get',
                            dataType: 'json',
                            data: {
                                id: data.id
                            }
                        }).done((result) => {
                            if (result.succ) {
                                $('#modalPhanQuyen').off('shown.bs.modal').on('shown.bs.modal', () => {
                                    initDanhSachPhanQuyen(result.data,data.id);
                                }).modal('show').find('.title').text(data.ten);
                            }
                        });
                    }
                }
                @endif
            ];
            @if(in_array('danh-muc.chuc-vu.chinh-sua',$info->phanquyen) !== false)
            if (cell.getField() === 'ten') {
                menus.unshift({
                    label: '<i class="fa fa-edit text-primary"></i> Chỉnh sửa',
                    action: (e, cell) => {
                        mInput(data.ten,cell.getValue(),true).text('Tên chức vụ','Nhập tên chức vụ...',
                            () => {
                                let value = $('#modalInput .value').val();
                                if (value === '') {
                                    showErrorModalInput('Tên chức vụ không được bỏ trống!');
                                    return false;
                                }
                                sToast.confirm('Xác nhận cập nhật tên chức vụ?','',
                                    (result) => {
                                        if (result.isConfirmed) {
                                            sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                                            $.ajax({
                                                url: '/api/quan-ly/danh-muc/chuc-vu/cap-nhat',
                                                type: 'get',
                                                dataType: 'json',
                                                data: {
                                                    id: data.id,
                                                    field: 'ten', value
                                                }
                                            }).done((result) => {
                                                if (result.succ) {
                                                    $('#modalInput').modal('hide');
                                                    tblDanhSach.updateData([{...result.data.model}]);
                                                    setTimeout(() => {tblDanhSach.getColumns()[0].updateDefinition()},10);
                                                }
                                                else if (!isUndefined(result.erro)) {
                                                    showErrorModalInput(result.erro);
                                                }
                                            });
                                        }
                                    });
                            });
                    }
                });
            }
            @endif

            return menus;
        }

        tblDanhSach = new Tabulator("#tblDanhSach", {
            columns: [
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", formatter: "rownum",
                    width: 40, headerSort: false, hozAlign: 'center', contextMenu},
                {title: "Loại", field: "loai", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Tên", field: "ten", vertAlign: 'middle', headerSort: false, contextMenu},
            ],
            ajaxURL: '/api/quan-ly/danh-muc/chuc-vu/danh-sach',
            height: '450px',
            movableColumns: false,
            pagination: 'local',
            paginationSize: 10,
            pageLoaded: () => {
                tblDanhSach.getColumns()[0].updateDefinition();
            },
            dataFiltered: function () {
                if (isNull(tblDanhSach) || isUndefined(tblDanhSach)) {
                    return false;
                }
                setTimeout(() => {tblDanhSach.getColumns()[0].updateDefinition()},10);
            }
        });
        initSearchTable(tblDanhSach,['ten']);
    }

    @if(in_array('danh-muc.chuc-vu.phan-quyen',$info->phanquyen) !== false)
    function initDanhSachPhanQuyen(data, id, isChinhSua = false) {
        let _data = JSON.parse(JSON.stringify(data));
        if (isChinhSua) {
            $('#modalPhanQuyen .btnChinhSua').off('click').addClass('d-none');
            $('#modalPhanQuyen .btnCancel').off('click').removeClass('d-none').click(() => {
                initDanhSachPhanQuyen(data,id);
            });
            $('#modalPhanQuyen .btnSubmit').off('click').removeClass('d-none').click(() => {
                actionPhanQuyen(id);
            })
        }
        else {
            $('#modalPhanQuyen .btnChinhSua').off('click').removeClass('d-none').click(() => {
                initDanhSachPhanQuyen(_data,id,true);
            })
            $('#modalPhanQuyen .btnCancel, #modalPhanQuyen .btnSubmit').off('click').addClass('d-none');
        }
        let contextMenu = () => {
            let menus;
            if (isChinhSua) {
                let isTatCa = true;
                let isEmpty = true;
                tblPhanQuyen.getData().forEach((value) => {
                    if (!value.checked) {
                        isTatCa = false;
                    }
                    else {
                        isEmpty = false;
                    }
                })
                menus = [
                    {
                        label: '<i class="fa fa-edit text-primary"></i> Xác nhận',
                        action: () => {
                            actionPhanQuyen(id);
                        }
                    },
                    {
                        label: '<i class="fa fa-times text-danger"></i> Hủy bỏ',
                        action: () => {
                            initDanhSachPhanQuyen(data,id);
                        }
                    },
                    {
                        label: '<i class="fa fa-check-square-o text-success"></i> Chọn tất cả',
                        action: () => {
                            $.each(tblPhanQuyen.getRows(), function (key, row) {
                                if (!row.getData().checked) {
                                    tblPhanQuyen.updateData([{id: row.getIndex(), checked: true}])
                                }
                            })
                            $('#modalPhanQuyen .lblSoQuyen').text(data.length);
                        },
                        disabled: isTatCa
                    },
                    {
                        label: '<i class="fa fa-square-o"></i> Bỏ chọn tất cả',
                        action: () => {
                            $.each(tblPhanQuyen.getRows(), function (key, row) {
                                if (row.getData().checked) {
                                    tblPhanQuyen.updateData([{id: row.getIndex(), checked: false}])
                                }
                            })
                            $('#modalPhanQuyen .lblSoQuyen').text(0);
                        },
                        disabled: isEmpty
                    }
                ];
            }
            else {
                menus = [
                    {
                        label: '<i class="fa fa-edit text-primary"></i> Chỉnh sửa',
                        action: () => {
                            initDanhSachPhanQuyen(_data,id,true);
                        }
                    }
                ];
            }

            return menus;
        }

        tblPhanQuyen = new Tabulator("#tblPhanQuyen", {
            columns: [
                {title: "Chọn", headerHozAlign: 'center', vertAlign: 'middle', field: "checked", contextMenu,
                    headerSort: false, hozAlign: 'center', formatter: (cell) => {
                        return cell.getValue() ? '<i class="fa fa-check text-success"></i>' : '';
                    }},
                {title: "STT", headerHozAlign: 'center', vertAlign: 'middle', field: "stt", contextMenu,
                    headerSort: false, hozAlign: 'center'},
                {title: "Tên", field: "ten", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Loại", field: "loai", vertAlign: 'middle', headerSort: false, contextMenu},
                {title: "Ghi chú", field: "ghichu", vertAlign: 'middle', headerSort: false, contextMenu},
            ],
            height: '450px',
            movableColumns: false,
            groupBy: 'chucnang',
            groupHeader: function(value, count){
                return value + '<span class="text-danger ml-3">(' + count + ' quyền)</span>';
            },
            rowClick: (e, row) => {
                if (isChinhSua) {
                    let checked = row.getData().checked;
                    tblPhanQuyen.updateData([{id: row.getIndex(), checked: !checked}]);
                    let soQuyen = parseInt($('#modalPhanQuyen .lblSoQuyen').text());
                    $('#modalPhanQuyen .lblSoQuyen').text(checked ? --soQuyen : ++soQuyen);
                }
            },
            pagination: 'local',
            paginationSize: 10,
            pageLoaded: () => {
                tblDanhSach.getColumns()[0].updateDefinition();
            },
            dataFiltered: function () {
                if (isNull(tblDanhSach) || isUndefined(tblDanhSach)) {
                    return false;
                }
                setTimeout(() => {tblDanhSach.getColumns()[0].updateDefinition()},10);
            }
        });
        initSearchTable(tblPhanQuyen,['ten','chucnang']);

        let soQuyen = 0;
        _data.forEach((value) => {
            if (value.checked) {
                soQuyen++;
            }
        });
        $('#modalPhanQuyen .lblSoQuyen').text(soQuyen);
        tblPhanQuyen.setData(_data);
    }

    function actionPhanQuyen(id) {
        let phanquyens = [];
        tblPhanQuyen.getData().forEach((value) => {
            if (value.checked) {
                phanquyens.push(value.id);
            }
        })
        sToast.confirm('Xác nhận cập nhật phân quyền chức vụ?','',
            (confirmed) => {
                if (confirmed.isConfirmed) {
                    sToast.loading('Đang cập nhật dữ liệu. Vui lòng chờ...')
                    $.ajax({
                        url: '/api/quan-ly/danh-muc/chuc-vu/phan-quyen',
                        type: 'get',
                        dataType: 'json',
                        data: {
                            id, phanquyens: JSON.stringify(phanquyens)
                        }
                    }).done((result) => {
                        if (result.succ) {
                            initDanhSachPhanQuyen(tblPhanQuyen.getData(),id);
                        }
                    });
                }
            })
    }
    @endif
</script>
