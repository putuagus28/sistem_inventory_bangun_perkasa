<?php

namespace App\Http\Controllers;

use App\Akun;
use App\Jurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class AkunController extends Controller
{
    function updateSaldoAkun($akun = '', $nominal = 0, $jenis = '')
    {
        $up = Akun::find($akun);
        $kategori_debet = ['Activa Tetap', 'Activa Lancar', 'Harga Pokok Penjualan', 'Beban'];
        $kategori_kredit = ['Kewajiban', 'Modal', 'Pendapatan'];
        if (in_array($up->kategori, $kategori_debet)) {
            if ($jenis == "debet") {
                $up->saldo_awal += $nominal;
                $up->{$jenis} += $nominal;
            } else {
                $up->saldo_awal -= $nominal;
                $up->{$jenis} += $nominal;
            }
        } else {
            // kebalikan 
            if ($jenis == "debet") {
                $up->saldo_awal -= $nominal;
                $up->{$jenis} += $nominal;
            } else {
                $up->saldo_awal += $nominal;
                $up->{$jenis} += $nominal;
            }
        }

        $up->save();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Akun::with('users')
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm mx-1" id="edit"><i class="fas fa-edit"></i></a>';
                    // $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    return $btn;
                })
                ->addColumn('saldo', function ($row) {
                    return 'Rp ' . number_format($row->saldo_awal, 0, ',', '.');
                })
                ->addColumn('debet', function ($row) {
                    return 'Rp ' . number_format($row->debet, 0, ',', '.');
                })
                ->addColumn('kredit', function ($row) {
                    return 'Rp ' . number_format($row->kredit, 0, ',', '.');
                })
                ->rawColumns(['action', 'saldo', 'debet', 'kredit'])
                ->make(true);
        }
        $data = [
            'title' => 'Data Akun',
            'kategori' => ['Activa Tetap', 'Activa Lancar', 'Kewajiban', 'Modal', 'Pendapatan', 'Harga Pokok Penjualan', 'Beban']
        ];
        return view('akun.index', $data);
    }

    public function insert(Request $req)
    {
        try {
            $q = new Akun;
            $no_reff = $req->no_reff_awal . '-' . $req->no_reff;
            $cek1 = $q->where(
                [
                    'no_reff' => $no_reff,
                    'kategori' => $req->kategori
                ]
            );
            $cek2 = $q->where(['kategori' => $req->kategori, 'akun' => $req->akun]);
            $cek3 = $q->where(['akun' => $req->akun]);
            if ($cek1->exists()) {
                return response()->json(['status' => false, 'message' => 'No Reff sudah ada!']);
            } else if ($req->kategori == "Modal" && $cek2->exists()) {
                return response()->json(['status' => false, 'message' => 'Hanya bisa menambah akun modal sekali!']);
            } else if ($cek3->exists()) {
                return response()->json(['status' => false, 'message' => 'Nama akun sudah terdaftar!']);
            } else {
                $saldo = str_replace(['Rp', '.', ',', ' '], '', ($req->saldo_awal == null ? 0 : $req->saldo_awal));
                $q = new Akun;
                $q->no_reff = $no_reff;
                $q->kategori = $req->kategori;
                $q->akun = $req->akun;
                if (in_array($req->kategori, ['Activa Tetap', 'Activa Lancar', 'Harga Pokok Penjualan', 'Beban'])) {
                    $q->saldo_awal += $saldo;
                    if ($req->akun == "Akm. Penyusutan Mesin & Peralatan") {
                        $q->kredit += $saldo;
                    } else {
                        $q->debet += $saldo;
                    }
                } else {
                    $q->saldo_awal += $saldo;
                    $q->kredit += $saldo;
                }
                $q->users_id = auth()->user()->id;
                $q->save();
                return response()->json(['status' => true, 'message' => 'Tersimpan']);
            }
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $q = Akun::with('users')->where('id', $request->id)->first();
        return response()->json($q);
    }

    public function delete(Request $request)
    {
        $query = Akun::find($request->id);
        $del = $query->delete();
        if ($del) {
            return response()->json(['status' => $del, 'message' => 'Hapus Sukses']);
        } else {
            return response()->json(['status' => $del, 'message' => 'Gagal']);
        }
    }

    public function update(Request $req)
    {
        try {
            $q = new Akun;
            $no_reff = $req->no_reff_awal . '-' . $req->no_reff;
            $cek1 = $q->where('id', '<>', $req->id)->where('no_reff', $no_reff);
            if ($cek1->exists()) {
                return response()->json(['status' => false, 'message' => 'No Reff sudah ada']);
                return false;
            }
            $saldo = str_replace(['Rp', '.', ',', ' '], '', ($req->saldo_awal == null ? 0 : $req->saldo_awal));
            $akun_d = Akun::find($req->id);
            $akun_modal = Akun::find('367db022-ed50-4671-9e6b-ca3efc3ea78b');
            $akun_kas = Akun::find('37ef5388-1bb4-40fb-95e2-45b5e1d22939');
            $saldo_awal = $akun_d->saldo_awal;

            $q = Akun::find($req->id);
            $no_reff = $req->no_reff_awal . '-' . $req->no_reff;
            $q->no_reff = $no_reff;
            $q->kategori = $req->kategori;
            $q->akun = $req->akun;

            // $q->saldo_awal = $saldo;
            // if (in_array($req->kategori, ['Activa Tetap', 'Activa Lancar','Harga Pokok Penjualan', 'Beban'])) {
            //     $q->debet = $saldo;
            // } else {
            //     $q->kredit = $saldo;
            // }
            $q->save();
            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }
}
