<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use App\JenisJasa;

class JenisJasaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = JenisJasa::with('users')->get();
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
            'title' => 'Jenis Jasa'
        ];
        return view('jenisjasa.index', $data);
    }

    public function submit(Request $req)
    {
        try {
            if (empty($req->id)) {
                $q = new JenisJasa;
                $q->kode = $req->kode;
                $q->users_id = auth()->user()->id;
            } else {
                $q = JenisJasa::find($req->id);
            }
            $q->nama = $req->nama;
            $q->kategori = $req->kategori;
            $q->jml = $req->jml;
            $q->harga = str_replace(["Rp", " ", ",", "."], "", $req->harga);
            $q->save();
            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $q = JenisJasa::find($request->id);
        return response()->json($q);
    }

    public function delete(Request $request)
    {
        $query = JenisJasa::find($request->id);
        $del = $query->delete();
        if ($del) {
            return response()->json(['status' => $del, 'message' => 'Hapus Sukses']);
        } else {
            return response()->json(['status' => $del, 'message' => 'Gagal']);
        }
    }
}
