<?php

namespace App\Http\Controllers;

use App\Functions\Funcs;
use App\Functions\Pusher;
use App\Models\DanhMuc\ChiNhanh;
use App\Models\DanhMuc\ChucVu;
use App\Models\DanhMuc\NhanVien;
use App\Models\DiemDanh;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiemDanhController extends Controller
{
    private $ngay = '2021-12-01';
    private $tg_batdau = '06:30:10';
    private $tg_ketthuc = '17:30:11';



    public function index_lichsu() {
        return view('mobile.lichsu-diemdanh.index');
    }

    public function index_danhsach() {
        $chucvus = ChucVu::all(['id','loai','ten']);
        $chinhanhs = ChiNhanh::where('loai','!=','khohanghong')->get(['id','ten']);
        $nhanviens = NhanVien::all(['id','ten as text']);

        return view('quanly.danhsach-diemdanh.index', [
            'chucvus' => $chucvus,
            'chinhanhs' => $chinhanhs,
            'nhanviens' => $nhanviens
        ]);
    }

    public function lich_su(Request $request) {
        $thang = $request->thang;
        $nam = $request->nam.'-'.((float)$thang < 10 ? '0'.$thang : $thang);
        $nhanvien_id = $request->nhanvien_id ?? Funcs::getNhanVienIDByToken($request->cookie('token'));

        $results = DiemDanh::where('nhanvien_id',$nhanvien_id)->where('ngay','like',$nam.'%')->orderByDesc('ngay')
            ->get(['id','ngay','tg_batdau','tg_ketthuc','ngaycong']);

        return $results;
    }

    public function danh_sach(Request $request) {
        $thang = $request->thang;
        $nam = $request->nam.'-'.((float)$thang < 10 ? '0'.$thang : $thang);
        $chinhanh_ids = json_decode($request->chinhanhs);
        $chucvus = json_decode($request->chucvus);
        $chinhanhs = ChiNhanh::withTrashed()->pluck('ten','id');
        $nhanviens = NhanVien::withTrashed();
        if (count($chinhanh_ids) > 0) {
            $nhanviens = $nhanviens->whereIn('chinhanh_id',$chinhanh_ids);
        }
        if (count($chucvus) > 0) {
            $nhanviens = $nhanviens->whereIn('chucvu',$chucvus);
        }
        $nhanviens = $nhanviens->get(['id','chucvu','ten','chinhanh_id']);
        $nhanvien_ids = [];
        $chucvus = ChucVu::withTrashed()->pluck('ten','loai');
        foreach($nhanviens as $key => $nhanvien) {
            $nhanvien_ids[] = $nhanvien->id;
            $nhanvien->tenchucvu = $chucvus[$nhanvien->chucvu] ?? 'Chưa rõ';
            $nhanvien->tenchinhanh = $chinhanhs[$nhanvien->chinhanh_id] ?? 'Chưa rõ';
            $nhanviens[$nhanvien->id] = $nhanvien;
            unset($nhanviens[$key]);
        }
        $nhanvien_ids = implode(', ', $nhanvien_ids);
        $danhsach = DB::select("
            select nhanvien_id, sum(ngaycong) as ngaycong
            from diemdanh
            where ngay like '$nam%'".
            ($nhanvien_ids == '' ? '' : " and nhanvien_id in ($nhanvien_ids) ")
            ."group by nhanvien_id
        ");
        foreach($danhsach as $item) {
            $item->id = rand(1000000000,9999999999);
            $item->tennhanvien = $nhanviens[$item->nhanvien_id]->ten ?? 'Chưa rõ';
            $item->tenchucvu = $nhanviens[$item->nhanvien_id]->tenchucvu ?? 'Chưa rõ';
            $item->tenchinhanh = $chinhanhs[$nhanviens[$item->nhanvien_id]->chinhanh_id ?? '---'] ?? 'Chưa rõ';
            $item->thang = $request->thang;
            $item->nam = $request->nam;
        }

        return $danhsach;
    }

    public function check_thong_tin(Request $request) {
        $token = $request->cookie('token');
//        $today = $this->ngay;
        $today = date('Y-m-d');
        $nhanvien = Funcs::getNhanVienByToken($token,['id','is_parttime']);
        $results = DiemDanh::where([
            'nhanvien_id' => $nhanvien->id,
            'ngay' => $today
        ])->orderByDesc('id')->get(['id','ngay','tg_batdau','tg_ketthuc','ngaycong','chinhanh_batdau','chinhanh_ketthuc']);
        $checked = count($results) == 0 ? 0 : ($results[0]->tg_ketthuc == null ? 1 : 2);
        if ($nhanvien->is_parttime && $checked == 2) {
            $checked = 0;
        }

        return [
            'succ' => 1,
            'data' => [
                'today' => $today,
                'checked' => $checked,
                'results' => $results
            ]
        ];
    }

    public function bat_dau(Request $request) {
        $token = $request->cookie('token');
        $nhanvien = Funcs::getNhanVienByToken($token,['id','chinhanh_id','is_parttime']);
        $nhanvien_id = $request->nhanvien_id ?? $nhanvien->id;
        $chinhanh_id = $request->chinhanh_id ?? $nhanvien->chinhanh_id;
        $toado = $request->toado ?? null;
//        $ngay = $this->ngay;
//        $time = $this->tg_batdau;
        $ngay = $request->ngay ?? date('Y-m-d');
        $time = $request->thoigian ?? date('H:i:s');

        $checked = DiemDanh::where([
            'nhanvien_id' => $nhanvien_id,
            'ngay' => $ngay
        ])->count();

        if ($checked > 0 && !$nhanvien->is_parttime) {
            return [
                'succ' => 0,
                'noti' => 'Bạn đã điểm danh bắt đầu rồi. Không thể điểm danh lại!'
            ];
        }
        $model = new DiemDanh();
        $model->nhanvien_id = $nhanvien_id;
        $model->chinhanh_batdau = $chinhanh_id;
        $model->ngay = $ngay;
        $model->toado_batdau = $toado;
        $model->tg_batdau = $time;

        try {
            $model->save();
            if ($nhanvien->chinhanh_id != $chinhanh_id) {
                $nhanvien->chinhanh_id = $chinhanh_id;
                $nhanvien->update();
                event(new Pusher('reload-info-'.$nhanvien_id,''));
            }
        }
        catch (QueryException $exception) {
            return [
                'succ' => 0,
                'noti' => 'Điểm danh bắt đầu thất bại. Vui lòng thử lại sau!',
                'mess' => $exception->getMessage()
            ];
        }

        return [
            'succ' => 1,
            'noti' => 'Điểm danh bắt đầu thành công.',
            'data' => $model
        ];
    }

    public function ket_thuc(Request $request) {
        $token = $request->cookie('token');
        $nhanvien = Funcs::getNhanVienByToken($token,['id','chinhanh_id','is_parttime']);
        $nhanvien_id = $request->nhanvien_id ?? $nhanvien->id;
        $chinhanh_id = $request->chinhanh_id ?? $nhanvien->chinhanh_id;
        $toado = $request->toado ?? null;
//        $ngay = $this->ngay;
//        $time = $this->tg_ketthuc;
        $ngay = $request->ngay ?? date('Y-m-d');
        $time = $request->thoigian ?? date('H:i:s');

        $model = DiemDanh::where([
            'nhanvien_id' => $nhanvien_id,
            'ngay' => $ngay
        ])->whereNull('tg_ketthuc')->first(['id','tg_batdau','chinhanh_ketthuc','tg_ketthuc','ngaycong']);

        if ($model == null) {
            return [
                'succ' => 0,
                'noti' => 'Bạn chưa điểm danh bắt đầu. Không thể điểm danh kết thúc!'
            ];
        }
        if ($model->tg_ketthuc != null) {
            return [
                'succ' => 0,
                'noti' => 'Bạn đã điểm danh kết thúc ngày hôm nay rồi. Không thể điểm danh lại!'
            ];
        }

        $ngaycong = strtotime($ngay.' '.$time) - strtotime($ngay.' '.$model->tg_batdau);
        $ngaycong = $ngaycong / 3600;
        $phanle = $ngaycong - ((int) $ngaycong);
        $ngaycong = (int) $ngaycong;
        $ngaycong = $phanle >= 0.75 ? ($ngaycong + 1) : $ngaycong;

        $model->chinhanh_ketthuc = $chinhanh_id;
        $model->tg_ketthuc = $time;
        $model->toado_ketthuc = $toado;
        $model->ngaycong = $nhanvien->is_parttime ? $ngaycong : (($ngaycong >= 4 && $ngaycong < 8) ? 0.5 : ($ngaycong >= 8 ? 1 : 0));

        if ($model->update()) {
            return [
                'succ' => 1,
                'noti' => 'Điểm danh kết thúc ngày thành công.',
                'data' => $model
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Điểm danh kết thúc ngày thất bại. Vui lòng thử lại sau!'
            ];
        }
    }

    public function xoa(Request $request) {
        $id = $request->id;

        $model = DiemDanh::find($id);
        if ($model == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }

        if ($model->forceDelete()) {
            return [
                'succ' => 1,
                'noti' => 'Xóa điểm danh thành công.'
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Xóa điểm danh thất bại!'
            ];
        }
    }

    public function reset(Request $request) {
        $id = $request->id;

        $model = DiemDanh::find($id);
        if ($model == null) {
            return [
                'succ' => 0,
                'noti' => 'Dữ liệu đầu vào không hợp lệ!'
            ];
        }

        $model->tg_ketthuc = null;
        $model->chinhanh_ketthuc = null;
        $model->toado_ketthuc = null;
        $model->ngaycong = 0;

        if ($model->update()) {
            return [
                'succ' => 1,
                'noti' => 'Reset điểm danh thành công.'
            ];
        }
        else {
            return [
                'succ' => 0,
                'noti' => 'Reset điểm danh thất bại!'
            ];
        }
    }
}
