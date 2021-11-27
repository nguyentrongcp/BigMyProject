@extends('mobile.layouts.main')
@section('style')
    <style>
        #boxDanhSach .product-description > span:first-child {
            min-width: 86px;
        }
    </style>
@endsection
@section('body')
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <div class="card card-info h-100 mb-0" id="boxDanhSach">
                <div class="card-body p-0">
                    <div class="d-flex h-100 flex-column">
                        <div class="px-3 py-2">
                            <div class="input-group mt-2 box-thang">
                                <span class="input-group-text"
                                      style="width: 69px; border-right: unset; border-top-right-radius: unset; border-bottom-right-radius: unset">Tháng</span>
                                <select class="form-control thang">
                                    @for($i=1; $i<=12; $i++)
                                        <option {{ date('m') == $i ? 'selected' : '' }} value="{{ $i }}">{{ $i < 10 ? '0'.$i : $i }}</option>
                                    @endfor
                                </select>
                                <select class="form-control nam">
                                    @for($i=2021; $i<=date('Y'); $i++)
                                        <option {{ date('Y') == $i ? 'selected' : '' }} value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
{{--                        <div class="px-3 d-flex">--}}
{{--                            <span class="ngay mr-2 text-nowrap">Tổng ngày công</span>--}}
{{--                            <span class="text-info ml-auto text-right tong"></span>--}}
{{--                        </div>--}}
                        <div style="height: calc(100% - 1rem)" class="overflow-auto px-3">
                            <ul class="products-list product-list-in-card box-content">
{{--                                <li class="item">--}}
{{--                                    <div class="product-info">--}}
{{--                                        <div class="product-title d-flex">--}}
{{--                                            <span class="ngay mr-2 text-nowrap">21-01-2021</span>--}}
{{--                                            <span class="ml-auto text-primary mr-2" style="width: 61px">19:12:01</span>--}}
{{--                                            <span class="ml-auto text-danger" style="width: 61px">19:12:01</span>--}}
{{--                                            <span class="text-info ml-auto text-right">0</span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </li>--}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('js-include')
    @include('mobile.lichsu-diemdanh.js')
@endsection
