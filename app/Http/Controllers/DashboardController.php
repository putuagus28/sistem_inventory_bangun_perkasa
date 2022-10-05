<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Customer;
use App\Penjualan;
use App\Pembelian;
use App\Barang;
use App\Supplier;
use App\DetailPenjualan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;
        $page = $role;

        if ($role == 'admin') {
            $data = [
                'title' => 'Dashboard',
                't_user' => User::all()->count(),
                't_customer' => Customer::all()->count(),
                't_barang' => Barang::all()->count(),
                't_supplier' => Supplier::all()->count(),
                'penjualan' => Penjualan::sum('subtotal'),
                'pembelian' => Pembelian::sum('subtotal'),
            ];
        }

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
            $db = DB::table("penjualans")
                ->selectRaw('SUM(subtotal) as total')
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
            $db = DB::table("pembelians")
                ->selectRaw('SUM(subtotal) as total')
                ->whereMonth('created_at', '=', $i)
                ->whereYear('created_at', '=', date('Y'))
                ->groupBy(DB::raw("MONTH(created_at)"))
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
        $data['title'] = 'Penjualan ' . date('Y');
        $data['title2'] = 'Pembelian ' . date('Y');

        return response()->json(array('data' => $data));
    }

    public function chart2()
    {
        $data = [];
        $db = DetailPenjualan::with('barang')
        ->select('*',DB::raw('SUM(qty) as total_qty'))
        ->whereYear('created_at', '=', date('Y'))
        ->groupBy('barangs_id')
        ->get();
        foreach($db as $v){
            $data[] = $v;
        }

        return response()->json($data);
    }
}
