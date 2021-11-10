<?php

namespace Database\Seeders;

use App\Functions\Funcs;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\HangHoa;
use App\Models\HangHoaChiTiet;
use Illuminate\Database\Seeder;

class DongBoGiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $chinhanhs = ChiNhanh::where('loai','cuahang')->get();
        $hanghoas = HangHoa::all();
        $chitiet = Funcs::CallAPI('POST','https://api-cskh.hailua.center/api/dev-tool-lay-dulieu', [
            'mbm' => '123kjk32954389034klj34',
            'query' => "select *, (select id from module_hanghoa where ma = hanghoa_ma and is_active = 1) as hh_id from module_hanghoa_giaban where giaban1 > 0"
        ]);
        $chitiet = json_decode($chitiet);
        $giabans = [];
        foreach($chitiet->dl as $value) {
            if (!isset($giabans[$value->chinhanh_id])) {
                $giabans[$value->chinhanh_id] = [];
            }
            if ($value->hh_id == null) {
                continue;
            }
            $giabans[$value->chinhanh_id][$value->hh_id] = $value->giaban1;
        }
        foreach($chinhanhs as $chinhanh) {
            foreach($hanghoas as $hanghoa) {
                $dongia = $giabans[$chinhanh->old_id][$hanghoa->old_id] ?? 0;
                HangHoaChiTiet::where(['chinhanh_id' => $chinhanh->id, 'hanghoa_id' => $hanghoa->id])->update(['dongia' => $dongia]);
            }
        }
    }
}
