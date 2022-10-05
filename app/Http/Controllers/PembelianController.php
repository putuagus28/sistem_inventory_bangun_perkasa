<?php

namespace App\Http\Controllers;

use App\Pembelian;
use App\DetailPembelian;
use App\Supplier;
use App\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PembelianController extends Controller
{
    public function index(Request $req)
    {
        if ($req->ajax()) {
            $data = Pembelian::with('user')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    $no = 'PB-' . strtotime($row->created_at) . $row->subtotal;
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" data-no="' . $no . '" class="btn btn-info btn-sm m-1" id="detail"><i class="fa fa-eye" aria-hidden="true"></i> detail</a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" data-no="' . $no . '" class="btn btn-danger btn-sm m-1" id="delete"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    return $btn;
                })
                ->addColumn('no', function ($row) {
                    return 'PB-' . strtotime($row->created_at) . $row->subtotal;
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
            'title' => 'Transaksi Pembelian',
            'brg' => Barang::orderBy('nama_barang', 'asc')->get()
        ];
        return view('transaksi.pembelian.index', $data);
    }

    public function add(Request $req)
    {
        $data = [
            'title' => 'Tambah Pembelian',
            'supplier' => Supplier::all(),
            'brg' => Barang::orderBy('nama_barang', 'asc')->get()
        ];
        return view('transaksi.pembelian.add', $data);
    }


    public function listCart(Request $req)
    {
        $html = '';
        $status = false;
        if (session('pembelian')) {
            $no = 1;
            $subtotal = 0;
            foreach (session('pembelian') as $id => $item) {
                $html .= '<tr>';
                $html .= '<td>' . $no . '</td>';
                $html .= '<td>' . $item['supplier'] . '</td>';
                $html .= '<td>' . $item['item'] . '</td>';
                $html .= '<td>' . $item['satuan'] . '</td>';
                $html .= '<td>' . $item['qty'] . '</td>';
                $html .= '<td>Rp ' . number_format($item['harga'], 0, ',', '.') . '</td>';
                $html .= '<td>Rp ' . number_format($item['harga'] * $item['qty'], 0, ',', '.') . '</td>';
                $html .= '<td class="text-right">
                <a href="javascript:void(0)" data-id="' . $id . '" data-cart="pembelian" class="btn btn-danger btn-sm m-1" id="delete"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </td>';
                $html .= '</tr>';
                $no++;
                $subtotal += ($item['harga'] * $item['qty']);
            }
            $html .= '<tr>';
            $html .= '<td colspan="5"></td>';
            $html .= '<td><b>SubTotal</b></td>';
            $html .= '<td><b>Rp ' . number_format($subtotal, 0, ',', '.') . '</b></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
            $status = true;
        } else {
            $html .= '<td colspan="8" class="text-center">Belum ada data</td>';
            $status = false;
        }
        return response()->json(['html' => $html, 'status' => $status]);
    }

    public function addToCart(Request $req)
    {
        try {
            $id = $req->barang;
            $product = Barang::findOrFail($id);
            $supplier = Supplier::findOrFail($req->suppliers_id);
            $cart = session()->get('pembelian', []);
            if (isset($cart[$id])) {
                $cart[$id]["qty"] += $req->qty;
            } else {
                // insert cart
                $cart[$id] = [
                    "id" => $product->id,
                    "item" => $product->nama_barang,
                    "suppliers_id" => $req->suppliers_id,
                    "supplier" => $supplier->nama,
                    "satuan" => $product->satuan,
                    "qty" => $req->qty,
                    "harga" => $product->harga,
                ];
            }
            session()->put('pembelian', $cart);
            return response()->json(['status' => true, 'message' => 'success']);
        } catch (\Exception $er) {
            return response()->json(['status' => false, 'message' => $er->getMessage()]);
        }
    }

    public function removeAll($finish = false)
    {
        Session::forget('pembelian');
        return response()->json(['status' => true, 'message' => 'Success Remove']);
    }

    public function removeOne(Request $req)
    {
        $jenis_cart = $req->cart;
        if ($req->id) {
            $cart = session()->get($jenis_cart);
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
                foreach ((array) session('pembelian') as $id => $details) {
                    $subtotal += ($details['harga'] * $details['qty']);
                }
                // insert
                $p = new Pembelian;
                $p->users_id = auth()->user()->id;
                $p->subtotal = $subtotal;
                $p->save();
                $last_id = $p->id;
                // insert detail
                foreach ((array) session('pembelian') as $id => $details) {
                    $this->upStok($id, $details['qty'], '+');
                    $detail = new DetailPembelian;
                    $detail->suppliers_id = $details['suppliers_id'];
                    $detail->pembelians_id = $last_id;
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
        $q = Pembelian::with('detail')->where('id', $req->id)->get();
        foreach($q as $v){
            foreach($v->detail as $d){
                $this->upStok($d->barangs_id, $d->qty, '-');
            }
        }
        $q = Pembelian::where('id', $req->id)->delete();
        return response()->json($q);
    }

    public function edit(Request $req)
    {
        $q = Pembelian::with('user', 'detail.barang', 'detail.supplier')->where('id', $req->id)->first();
        return response()->json($q);
    }

    public function update(Request $req)
    {
        try {
            $up = Pembelian::find($req->id);
            $barangs_id = $up->barangs_id;
            $up->stok_nyata = $req->stok_nyata;
            $up->stok_comp = $req->stok_comp;
            $up->users_id = auth()->user()->id;
            $simpan = $up->save();
            if ($simpan) {
                $brg = Barang::find($barangs_id);
                $brg->jumlah = $req->stok_nyata;
                $brg->save();
                return response()->json(['status' => $simpan, 'message' => 'Sukses']);
            }
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }
}
