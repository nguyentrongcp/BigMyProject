<?php

namespace Database\Seeders;

use App\Functions\Funcs;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\HangHoa;
use App\Models\Phieu;
use App\Models\PhieuChiTiet;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DongBoTonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $_data = [
            'mbm' => '123kjk32954389034klj34',
            'query' => "select hanghoa_ma, soluongton, chinhanh_id, (select id from module_hanghoa where ma = hanghoa_ma) as hh_id
                        from module_hanghoa_soluongton"
        ];
        $data = Funcs::CallAPI('POST','https://api-cskh.hailua.center/api/dev-tool-lay-dulieu', $_data);
        $data = json_decode($data);

        $toncus = [];
        foreach($data->dl as $value) {
            if (!isset($toncus[$value->chinhanh_id])) {
                $toncus[$value->chinhanh_id] = [];
            }
            $toncus[$value->chinhanh_id][$value->hh_id] = $value->soluongton;
        }

        $hanghoas = HangHoa::all(['id','old_id','ma']);
        $chinhanhs = ChiNhanh::all(['id','old_id']);

        DB::beginTransaction();
        foreach($chinhanhs as $chinhanh) {
            $ids = [];
            foreach($hanghoas as $hanghoa) {
                $tonkho = Funcs::getTonKho($hanghoa->id,$chinhanh->id);
                $toncu = $toncus[$chinhanh->old_id][$hanghoa->old_id] ?? 0;
                if ($toncu != $tonkho) {
                    $ids[] = (object) [
                        'hanghoa_id' => $hanghoa->id,
                        'hanghoa_ma' => $hanghoa->ma,
                        'soluong' => $toncu - $tonkho
                    ];
                }
            }
            if (count($ids) > 0) {
                $phieu = new Phieu();
                $phieu->maphieu = 'DKHH20201231-0001';
                $phieu->loaiphieu = 'DKHH';
                $phieu->sophieu = 1;
                $phieu->nhanvien_id = '1000000000';
                $phieu->chinhanh_id = $chinhanh->id;
                $phieu->ngay = '2020-12-31';
                $phieu->gio = date('H:i:s');
                $phieu->created_at = '2020-12-31 12:00:00';

                $phieu->save();

                foreach($ids as $id) {
                    $chitiet = new PhieuChiTiet();
                    $chitiet->phieu_id = $phieu->id;
                    $chitiet->maphieu = $phieu->maphieu;
                    $chitiet->loaiphieu = 'DKHH';
                    $chitiet->hanghoa_id = $id->hanghoa_id;
                    $chitiet->hanghoa_ma = $id->hanghoa_ma;
                    $chitiet->soluong = $id->soluong;
                    $chitiet->is_tangkho = 1;
                    $chitiet->chinhanh_id = $phieu->chinhanh_id;
                    $chitiet->sophieu = $phieu->sophieu;
                    $chitiet->ngay = '2020-12-31';
                    $chitiet->gio = date('H:i:s');
                    $chitiet->created_at = '2020-12-31 12:00:00';
                    $chitiet->save();
//                    $chitiet->capNhatTonKho();
                }
            }
        }

        DB::commit();
    }
}
