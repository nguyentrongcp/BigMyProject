<?php

namespace Database\Seeders;

use App\Models\DanhMuc\KhoanMuc;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//         \App\Models\User::factory(10)->create();
//        $this->call(DiaChiSeeder::class);
        $this->call(CayTrongSeeder::class);
        $this->call(ChucVuSeeder::class);
        $this->call(ChiNhanhSeeder::class);
        $this->call(NhanVienSeeder::class);
        $this->call(DonViTinhSeeder::class);
        $this->call(HangHoaNhomSeeder::class);
        $this->call(KhachHangSeeder::class);
        $this->call(KhoanMucSeeder::class);
        $this->call(HangHoaSeeder::class);
//        $this->call(PhieuSeeder::class);
    }
}
