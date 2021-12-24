@extends('nongdan.layouts.main')
@section('style-custom')
    <style>
        .font-size-mobile {
            font-size: 14px !important;
        }
        .font-size-btn-sm-mobile {
            font-size: 12px !important;
        }
        #boxMain .timeline-trangthai {
            border-top: 1px solid rgba(0,0,0,.125);
            border-bottom: 1px solid rgba(0,0,0,.125);
            display: flex;
            justify-content: space-between;
        }
    </style>
@endsection
@section('body')
    <div class="content-wrapper kanban">
        <!-- Main content -->
        <section class="content">
            <div class="h-100 d-flex flex-column">
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
    </div>

    <footer class="main-footer text-center overflow-auto text-nowrap" id="selThuaRuong">
        <strong>
            @if($thuaruong != null)
                <span class="ten">{{ $thuaruong->ten }}</span> - Sạ ngày
                <span class="text-info ngaysa">{{ date('d-m-Y',strtotime($thuaruong->ngaysa)) }}</span>
                @if($thuaruong->songay > 0)
                    <span class="ml-1">({{ $thuaruong->songay }} ngày)</span>
                @endif
            @else
                Chưa đăng ký thửa ruộng
            @endif
        </strong>
        {{--    <div class="float-right d-none d-sm-inline-block">--}}
        {{--        <b>Version</b> 3.0.5--}}
        {{--    </div>--}}
    </footer>
@endsection

@section('js-include')
    @include('nongdan.quytrinh.js')
@endsection
