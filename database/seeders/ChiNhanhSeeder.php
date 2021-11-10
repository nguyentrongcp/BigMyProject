<?php

namespace Database\Seeders;

use App\Functions\Funcs;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChiNhanhSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $models = Storage::get('public/db/danhmuc_chinhanh.txt');
        $models = json_decode(str_replace('\n','',$models));

        foreach($models as $model) {
            DB::table('danhmuc_chinhanh')->insert((array) $model);
        }
    }
}
