<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use App\Barang;
use App\Jenis;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Barang::with('users')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn text-success btn-sm px-1" id="edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn text-danger btn-sm px-1" id="delete"><i class="fas fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $data = [
            'title' => 'Data Barang',
        ];
        return view('barang.index', $data);
    }


    public function submit(Request $req)
    {
        try {
            if (empty($req->id)) {
                $q = new Barang;
                $q->kode = $req->kode;
                $q->users_id = auth()->user()->id;
            } else {
                $q = Barang::find($req->id);
            }
            $q->kode_barang = $req->kode_barang;
            $q->nama = $req->nama;
            $q->kategori = $req->kategori;
            $q->jml = $req->jml;
            $q->harga = str_replace(["Rp", " ", ",", "."], "", trim($req->harga));
            $q->save();
            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $q = Barang::find($request->id);
        return response()->json($q);
    }

    public function delete(Request $request)
    {
        $query = Barang::find($request->id);
        $del = $query->delete();
        if ($del) {
            return response()->json(['status' => $del, 'message' => 'Hapus Sukses']);
        } else {
            return response()->json(['status' => $del, 'message' => 'Gagal']);
        }
    }
}
