@extends('mobile.layouts.main')
@section('style')
    <style>

    </style>
@endsection
@section('body')
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <div class="h-100 px-2 py-1" style="overflow-y: auto; margin: 0 -0.5rem">
                <div class="form-group mb-2">
                    <div class="input-group date" id="inpNgay" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" data-target="#inpNgay" placeholder="Chọn ngày...">
                        <div class="input-group-append" data-target="#inpNgay" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <select class="form-control" id="selChiNhanh">
                        @foreach($chinhanhs as $chinhanh)
                        <option {{ $chinhanh->id == $info->chinhanh_id ? 'selected' : '' }} value="{{ $chinhanh->id }}">{{ $chinhanh->text }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card card-primary card-outline card-outline-tabs mb-0">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                            <li class="nav-item flex-grow-1 text-center">
                                <a class="nav-link active font-weight-bolder" data-toggle="pill" href="#boxTienVao" role="tab"
                                   aria-controls="custom-tabs-four-home" aria-selected="true">TIỀN VÀO</a>
                            </li>
                            <li class="nav-item flex-grow-1 text-center">
                                <a class="nav-link font-weight-bolder" data-toggle="pill" href="#boxTienRa" role="tab"
                                   aria-controls="custom-tabs-four-profile" aria-selected="false">TIỀN RA</a>
                            </li>
                            <li class="nav-item flex-grow-1 text-center">
                                <a class="nav-link font-weight-bolder" data-toggle="pill" href="#boxTongCuoi" role="tab"
                                   aria-controls="custom-tabs-four-messages" aria-selected="false">TỔNG CUỐI</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="boxTienVao" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">

                            </div>
                            <div class="tab-pane fade" id="boxTienRa" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab">

                            </div>
                            <div class="tab-pane fade" id="boxTongCuoi" role="tabpanel" aria-labelledby="custom-tabs-four-messages-tab">

                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>

    <div class="modal fade" id="modalDanhSachPhieu">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="font-weight-bolder text-primary">KFSLKFJWLẸLWÈLWIE</div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                    </div>
                    <div class="divider my-2"></div>
                    <div>
                        <div class="font-weight-bolder text-primary">KFSLKFJWLẸLWÈLWIE</div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                    </div>
                    <div class="divider my-2"></div>
                    <div>
                        <div class="font-weight-bolder text-primary">KFSLKFJWLẸLWÈLWIE</div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                    </div>
                    <div>
                        <div class="font-weight-bolder text-primary">KFSLKFJWLẸLWÈLWIE</div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                        <div class="d-flex">
                            <span class="font-weight-bolder">Đối tượng:</span>
                            <span class="ml-2">rwẻwe</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-include')
    @include('mobile.thuchi.js')
@endsection
