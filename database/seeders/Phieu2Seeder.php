<?php

namespace Database\Seeders;

use App\Functions\Funcs;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\HangHoa;
use App\Models\DanhMuc\KhachHang;
use App\Models\DanhMuc\NhaCungCap;
use App\Models\DanhMuc\NhanVien;
use App\Models\Phieu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Phieu2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $chinhanhs = ChiNhanh::withTrashed()->pluck('id','old_id');
        $nhanviens = NhanVien::withTrashed()->pluck('id','old_id');
        $hanghoaids = HangHoa::withTrashed()->pluck('id','old_id');
        $hanghoamas = HangHoa::withTrashed()->pluck('ma','old_id');

        $_data = [
            'mbm' => '123kjk32954389034klj34',
            'query' => "select * from module_phieu where
                                 loaiphieu = 'CKNB' and is_active = 1 and created_at > '2021-01-01'"
        ];
        $data = Funcs::CallAPI('POST','https://api-cskh.hailua.center/api/dev-tool-lay-dulieu', $_data);
        $data = json_decode($data);

        foreach ($data->dl as $value) {

            $model = new Phieu();
            $model->old_id = $value->id;
            $model->ghichu = $value->ghichu ?? null;
            $model->loaiphieu = 'XKNB';
            $model->maphieu = str_replace('CKNB','XKNB',$value->maphieu);
            $model->nhanvien_id = $nhanviens[$value->nhanvien_id] ?? '';
            $model->nguoiduyet_id = $nhanviens[$value->nhanvien_id] ?? '';
            $model->chinhanh_id = $chinhanhs[$value->chinhanh_id] ?? '';
            $model->sophieu = $value->stt;
            $model->created_at = $value->created_at;
            $model->updated_at = $value->created_at;
            $model->ngay = explode(' ',$value->created_at)[0];
            $model->gio = explode(' ',$value->created_at)[1];
            $model->save();

            $chitiet = [
                'mbm' => '123kjk32954389034klj34',
                'query' => "select * from module_phieu_chitiet where
                                 phieu_id = '$value->id'"
            ];
            $chitiet = Funcs::CallAPI('POST','https://api-cskh.hailua.center/api/dev-tool-lay-dulieu', $chitiet);
            $chitiet = json_decode($chitiet);

            $model2 = null;

            if (isset($chitiet->dl)) {
                foreach($chitiet->dl as $phieu_chitiet) {
                    if ($phieu_chitiet->chinhanh_id == $value->chinhanh_id) {
                        DB::table('phieu_chitiet')->insert([
                            'id' => rand(1000000000,9999999999),
                            'phieu_id' => $model->id,
                            'maphieu' => str_replace('CKNB','XKNB',$value->maphieu),
                            'loaiphieu' => 'XKNB',
                            'hanghoa_id' => $hanghoaids[$phieu_chitiet->hanghoa_id] ?? '',
                            'hanghoa_ma' => $hanghoamas[$phieu_chitiet->hanghoa_id] ?? '',
                            'soluong' => $phieu_chitiet->tongsoluong,
                            'sophieu' => $phieu_chitiet->stt,
                            'is_tangkho' => -1,
                            'chinhanh_id' => $chinhanhs[$value->chinhanh_id] ?? '',
                            'ngay' => explode(' ',$value->created_at)[0],
                            'gio' => explode(' ',$value->created_at)[1],
                            'created_at' => $value->created_at,
                            'updated_at' => $value->created_at
                        ]);
                    }
                    else {
                        if ($model2 == null) {
                            $model2 = new Phieu();
                            $model2->doituong_id = $model->id;
                            $model2->loaiphieu = 'NKNB';
                            $model2->maphieu = str_replace('CKNB','NKNB',$value->maphieu);
                            $model2->nhanvien_id = $nhanviens[$phieu_chitiet->nhanvien_id] ?? '';
                            $model2->nguoiduyet_id = $nhanviens[$phieu_chitiet->rid] ?? '';
                            $model2->chinhanh_id = $chinhanhs[$phieu_chitiet->chinhanh_id] ?? '';
                            $model2->sophieu = $value->stt;
                            $model2->created_at = $value->created_at;
                            $model2->updated_at = $value->created_at;
                            $model2->ngay = explode(' ',$value->created_at)[0];
                            $model2->gio = explode(' ',$value->created_at)[1];
                            $model2->save();
                            $model->doituong_id = $chinhanhs[$phieu_chitiet->chinhanh_id] ?? '';
                            $model->update();
                        }
                        DB::table('phieu_chitiet')->insert([
                            'id' => rand(1000000000,9999999999),
                            'phieu_id' => $model2->id,
                            'maphieu' => $model2->maphieu,
                            'loaiphieu' => 'NKNB',
                            'hanghoa_id' => $hanghoaids[$phieu_chitiet->hanghoa_id] ?? '',
                            'hanghoa_ma' => $hanghoamas[$phieu_chitiet->hanghoa_id] ?? '',
                            'soluong' => $phieu_chitiet->tongsoluong,
                            'sophieu' => $phieu_chitiet->stt,
                            'is_tangkho' => 1,
                            'chinhanh_id' => $model2->chinhanh_id,
                            'ngay' => explode(' ',$value->created_at)[0],
                            'gio' => explode(' ',$value->created_at)[1],
                            'created_at' => $value->created_at,
                            'updated_at' => $value->created_at
                        ]);
                    }
                }
            }
        }
    }
}
