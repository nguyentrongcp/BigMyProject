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
@endsection

@section('js-include')
    @include('nongdan.quytrinh-hientai.js')
@endsection
