<?php

namespace Database\Seeders;

use App\Functions\Funcs;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\HangHoa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HangHoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $chinhanhs = ChiNhanh::pluck('id','old_id');
        $_data = [
            'mbm' => '123kjk32954389034klj34',
            'query' => "select * from module_hanghoa where is_active = 1"
        ];
        $data = Funcs::CallAPI('POST','https://api-cskh.hailua.center/api/dev-tool-lay-dulieu', $_data);
        $data = json_decode($data);
        if ($data->tt->s === 'success') {
            forEach($data->dl as $key => $item) {
                $ma = $key + 1;
                while (strlen((string) $ma) < 6) {
                    $ma = '0'.$ma;
                }
                $id = rand(1000000000,9999999999);
                $data_insert = [
                    'id' => $id,
                    'old_id' => $item->id,
                    'ma' => 'HH'.$ma,
                    'ten' => $item->ten,
                    'slug' => Funcs::convertToSlug($item->ten),
                    'donvitinh' => $item->donvitinh,
                    'quycach' => $item->quycach,
                    'nhom' => $item->nhom,
                    'gianhap' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $chitiet = Funcs::CallAPI('POST','https://api-cskh.hailua.center/api/dev-tool-lay-dulieu', [
                    'mbm' => '123kjk32954389034klj34',
                    'query' => "select *,
                        (select giaban1 from module_hanghoa_giaban where hanghoa_ma = '$item->ma'
                                        and chinhanh_id = slt.chinhanh_id order by giaban1 desc limit 1) as dongia
                        from module_hanghoa_soluongton as slt where hanghoa_ma = '$item->ma'"
                ]);
                $chitiet = json_decode($chitiet);
                foreach($chinhanhs as $key2 => $chinhanh_id) {
                    $dongia = null;
                    $soluongton = null;
                    if (isset($chitiet->dl)) {
                        foreach($chitiet->dl as $value) {
                            if (isset($value->chinhanh_id)) {
                                if ($value->chinhanh_id == $key2) {
                                    $dongia = $value->dongia;
                                    $soluongton = $value->soluongton;
                                }
                            }
                        }
                    }

                    DB::table('hanghoa_chitiet')->insert([
                        'hanghoa_ma' => 'HH'.$ma,
                        'hanghoa_id' => $id,
                        'chinhanh_id' => $chinhanh_id,
                        'tonkho' => $soluongton ?? 0,
                        'dongia' => $dongia ?? 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }

                DB::table('danhmuc_hanghoa')->insert($data_insert);
            }
        }
    }
}
