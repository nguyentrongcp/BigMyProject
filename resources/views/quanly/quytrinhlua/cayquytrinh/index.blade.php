@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item">Quy Trình Lúa</li>
    <li class="breadcrumb-item active">Quy Trình Sử Dụng Phân Thuốc</li>
@stop

@section('body')
    <div class="content-wrapper">
        <section class="content">
            <div class="row py-3">
                <div class="col">
                    <div class="card card-outline card-info mb-0">
                        <div class="card-body">
                            <div class="d-flex box-search-table mb-1" data-target="tblDanhSach">
{{--                                <div class="input-search input-with-icon">--}}
{{--                                    <input class="form-control non-border" type="text" placeholder="Nhập từ khóa tìm kiếm...">--}}
{{--                                    <span class="icon">--}}
{{--                                        <i class="fa fa-times"></i>--}}
{{--                                    </span>--}}
{{--                                </div>--}}
{{--                                <button class="btn bg-gradient-secondary excel font-weight-bolder">--}}
{{--                                    <i class="fas fa-download mr-1"></i>--}}
{{--                                    Xuất Excel--}}
{{--                                </button>--}}
                                <div class="ml-auto d-flex">
                                    <div style="width: 300px">
                                        <select id="selMuaVu"></select>
                                    </div>
                                    <button class="btn btn-default ml-1" id="btnLamMoi">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                    @if(in_array('quy-trinh-lua.quy-trinh.them-moi',$info->phanquyen) !== false)
{{--                                        <button class="btn bg-gradient-success font-weight-bolder ml-1" id="btnThemQuyTrinh">--}}
{{--                                            Thêm Nhóm--}}
{{--                                        </button>--}}
                                        <button class="btn bg-gradient-primary font-weight-bolder ml-1" data-toggle="modal" data-target="#modalThemMoi">
                                            Thêm Mới
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="card card-primary card-outline card-tabs">
                                <div class="card-header p-0 pt-1 border-bottom-0">
                                    <ul class="nav nav-tabs" role="tablist" id="boxTabMain">
                                        <li class="nav-item">
                                            <a class="nav-link active font-weight-bolder" id="tabPhanBon" data-toggle="pill" href="#boxPhanBon" role="tab">
                                                Quy Trình Sử Dụng Phân Bón
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link font-weight-bolder" id="tabThuoc" data-toggle="pill" href="#boxThuoc" role="tab">
                                                Quy Trình Sử Dụng Thuốc
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="boxPhanBon" role="tabpanel">
                                            <!-- The time line -->
                                            <div class="timeline">
                                                <!-- timeline time label -->
                                                <div class="time-label">
                                                    <span class="bg-red">07 ngày trước khi sạ</span>
                                                    <span class="float-right text-muted">
                                                        <span><i class="fa fa-commenting mr-1 text-primary"></i><strong class="text-info">10000</strong> phản hồi</span>
                                                        <span class="mx-2">/</span>
                                                        <span><i class="fa fa-check-square mr-1 text-success"></i><strong class="text-info">1004</strong> hoàn thành</span>
                                                        <span class="mx-2">/</span>
                                                        <span><i class="fas fa-users mr-1"></i><strong class="text-info">5359</strong> thửa ruộng</span>
                                                    </span>
                                                </div>
                                                <!-- /.timeline-label -->
                                                <!-- timeline item -->
                                                <div>
{{--                                                    <i class="fas fa-envelope bg-blue"></i>--}}
                                                    <div class="timeline-item">
                                                        <span class="time">
                                                            <span><i class="fa fa-check-square-o mr-1 text-success"></i><strong class="text-info">1004</strong> đã check</span>
                                                        </span>
                                                        <h3 class="timeline-header font-weight-bolder">PHÂN HỮU CƠ OMEGA 60 25KG (BAO)</h3>
                                                        <div class="timeline-body">
                                                            Cải thiện độ phì của đất, cung cấp dinh dưỡng cho cây trồng
                                                        </div>
                                                        <div class="timeline-footer">
                                                            <a class="btn btn-primary btn-sm font-weight-bolder">250,000</a>
                                                            <strong> X </strong>
                                                            <a class="btn btn-info btn-sm font-weight-bolder">4</a>
                                                            <strong> = </strong>
                                                            <a class="btn btn-danger btn-sm font-weight-bolder">1,000,000</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- END timeline item -->
                                                <!-- timeline item -->
                                                <div>
                                                    <i class="fas fa-user bg-green"></i>
                                                    <div class="timeline-item">
                                                        <span class="time"><i class="fas fa-clock"></i> 5 mins ago</span>
                                                        <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request</h3>
                                                    </div>
                                                </div>
                                                <!-- END timeline item -->
                                                <!-- timeline item -->
                                                <div>
                                                    <i class="fas fa-comments bg-yellow"></i>
                                                    <div class="timeline-item">
                                                        <span class="time"><i class="fas fa-clock"></i> 27 mins ago</span>
                                                        <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>
                                                        <div class="timeline-body">
                                                            Take me to your leader!
                                                            Switzerland is small and neutral!
                                                            We are more like Germany, ambitious and misunderstood!
                                                        </div>
                                                        <div class="timeline-footer">
                                                            <a class="btn btn-warning btn-sm">View comment</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- END timeline item -->
                                                <!-- timeline time label -->
                                                <div class="time-label">
                                                    <span class="bg-green">3 Jan. 2014</span>
                                                </div>
                                                <!-- /.timeline-label -->
                                                <!-- timeline item -->
                                                <div>
                                                    <i class="fa fa-camera bg-purple"></i>
                                                    <div class="timeline-item">
                                                        <span class="time"><i class="fas fa-clock"></i> 2 days ago</span>
                                                        <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>
                                                        <div class="timeline-body">
                                                            <img src="https://placehold.it/150x100" alt="...">
                                                            <img src="https://placehold.it/150x100" alt="...">
                                                            <img src="https://placehold.it/150x100" alt="...">
                                                            <img src="https://placehold.it/150x100" alt="...">
                                                            <img src="https://placehold.it/150x100" alt="...">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- END timeline item -->
                                                <!-- timeline item -->
                                                <div>
                                                    <i class="fas fa-video bg-maroon"></i>

                                                    <div class="timeline-item">
                                                        <span class="time"><i class="fas fa-clock"></i> 5 days ago</span>

                                                        <h3 class="timeline-header"><a href="#">Mr. Doe</a> shared a video</h3>

                                                        <div class="timeline-body">
                                                            <div class="embed-responsive embed-responsive-16by9">
                                                                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/tMWkeBIohBs" allowfullscreen></iframe>
                                                            </div>
                                                        </div>
                                                        <div class="timeline-footer">
                                                            <a href="#" class="btn btn-sm bg-maroon">See comments</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- END timeline item -->
                                                <div>
                                                    <i class="fas fa-clock bg-gray"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="boxThuoc" role="tabpanel">
                                            <div id="tblDanhSachThuoc"></div>
                                        </div>
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
    @include('quanly.quytrinhlua.quytrinh-sudung.js')
@stop
