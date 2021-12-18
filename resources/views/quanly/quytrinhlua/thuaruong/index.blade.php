@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Quy Trình Lúa</li>
    <li class="breadcrumb-item active">Anh Nông Dân Số 1 / Mùa Vụ Đông Xuân 2021</li>
@stop

@section('body')
    <div class="content-wrapper">
        <section class="content">
            <div class="row py-3">
                <div class="col">
                    <div class="card card-outline card-info mb-0">
                        <div class="card-body">
                            <div class="card card-primary card-outline card-tabs">
                                <div class="card-header p-0 pt-1 border-bottom-0">
                                    <ul class="nav nav-tabs" role="tablist" id="boxTabMain">
                                        <li class="nav-item">
                                            <a class="nav-link active font-weight-bolder" id="tabPhanBon" data-title="Phân bón"
                                               data-toggle="pill" href="#boxPhanBon" role="tab">
                                                Quy Trình Sử Dụng Phân Bón
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link font-weight-bolder" id="tabThuoc" data-title="Thuốc"
                                               data-toggle="pill" href="#boxThuoc" role="tab">
                                                Quy Trình Sử Dụng Thuốc
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="boxPhanBon" role="tabpanel"></div>
                                        <div class="tab-pane fade" id="boxThuoc" role="tabpanel"></div>
                                    </div>
                                </div>
                                <!-- /.card -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop

@section('js-custom')
    @include('quanly.quytrinhlua.thuaruong.js')
@stop
