<?php

namespace Database\Seeders;

use App\Functions\Funcs;
use App\Models\DanhMuc\KhachHang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhachHang2Seeder extends Seeder
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
            'query' => "select *, (select loaicaytrong from module_khachhang_canhtac where khachhang_id = kh.id limit 1) as caytrong,
                (select dientich_canhtac from module_khachhang_canhtac where khachhang_id = kh.id limit 1) as dientich_canhtac
                from module_khachhang as kh where id in (select doituong_id from module_phieu where created_at > '2021-01-01')"
        ];
        $data = Funcs::CallAPI('POST','https://api-cskh.hailua.center/api/dev-tool-lay-dulieu', $_data);
        $data = json_decode($data);
        $maxMaKH = DB::select("select max(ma) as ma from danhmuc_khachhang")[0];
        $maxMaKH = (int) substr($maxMaKH->ma,2);
        if ($data->tt->s === 'success') {
            forEach($data->dl as $key => $item) {
                if ($item->ten == '' || $item->ten == null || KhachHang::where('old_id',$item->id)->count() > 0) {
                    continue;
                }
                $item->diachi = '';
                if ($item->_ap != '') {
                    $item->diachi .= ($item->diachi == '' ? '' : ', ').$item->_ap;
                }
                if ($item->_xa != '') {
                    $item->diachi .= ($item->diachi == '' ? '' : ', ').$item->_xa;
                }
                if ($item->_huyen != '') {
                    $item->diachi .= ($item->diachi == '' ? '' : ', ').$item->_huyen;
                }
                if ($item->_tinh != '') {
                    $item->diachi .= ($item->diachi == '' ? '' : ', ').$item->_tinh;
                }
                $ma = $maxMaKH++;
                while (strlen((string) $ma) < 6) {
                    $ma = '0'.$ma;
                }
                $data_insert = [
                    'id' => rand(1000000000,9999999999),
                    'old_id' => $item->id,
                    'ma' => 'KH'.$ma,
                    'ten' => $item->ten,
                    'slug' => Funcs::convertToSlug($item->ten),
                    'dienthoai' => $item->dienthoai,
                    'chinhanh_id' => $chinhanhs[$item->chinhanh_id] ?? '',
                    'diachi' => $item->diachi,
                    'danhxung' => '',
                    'nhanvien_id' => $nhanvien_id[$item->nhanvien_tao_id] ?? null,
                    'caytrong' => $item->caytrong,
                    'dientich' => $item->dientich_canhtac,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                DB::table('danhmuc_khachhang')->insert($data_insert);
            }
        }
    }
}
