<?php

namespace App\Http\Controllers;

use App\Penjualan;
use App\DetailPenjualan;
use App\Customer;
use App\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenjualanController extends Controller
{
    public function index(Request $req)
    {
        if ($req->ajax()) {
            $data = Penjualan::with('user', 'customers')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    $no = 'PJ-' . strtotime($row->created_at) . $row->subtotal;
                    if ($row->delivery == 0) {
                        $btn .= '<div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-expanded="false">Status
                        </button>
                        <div class="dropdown-menu" style="">
                        <a class="dropdown-item" id="terkirim" data-id="' . $row->id . '" href="#">Sudah dikirim</a>
                        </div>
                        </div>';
                    }
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" data-customers="' . ucwords($row->customers->nama) . '" data-no="' . $no . '" class="btn btn-info btn-sm m-1" id="detail"><i class="fa fa-eye" aria-hidden="true"></i> detail</a>';
                    if ($row->delivery == 0) {
                        $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" data-no="' . $no . '" class="btn btn-danger btn-sm m-1" id="delete"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    }
                    return $btn;
                })
                ->addColumn('no', function ($row) {
                    return 'PJ-' . strtotime($row->created_at) . $row->subtotal;
                })
                ->addColumn('tanggal', function ($row) {
                    return date('d/m/Y G:i:s', strtotime($row->created_at));
                })
                ->addColumn('subtotal', function ($row) {
                    return 'Rp ' . number_format($row->subtotal, 0, ',', '.');
                })
                ->rawColumns(['action', 'no', 'tanggal', 'subtotal'])
                ->make(true);
        }

        $data = [
            'title' => 'Transaksi Penjualan',
            'brg' => Barang::orderBy('nama_barang', 'asc')->get()
        ];
        return view('transaksi.penjualan.index', $data);
    }

    public function add(Request $req)
    {
        $data = [
            'title' => 'Tambah Penjualan',
            'customers' => Customer::all(),
            'brg' => Barang::orderBy('nama_barang', 'asc')->get()
        ];
        return view('transaksi.penjualan.add', $data);
    }


    public function listCart(Request $req)
    {
        $html = '';
        $status = false;
        if (session('penjualan')) {
            $no = 1;
            $subtotal = 0;
            foreach (session('penjualan') as $id => $item) {
                $html .= '<tr>';
                $html .= '<td>' . $no . '</td>';
                $html .= '<td>' . $item['item'] . '</td>';
                $html .= '<td>' . $item['satuan'] . '</td>';
                $html .= '<td>' . $item['qty'] . '</td>';
                $html .= '<td>Rp ' . number_format($item['harga'], 0, ',', '.') . '</td>';
                $html .= '<td>Rp ' . number_format($item['harga'] * $item['qty'], 0, ',', '.') . '</td>';
                $html .= '<td class="text-right">
                <a href="javascript:void(0)" data-id="' . $id . '" data-cart="penjualan" class="btn btn-danger btn-sm m-1" id="delete"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </td>';
                $html .= '</tr>';
                $no++;
                $subtotal += ($item['harga'] * $item['qty']);
            }
            $html .= '<tr>';
            $html .= '<td colspan="4"></td>';
            $html .= '<td><b>SubTotal</b></td>';
            $html .= '<td><b>Rp ' . number_format($subtotal, 0, ',', '.') . '</b></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
            $status = true;
        } else {
            $html .= '<td colspan="7" class="text-center">Belum ada data</td>';
            $status = false;
        }
        return response()->json(['html' => $html, 'status' => $status]);
    }

    public function addToCart(Request $req)
    {
        try {
            $id = $req->barang;
            $product = Barang::findOrFail($id);
            if ($product->jumlah - $req->qty < 0) {
                return response()->json(['status' => false, 'message' => 'Stok barang tidak cukup!']);
            } else {
                $cart = session()->get('penjualan', []);
                if (isset($cart[$id])) {
                    $cart[$id]["qty"] += $req->qty;
                    $this->upStok($id, $req->qty, '-');
                } else {
                    // insert cart
                    $cart[$id] = [
                        "id" => $product->id,
                        "item" => $product->nama_barang,
                        "satuan" => $product->satuan,
                        "qty" => $req->qty,
                        "harga" => $product->harga,
                    ];
                    // kurangi stok langsung
                    $this->upStok($product->id, $req->qty, '-');
                }
                session()->put('penjualan', $cart);
                return response()->json(['status' => true, 'message' => 'success']);
            }
        } catch (\Exception $er) {
            return response()->json(['status' => false, 'message' => $er->getMessage()]);
        }
    }

    public function removeAll($finish = false)
    {
        // kembalikan semua qty
        foreach (session('penjualan') as $id => $item) {
            $this->upStok($id, $item['qty'], '+');
        }
        Session::forget('penjualan');
        return response()->json(['status' => true, 'message' => 'Success Remove']);
    }

    public function removeOne(Request $req)
    {
        $jenis_cart = $req->cart;
        if ($req->id) {
            $cart = session()->get($jenis_cart);
            $this->upStok($req->id, $cart[$req->id]['qty'], '+');
            if (isset($cart[$req->id])) {
                unset($cart[$req->id]);
                session()->put($jenis_cart, $cart);
            }
            return response()->json(['status' => true, 'message' => 'Success Remove']);
        }
    }

    // finish cart
    public function finish(Request $req)
    {
        try {
            DB::transaction(function () use ($req) {
                $subtotal = 0;
                foreach ((array) session('penjualan') as $id => $details) {
                    $subtotal += ($details['harga'] * $details['qty']);
                }
                // insert
                $p = new Penjualan;
                $p->users_id = auth()->user()->id;
                $p->customers_id = $req->customers_id;
                $p->subtotal = $subtotal;
                $p->delivery = 0;
                $p->save();
                $last_id = $p->id;
                // insert detail
                foreach ((array) session('penjualan') as $id => $details) {
                    $this->upStok($id, $details['qty'], '-');
                    $detail = new DetailPenjualan;
                    $detail->penjualans_id = $last_id;
                    $detail->barangs_id = $id;
                    $detail->qty = $details['qty'];
                    $detail->save();
                }
                $this->removeAll(true);
            });
            return response()->json(['status' => true, 'message' => 'Success']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    function get_stok(Request $req)
    {
        $barang = Barang::find($req->id);
        return response()->json($barang->jumlah);
    }

    function upStok($id = null, $qty = 0, $act = null)
    {
        $b = Barang::find($id);
        if ($act == '+') {
            $b->jumlah = $b->jumlah + $qty;
        } else {
            $b->jumlah = $b->jumlah - $qty;
        }
        return $b->save();
    }

    public function delete(Request $req)
    {
        $q = Penjualan::with('detail')->where('id', $req->id)->get();
        foreach ($q as $v) {
            foreach ($v->detail as $d) {
                $this->upStok($d->barangs_id, $d->qty, '+');
            }
        }
        $q = Penjualan::where('id', $req->id)->delete();
        return response()->json($q);
    }

    public function kirim(Request $req)
    {
        $q = Penjualan::find($req->id);
        $q->delivery = 1;
        $q->save();
        return response()->json($q);
    }


    public function edit(Request $req)
    {
        $q = Penjualan::with('user', 'customers', 'detail.barang')->where('id', $req->id)->first();
        return response()->json($q);
    }
}
