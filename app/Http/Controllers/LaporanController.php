<?php

namespace App\Http\Controllers;

use App\Barang;
use App\Penjualan;
use App\Pembelian;
use App\DetailPenjualan;
use App\DetailPembelian;
use App\Jenis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PDF;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index($jenis = "")
    {
        $bulan = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
            "September", "Oktober", "November", "Desember"
        ];
        if ($jenis == "pembelian") {
            $data = [
                'title' => 'Laporan ' . ucwords($jenis),
                'jenis' => $jenis,
                'bulan' => $bulan,
            ];
        } elseif ($jenis == "penjualan") {
            $data = [
                'title' => 'Laporan ' . ucwords($jenis),
                'jenis' => $jenis,
                'bulan' => $bulan,
            ];
        } elseif ($jenis == "stok") {
            $data = [
                'title' => 'Laporan ' . ucwords($jenis),
                'jenis' => $jenis,
                'kategori' => Jenis::orderBy('nama_jenis', 'asc')->get(),
            ];
        }
        return view('laporan.' . $jenis, $data);

        return abort(404);
    }

    public function getLaporan(Request $req)
    {
        if ($req->jenis == "pembelian") {
            $d1 = date('d-m-Y', strtotime($req->tgl_awal));
            $d2 = date('d-m-Y', strtotime($req->tgl_akhir));
            $bulan = $req->bulan;
            $tahun = $req->tahun;
            if ($req->pilihan == 'tanggal') {
                $query = Pembelian::with("user", "detail")
                    ->select('*', DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y") as tanggal'))
                    ->where(DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y")'), '>=', $d1)
                    ->where(DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y")'), '<=', $d2)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } elseif ($req->pilihan == 'bulan') {
                $query = Pembelian::with("user", "detail")
                    ->select('*', DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y") as tanggal'))
                    ->whereMonth('created_at', $bulan)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } elseif ($req->pilihan == 'tahun') {
                $query = Pembelian::with("user", "detail")
                    ->select('*', DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y") as tanggal'))
                    ->whereYear('created_at', $tahun)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            $data = [
                'query' => $query,
            ];
        } elseif ($req->jenis == "penjualan") {
            $d1 = date('d-m-Y', strtotime($req->tgl_awal));
            $d2 = date('d-m-Y', strtotime($req->tgl_akhir));
            $bulan = $req->bulan;
            $tahun = $req->tahun;
            if ($req->pilihan == 'tanggal') {
                $query = Penjualan::with("customers", "detail")
                    ->select('*', DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y") as tanggal'))
                    ->where(DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y")'), '>=', $d1)
                    ->where(DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y")'), '<=', $d2)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } elseif ($req->pilihan == 'bulan') {
                $query = Penjualan::with("customers", "detail")
                    ->select('*', DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y") as tanggal'))
                    ->whereMonth('created_at', $bulan)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } elseif ($req->pilihan == 'tahun') {
                $query = Penjualan::with("customers", "detail")
                    ->select('*', DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y") as tanggal'))
                    ->whereYear('created_at', $tahun)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            $data = [
                'query' => $query,
            ];
        } elseif ($req->jenis == "stok") {
            $kategori = $req->kategori;
            $q = Barang::with("jenis", "d_penjualan", "d_pembelian")
                ->select('*', DB::raw('DATE_FORMAT(created_at, "%d-%m-%Y") as tanggal'))
                ->where('jenis_id', $kategori)
                ->orderBy('created_at', 'desc')
                ->get();

            $query = [];
            foreach ($q as $key => $v) {
                $stok1 = 0;
                foreach ($v->d_pembelian as $dp1) {
                    $stok1 += $dp1->qty;
                }

                $stok2 = 0;
                foreach ($v->d_penjualan as $dp2) {
                    $stok2 += $dp2->qty;
                }

                $query[] = [
                    'barang' => ucwords($v->nama_barang),
                    'pembelian' => $stok1,
                    'penjualan' => $stok2,
                    'stok' => $v->jumlah,
                ];
            }
            $data = [
                'query' => $query,
            ];
        }
        return response()->json($data);
    }
}
