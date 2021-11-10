<?php

namespace Database\Seeders;

use App\Models\DanhMuc\KhachHang;
use App\Models\Phieu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DongBoKhachHangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $phieus = DB::select("
            select * from phieu
            where loaiphieu = 'BH' and doituong_id != '1000000000'
            group by doituong_id order by created_at desc
        ");

        DB::beginTransaction();
        $doituongs = [];
        foreach($phieus as $key => $phieu) {
            $doituongs[] = $phieu->doituong_id;
            $phieus[$phieu->doituong_id] = $phieu->created_at;
            unset($phieus[$key]);
        }

        $khachhangs = KhachHang::whereIn('id',$doituongs)->get(['id','lancuoi_muahang','updated_at']);
        foreach($khachhangs as $khachhang) {
            if ($khachhang->lancuoi_muahang == null && isset($phieus[$khachhang->id])) {
                $khachhang->lancuoi_muahang = $phieus[$khachhang->id];
                $khachhang->update();
            }
        }
        DB::commit();
    }
}
