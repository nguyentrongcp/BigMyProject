<?php

namespace Database\Seeders;

use App\Functions\Funcs;
use App\Models\DanhMuc\ChiNhanh;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Psy\Util\Str;

class NhanVienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('danhmuc_nhanvien')->insert([
            [
                'id' => '1000000000',
                'ma' => 'SADMIN96',
                'ten' => 'Super Admin',
                'slug' => Funcs::convertToSlug('Super Admin'),
                'taikhoan' => 'sadmin96',
                'dienthoai' => '123456',
                'ngaysinh' => '1996-10-12',
                'matkhau' => Hash::make('123'),
                'loai' => 9,
                'chinhanh_id' => '1000000000',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ]);

//        $models = Storage::get('public/db/danhmuc_nhanvien.txt');
//        $models = json_decode(str_replace('\n','',$models));
//
//        foreach($models as $model) {
//            if ($model->id == '1000000000') {
//                $model->matkhau = Hash::make('123');
//            }
//            else {
//                $model->matkhau = Hash::make('Hailuannv2021');
//            }
//            DB::table('danhmuc_nhanvien')->insert((array) $model);
//        }

        $chinhanhs = ChiNhanh::pluck('id','old_id');
        $_data = [
            'mbm' => '123kjk32954389034klj34',
            'query' => "select * from module_nhanvien
                where is_active = 1 or
                      (id in (select nhanvien_id from module_phieu where is_active = 1 and created_at > '2021-01-01')
                          or id in (select nhanvien_banhang_id from module_phieu where is_active = 1 and created_at > '2021-01-01'))
                          or id in (select rid from module_phieu_chitiet where created_at > '2021-01-01')"
        ];
        $data = Funcs::CallAPI('POST','https://api-cskh.hailua.center/api/dev-tool-lay-dulieu', $_data);
        $data = json_decode($data);
        if ($data->tt->s === 'success') {
            forEach($data->dl as $key => $item) {
                if (strlen($item->dienthoai) > 11) {
                    continue;
                }
                if (strpos($item->ngaysinh,'-00') !== false) {
                    $item->ngaysinh = '1970-01-01';
                }
                $ma = $key + 1;
                while (strlen((string) $ma) < 6) {
                    $ma = '0'.$ma;
                }
                $data_insert = [
                    'id' => rand(1000000000,9999999999),
                    'old_id' => $item->id,
                    'ma' => 'NV'.$ma,
                    'ten' => $item->ten,
                    'slug' => Funcs::convertToSlug($item->ten),
                    'taikhoan' => '',
                    'dienthoai' => $item->dienthoai,
                    'ngaysinh' => $item->ngaysinh ?? '1970-01-01',
                    'matkhau' => Hash::make('Hailuannv2021'),
                    'loai' => 1,
                    'chinhanh_id' => $chinhanhs[$item->chinhanh_id] ?? null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'deleted_at' => $item->is_active ? null : date('Y-m-d H:i:s')
                ];

                DB::table('danhmuc_nhanvien')->insert($data_insert);
            }
        }
    }
}
