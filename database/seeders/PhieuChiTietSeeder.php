<?php

namespace Database\Seeders;

use App\Functions\Funcs;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\HangHoa;
use App\Models\DanhMuc\KhachHang;
use App\Models\DanhMuc\NhaCungCap;
use App\Models\DanhMuc\NhanVien;
use App\Models\Phieu;
use App\Models\PhieuChiTiet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhieuChiTietSeeder extends Seeder
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
        $phieus = Phieu::withTrashed()->pluck('id','old_id');
        $hanghoaids = HangHoa::withTrashed()->pluck('id','old_id');
        $hanghoamas = HangHoa::withTrashed()->pluck('ma','old_id');

        $_data = [
            'mbm' => '123kjk32954389034klj34',
            'query' => "select * from module_phieu_chitiet
                where phieu_id in (select id from module_phieu where
                                 loaiphieu in ('BH','KTH','DKHH','KTH','NH','THNCC')
                                               and is_active = 1 and created_at > '2021-05-27')"
        ];
        $data = Funcs::CallAPI('POST','https://api-cskh.hailua.center/api/dev-tool-lay-dulieu', $_data);
        $data = json_decode($data);

        foreach ($data->dl as $value) {
            if (!isset($phieus[$value->phieu_id])) {
                continue;
            }
            DB::table('phieu_chitiet')->insert([
                'id' => rand(1000000000,9999999999),
                'phieu_id' => $phieus[$value->phieu_id],
                'maphieu' => $value->maphieu_ct,
                'loaiphieu' => $value->loaiphieu,
                'hanghoa_id' => $hanghoaids[$value->hanghoa_id] ?? '',
                'hanghoa_ma' => $hanghoamas[$value->hanghoa_id] ?? '',
                'dongia' => $value->gia,
                'soluong' => $value->tongsoluong,
                'giamgia' => $value->giamgia,
                'thanhtien' => $value->thanhtien,
                'sophieu' => $value->stt,
                'is_tangkho' => $value->tonkho,
                'chinhanh_id' => $chinhanhs[$value->chinhanh_id] ?? '',
                'ngay' => explode(' ',$value->created_at)[0],
                'gio' => explode(' ',$value->created_at)[1],
                'created_at' => $value->created_at,
                'updated_at' => $value->created_at
            ]);
        }
    }
}
