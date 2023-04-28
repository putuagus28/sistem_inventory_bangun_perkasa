<?php

namespace App\Http\Controllers;

use App\Akun;
use App\Cart;
use App\Jurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class JurnalController extends Controller
{
    function getAkun(Request $req)
    {
        return Akun::find($req->id);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Jurnal::with('users', 'akun')
                ->orderBy('created_at', 'asc')
                ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    // $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-success btn-sm mx-1" id="edit"><i class="fas fa-edit"></i></a>';
                    // $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                    return $btn;
                })
                ->addColumn('created_at', function ($row) {
                    return date('d-m-Y G:i:s', strtotime($row->created_at));
                })
                ->addColumn('tanggal', function ($row) {
                    return date('d/m/Y', strtotime($row->tanggal));
                })
                ->addColumn('debet', function ($row) {
                    return 'Rp ' . number_format($row->debet, 0, ',', '.');
                })
                ->addColumn('kredit', function ($row) {
                    return 'Rp ' . number_format($row->kredit, 0, ',', '.');
                })
                ->rawColumns(['action', 'saldo', 'debet', 'kredit', 'created_at'])
                ->make(true);
        }
        $data = [
            'title' => 'Jurnal Umum',
            'akun' => Akun::all(),
        ];

        // $arr_jurnal  = [
        //     [
        //         'akun' => 'Kas',
        //         'debet' => 0,
        //         'kredit' => 1000,
        //     ],
        //     [
        //         'akun' => 'Utang',
        //         'debet' => 2000,
        //         'kredit' => 0,
        //     ],
        //     [
        //         'akun' => 'Potongan Pembelian',
        //         'debet' => 0,
        //         'kredit' => 1000,
        //     ],
        // ];

        // $arr_debet = [];
        // $arr_kredit = [];
        // foreach ($arr_jurnal as $key => $v) {
        //     if ($v['debet'] != 0) {
        //         $arr_debet[] = [
        //             'akun' => $v['akun'],
        //             'debet' => $v['debet'],
        //             'kredit' => 0,
        //         ];
        //     }
        //     if ($v['kredit'] != 0) {
        //         $arr_kredit[] = [
        //             'akun' => $v['akun'],
        //             'debet' => 0,
        //             'kredit' => $v['kredit'],
        //         ];
        //     }
        // }

        // echo '<pre>';
        // print_r(array_merge($arr_debet, $arr_kredit));
        return view('jurnal.index', $data);
    }

    public function insert(Request $req)
    {
        try {
            $cart = Cart::with('akun')
                ->orderBy('created_at', 'desc')
                ->get();
            $kode = Str::random(6);
            foreach ($cart as $item) {
                $jenis_saldo = ($item->debet == 0 ? 'kredit' : 'debet');
                $saldo = ($item->debet == 0 ? $item->kredit : $item->debet);
                $akun = Akun::find($item->akuns_id);

                $jurnal = new Jurnal;
                $jurnal->tanggal = date('Y-m-d', strtotime($item->tanggal));
                $jurnal->no_reff = $item->no_reff;
                $jurnal->keterangan = 'jurnal umum';
                $jurnal->akuns_id = $item->akuns_id;
                $jurnal->id_transaksi = $kode;
                // jika akun modal, maka kebalikan
                if (in_array($akun->kategori, ["Modal", "Kewajiban", "Pendapatan"])) {
                    $up = Akun::find($item->akuns_id);
                    // khusus modal
                    if ($akun->kategori == "Modal") {
                        if ($jenis_saldo == "debet") {
                            $up->saldo_awal -= $saldo;
                        } else {
                            $up->saldo_awal += $saldo;
                        }
                        $up->{$jenis_saldo} += $saldo;
                        $up->save();
                    } else {
                        // pendapatan, kewajiban 
                        $m = Akun::where('kategori', 'Modal')->first();
                        $modal = Akun::find($m->id);
                        // kalkulasi saldo akun
                        if ($jenis_saldo == "debet") {
                            $modal->saldo_awal -= $saldo;
                        } else {
                            $modal->saldo_awal += $saldo;
                        }
                        $modal->save();
                        $up->{$jenis_saldo} += $saldo;
                        $up->save();
                    }
                } else {
                    // update saldo akun yg dipilih
                    $up = Akun::find($item->akuns_id);
                    if ($jenis_saldo == "debet") {
                        $up->saldo_awal += $saldo;
                    } else {
                        $up->saldo_awal -= $saldo;
                    }
                    $up->{$jenis_saldo} += $saldo;
                    $up->save();


                    // update saldo akun modal
                    $m = Akun::where('kategori', 'Modal')->first();
                    $modal = Akun::find($m->id);
                    // kalkulasi saldo akun
                    if ($jenis_saldo == "debet") {
                        $modal->saldo_awal += $saldo;
                    } else {
                        $modal->saldo_awal -= $saldo;
                    }
                    $modal->save();
                }
                $jurnal->{$jenis_saldo} = $saldo;
                $jurnal->created_at = $item->created_at;
                $jurnal->users_id = auth()->user()->id;
                $jurnal->save();
            }
            $this->delAll_cart();
            return response()->json(['status' => true, 'message' => 'Tersimpan']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $q = Jurnal::with('users')->where('id', $request->id)->first();
        return response()->json($q);
    }

    public function delete(Request $request)
    {
        $jurnal = Jurnal::find($request->id);
        $nominal = $jurnal->debet == 0 ? $jurnal->kredit : $jurnal->debet;
        $jenis = $jurnal->debet == 0 ? 'kredit' : 'debet';
        // select akun modal
        $m = Akun::where('kategori', 'Modal')->first();
        // kurangi saldo akun
        $akun = Akun::find($jurnal->akuns_id);
        $hasil = ($akun->{$jenis} - $nominal) <= 0 ? 0 : $akun->{$jenis} - $nominal;
        $akun->{$jenis} = $hasil;
        $akun->save();
        // update saldo awal modal
        $modal = Akun::find($m->id);
        if ($jenis == "kredit") {
            if (in_array($akun->kategori, ["Modal", "Kewajiban", "Pendapatan"])) {
                $modal->saldo_awal -= $nominal;
            } else {
                $modal->saldo_awal += $nominal;
            }
        } else {
            if (in_array($akun->kategori, ["Modal", "Kewajiban", "Pendapatan"])) {
                $modal->saldo_awal += $nominal;
            } else {
                $modal->saldo_awal -= $nominal;
            }
        }
        $modal->save();
        // delete jurnal
        $del = $jurnal->delete();
        if ($del) {
            return response()->json(['status' => $del, 'message' => 'Hapus Sukses']);
        } else {
            return response()->json(['status' => $del, 'message' => 'Gagal']);
        }
    }

    public function update(Request $req)
    {
        try {
            $q = Jurnal::find($req->id);
            $debet_lama = $q->debet;
            $kredit_lama = $q->kredit;
            $jenis_lama = ($debet_lama == 0 ? 'kredit' : 'debet');

            $q->tanggal = date('Y-m-d', strtotime($req->tanggal));
            $q->no_reff = $req->no_reff;
            $q->keterangan = 'jurnal umum';
            // jika akun masih sama, update biasa
            if ($q->akuns_id == $req->akuns_id) {
                $type = Akun::find($req->akuns_id);
                // update debet kredit jurnal
                $jenis = $req->jenis_saldo;
                if ($jenis == "debet") {
                    $q->debet = $req->saldo;
                    $q->kredit = 0;
                } else {
                    $q->kredit = $req->saldo;
                    $q->debet = 0;
                }
                // reset saldo lama
                $akun_min = Akun::find($req->akuns_id);
                $cek = $akun_min->{$jenis} -= $q->{$jenis};
                if ($cek <= 0) {
                    $nominal = 0;
                } else {
                    $nominal = $akun_min->{$jenis} -= $q->{$jenis};
                }
                $akun_min->{$jenis} = $nominal;
                $akun_min->save();
                // update saldo baru
                $akun_plus = Akun::find($req->akuns_id);
                $act = '';
                // jika jenis sama
                if ($jenis_lama == $jenis) {
                    $akun_plus->{$jenis} = $req->saldo;
                    if ($jenis == "debet") {
                        $act = '+';
                    } else {
                        $act = '-';
                    }
                } else {
                    if ($jenis == "debet") {
                        $act = '+';
                        $akun_plus->kredit -= $req->saldo;
                    } else {
                        $act = '-';
                        $akun_plus->debet -= $req->saldo;
                    }
                    $akun_plus->{$jenis} = $req->saldo;
                }
                $akun_plus->save();
                // update saldo akun modal
                $akun_m = Akun::where('kategori', 'Modal')->first();
                $akun_modal = Akun::find($akun_m->id);
                $modal = 0;
                if ($q->debet == 0) {
                    $modal = $req->saldo - $kredit_lama;
                } else {
                    $modal = $req->saldo - $debet_lama;
                }
                // kebalikan jika akunnya modal
                if ($type->kategori == "Modal") {
                    if ($act == '+') {
                        $akun_modal->saldo_awal -= $modal;
                    } else {
                        $akun_modal->saldo_awal += $modal;
                    }
                } else {
                    if ($act == '+') {
                        $akun_modal->saldo_awal += $modal;
                    } else {
                        $akun_modal->saldo_awal -= $modal;
                    }
                }

                $akun_modal->save();
            } else {
                $type = Akun::find($req->akuns_id);
                // update debet kredit jurnal
                $jenis = $req->jenis_saldo;
                if ($jenis == "debet") {
                    $q->debet = $req->saldo;
                    $q->kredit = 0;
                } else {
                    $q->kredit = $req->saldo;
                    $q->debet = 0;
                }
                $q->akuns_id = $req->akuns_id;
                // reset saldo lama
                $akun_min = Akun::find($req->akuns_id);
                $cek = $akun_min->{$jenis} -= $q->{$jenis};
                if ($cek <= 0) {
                    $nominal = 0;
                } else {
                    $nominal = $akun_min->{$jenis} -= $q->{$jenis};
                }
                $akun_min->{$jenis} = $nominal;
                $akun_min->save();
                // update saldo baru
                $akun_plus = Akun::find($req->akuns_id);
                $act = '';
                // jika jenis sama
                if ($jenis_lama == $jenis) {
                    $akun_plus->{$jenis} = $req->saldo;
                    if ($jenis == "debet") {
                        $act = '+';
                    } else {
                        $act = '-';
                    }
                } else {
                    if ($jenis == "debet") {
                        $act = '+';
                        $akun_plus->kredit -= $req->saldo;
                    } else {
                        $act = '-';
                        $akun_plus->debet -= $req->saldo;
                    }
                    $akun_plus->{$jenis} = $req->saldo;
                }
                $akun_plus->save();
                // update saldo akun modal
                $akun_m = Akun::where('kategori', 'Modal')->first();
                $akun_modal = Akun::find($akun_m->id);
                $modal = 0;
                if ($q->debet == 0) {
                    $modal = $req->saldo - $kredit_lama;
                } else {
                    $modal = $req->saldo - $debet_lama;
                }
                // kebalikan jika akunnya modal
                if ($type->kategori == "Modal") {
                    if ($act == '+') {
                        $akun_modal->saldo_awal -= $modal;
                    } else {
                        $akun_modal->saldo_awal += $modal;
                    }
                } else {
                    if ($act == '+') {
                        $akun_modal->saldo_awal += $modal;
                    } else {
                        $akun_modal->saldo_awal -= $modal;
                    }
                }
                $akun_modal->save();
            }

            $q->save();
            return response()->json(['status' => true, 'message' => 'Sukses Update ' . $act]);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
    }

    // cart
    public function list()
    {
        $cart = Cart::with('akun')
            ->orderBy('created_at', 'desc')->get();
        return response()->json($cart);
    }

    public function add(Request $req)
    {
        $akun = Akun::find($req->akuns_id);
        $cart = new Cart;
        $cart->tanggal = date('Y-m-d', strtotime($req->tanggal));
        $cart->kategori = $akun->kategori;
        $cart->keterangan = $akun->akun;
        $cart->no_reff = $req->no_reff;
        $cart->akuns_id = $req->akuns_id;
        $cart->{$req->jenis_saldo} = $req->saldo;
        $cart->users_id = auth()->user()->id;
        $cart->save();
        return response()->json($cart);
    }

    public function del_cart(Request $req)
    {
        $cart = Cart::find($req->id)->delete();
        return $cart;
    }

    public function delAll_cart()
    {
        $cart = Cart::with('akun')
            ->where('users_id', auth()->user()->id)
            ->delete();
        return $cart;
    }
}
