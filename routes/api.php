<?php

use App\Http\Controllers\BanHangController;
use App\Http\Controllers\ChuyenKhoNoiBo\NhapKhoController;
use App\Http\Controllers\ChuyenKhoNoiBo\XuatKhoController;
use App\Http\Controllers\CongNoKhachHangController;
use App\Http\Controllers\DanhMuc\ChiNhanhController;
use App\Http\Controllers\DanhMuc\ChucVuController;
use App\Http\Controllers\DanhMuc\DiaChiController;
use App\Http\Controllers\DanhMuc\DoiTuongController;
use App\Http\Controllers\DanhMuc\HangHoaController;
use App\Http\Controllers\DanhMuc\KhachHangController;
use App\Http\Controllers\DanhMuc\NhaCungCapController;
use App\Http\Controllers\DanhMuc\NhanVienController;
use App\Http\Controllers\DanhMuc\NongDanController;
use App\Http\Controllers\DanhMuc\PhanQuyenController;
use App\Http\Controllers\DiemDanhController;
use App\Http\Controllers\HangHoa\GiaBanController;
use App\Http\Controllers\HangHoa\PhatSinhTonController;
use App\Http\Controllers\KhachTraHangController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NhapHang\DanhSachController;
use App\Http\Controllers\NhapHang\TaoPhieuController;
use App\Http\Controllers\PhieuController;
use App\Http\Controllers\ThuChiController;
use App\Http\Controllers\XemPhieuController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('mobile')->group(function() {
    Route::middleware('role.api')->group(function() {
        Route::prefix('danh-muc')->group(function() {
            Route::get('nhan-vien/danh-sach', [NhanVienController::class, 'danh_sach_mobile']);
            Route::get('hang-hoa/danh-sach', [HangHoaController::class, 'danh_sach_mobile']);
        });
    });
});

Route::prefix('quan-ly')->group(function() {
    Route::prefix('xac-thuc')->group(function() {
        Route::get('dang-nhap',[LoginController::class,'dang_nhap']);
    });

    Route::middleware('role.api')->group(function() {
        Route::prefix('danh-muc')->group(function() {
            Route::prefix('hang-hoa')->group(function() {
                Route::get('them-moi',[HangHoaController::class, 'them_moi']);
                Route::get('cap-nhat',[HangHoaController::class, 'cap_nhat']);
                Route::get('xoa',[HangHoaController::class, 'xoa']);
                Route::get('phuc-hoi',[HangHoaController::class, 'phuc_hoi']);
                Route::get('danh-sach',[HangHoaController::class, 'danh_sach']);
                Route::get('tim-kiem',[HangHoaController::class, 'tim_kiem']);
                Route::get('danhmuc-quydoi',[HangHoaController::class, 'danhmuc_quydoi']);
                Route::get('capnhat-quydoi',[HangHoaController::class, 'capnhat_quydoi']);
            });

            Route::prefix('chi-nhanh')->group(function() {
                Route::get('them-moi',[ChiNhanhController::class, 'them_moi']);
                Route::get('cap-nhat',[ChiNhanhController::class, 'cap_nhat']);
                Route::get('xoa',[ChiNhanhController::class, 'xoa']);
                Route::get('phuc-hoi',[ChiNhanhController::class, 'phuc_hoi']);
                Route::get('danh-sach',[ChiNhanhController::class, 'danh_sach']);
                Route::get('tim-kiem',[ChiNhanhController::class, 'tim_kiem']);
            });

            Route::prefix('nhan-vien')->group(function() {
                Route::get('them-moi',[NhanVienController::class, 'them_moi']);
                Route::get('cap-nhat',[NhanVienController::class, 'cap_nhat']);
                Route::get('phan-quyen',[NhanVienController::class, 'phan_quyen']);
                Route::get('chon-quyen',[NhanVienController::class, 'chon_quyen']);
                Route::get('xoa',[NhanVienController::class, 'xoa']);
                Route::get('phuc-hoi',[NhanVienController::class, 'phuc_hoi']);
                Route::get('danh-sach',[NhanVienController::class, 'danh_sach']);
                Route::get('chuyen-cua-hang',[NhanVienController::class, 'chuyen_cua_hang']);
                Route::get('doi-mat-khau',[NhanVienController::class, 'doi_mat_khau']);
                Route::get('check-matkhau-macdinh',[NhanVienController::class, 'check_matkhau_macdinh']);
                Route::get('reset-mat-khau',[NhanVienController::class, 'reset_mat_khau']);
                Route::get('danhsach-phanquyen',[NhanVienController::class, 'danhsach_phanquyen']);
            });

            Route::prefix('nha-cung-cap')->group(function() {
                Route::get('them-moi',[NhaCungCapController::class, 'them_moi']);
                Route::get('cap-nhat',[NhaCungCapController::class, 'cap_nhat']);
                Route::get('xoa',[NhaCungCapController::class, 'xoa']);
                Route::get('phuc-hoi',[NhaCungCapController::class, 'phuc_hoi']);
                Route::get('danh-sach',[NhaCungCapController::class, 'danh_sach']);
                Route::get('tim-kiem',[NhaCungCapController::class, 'tim_kiem']);
            });

            Route::prefix('khach-hang')->group(function() {
                Route::get('them-moi',[KhachHangController::class, 'them_moi']);
                Route::get('cap-nhat',[KhachHangController::class, 'cap_nhat']);
                Route::get('xoa',[KhachHangController::class, 'xoa']);
                Route::get('phuc-hoi',[KhachHangController::class, 'phuc_hoi']);
                Route::get('danh-sach',[KhachHangController::class, 'danh_sach']);
                Route::get('thong-tin',[KhachHangController::class, 'thong_tin']);
                Route::get('tim-kiem',[KhachHangController::class, 'tim_kiem']);
            });

            Route::prefix('doi-tuong')->group(function() {
                Route::get('them-moi',[DoiTuongController::class, 'them_moi']);
                Route::get('cap-nhat',[DoiTuongController::class, 'cap_nhat']);
                Route::get('xoa',[DoiTuongController::class, 'xoa']);
                Route::get('phuc-hoi',[DoiTuongController::class, 'phuc_hoi']);
                Route::get('danh-sach',[DoiTuongController::class, 'danh_sach']);
                Route::get('tim-kiem',[DoiTuongController::class, 'tim_kiem']);
            });

            Route::prefix('phan-quyen')->group(function() {
                Route::get('them-moi',[PhanQuyenController::class, 'them_moi']);
                Route::get('cap-nhat',[PhanQuyenController::class, 'cap_nhat']);
                Route::get('xoa',[PhanQuyenController::class, 'xoa']);
                Route::get('danh-sach',[PhanQuyenController::class, 'danh_sach']);
            });

            Route::prefix('chuc-vu')->group(function() {
                Route::get('them-moi',[ChucVuController::class, 'them_moi']);
                Route::get('cap-nhat',[ChucVuController::class, 'cap_nhat']);
                Route::get('phan-quyen',[ChucVuController::class, 'phan_quyen']);
                Route::get('xoa',[ChucVuController::class, 'xoa']);
                Route::get('danh-sach',[ChucVuController::class, 'danh_sach']);
                Route::get('danhsach-phanquyen',[ChucVuController::class, 'danhsach_phanquyen']);
            });

            Route::prefix('nong-dan')->group(function() {
                Route::get('them-moi',[NongDanController::class, 'them_moi']);
                Route::get('cap-nhat',[NongDanController::class, 'cap_nhat']);
                Route::get('xoa',[NongDanController::class, 'xoa']);
                Route::get('phuc-hoi',[NongDanController::class, 'phuc_hoi']);
                Route::get('danh-sach',[NongDanController::class, 'danh_sach']);
                Route::get('tim-kiem',[NongDanController::class, 'tim_kiem']);
            });

            Route::prefix('dia-chi')->group(function() {
                Route::get('tinh',[DiaChiController::class, 'danhmuc_tinh']);
                Route::get('huyen',[DiaChiController::class, 'danhmuc_huyen']);
                Route::get('xa',[DiaChiController::class, 'danhmuc_xa']);
            });
        });

        Route::prefix('ban-hang')->group(function() {
            Route::get('tim-kiem',[BanHangController::class, 'tim_kiem']);
            Route::post('luu-phieu',[BanHangController::class, 'luu_phieu']);
            Route::get('danh-sach',[BanHangController::class, 'danh_sach']);
            Route::get('danhsach-khachhang',[BanHangController::class, 'danhsach_khachhang']);
        });

        Route::prefix('khach-tra-hang')->group(function() {
            Route::get('lichsu-muahang',[KhachTraHangController::class, 'lichsu_muahang']);
            Route::post('luu-phieu',[KhachTraHangController::class, 'luu_phieu']);
        });

        Route::prefix('nhap-hang')->group(function() {
            Route::get('tim-kiem',[TaoPhieuController::class, 'tim_kiem']);
            Route::get('danh-sach',[DanhSachController::class, 'danh_sach']);
            Route::post('luu-phieu',[TaoPhieuController::class, 'luu_phieu']);
            Route::get('huy-phieu',[DanhSachController::class, 'huy_phieu']);
            Route::post('duyet-phieu',[DanhSachController::class, 'duyet_phieu']);
            Route::get('so-phieunhap',[DanhSachController::class, 'so_phieunhap']);
        });

        Route::prefix('chuyenkho-noibo')->group(function() {
            Route::post('xuat-kho/luu-phieu',[XuatKhoConTroller::class, 'luu_phieu']);

            Route::prefix('nhap-kho')->group(function() {
                Route::get('danhsach-phieuxuat',[NhapKhoConTroller::class, 'danhsach_phieuxuat']);
                Route::post('luu-phieu',[NhapKhoConTroller::class, 'luu_phieu']);
                Route::get('huy-phieu',[NhapKhoConTroller::class, 'huy_phieu']);
                Route::get('so-phieuxuat',[NhapKhoConTroller::class, 'so_phieuxuat']);
            });
        });

        Route::prefix('congno-khachhang')->group(function() {
            Route::get('thu-congno',[CongNoKhachHangController::class, 'thu_congno']);
            Route::get('dieuchinh-congno',[CongNoKhachHangController::class, 'dieuchinh_congno']);
        });

        Route::prefix('hang-hoa')->group(function() {
            Route::get('ton-kho/danh-sach',[\App\Http\Controllers\HangHoa\TonKhoController::class, 'danh_sach']);
            Route::get('ton-kho/lay-thong-tin',[\App\Http\Controllers\HangHoa\TonKhoController::class, 'lay_thong_tin']);
            Route::prefix('gia-ban')->group(function() {
                Route::get('danh-sach',[GiaBanController::class, 'danh_sach']);
                Route::get('cap-nhat',[GiaBanController::class, 'cap_nhat']);
                Route::get('dong-bo',[GiaBanController::class, 'dong_bo']);
                Route::get('thong-bao-gia',[GiaBanController::class, 'thong_bao_gia']);
            });
            Route::prefix('phat-sinh-ton')->group(function() {
                Route::get('danh-sach',[PhatSinhTonController::class, 'danh_sach']);
                Route::get('dau-ky',[PhatSinhTonController::class, 'dau_ky']);
            });
            Route::post('qrcode/tao-ma',[\App\Http\Controllers\QrcodeController::class, 'tao_ma']);
            Route::get('qrcode/tim-kiem',[\App\Http\Controllers\QrcodeController::class, 'tim_kiem']);
            Route::get('so-luong-ban/danh-sach',[\App\Http\Controllers\HangHoa\SoLuongBanController::class, 'danh_sach']);
        });

        Route::prefix('thu-chi')->group(function() {
            Route::get('luu-phieu',[ThuChiController::class, 'luu_phieu']);
            Route::get('tra-cuu',[ThuChiController::class, 'tra_cuu']);
            Route::get('ket-so',[ThuChiController::class, 'ket_so']);
            Route::get('mo-so',[ThuChiController::class, 'mo_so']);
        });

        Route::prefix('diem-danh')->group(function() {
            Route::get('check-thong-tin',[DiemDanhController::class,'check_thong_tin']);
            Route::get('bat-dau',[DiemDanhController::class,'bat_dau']);
            Route::get('ket-thuc',[DiemDanhController::class,'ket_thuc']);
            Route::get('lich-su',[DiemDanhController::class,'lich_su']);
            Route::get('danh-sach',[DiemDanhController::class,'danh_sach']);
            Route::get('xoa',[DiemDanhController::class,'xoa']);
            Route::get('reset',[DiemDanhController::class,'reset']);
        });

        Route::prefix('phieu')->group(function() {
            Route::post('tao-phieu/{loaiphieu}',[XemPhieuController::class,'tao_phieu']);
            Route::get('danh-sach',[PhieuController::class,'danh_sach']);
            Route::get('xoa',[PhieuController::class,'xoa']);
            Route::get('phuc-hoi',[PhieuController::class,'phuc_hoi']);
        });
    });
});
