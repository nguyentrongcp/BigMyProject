<?php

namespace App\Http\Controllers;

use App\Functions\Funcs;
use App\Models\DanhMuc\HangHoa;
use App\Models\HangHoaChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QrcodeController extends Controller
{
    public function index() {
        return view('quanly.hanghoa.qrcode.index');
    }

    public function index_inqrcode(Request $request) {
        if (!Storage::exists('public/qrcode_tam/'.$request->matam.'.txt')) {
            abort(404);
        }
        $danhsachs = Storage::get('public/qrcode_tam/'.$request->matam.'.txt');
//        Storage::delete('public/qrcode_tam/'.$request->matam.'.txt');
        $danhsachs = json_decode(str_replace('\n','',$danhsachs));
        $chinhanh_id = Funcs::getChiNhanhByToken($request->cookie('token'));

        return view('quanly.hanghoa.qrcode.qrcode', [
            'danhsachs' => $danhsachs,
            'chinhanh_id' => $chinhanh_id
        ]);
    }

    public function tao_ma(Request $request) {
        $time = time();
        if (Storage::put('public/qrcode_tam/'.$time.'.txt', json_encode($request->data))) {
            return [
                'succ' => 1,
                'data' => [
                    'matam' => $time
                ]
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Tạo mã Qrcode thất bại!'
            ];
        }
    }

    public function tim_kiem(Request $request) {
        $q = Funcs::convertToSlug($request->q);

        $options = ['id','ma','ten','donvitinh','nhom','quycach'];

        $models = HangHoa::whereRaw("(slug like '%$q%' or ma like '%$q%') and is_quydoi = 0");

        $models = $models->limit(20)->get($options);

        $ids = [];
        foreach($models as $model) {
            $model->text = $model->ma.' - '.$model->ten;
            $ids[] = $model->id;
        }
        $dongias = HangHoaChiTiet::whereIn('hanghoa_id',$ids)
            ->where('chinhanh_id',Funcs::getChiNhanhByToken($request->cookie('token')))->pluck('dongia','hanghoa_id');
        foreach($models as $model) {
            $model->dongia = $dongias[$model->id] ?? 'Chưa rõ';
        }

        return [
            'results' => $models
        ];
    }
}
