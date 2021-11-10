<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DiaChiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tinhs = Storage::get('public/db/danhmuc_tinh.txt');
        $huyens = Storage::get('public/db/danhmuc_huyen.txt');
        $xas = Storage::get('public/db/danhmuc_xa.txt');
        $tinhs = json_decode($tinhs);
        $huyens = json_decode($huyens);
        $xas = json_decode($xas);

        foreach($tinhs as $tinh) {
            DB::table('danhmuc_tinh')->insert((array) $tinh);
        }
        foreach($huyens as $huyen) {
            DB::table('danhmuc_huyen')->insert((array) $huyen);
        }
        foreach($xas as $xa) {
            DB::table('danhmuc_xa')->insert((array) $xa);
        }
    }
}
