<?php

namespace Database\Seeders;

use App\Functions\Funcs;
use App\Models\DanhMuc\ChiNhanh;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KhachHangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $models = Storage::get('public/db/danhmuc_khachhang.txt');
//        $models = json_decode(str_replace('\n','',$models));
//
//        foreach($models as $model) {
//            DB::table('danhmuc_khachhang')->insert((array) $model);
//        }

        $chinhanhs = ChiNhanh::pluck('id','old_id');
        $nhanvien_id = DB::table('danhmuc_nhanvien')->pluck('id', 'old_id');

        $_data = [
            'mbm' => '123kjk32954389034klj34',
            'query' => "select *, (select loaicaytrong from module_khachhang_canhtac where khachhang_id = kh.id limit 1) as caytrong,
                (select dientich_canhtac from module_khachhang_canhtac where khachhang_id = kh.id limit 1) as dientich_canhtac
                from module_khachhang as kh where (is_active = 1
                     and (dienthoai like '03%' or dienthoai like '05%' or dienthoai like '08%'
                     or dienthoai like '07%' or dienthoai like '09%') and length(dienthoai) = 10 and trim(ten) != '' and ten is not null
                     ) or id in (select doituong_id from module_phieu where created_at > '2021-01-01')"
//            'query' => "select *, (select loaicaytrong from module_khachhang_canhtac where khachhang_id = kh.id limit 1) as caytrong,
//                (select dientich_canhtac from module_khachhang_canhtac where khachhang_id = kh.id limit 1) as dientich_canhtac
//                from module_khachhang where created_at > '2021-10-22 14:37:00'"
        ];
        $data = Funcs::CallAPI('POST','https://api-cskh.hailua.center/api/dev-tool-lay-dulieu', $_data);
        $data = json_decode($data);
        if ($data->tt->s === 'success') {
            forEach($data->dl as $key => $item) {
                if ($item->ten == '' || $item->ten == null) {
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
                $ma = $key + 1;
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

        DB::table('danhmuc_doituong')->insert([
            'id' => '1000000000',
            'ma' => 'DT000000',
            'ten' => 'ĐỐI TƯỢNG LẺ',
            'slug' => 'doituongle',
            'dienthoai' => '',
            'diachi' => '',
            '_diachi' => '',
            'chinhanh_id' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'deleted_at' => date('Y-m-d H:i:s')
        ]);
    }
}
