<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonViTinhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data_insert = [];
        $tens = ['GÓI','CHAI','HỘP','BAO','HỦ','XÔ','KG','LÍT','SỢI','THÙNG','TIP','PHI',
            'ỐNG','TỜ','ĐÔI','VIÊN','BỊT','CAN','BÓ','CUỘN','LẠNG','CẶP','CÂY','CÁI','BỘ'];
//        $tens = ['KG','HỘP','CÁI','GÓI','LÍT','THÙNG','BỊT'];
        foreach($tens as $ten) {
            $data_insert[] = [
                'ten' => $ten,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        DB::table('danhmuc_donvitinh')->insert($data_insert);
    }
}
