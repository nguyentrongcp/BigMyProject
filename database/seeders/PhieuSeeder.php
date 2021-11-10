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

class PhieuSeeder extends Seeder
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
        $khachhangs = KhachHang::withTrashed()->pluck('id','old_id');
        $nhacungcaps = NhaCungCap::withTrashed()->pluck('id','old_id');
        $hanghoaids = HangHoa::withTrashed()->pluck('id','old_id');
        $hanghoamas = HangHoa::withTrashed()->pluck('ma','old_id');

        $_data = [
            'mbm' => '123kjk32954389034klj34',
            'query' => "select * from module_phieu where
                                 loaiphieu in ('BH','KTH','DKHH','KTH','NH',
                                               'PC','PCCNNCC','PCL','PCNB','PCVCBX','PCX','PT','PTCNKH','PTNB','THNCC')
                                               and is_active = 1 and created_at > '2021-01-01' limit 30000"
        ];
        $data = Funcs::CallAPI('POST','https://api-cskh.hailua.center/api/dev-tool-lay-dulieu', $_data);
        $data = json_decode($data);

        foreach ($data->dl as $value) {
            $khoanmuc_id = null;
            if (in_array($value->loaiphieu,['BH','KTH','PTCNKH']) !== false) {
                $doituong_id = $khachhangs[$value->doituong_id] ?? '1000000000';
                if ($value->loaiphieu == 'PTCNKH') {
                    $value->loaiphieu = 'TCNKH';
                }
            }
            elseif (in_array($value->loaiphieu,['NH','THNCC','PCCNNCC']) !== false) {
                $doituong_id = $nhacungcaps[$value->doituong_id] ?? '1000000000';
                if ($value->loaiphieu == 'PCCNNCC') {
                    $value->loaiphieu = 'CCNNCC';
                }
            }
            elseif ($value->loaiphieu == 'DKHH') {
                $doituong_id = null;
            }
            else {
                $doituong_id = '1000000000';
                if (in_array($value->loaiphieu,['PC','PCL','PCNB','PCVCBX','PCX']) !== false) {
                    $khoanmuc_id = 19;
                    $value->loaiphieu = 'PC';
                }
                else {
                    $khoanmuc_id = 3;
                    $value->loaiphieu = 'PT';
                }
            }
            $model = new Phieu();
            $model->old_id = $value->id;
            $model->doituong_id = $doituong_id;
            $model->khoanmuc_id = $khoanmuc_id;
            $model->maphieu = $value->maphieu;
            $model->loaiphieu = $value->loaiphieu;
            $model->tongthanhtien = $value->tongthanhtien ?? 0;
            $model->phuthu = $value->phuthu ?? 0;
            $model->giamgia = $value->giamgia ?? 0;
            $model->tienthanhtoan = $value->tienthanhtoan ?? 0;
            $model->ghichu = $value->ghichu ?? null;
            $model->noidung = $value->lydo ?? null;
            $model->tienkhachdua = $value->tienkhachdua ?? 0;
            $model->tienthua = $value->tienconlai > 0 ? -$value->tienconlai : $value->tienthua;
            $model->nhanvien_id = $nhanviens[$value->nhanvien_id] ?? '';
            $model->nhanvien_tuvan_id = $nhanviens[$value->nhanvien_banhang_id] ?? null;
            $model->chinhanh_id = $chinhanhs[$value->chinhanh_id] ?? '';
            $model->sophieu = $value->stt;
            $model->status = $value->status ?? 1;
            $model->created_at = $value->created_at;
            $model->updated_at = $value->created_at;
            $model->ngay = explode(' ',$value->created_at)[0];
            $model->gio = explode(' ',$value->created_at)[1];
            $model->save();

//            if (in_array($value->loaiphieu,['BH','KTH','NH','THNCC','DKHH'])) {
//                $chitiet = [
//                    'mbm' => '123kjk32954389034klj34',
//                    'query' => "select * from module_phieu_chitiet where
//                                 phieu_id = '$value->id'"
//                ];
//                $chitiet = Funcs::CallAPI('POST','https://api-cskh.hailua.center/api/dev-tool-lay-dulieu', $chitiet);
//                $chitiet = json_decode($chitiet);
//
//                if (isset($chitiet->dl)) {
//                    foreach($chitiet->dl as $phieu_chitiet) {
//                        DB::table('phieu_chitiet')->insert([
//                            'id' => rand(1000000000,9999999999),
//                            'phieu_id' => $model->id,
//                            'maphieu' => $model->maphieu,
//                            'loaiphieu' => $model->loaiphieu,
//                            'hanghoa_id' => $hanghoaids[$phieu_chitiet->hanghoa_id] ?? '',
//                            'hanghoa_ma' => $hanghoamas[$phieu_chitiet->hanghoa_id] ?? '',
//                            'dongia' => $phieu_chitiet->gia,
//                            'soluong' => $phieu_chitiet->tongsoluong,
//                            'giamgia' => $phieu_chitiet->giamgia,
//                            'thanhtien' => $phieu_chitiet->thanhtien,
//                            'sophieu' => $phieu_chitiet->stt,
//                            'is_tangkho' => $phieu_chitiet->tonkho,
//                            'chinhanh_id' => $model->chinhanh_id,
//                            'ngay' => explode(' ',$value->created_at)[0],
//                            'gio' => explode(' ',$value->created_at)[1],
//                            'created_at' => $value->created_at,
//                            'updated_at' => $value->created_at
//                        ]);
//                    }
//                }
//            }
        }
    }
}
