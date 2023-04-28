<?php

namespace App\Http\Controllers;

use App\Barang;
use App\Penjualan;
use App\Pembelian;
use App\DetailPenjualan;
use App\DetailPembelian;
use App\Jenis;
use App\Service;
use App\StokOpname;
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
        if ($jenis == "service") {
            $data = [
                'title' => 'Laporan Data Service',
                'jenis' => $jenis,
                'bulan' => $bulan,
                'tahun' => Service::select(
                    "id",
                    DB::raw("(DATE_FORMAT(tanggal, '%Y')) as tahun")
                )
                    ->orderBy('tanggal')
                    ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y')"))
                    ->get()
            ];
        }
        return view('laporan.' . $jenis, $data);

        return abort(404);
    }

    public function getLaporan(Request $req)
    {
        if ($req->jenis == "service") {
            $d1 = date('d-m-Y', strtotime($req->tgl_awal));
            $d2 = date('d-m-Y', strtotime($req->tgl_akhir));
            $bulan = $req->bulan;
            $tahun = $req->tahun;
            if ($req->pilihan == 'tanggal') {
                $query = Service::with("users", "pelanggan", "teknisi")
                    ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                    ->where(DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y")'), '>=', $d1)
                    ->where(DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y")'), '<=', $d2)
                    ->orderBy('tanggal', 'desc')
                    ->get();
            } elseif ($req->pilihan == 'bulan') {
                $query = Service::with("users", "pelanggan", "teknisi")
                    ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->orderBy('tanggal', 'desc')
                    ->get();
            } elseif ($req->pilihan == 'tahun') {
                $query = Service::with("users", "pelanggan", "teknisi")
                    ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                    ->whereYear('tanggal', $tahun)
                    ->orderBy('tanggal', 'desc')
                    ->get();
            }

            $data = [
                'query' => $query,
            ];
        }
        return response()->json($data);
    }
}
