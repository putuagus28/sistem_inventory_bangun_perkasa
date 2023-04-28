<?php

namespace App\Http\Controllers;

use App\Akun;
use App\Barang;
use App\DetailPembayaran;
use App\JenisJasa;
use App\Pelanggan;
use App\Pembayaran;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Ajax;
use App\Jurnal;
use App\Pengeluaran;

class PengeluaranController extends Controller
{
    public function index(Request $req)
    {
        if ($req->ajax()) {
            $data = Pengeluaran::with('akun', 'users')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    // $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn text-success btn-sm px-1" id="edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn text-danger btn-sm px-1" id="delete"><i class="fas fa-trash"></i></a>';
                    return $btn;
                })
                ->addColumn('tanggal', function ($row) {
                    return date('d-m-Y', strtotime($row->tgl));
                })
                ->rawColumns(['action', 'tanggal'])
                ->make(true);
        }

        $data = [
            'title' => 'Transaksi Pembayaran',
            'akun' => Akun::orderBy('no_reff', 'asc')->where('kategori', 'Beban')->get()
        ];
        return view('transaksi.pengeluaran.index', $data);
    }

    public function submit(Request $req)
    {
        try {
            DB::transaction(function () use ($req) {


                // get kode
                $ajx = new Ajax();
                $kode = $ajx->kode('pembayaran');

                // insert
                $nominal = str_replace([",", ".", " "], "", $req->nominal);
                $p = new Pengeluaran;
                $p->tgl = $req->tgl;
                $p->kode = $kode;
                $p->akuns_id = $req->akuns_id;
                $p->nominal = $nominal;
                $p->keterangan = $req->keterangan;
                $p->users_id = auth()->user()->id;
                $p->save();

                //  KAS k
                //  Akun Beban D

                $akun_kas = '37ef5388-1bb4-40fb-95e2-45b5e1d22939';
                $akun_modal = '5c692233-77ec-4426-ac12-110fa428f1ce';

                // insert jurnal
                for ($i = 0; $i < 2; $i++) {
                    $type = '';
                    $total = 0;
                    if ($i == 0) {
                        $akun_d = Akun::find($akun_kas);
                        $type = 'kredit';
                    } elseif ($i == 1) {
                        $akun_d = Akun::find($req->akuns_id);
                        $type = 'debet';
                    }
                    $jurnal = new Jurnal;
                    $jurnal->no_reff = $akun_d->no_reff;
                    $jurnal->{$type} = $nominal;
                    $jurnal->tanggal = $req->tgl;
                    $jurnal->akuns_id = $akun_d->id;
                    $jurnal->id_transaksi = $kode;
                    $jurnal->keterangan = 'pengeluaran (' . $req->keterangan . ')';
                    $jurnal->akuns_id = $akun_d->id;
                    $jurnal->users_id = auth()->user()->id;
                    if ($i > 0) {
                        $jurnal->created_at = date('Y-m-d H:i:s', strtotime('+' . $i . ' sec'));
                    }
                    $jurnal->save();

                    $this->updateSaldoAkun($akun_d->id, $total, $type);
                }
                // kurangi modal juga
                $this->updateSaldoAkun($akun_modal, $nominal, 'debet');
            });
            return response()->json(['status' => true, 'message' => 'Success']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    function upStok($id = null, $qty = 0, $act = null)
    {
        $cek_barang = Barang::where('id', $id)->exists();
        if ($cek_barang) {
            $b = Barang::find($id);
            if ($act == '+') {
                $b->jml = $b->jml + $qty;
            } else {
                $b->jml = $b->jml - $qty;
            }
            $b->save();
        } else {
            $b = JenisJasa::find($id);
        }
        return true;
    }

    public function delete(Request $req)
    {
        // delete jurnal & balikan saldo
        $p = Pengeluaran::find($req->id);
        $j = Jurnal::where('id_transaksi', $p->kode)->get();
        foreach ($j as $key => $v) {
            $jenis = ($v->debet == 0) ? 'kredit' : 'debet';

            // balik saldo akun
            $nominal_jurnal = $v->{$jenis};
            $this->balikSaldo($v->akuns_id, $nominal_jurnal, $jenis);

            // delete jurnal
            $jurnal = Jurnal::find($v->id);
            $jurnal->delete();
        }

        // kembalikan modal juga
        $akun_modal = '5c692233-77ec-4426-ac12-110fa428f1ce';
        $this->balikSaldo($akun_modal, $p->nominal, 'kredit');

        // delete pembayaran
        $q = Pengeluaran::where('id', $req->id)->delete();
        return response()->json($q);
    }

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

    function balikSaldo($akun = '', $nominal = 0, $jenis = '')
    {
        $up = Akun::find($akun);
        $kategori_debet = ['Activa Tetap', 'Activa Lancar', 'Harga Pokok Penjualan', 'Beban'];
        $kategori_kredit = ['Kewajiban', 'Modal', 'Pendapatan'];
        if (in_array($up->kategori, $kategori_debet)) {
            if ($jenis == "debet") {
                $up->saldo_awal -= $nominal;
                $up->{$jenis} -= $nominal;
            } else {
                $up->saldo_awal += $nominal;
                $up->{$jenis} -= $nominal;
            }
        } else {
            // kebalikan 
            if ($jenis == "debet") {
                $up->saldo_awal += $nominal;
                $up->{$jenis} -= $nominal;
            } else {
                $up->saldo_awal -= $nominal;
                $up->{$jenis} -= $nominal;
            }
        }

        $up->save();
    }

    public function edit(Request $req)
    {
        $q = Pengeluaran::with('akun', 'users')
            ->select(
                "*",
                DB::raw('DATE_FORMAT(pengeluarans.tgl, "%d-%m-%Y") as tanggal_format')
            )
            ->where('id', $req->id)
            ->first();
        return response()->json($q);
    }
}
