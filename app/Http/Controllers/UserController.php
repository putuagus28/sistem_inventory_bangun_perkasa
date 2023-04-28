<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use App\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::all();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn text-success btn-sm px-1" id="edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn text-danger btn-sm px-1" id="delete"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $role = array("admin", "teknisi", "pelanggan", "owner");
        $data = [
            'title' => "Halaman Data User",
            'role' => $role,
        ];
        return view('user.index', $data);
    }

    public function add(Request $request)
    {
        try {
            $user = new User;
            $user->name = $request->name;
            $user->alamat = $request->alamat;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->role = $request->role;
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();
            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $q = User::find($request->id);
        return response()->json($q);
    }

    public function delete(Request $request)
    {
        $query = User::find($request->id);
        $del = $query->delete();
        if ($del) {
            return response()->json(['status' => $del, 'message' => 'Hapus Sukses']);
        } else {
            return response()->json(['status' => $del, 'message' => 'Gagal']);
        }
    }

    public function profile(Request $request)
    {
        if ($request->ajax()) {
            $q = User::find(auth()->user()->id);
            return response()->json($q);
        }
        $q = User::find(auth()->user()->id);
        $data = [
            'data' => $q
        ];
        return view('user.profile', $data);
    }


    public function update(Request $request)
    {
        try {
            $user = User::find($request->id);
            if (!empty($request->name)) {
                $user->name = $request->name;
            }
            if (!empty($request->alamat)) {
                $user->alamat = $request->alamat;
            }
            if (!empty($request->email)) {
                $user->email = $request->email;
            }
            if (!empty($request->role)) {
                $user->role = $request->role;
            }
            if (!empty($request->username)) {
                $user->username = $request->username;
            }
            if (!empty($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->save();
            return response()->json(['status' => true, 'message' => 'Sukses']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }
}
