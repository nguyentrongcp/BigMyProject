<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CayTrongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $caytrongs = [
            'Bưởi','Bưởi Năm Roi','Bưởi Da Xanh','Mít','Mít Thái','Cam','Cam Xoàn','Cam Sành','Cam Mật','Nhãn',
            'Sầu Riêng','Thanh Long','Mận','Quýt','Dừa','Bơ','Cà Na','Chôm Chôm','Măng Cụt','Ổi','Tắc','Chanh',
            'Chanh Không Hạt','Thanh Trà','Chuối','Chuối Xiêm','Chuối Già Hương','Chuối Cau','Chuối Ngự',
            'Chuối Tiêu','Chuối Hột','Cóc','Dâu Tây','Dâu Tằm','Dưa Hấu','Dưa Gang','Dưa Lưới','Dưa Leo',
            'Mãng Cầu','Mãng Cầu Gai','Vú Sữa','Mía','Xoài','Nho','Táo','Thơm','Đu Đủ','Khoai','Khoai Lang',
            'Khoai Tây','Lúa','Lúa Mè','Bắp Cải','Ớt','Bầu','Bí Ngô','Bí Rợ','Bí Đao','Đậu Đũa','Bí Đỏ','Bồ Ngót',
            'Bông','Mai','Rau Màu','Cải Xà Lách Xoong','Cà Tím','Cà Chua','Đậu Bắp','Cà Phổi',
            'Củ Cải Trắng','Củ Cải Đỏ','Cây Giống','Cây Kiểng','Cỏ','Củ Sắn','Diếp Cá','Gừng','Khổ Qua','Hành',
            'Hẹ','Khoai Từ','Khoai Môn','Khóm','Mồng Tơi','Mướp','Nấm Rơm','Ngò','Ngò Gai','Quế','Rau Cần','Rau Má',
            'Rau Muống','Rau Nhút','Rau Đắng','Sơ Ri','Tre','Đậu Que'
        ];

        foreach($caytrongs as $caytrong) {
            DB::table('danhmuc_caytrong')->insert([
                'ten' => $caytrong,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
