<?php

namespace App\Http\Controllers;

use App\Mahasiswa;
use App\Ukm;
use App\AnggotaUkm;
use App\Pemasukan;
use App\Pembayaran;
use App\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Barang;
use App\JenisJasa;
use App\Pelanggan;
use App\Service;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class Ajax extends Controller
{

    public function kode($jenis)
    {
        if ($jenis == "pelanggan") {
            $max = Pelanggan::max('kode');
            $kodeBarang = $max;
            // mengambil angka dari kode barang terbesar, menggunakan fungsi substr
            // dan diubah ke integer dengan (int)
            $urutan = (int) substr($kodeBarang, 3, 3);
            $urutan++;
            $huruf = "PLG";
            $kode = $huruf . sprintf("%03s", $urutan);
        } elseif ($jenis == "jenisjasa") {
            $max = JenisJasa::max('kode');
            $kodeBarang = $max;
            // mengambil angka dari kode barang terbesar, menggunakan fungsi substr
            // dan diubah ke integer dengan (int)
            $urutan = (int) substr($kodeBarang, 3, 3);
            $urutan++;
            $huruf = "JSA";
            $kode = $huruf . sprintf("%03s", $urutan);
        } elseif ($jenis == "barang") {
            $max = Barang::max('kode');
            $kodeBarang = $max;
            // mengambil angka dari kode barang terbesar, menggunakan fungsi substr
            // dan diubah ke integer dengan (int)
            $urutan = (int) substr($kodeBarang, 3, 3);
            $urutan++;
            $huruf = "BRG";
            $kode = $huruf . sprintf("%03s", $urutan);
        } elseif ($jenis == "service") {
            $max = Service::max('kode');
            $kodeBarang = $max;
            // mengambil angka dari kode barang terbesar, menggunakan fungsi substr
            // dan diubah ke integer dengan (int)
            $urutan = (int) substr($kodeBarang, 3, 3);
            $urutan++;
            $huruf = "SER";
            $kode = $huruf . sprintf("%03s", $urutan);
        } elseif ($jenis == "no_antrean_service") {
            $max = Service::max('no_antrean');
            $kodeBarang = $max;
            // mengambil angka dari kode barang terbesar, menggunakan fungsi substr
            // dan diubah ke integer dengan (int)
            $urutan = (int) substr($kodeBarang, 3, 3);
            $urutan++;
            $huruf = "A";
            $kode = $huruf . sprintf("%03s", $urutan);
        } elseif ($jenis == "pembayaran") {
            $max = Pembayaran::max('kode');
            $kodeBarang = $max;
            // mengambil angka dari kode barang terbesar, menggunakan fungsi substr
            // dan diubah ke integer dengan (int)
            $urutan = (int) substr($kodeBarang, 3, 3);
            $urutan++;
            $huruf = "PEM";
            $kode = $huruf . sprintf("%03s", $urutan);
        } elseif ($jenis == "pengeluaran") {
            $max = Pengeluaran::max('kode');
            $kodeBarang = $max;
            // mengambil angka dari kode barang terbesar, menggunakan fungsi substr
            // dan diubah ke integer dengan (int)
            $urutan = (int) substr($kodeBarang, 3, 3);
            $urutan++;
            $huruf = "PGN";
            $kode = $huruf . sprintf("%03s", $urutan);
        }
        return $kode;
    }

    public function detail_service(Request $req)
    {
        $q = Service::with('pelanggan', 'users')->where('id', $req->id)->first();
        return response()->json($q, 200);
    }

    function hari($date)
    {
        $daftar_hari = array(
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        );

        $namahari = date('l', strtotime($date));
        return $daftar_hari[$namahari];
    }

    function formatMoney($money = null)
    {
        return 'Rp ' . number_format($money, 0, ',', '.');
    }
}
