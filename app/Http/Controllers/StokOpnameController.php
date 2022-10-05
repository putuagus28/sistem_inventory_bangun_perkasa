<?php

namespace App\Http\Controllers;

use App\StokOpname;
use App\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StokOpnameController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = StokOpname::with('user', 'barang')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm m-1" id="edit"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $data = [
            'brg' => Barang::orderBy('nama_barang', 'asc')->get()
        ];
        return view('stokopname.index', $data);
    }

    // listCart stokopname
    public function listCart(Request $request)
    {
        $html = '';
        $status = false;
        if (session('stokopname')) {
            foreach (session('stokopname') as $id => $item) {
                $html .= '<tr>';
                $html .= '<td>' . $item['item'] . '</td>';
                $html .= '<td>' . $item['stok_comp'] . '</td>';
                $html .= '<td>' . $item['stok_nyata'] . '</td>';
                $html .= '<td>' . $item['selisih'] . '</td>';
                $html .= '<td class="text-right">
                <a href="javascript:void(0)" data-id="' . $id . '" data-cart="stokopname" class="btn btn-danger btn-sm m-1" id="delete"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </td>';
                $html .= '</tr>';
            }
            $status = true;
        } else {
            $html .= '<td colspan="5" class="text-center">Belum ada data</td>';
            $status = false;
        }
        return response()->json(['html' => $html, 'status' => $status]);
    }
    // cart stokopname
    public function addToCart(Request $request)
    {
        try {
            $id = $request->barang;
            $product = Barang::findOrFail($id);
            $cart = session()->get('stokopname', []);
            if (isset($cart[$id])) {
                return response()->json(['status' => false, 'message' => 'Hanya bisa menambah sekali barang yang sama']);
            } else {
                // insert cart
                $cart[$id] = [
                    "id" => $product->id,
                    "item" => $product->nama_barang,
                    "stok_nyata" => $request->stok_nyata,
                    "stok_comp" => $product->jumlah,
                    "selisih" => ($request->stok_nyata - $request->stok_comp),
                ];
            }
            session()->put('stokopname', $cart);
            return response()->json(['status' => true, 'message' => 'success']);
        } catch (\Exception $er) {
            return response()->json(['status' => false, 'message' => $er->getMessage()]);
        }
    }

    public function removeAll($finish = false)
    {
        Session::forget('stokopname');
        return response()->json(['status' => true, 'message' => 'Success Remove']);
    }

    public function removeOne(Request $request)
    {
        $jenis_cart = $request->cart;
        if ($request->id) {
            $cart = session()->get($jenis_cart);
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put($jenis_cart, $cart);
            }
            return response()->json(['status' => true, 'message' => 'Success Remove']);
        }
    }

    // finish cart
    public function finish(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // insert
                foreach ((array) session('stokopname') as $id => $details) {
                    $this->rubahStok($id, $details['stok_nyata']);
                    $detail = new StokOpname;
                    $detail->stok_nyata = $details['stok_nyata'];
                    $detail->stok_comp = $details['stok_comp'];
                    $detail->barangs_id = $id;
                    $detail->users_id = auth()->user()->id;
                    $detail->save();
                }
                $this->removeAll(true);
            });
            return response()->json(['status' => true, 'message' => 'Success']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function stokAdd(Request $request)
    {
        $data = [
            'brg' => Barang::orderBy('nama_barang', 'asc')->get()
        ];
        return view('stokopname.add', $data);
    }

    function get_stok(Request $request)
    {
        $barang = Barang::find($request->id);
        return response()->json($barang->jumlah);
    }

    function rubahStok($id = null, $qty = 0)
    {
        $b = Barang::find($id);
        $b->jumlah = $qty;
        return $b->save();
    }

    public function edit(Request $request)
    {
        $user = new StokOpname;
        return response()->json($user->find($request->id));
    }

    public function update(Request $request)
    {
        try {
            $up = StokOpname::find($request->id);
            $barangs_id = $up->barangs_id;
            $up->stok_nyata = $request->stok_nyata;
            $up->stok_comp = $request->stok_comp;
            $up->users_id = auth()->user()->id;
            $simpan = $up->save();
            if ($simpan) {
                $brg = Barang::find($barangs_id);
                $brg->jumlah = $request->stok_nyata;
                $brg->save();
                return response()->json(['status' => $simpan, 'message' => 'Sukses']);
            }
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }
}
