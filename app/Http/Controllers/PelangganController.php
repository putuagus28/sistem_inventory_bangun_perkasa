<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use App\Pelanggan;

class PelangganController extends Controller
{
    public function index(Request $req)
    {
        if ($req->ajax()) {
            $data = Pelanggan::all();
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
            'title' => 'Data Pelanggan',
        ];
        return view('pelanggan.index', $data);
    }

    public function submit(Request $req)
    {
        try {
            if (empty($req->id)) {
                $q = new Pelanggan;
                $q->kode = $req->kode;
                $q->users_id = auth()->user()->id;
            } else {
                $q = Pelanggan::find($req->id);
            }
            $q->nama = $req->nama;
            $q->alamat = $req->alamat;
            $q->no_telp = $req->no_telp;
            $q->username = $req->username;
            $q->role = 'pelanggan';
            if (!empty($req->password)) {
                $q->password = bcrypt($req->password);
            }
            $q->save();
            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $req)
    {
        $q = Pelanggan::find($req->id);
        return response()->json($q);
    }

    public function delete(Request $req)
    {
        $query = Pelanggan::find($req->id);
        $del = $query->delete();
        if ($del) {
            return response()->json(['status' => $del, 'message' => 'Hapus Sukses']);
        } else {
            return response()->json(['status' => $del, 'message' => 'Gagal']);
        }
    }
}
