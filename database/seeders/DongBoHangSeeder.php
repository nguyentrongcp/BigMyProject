<?php

namespace Database\Seeders;

use App\Models\DanhMuc\HangHoa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DongBoHangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dongbos = DB::select("
            select * from dongbo_hanghoa
        ");
        foreach($dongbos as $dongbo) {
            $hanghoa = HangHoa::where('old_id',$dongbo->id_cu)->first(['id','mamoi']);
            $hanghoa->mamoi = $dongbo->ma;
            $hanghoa->update();
        }
    }
}
