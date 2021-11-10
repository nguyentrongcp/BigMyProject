@extends(isset($info) ? 'quanly.layouts.main' : 'errors::minimal')

@if(isset($info))
    @section('breadcrumb')
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active">Page not found!</li>
    @stop

    @section('body')
        <div class="content-wrapper pt-5">
            <section class="content">
                <div class="error-page d-flex">
                    <h2 class="headline text-warning mb-0">404!</h2>
                    <div class="error-content d-flex flex-column justify-content-center pt-1 ml-3">
                        <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Trang không tồn tại.</h3>
                        <p class="mb-0">
                            Xin lỗi! Trang bạn tìm kiếm không tồn tại trên hệ thống.
                            Bạn có thể tìm trang mới hoặc <a href="/">trở về trang chủ</a>.
                        </p>
                    </div>
                </div>
            </section>
        </div>
    @stop
@else
    @section('title', __('Not Found'))
    @section('code', '404')
    @section('message', __('Not Found'))
@endif
