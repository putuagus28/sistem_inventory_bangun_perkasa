<?php

namespace App\Http\Controllers;

use App\Akun;
use App\Jurnal;
use App\Saldo_awal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class SaldoAwaController extends Controller
{
    function updateSaldoAkun($akun = '', $nominal = 0, $jenis = '')
    {
        $up = Akun::find($akun);
        $kategori_debet = ['Activa Tetap', 'Activa Lancar', 'Beban'];
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
            $data = Saldo_awal::with('akuns', 'users')
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    // $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm mx-1" id="edit"><i class="fas fa-edit"></i></a>';
                    // $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    return $btn;
                })
                ->addColumn('saldo', function ($row) {
                    return 'Rp ' . number_format($row->debet == 0 ? $row->kredit : $row->debet, 0, ',', '.');
                })
                ->addColumn('tanggal', function ($row) {
                    return date('d/m/Y', strtotime($row->created_at));
                })
                ->rawColumns(['action', 'saldo', 'tanggal'])
                ->make(true);
        }
        $data = [
            'title' => 'Data Saldo Akun',
            'kategori' => ['Activa Tetap', 'Activa Lancar', 'Kewajiban', 'Modal', 'Pendapatan', 'Beban'],
            'akun' => Akun::whereIn('kategori', ['Activa Tetap', 'Activa Lancar', 'Kewajiban', 'Modal'])
                ->orderBy('no_reff', 'asc')
                ->get()
        ];
        return view('akun.saldo_awal', $data);
    }

    public function submit(Request $req)
    {
        try {
            $saldo = str_replace(['Rp', '.', ',', ' '], '', ($req->saldo_awal == null ? 0 : $req->saldo_awal));
            $akun_d = Akun::find($req->akuns_id);
            $akun_modal = Akun::find('367db022-ed50-4671-9e6b-ca3efc3ea78b');
            $akun_kas = Akun::find('37ef5388-1bb4-40fb-95e2-45b5e1d22939');
            $saldo_awal = $akun_d->saldo_awal;

            // update ke akun
            $q = Akun::find($req->akuns_id);
            $q->saldo_awal += $saldo;
            // if (in_array($req->kategori, ['Activa Tetap', 'Activa Lancar', 'Beban'])) {
            //     $q->debet = $saldo;
            // } else {
            //     $q->kredit = $saldo;
            // }
            $q->save();

            // insert ke saldo_awals
            $q_saldo = new Saldo_awal;
            if (in_array($req->kategori, ['Activa Tetap', 'Activa Lancar', 'Beban'])) {
                $q_saldo->debet = $saldo;
            } else {
                $q_saldo->kredit = $saldo;
            }
            $q_saldo->akuns_id = $req->akuns_id;
            $q_saldo->users_id = auth()->user()->id;
            $q_saldo->save();

            $id_transaksi = $q_saldo->id;

            // insert ke jurnal jika saldo awal baru input
            if (in_array($akun_d->kategori, ['Activa Tetap', 'Activa Lancar'])) {
                $jurnal = new Jurnal;
                $jurnal->no_reff = $akun_d->no_reff;
                $jurnal->debet = $saldo;
                $jurnal->tanggal = date('Y-m-d');
                $jurnal->akuns_id = $akun_d->id;
                $jurnal->id_transaksi = $id_transaksi;
                $jurnal->keterangan = 'setoran saldo awal';
                $jurnal->users_id = auth()->user()->id;
                $jurnal->save();

                $jurnal_modal = new Jurnal;
                $jurnal_modal->no_reff = $akun_modal->no_reff;
                $jurnal_modal->kredit = $saldo;
                $jurnal_modal->tanggal = date('Y-m-d');
                $jurnal_modal->akuns_id = $akun_modal->id;
                $jurnal_modal->id_transaksi = $id_transaksi;
                $jurnal_modal->keterangan = 'setoran saldo awal';
                $jurnal_modal->users_id = auth()->user()->id;
                $jurnal_modal->created_at = date('Y-m-d H:i:s', strtotime('+1 sec'));
                $jurnal_modal->save();
            } elseif (in_array($akun_d->kategori, ['Kewajiban', 'Modal'])) {
                $jurnal = new Jurnal;
                $jurnal->no_reff = $akun_kas->no_reff;
                $jurnal->debet = $saldo;
                $jurnal->tanggal = date('Y-m-d');
                $jurnal->akuns_id = $akun_kas->id;
                $jurnal->id_transaksi = $id_transaksi;
                $jurnal->keterangan = 'setoran saldo awal';
                $jurnal->users_id = auth()->user()->id;
                $jurnal->save();

                $jurnal_modal = new Jurnal;
                $jurnal_modal->no_reff = $akun_d->no_reff;
                $jurnal_modal->kredit = $saldo;
                $jurnal_modal->tanggal = date('Y-m-d');
                $jurnal_modal->akuns_id = $akun_d->id;
                $jurnal_modal->id_transaksi = $id_transaksi;
                $jurnal_modal->keterangan = 'setoran saldo awal';
                $jurnal_modal->users_id = auth()->user()->id;
                $jurnal_modal->created_at = date('Y-m-d H:i:s', strtotime('+1 sec'));
                $jurnal_modal->save();
            }
            $this->updateSaldoAkun($akun_modal->id, $saldo, 'kredit');
            return response()->json(['status' => true, 'message' => 'Tersimpan']);
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
}
