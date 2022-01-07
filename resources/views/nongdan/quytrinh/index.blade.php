@extends('nongdan.layouts.main')
@section('style-custom')
    <style>
        #boxMain .timeline-trangthai {
            border-top: 1px solid rgba(0,0,0,.125);
            border-bottom: 1px solid rgba(0,0,0,.125);
            display: flex;
            justify-content: space-between;
        }
        #boxMain .timeline-ghichu > div {
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
        #boxMain .boxPhanHoi .item-phanhoi:not(:first-child) {
            border-top: 1px solid #ebebeb;
            padding-top: 0.25rem;
            margin-top: 0.25rem;
        }
        #boxMain .boxPhanHoi .item-phanhoi .btnXoa {
            display: none;
        }
        #boxMain .boxPhanHoi .item-phanhoi:last-child:not(.reply) .btnXoa {
            display: block;
        }
        #boxMain .boxPhanHoi .item-phanhoi .thoigian {
            margin-left: auto;
        }
        #boxMain .boxPhanHoi .item-phanhoi .box-action {
            font-size: 12px;
        }
        .boxPhanHoi .item-phanhoi.reply .ten {
            color: #007bff !important;
        }
    </style>
@endsection
@section('body')
    <div class="content-wrapper kanban">
        <!-- Main content -->
        <section class="content">
            <div class="h-100 d-flex flex-column">
                <div class="mb-2 text-center d-none" id="boxAction">
                    <span class="btnCapNhatViTri btn btn-sm font-size-btn-sm-mobile btn-danger">Chưa cập nhật vị trí! Nhấn cập nhật ngay!!!</span>
                </div>
{{--                <div class="mb-3">--}}
{{--                    <div class="form-group mb-2">--}}
{{--                        <select class="form-control" id="selThuaRuong">--}}
{{--                            @foreach($thuaruongs as $item)--}}
{{--                                <option value="{{ $item->id }}"{{ $item->id == $thuaruong->id ? ' selected' : '' }}>--}}
{{--                                    {{ $item->ten.' - Sạ ngày '.date('d-m-Y',strtotime($item->ngaysa)) }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="overflow-auto font-size-mobile" id="container">
                    <div class="timeline mb-0" id="boxMain">

                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->

{{--        <span id="btnThem" class="btn btn-primary position-fixed btn-sm" role="button" style="z-index: 1032; right: 1.25rem; bottom: calc(1.25rem + 57px)">--}}
{{--            <i class="fas fa-plus"></i>--}}
{{--        </span>--}}
    </div>

    <footer class="main-footer text-center overflow-auto text-nowrap" id="selThuaRuong">
        <strong>
            @if($thuaruong != null)
                <span class="ten">{{ $thuaruong->ten }}</span> - Sạ ngày
                <span class="text-info ngaysa">{{ date('d-m-Y',strtotime($thuaruong->ngaysa)) }}</span>
                @if($thuaruong->songay > 0)
                    <span class="ml-1">(<span class="songay">{{ $thuaruong->songay }}</span> ngày)</span>
                @endif
            @else
                Chưa đăng ký thửa ruộng
            @endif
        </strong>
    </footer>
@endsection

@section('js-include')
    @include('nongdan.quytrinh.js')
@endsection
