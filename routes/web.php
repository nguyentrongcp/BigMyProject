<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/quan-ly/ban-hang');
});

Route::prefix('mobile')->group(function() {
    Route::get('/', function () {
        return redirect('/mobile/danh-muc/nhan-vien');
    });
    Route::get('dang-nhap', function() {
        return view('mobile.login');
    })->name('mobile.dang-nhap');

    Route::middleware('role.mobile')->group(function() {
        Route::prefix('danh-muc')->group(function() {
            Route::get('nhan-vien', [\App\Http\Controllers\DanhMuc\NhanVienController::class, 'mobile_index'])
                ->name('mobile.nhan-vien');
            Route::get('chi-nhanh', [\App\Http\Controllers\DanhMuc\ChiNhanhController::class, 'mobile_index'])
                ->name('mobile.chi-nhanh');
            Route::get('hang-hoa', [\App\Http\Controllers\DanhMuc\HangHoaController::class, 'mobile_index'])
                ->name('mobile.hang-hoa');
        });
        Route::get('thu-chi', [\App\Http\Controllers\ThuChiController::class, 'mobile_index'])
            ->name('mobile.thu-chi');
        Route::get('lichsu-diemdanh', [\App\Http\Controllers\DiemDanhController::class, 'index_lichsu'])
            ->name('mobile.lichsu-diemdanh');
    });
});

Route::prefix('quan-ly')->group(function() {
    Route::get('dang-nhap', function() {
        return view('quanly.login');
    })->name('dang-nhap');

    Route::middleware('role.quanly')->group(function() {
        Route::prefix('danh-muc')->group(function() {
            Route::get('hang-hoa', [\App\Http\Controllers\DanhMuc\HangHoaController::class, 'index'])
                ->name('danh-muc.hang-hoa');
            Route::get('chi-nhanh', [\App\Http\Controllers\DanhMuc\ChiNhanhController::class, 'index'])
                ->name('danh-muc.chi-nhanh');
            Route::get('nhan-vien', [\App\Http\Controllers\DanhMuc\NhanVienController::class, 'index'])
                ->name('danh-muc.nhan-vien');
            Route::get('nha-cung-cap', [\App\Http\Controllers\DanhMuc\NhaCungCapController::class, 'index'])
                ->name('danh-muc.nha-cung-cap');
            Route::get('khach-hang', [\App\Http\Controllers\DanhMuc\KhachHangController::class, 'index'])
                ->name('danh-muc.khach-hang');
            Route::get('doi-tuong', [\App\Http\Controllers\DanhMuc\DoiTuongController::class, 'index'])
                ->name('danh-muc.doi-tuong');
            Route::get('phan-quyen', [\App\Http\Controllers\DanhMuc\PhanQuyenController::class, 'index'])
                ->name('danh-muc.phan-quyen');
        });

        Route::get('ban-hang', [\App\Http\Controllers\BanHangController::class, 'index'])->name('ban-hang');

        Route::get('khach-tra-hang', [\App\Http\Controllers\KhachTraHangController::class, 'index'])->name('khach-tra-hang');

        Route::prefix('nhap-hang')->group(function() {
            Route::get('tao-phieu', [\App\Http\Controllers\NhapHang\TaoPhieuController::class, 'index'])
                ->name('nhap-hang.tao-phieu');

            Route::get('danh-sach', [\App\Http\Controllers\NhapHang\DanhSachController::class, 'index'])
                ->name('nhap-hang.danh-sach');
        });

        Route::prefix('chuyenkho-noibo')->group(function() {
            Route::get('xuat-kho',[\App\Http\Controllers\ChuyenKhoNoiBo\XuatKhoController::class, 'index'])
                ->name('xuatkho-noibo');

            Route::get('nhap-kho',[\App\Http\Controllers\ChuyenKhoNoiBo\NhapKhoController::class, 'index'])
                ->name('nhapkho-noibo');
        });

        Route::prefix('hang-hoa')->group(function() {
            Route::get('ton-kho',[\App\Http\Controllers\HangHoa\TonKhoController::class, 'index'])
                ->name('hang-hoa.ton-kho');
            Route::get('gia-ban',[\App\Http\Controllers\HangHoa\GiaBanController::class, 'index'])
                ->name('hang-hoa.gia-ban');
            Route::get('phat-sinh-ton',[\App\Http\Controllers\HangHoa\PhatSinhTonController::class, 'index'])
                ->name('hang-hoa.phat-sinh-ton');
            Route::get('qrcode',[\App\Http\Controllers\QrcodeController::class, 'index'])->name('hang-hoa.qrcode');
            Route::get('in-qrcode',[\App\Http\Controllers\QrcodeController::class, 'index_inqrcode'])->name('hang-hoa.in-qrcode');
            Route::get('so-luong-ban',[\App\Http\Controllers\HangHoa\SoLuongBanController::class, 'index'])->name('hang-hoa.so-luong-ban');
        });

        Route::get('thu-chi',[\App\Http\Controllers\ThuChiController::class, 'index'])->name('thu-chi');

        Route::get('tim-phieu',[\App\Http\Controllers\TimPhieuController::class, 'index'])->name('tim-phieu');

        Route::get('danhsach-diemdanh',[\App\Http\Controllers\DiemDanhController::class, 'index_danhsach'])->name('danhsach-diemdanh');

        Route::get('xem-phieu/{maphieu}/{auto_print?}', [\App\Http\Controllers\XemPhieuController::class, 'xem_phieu'])
            ->name('xem-phieu');
    });
});
