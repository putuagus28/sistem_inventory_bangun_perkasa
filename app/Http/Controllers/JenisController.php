<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use App\Jenis;

class JenisController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Jenis::all();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm mx-1" id="edit"><i class="fas fa-edit"></i></a>';
                    // $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $data = [
            'title' => 'Jenis Barang'
        ];
        return view('barang.jenis', $data);
    }

    public function insert(Request $request)
    {
        try {
            $q = new Jenis;
            $q->nama_jenis = $request->nama_jenis;
            if (!empty($request->password)) {
                $q->password = bcrypt($request->password);
            }
            $q->save();
            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $q = Jenis::find($request->id);
        return response()->json($q);
    }

    public function delete(Request $request)
    {
        $query = Jenis::find($request->id);
        $del = $query->delete();
        if ($del) {
            return response()->json(['status' => $del, 'message' => 'Hapus Sukses']);
        } else {
            return response()->json(['status' => $del, 'message' => 'Gagal']);
        }
    }

    public function update(Request $request)
    {
        try {
            $q = Jenis::find($request->id);
            $q->nama_jenis = $request->nama_jenis;
            if (!empty($request->password)) {
                $q->password = bcrypt($request->password);
            }
            $q->save();
            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }
}
