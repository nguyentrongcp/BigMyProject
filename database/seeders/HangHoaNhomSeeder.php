<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HangHoaNhomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data_insert = [
            [
                'ma' => 'PBG',
                'ten' => 'PHÂN BÓN GỐC',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'PBL',
                'ten' => 'PHÂN BÓN LÁ',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'TTS',
                'ten' => 'THUỐC TRỪ SÂU',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'TTC',
                'ten' => 'THUỐC TRỪ CỎ',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'TTB',
                'ten' => 'THUỐC TRỪ BỆNH',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'TDC',
                'ten' => 'THUỐC DIỆT CHUỘT',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'BAT',
                'ten' => 'BẠT',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'QUATANG',
                'ten' => 'QUÀ TẶNG',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'TTO',
                'ten' => 'THUỐC TRỪ ỐC',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'TOI',
                'ten' => 'DỊCH TỎI',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'KTTT',
                'ten' => 'KÍCH THÍCH TĂNG TRƯỞNG',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'KTST',
                'ten' => 'KÍCH THÍCH SINH TRƯỞNG',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'DOIQUA',
                'ten' => 'ĐỔI QUÀ',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'UCST',
                'ten' => 'ỨC CHẾ SINH TRƯỞNG',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'VT',
                'ten' => 'VẬT TƯ',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'CM',
                'ten' => 'CỎ MẦM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'HG',
                'ten' => 'HẠT GIỐNG',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'VOI',
                'ten' => 'VÔI',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'PBR',
                'ten' => 'PHÂN BÓN RỂ',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'TTBSH',
                'ten' => 'THUỐC TRỪ BỆNH SINH HỌC',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'VTCS',
                'ten' => 'VẬT TƯ CAO SU',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'TXLG',
                'ten' => 'THUỐC XỬ LÝ GỐC',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'KTM',
                'ten' => 'KÍCH THÍCH MỦ',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'ma' => 'KHAC',
                'ten' => 'KHÁC',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ];

        DB::table('hanghoa_nhom')->insert($data_insert);
    }
}
