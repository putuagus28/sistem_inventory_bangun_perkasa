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
            $data = Barang::with('jenis')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm mx-1" id="edit"><i class="fas fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('harga', function ($row) {
                    return 'Rp ' . number_format($row->harga, 0, ',', '.');
                })
                ->rawColumns(['action', 'harga'])
                ->make(true);
        }
        $data = [
            'title' => 'Data Barang',
            'jenis' => Jenis::orderBy('nama_jenis', 'asc')->get(),
        ];
        return view('barang.index', $data);
    }

    public function insert(Request $request)
    {
        try {
            $q = new Barang;
            $q->jenis_id = $request->jenis_id;
            $q->nama_barang = $request->nama_barang;
            $q->ukuran = $request->ukuran;
            $q->satuan = $request->satuan;
            $q->harga = $request->harga;
            $q->jumlah = $request->jumlah;
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
        $foto = $query->foto;
        $del = $query->delete();
        if ($del) {
            if ($foto != "") {
                $filePath = 'barang/' . $foto;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            return response()->json(['status' => $del, 'message' => 'Hapus Sukses']);
        } else {
            return response()->json(['status' => $del, 'message' => 'Gagal']);
        }
    }

    public function update(Request $request)
    {
        try {
            $q = Barang::find($request->id);
            $q->jenis_id = $request->jenis_id;
            $q->nama_barang = $request->nama_barang;
            $q->ukuran = $request->ukuran;
            $q->satuan = $request->satuan;
            $q->harga = $request->harga;
            $q->jumlah = $request->jumlah;
            $q->save();
            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }
}
