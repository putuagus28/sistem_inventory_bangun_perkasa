<?php

namespace App\Http\Controllers;

use App\Akun;
use App\Jurnal;
use App\User;
use App\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use App\Service;


class ServiceController extends Controller
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

    function getAkun($id)
    {
        $q = Akun::find($id);
        return response()->json($q);
    }

    public function index(Request $req, $status = "")
    {
        if ($req->ajax()) {
            $role = auth()->user()->role;
            if ($role == "admin") {
                $data = Service::with('pelanggan', 'users', 'teknisi', 'pembayaran')->get();
            } else {
                if ($role == 'teknisi') {
                    $data = Service::with('pelanggan', 'users', 'teknisi', 'pembayaran')
                        ->where('teknisi_id', auth()->user()->id)
                        ->where('status', $req->status)
                        ->get();
                } else {
                    $data = Service::with('pelanggan', 'users', 'teknisi', 'pembayaran')
                        ->where('pelanggans_id', auth()->user()->id)
                        ->where('status', $req->status)
                        ->get();
                }
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    if (auth()->user()->role == "admin") {
                        $css = empty($row->pembayaran) ? '' : 'd-none';
                        $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm" id="edit"><i class="fas fa-edit"></i></a>';
                        $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm ' . $css . '" id="delete"><i class="fas fa-trash"></i></a>';
                    }
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-info btn-sm" id="detail"><i class="fas fa-eye"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'keluhan'])
                ->make(true);
        }

        $role = auth()->user()->role;
        if ($role == "admin") {
            $data = [
                'title' => 'Data Service',
                'pelanggan' => Pelanggan::all(),
                'teknisi' => User::where('role', 'teknisi')->get(),
            ];
        } elseif ($role == "teknisi") {
            $data = [
                'title' => $req->status == 'open' ? 'Daftar Antrean Permintaan Service' : 'Riwayat Service Sebelumnya',
                'pelanggan' => Pelanggan::all(),
                'teknisi' => User::where('role', 'teknisi')->get(),
            ];
        } elseif ($role == "pelanggan") {
            $data = [
                'title' => $req->status == 'open' ? 'Daftar Antrean Service Saya' : 'Riwayat Service Saya Sebelumnya ',
                'pelanggan' => Pelanggan::all(),
                'teknisi' => User::where('role', 'teknisi')->get(),
            ];
        }
        $data['status'] = empty($req->status) ? 'open' : $req->status;
        return view('service.index', $data);
    }

    public function submit(Request $req)
    {
        // 1. Rumus kalo DP 0 :
        // Piutang usaha -Debit(rp.xx total pembayaran)
        // Pendapatan - kredit (Rp. Total pembayaran)

        // Kalo udah dibyr:
        // Pendapatan -dbit
        // Piutang-kredit

        // 2. Rumus kalo DP ada :
        // Uang muka -Debit (Rp. 500k)
        // Piutang usaha -Kredit (Rp. misal 500k)
        // Pendapatan usaha -Kredit (Rp. 1juta)

        // Klo udh dbyr:
        // Kas-debit
        // Piutang-kredit

        try {
            if (empty($req->id)) {
                $q = new Service;
                $q->kode = $req->kode;
                $q->users_id = auth()->user()->id;
            } else {
                $q = Service::find($req->id);
            }
            $uang_muka = str_replace(["Rp", " ", ",", "."], "", $req->uang_muka);
            $q->tanggal = $req->tanggal;
            $q->uang_muka = $uang_muka;
            $q->jenis_antrean = $req->jenis_antrean;
            $q->no_antrean = $req->no_antrean;
            $q->jenis_barang = $req->jenis_barang;
            $q->lama_service = $req->lama_service;
            $q->keluhan = $req->keluhan;
            $q->status = 'open'; //open atau close
            $q->pelanggans_id = $req->pelanggans_id;
            $q->teknisi_id = $req->teknisi_id;
            $q->save();

            $kode = $q->id;

            $akun_piutang = '033ac065-9757-4b8a-bfa1-67d643e9b439';
            $akun_pendapatan = 'eddd5391-88fc-47a7-88c4-7ff032cc1730';
            $akun_uang_muka = '500e5be6-9a35-4ca1-9b40-7df78d34174a';
            // ketika tambah
            if (empty($req->id)) {
                // insert jurnal 
                if ($uang_muka > 0  || $uang_muka != "") {
                    // Uang muka -Debit (Rp. 500k)
                    // Piutang usaha -Kredit (Rp. misal 500k)
                    // Pendapatan usaha -Kredit (Rp. 1juta)
                    for ($i = 0; $i < 3; $i++) {
                        $type = '';
                        $total = 0;
                        if ($i == 0) {
                            $akun_d = Akun::find($akun_uang_muka);
                            $type = 'debet';
                            $total = $uang_muka;
                        } elseif ($i == 1) {
                            $akun_d = Akun::find($akun_piutang);
                            $type = 'debet';
                            $total = $uang_muka;
                        } elseif ($i == 2) {
                            $akun_d = Akun::find($akun_pendapatan);
                            $type = 'kredit';
                            $total = $uang_muka * 2;
                        }
                        $jurnal = new Jurnal;
                        $jurnal->no_reff = $akun_d->no_reff;
                        $jurnal->{$type} = $total;
                        $jurnal->tanggal = $req->tanggal;
                        $jurnal->akuns_id = $akun_d->id;
                        $jurnal->id_transaksi = $kode;
                        $jurnal->keterangan = 'permintaan_service';
                        $jurnal->akuns_id = $akun_d->id;
                        $jurnal->users_id = auth()->user()->id;
                        if ($i > 0) {
                            $jurnal->created_at = date('Y-m-d H:i:s', strtotime('+' . $i . ' sec'));
                        }
                        $jurnal->save();

                        $this->updateSaldoAkun($akun_d->id, $total, $type);
                    }
                }
            }

            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function update_progress(Request $req)
    {

        try {
            $q = Service::find($req->id);
            $q->riwayat = $req->riwayat;
            $q->status = (isset($req->status) ? 'close' : 'open'); //open atau close
            $q->save();

            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $req)
    {
        $q = Service::with('pelanggan', 'users', 'teknisi')->where('id', $req->id)->first();
        return response()->json($q);
    }

    public function delete(Request $req)
    {
        $p = Service::find($req->id);
        // jurnal
        $j = Jurnal::where('id_transaksi', $p->id)->get();
        foreach ($j as $key => $v) {
            $jenis = ($v->debet == 0) ? 'kredit' : 'debet';

            // balik saldo akun
            $nominal_jurnal = $v->{$jenis};
            $this->balikSaldo($v->akuns_id, $nominal_jurnal, $jenis);

            // delete jurnal
            $jurnal = Jurnal::find($v->id);
            $jurnal->delete();
        }

        $query = Service::find($req->id);
        $del = $query->delete();
        if ($del) {
            return response()->json(['status' => $del, 'message' => 'Hapus Sukses']);
        } else {
            return response()->json(['status' => $del, 'message' => 'Gagal']);
        }
    }
}
