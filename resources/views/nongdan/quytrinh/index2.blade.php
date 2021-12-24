@extends('nongdan.layouts.main')
@section('style-custom')
    <style>
        .font-size-mobile {
            font-size: 14px !important;
        }
        #boxMain .card-footer {
            /*background-color: unset;*/
            display: flex;
            padding: 0.75rem;
            border-top: 1px solid rgba(0,0,0,.125);
        }
    </style>
@endsection
@section('body')
    <div class="content-wrapper kanban">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid h-100 font-size-mobile" id="boxMain">

            </div>
        </section>
        <!-- /.content -->
    </div>

    <footer class="main-footer text-center overflow-auto" id="selThuaRuong">
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
