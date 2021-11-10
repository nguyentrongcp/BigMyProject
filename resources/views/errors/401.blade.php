{{--@extends('errors::minimal')--}}

{{--@section('title', __('Unauthorized'))--}}
{{--@section('code', '401')--}}
{{--@section('message', __('Unauthorized'))--}}

@extends('quanly.layouts.main')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Không được phép truy cập!</li>
@stop

@section('body')
    <div class="content-wrapper pt-5">
        <section class="content">
            <div class="error-page d-flex">
                <h2 class="headline text-warning mb-0">LỖI!</h2>
                <div class="error-content d-flex flex-column justify-content-center pt-1 ml-3">
                    <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Không được phép truy cập.</h3>
                    <p class="mb-0">
                        Xin lỗi! Bạn không thuộc cửa hàng nào trong hệ thống.
                        Vui lòng cập nhật cửa hàng cho tài khoản của bạn, sau đó đăng nhập lại.
                    </p>
                </div>
            </div>
        </section>
    </div>
@stop
