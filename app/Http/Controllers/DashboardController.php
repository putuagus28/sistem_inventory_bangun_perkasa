<?php

namespace App\Http\Controllers;

use App\Barang;
use App\DetailPembayaran;
use App\Pelanggan;
use App\Pembayaran;
use App\Pengeluaran;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;

        if (in_array($role, ['admin', 'owner'])) {
            $data = [
                'title' => 'Selamat Datang ' . ucwords($role),
                't_user' => User::all()->count(),
                't_service' => Service::all()->count(),
                't_pelanggan' => Pelanggan::all()->count(),
                't_barang' => Barang::all()->count(),
                'pendapatan' => DetailPembayaran::whereMonth('created_at', date('m'))
                    ->whereYear('created_at', date('Y'))
                    ->sum(DB::raw('harga * qty')),
                'pengeluaran' => Pengeluaran::whereMonth('tgl', date('m'))->whereYear('tgl', date('Y'))->sum('nominal'),
            ];
        } elseif ($role == "teknisi") {
            $data = [
                'title' => 'Selamat Datang ' . ucwords($role),
                't_user' => User::all()->count(),
                't_service' => Service::where('teknisi_id', auth()->user()->id)->where('status', 'open')->get()->count(),
                't_riwayat' => Service::where('teknisi_id', auth()->user()->id)->where('status', 'close')->get()->count(),
            ];
        } elseif ($role == "pelanggan") {
            $data = [
                'title' => 'Selamat Datang ' . ucwords($role),
                't_user' => User::all()->count(),
                't_service' => Service::where('pelanggans_id', auth()->user()->id)->get()->count(),
                't_done' => Service::where('pelanggans_id', auth()->user()->id)->where('status', 'close')->get()->count(),
                't_on_progress' => Service::where('pelanggans_id', auth()->user()->id)->where('status', 'open')->get()->count(),
            ];
        }

        $data['role'] = $role;

        $data['bulan'] = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
            "September", "Oktober", "November", "Desember"
        ];

        return view('dashboard', $data);
    }

    public function chart()
    {
        $data['bulan'] = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
            "September", "Oktober", "November", "Desember"
        ];

        $total = [];
        $total2 = [];
        for ($i = 1; $i <= 12; $i++) {
            $db = DB::table("detail_pembayarans")
                ->selectRaw('SUM(harga * qty) as total')
                ->whereMonth('created_at', '=', $i)
                ->whereYear('created_at', '=', date('Y'))
                ->groupBy(DB::raw("MONTH(created_at)"))
                ->get();
            if ($db->count() == null) {
                $total[] = 0;
            } else {
                foreach ($db as $val) {
                    $total[] = $val->total;
                }
            }
        }

        for ($i = 1; $i <= 12; $i++) {
            $db = DB::table("pengeluarans")
                ->selectRaw('SUM(nominal) as total')
                ->whereMonth('tgl', '=', $i)
                ->whereYear('tgl', '=', date('Y'))
                ->groupBy(DB::raw("MONTH(tgl)"))
                ->get();
            if ($db->count() == null) {
                $total2[] = 0;
            } else {
                foreach ($db as $val) {
                    $total2[] = $val->total;
                }
            }
        }
        $data['total'] = $total;
        $data['total2'] = $total2;
        $data['title'] = 'Pendapatan ' . date('Y');
        $data['title2'] = 'Pengeluaran ' . date('Y');

        return response()->json(array('data' => $data));
    }
    public function chart2()
    {
        $data = [];
        $barang = [];
        $barang_id = [];
        $d = DetailPembayaran::with('barang')->where('kategori', 'barang')->groupBy('jasa_barang_id')->get();
        foreach ($d as $v) {
            $barang[] = $v->barang->nama;
            $barang_id[] = $v->barang->id;
        }
        // $bulan = [
        //     "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
        //     "September", "Oktober", "November", "Desember"
        // ];
        for ($i = 0; $i < count($barang); $i++) {
            $db = DetailPembayaran::with('barang')
                ->selectRaw('COUNT(*) as total')
                ->select('*', DB::raw('COUNT(*) as total'))
                // ->whereMonth('created_at', '=', ($i + 1))
                // ->whereYear('created_at', '=', date('Y'))
                ->where('jasa_barang_id', $barang_id)
                ->orderBy('total', 'desc')
                ->groupBy('jasa_barang_id')
                ->first();
            if ($db->count() == 0) {
                $data[] = [
                    'bulan' => $barang[$i],
                    'total' => 0,
                ];
            } else {
                $data[] = [
                    'bulan' => $barang[$i],
                    'total' => $db->total,
                ];
            }
        }

        return response()->json($data);
    }
}
