<?php

namespace Database\Seeders;

use App\Functions\Funcs;
use Illuminate\Database\Seeder;

class Phieu3Seeder extends Seeder
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
            'query' => "select * from module_phieu where
                                 loaiphieu = 'CKNB' and is_active = 1 and created_at > '2021-01-01'"
        ];
        $data = Funcs::CallAPI('POST','https://api-cskh.hailua.center/api/dev-tool-lay-dulieu', $_data);
        $data = json_decode($data);
    }
}
