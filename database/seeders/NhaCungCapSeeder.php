<?php

namespace Database\Seeders;

use App\Functions\Funcs;
use App\Models\DanhMuc\NhaCungCap;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhaCungCapSeeder extends Seeder
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
            'query' => "select * from module_nhacungcap where is_active = 1 or id in
                                                       (select doituong_id from module_phieu where loaiphieu = 'NH' and is_active = 1 and created_at > '2021-01-01')"
        ];
        $data = Funcs::CallAPI('POST','https://api-cskh.hailua.center/api/dev-tool-lay-dulieu', $_data);
        $data = json_decode($data);
        if ($data->tt->s === 'success') {
            forEach($data->dl as $key => $item) {
                $ma = $key + 1;
                while (strlen((string) $ma) < 5) {
                    $ma = '0'.$ma;
                }
                $data_insert = [
                    'id' => rand(1000000000,9999999999),
                    'old_id' => $item->id,
                    'ma' => 'NCC'.$ma,
                    'ten' => $item->ten,
                    'slug' => Funcs::convertToSlug($item->ten),
                    'dienthoai' => $item->dienthoai,
                    'diachi' => $item->diachi,
                    'sotaikhoan' => $item->sotaikhoan,
                    'nguoidaidien' => $item->nguoidaidien,
                    'ghichu' => $item->ghichu ?? null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                DB::table('danhmuc_nhacungcap')->insert($data_insert);
            }
        }
    }
}
