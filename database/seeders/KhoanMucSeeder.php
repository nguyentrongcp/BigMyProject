<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhoanMucSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $khoanmucs = [
            'KHOẢN THU NỘI BỘ', 'KHOẢN THU HOÀN ỨNG', 'KHOẢN THU KHÁC',
            'KHOẢN CHI NỘI BỘ','KHOẢN CHI TẠM ỨNG', 'CHI PHÍ THUÊ NHÀ', 'CHI PHÍ QUẢNG CÁO', 'TRẢ THƯỞNG NHÂN VIÊN',
            'CHI PHÍ TÀI CHÍNH', 'CHI PHÍ HÀNG THÁNG (ĐIỆN, NƯỚC, INTERNET,...)', 'CHI PHÍ HÀNG NGÀY (TRÁI CÂY, HOA QUẢ,...)',
            'CHI PHÍ XĂNG XE', 'CHI PHÍ BỐC XẾP', 'CHI PHÍ NHÂN CÔNG (XÂY DỰNG, SỬA CHỮA...)', 'CHI PHÍ VĂN PHÒNG PHẨM',
            'CHI PHÍ VẬT TƯ (GẠCH, XI MĂNG, DÂY ĐIỆN,...)', 'CHI PHÍ SINH HOẠT (GIẤY VỆ SINH, NƯỚC UỐNG,...)',
            'NỘP QUỸ CÔNG TY', 'KHOẢN CHI KHÁC'
        ];

        foreach($khoanmucs as $key => $khoanmuc) {
            DB::table('danhmuc_khoanmuc')->insert([
                'ten' => $khoanmuc,
                'is_khoanthu' => $key < 3 ? 1 : 0,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ]);
        }
    }
}
