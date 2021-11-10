@extends('mobile.layouts.main')
@section('style')
    <style>
        #boxDanhSach .product-description {
            align-items: center;
        }
        #boxDanhSach .product-description > span:nth-child(2) {
            border-right: 1px solid #bbc1c7;
            height: 15px;
        }
        #boxDanhSach .product-description > span:last-child {
            margin-left: 0.5rem;
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
                        <div class="d-flex box-search p-3">
                            <div class="ui icon input w-100">
                                <input class="prompt form-control input-search input-no-border" type="text" placeholder="Nhập từ khóa tìm kiếm...">
                                <i class="link icon times"></i>
                            </div>
                        </div>
                        <div style="height: calc(100% - 1rem)" class="overflow-auto px-3">
                            <ul class="products-list product-list-in-card box-content">

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
    @include('mobile.danhmuc.chinhanh.js')
@endsection
