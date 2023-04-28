<?php

namespace App\Http\Controllers;

use App\Akun;
use App\Barang;
use App\DetailPembayaran;
use App\JenisJasa;
use App\Pelanggan;
use App\Pembayaran;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Ajax;
use App\Jurnal;

class PembayaranController extends Controller
{
    public function index(Request $req)
    {
        if ($req->ajax()) {
            $data = Pembayaran::with('users', 'service.pelanggan', 'detail')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    // $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn text-success btn-sm px-1" id="edit"><i class="fas fa-edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn text-danger btn-sm px-1" id="delete"><i class="fas fa-trash"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn text-info btn-sm px-1" id="detail"><i class="fas fa-eye"></i></a>';
                    return $btn;
                })
                ->addColumn('tgl_bayar', function ($row) {
                    return date('d-m-Y', strtotime($row->tgl_bayar));
                })
                ->addColumn('total_tagihan', function ($row) {
                    $total = 0;
                    foreach ($row->detail as $key => $d) {
                        $total += $d->harga;
                    }
                    return "Rp " . number_format($total, 0, ',', '.');
                })
                ->rawColumns(['action', 'tgl_bayar', 'total_tagihan'])
                ->make(true);
        }

        $data = [
            'title' => 'Transaksi Pembayaran',
            'brg' => Barang::all()
        ];
        return view('transaksi.pembayaran.index', $data);
    }

    public function add(Request $req)
    {
        $data = [
            'title' => 'Tambah Pembayaran',
            'customers' => Pelanggan::all(),
            'service' => Service::with('pelanggan', 'pembayaran')
                ->doesntHave('pembayaran')
                ->get(),
            'brg' => Barang::orderBy('nama', 'asc')->get(),
            'jasa' => JenisJasa::all(),
        ];
        return view('transaksi.pembayaran.add', $data);
    }


    public function listCart(Request $req)
    {
        $html = '';
        $status = false;
        $item_barang = 0;
        $item_jasa = 0;
        $subtotal = 0;
        if (session('pembayaran')) {
            $no = 1;
            foreach (session('pembayaran') as $id => $item) {
                $html .= '<tr>';
                $html .= '<td>' . $no . '</td>';
                $html .= '<td>' . $item['item'] . '</td>';
                $html .= '<td>Rp ' . number_format($item['harga'], 0, ',', '.') . '</td>';
                $html .= '<td>' . $item['qty'] . '</td>';
                $html .= '<td>Rp ' . number_format($item['harga'] * $item['qty'], 0, ',', '.') . '</td>';
                $html .= '<td class="text-right">
                <a href="javascript:void(0)" data-id="' . $id . '" data-cart="pembayaran" class="btn btn-danger btn-sm m-1" id="delete"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </td>';
                $html .= '</tr>';
                $no++;
                $subtotal += ($item['harga'] * $item['qty']);

                if ($item['kategori'] == "barang") {
                    $item_barang++;
                } else {
                    $item_jasa++;
                }
            }
            $html .= '<tr>';
            $html .= '<td colspan="3"></td>';
            $html .= '<td><b>SubTotal</b></td>';
            $html .= '<td><b>Rp ' . number_format($subtotal, 0, ',', '.') . '</b></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
            $status = true;
        } else {
            $html .= '<td colspan="7" class="text-center">Belum ada data</td>';
            $status = false;
        }
        return response()->json(
            [
                'html' => $html,
                'status' => $status,
                'uang_muka' => empty(session('uang_muka')) ? 0 : number_format(session('uang_muka'), 0, ',', '.'),
                'total_barang' => $item_barang,
                'total_jasa' => $item_jasa,
                'total_tagihan' => number_format($subtotal, 0, ',', '.'),
                'sisa_tagihan' => number_format($subtotal - session('uang_muka'), 0, ',', '.'),
            ]
        );
    }

    public function addToCart(Request $req)
    {
        try {
            $id = $req->jasa_barang;
            $cek_barang = Barang::where('id', $id)->exists();
            if ($cek_barang) {
                $kategori = 'barang';
                $product = Barang::findOrFail($id);
            } else {
                $kategori = 'jasa';
                $product = JenisJasa::findOrFail($id);
            }

            if (($product->jml - $req->qty) < 0) {
                return response()->json(['status' => false, 'message' => 'Stok barang tidak cukup!']);
            } else {
                $cart = session()->get('pembayaran', []);
                if (isset($cart[$id])) {
                    $cart[$id]["qty"] += $req->qty;
                    $this->upStok($id, $req->qty, '-');
                } else {
                    // insert cart
                    $cart[$id] = [
                        "id" => $product->id,
                        "item" => $product->nama,
                        "kategori" => $kategori,
                        "qty" => $req->qty,
                        "harga" => $product->harga,
                    ];
                    // kurangi stok langsung
                    $this->upStok($product->id, $req->qty, '-');
                }
                session()->put('pembayaran', $cart);
                return response()->json(['status' => true, 'message' => 'success']);
            }
        } catch (\Exception $er) {
            return response()->json(['status' => false, 'message' => $er->getMessage()]);
        }
    }

    public function setUangMuka(Request $req)
    {
        $q = Service::find($req->id);
        session()->put('services_id', $req->id);
        session()->put('uang_muka', $q->uang_muka);
        return true;
    }

    public function removeAll($finish = false)
    {
        // kembalikan semua qty
        foreach (session('pembayaran') as $id => $item) {
            $this->upStok($id, $item['qty'], '+');
        }
        Session::forget('pembayaran');
        Session::forget('uang_muka');
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
                foreach ((array) session('pembayaran') as $id => $details) {
                    $subtotal += ($details['harga'] * $details['qty']);
                }

                // get kode
                $ajx = new Ajax();
                $kode = $ajx->kode('pembayaran');

                // insert
                $p = new Pembayaran;
                $p->tgl_bayar = $req->tgl_bayar;
                $p->kode = $kode;
                $p->services_id = session('services_id');
                $p->users_id = auth()->user()->id;
                $p->save();
                $last_id = $p->id;

                $subtotal = 0;
                // insert detail
                foreach ((array) session('pembayaran') as $id => $details) {
                    if ($details['kategori'] == "barang") {
                        $this->upStok($id, $details['qty'], '-');
                    }
                    $detail = new DetailPembayaran;
                    $detail->pembayarans_id = $last_id;
                    $detail->jasa_barang_id = $id;
                    $detail->kategori = $details['kategori'];
                    $detail->qty = $details['qty'];
                    $detail->harga = $details['harga'];
                    $detail->save();

                    $subtotal += ($details['harga'] * $details['qty']);
                }

                $sisa_tagihan = $subtotal - session('uang_muka');

                // 1. Rumus kalo DP 0 :

                // Kalo udah dibyr:

                // Pendapatan -dbit
                // Piutang-kredit

                // 2. Rumus kalo DP ada :

                // Klo udh dbyr:

                // Kas-debit
                // Piutang-kredit

                $akun_kas = '37ef5388-1bb4-40fb-95e2-45b5e1d22939';
                $akun_piutang = '033ac065-9757-4b8a-bfa1-67d643e9b439';
                $akun_pendapatan = 'eddd5391-88fc-47a7-88c4-7ff032cc1730';

                $d_service = Service::find(session('services_id'));

                // insert jurnal
                for ($i = 0; $i < 2; $i++) {
                    $type = '';
                    $total = 0;
                    if ($i == 0) {
                        if ($d_service->uang_muka == 0) {
                            $akun_d = Akun::find($akun_pendapatan);
                        } else {
                            $akun_d = Akun::find($akun_kas);
                        }
                        $type = 'debet';
                        $total = $sisa_tagihan;
                    } elseif ($i == 1) {
                        $akun_d = Akun::find($akun_piutang);
                        $type = 'kredit';
                        $total = $sisa_tagihan;
                    }
                    $jurnal = new Jurnal;
                    $jurnal->no_reff = $akun_d->no_reff;
                    $jurnal->{$type} = $total;
                    $jurnal->tanggal = $req->tanggal;
                    $jurnal->akuns_id = $akun_d->id;
                    $jurnal->id_transaksi = $kode;
                    $jurnal->keterangan = 'pembayaran';
                    $jurnal->akuns_id = $akun_d->id;
                    $jurnal->users_id = auth()->user()->id;
                    if ($i > 0) {
                        $jurnal->created_at = date('Y-m-d H:i:s', strtotime('+' . $i . ' sec'));
                    }
                    $jurnal->save();

                    $this->updateSaldoAkun($akun_d->id, $total, $type);
                }

                // set status service close
                $up_service = Service::find(session('services_id'));
                $up_service->status = 'close';
                $up_service->save();

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
        $cek_barang = Barang::where('id', $id)->exists();
        if ($cek_barang) {
            $b = Barang::find($id);
            if ($act == '+') {
                $b->jml = $b->jml + $qty;
            } else {
                $b->jml = $b->jml - $qty;
            }
            $b->save();
        } else {
            $b = JenisJasa::find($id);
        }
        return true;
    }

    public function delete(Request $req)
    {
        $q = Pembayaran::with('detail')->where('id', $req->id)->get();
        // kembalikan stok barang
        foreach ($q as $v) {
            foreach ($v->detail as $d) {
                if ($d->kategori == "barang") {
                    $this->upStok($d->jasa_barang_id, $d->qty, '+');
                }
            }
        }

        // delete jurnal & balikan saldo
        $p = Pembayaran::find($req->id);
        $j = Jurnal::where('id_transaksi', $p->kode)->get();
        foreach ($j as $key => $v) {
            $jenis = ($v->debet == 0) ? 'kredit' : 'debet';

            // balik saldo akun
            $nominal_jurnal = $v->{$jenis};
            $this->balikSaldo($v->akuns_id, $nominal_jurnal, $jenis);

            // delete jurnal
            $jurnal = Jurnal::find($v->id);
            $jurnal->delete();
        }

        // delete pembayaran
        $q = Pembayaran::where('id', $req->id)->delete();
        return response()->json($q);
    }

    function updateSaldoAkun($akun = '', $nominal = 0, $jenis = '')
    {
        $up = Akun::find($akun);
        $kategori_debet = ['Activa Tetap', 'Activa Lancar', 'Beban'];
        $kategori_kredit = ['Kewajiban', 'Modal', 'Pendapatan'];
        if (in_array($up->kategori, $kategori_debet)) {
            if ($jenis == "debet") {
                $up->saldo_awal += $nominal;
                $up->{$jenis} += $nominal;
            } else {
                $up->saldo_awal -= $nominal;
                $up->{$jenis} += $nominal;
            }
        } else {
            // kebalikan 
            if ($jenis == "debet") {
                $up->saldo_awal -= $nominal;
                $up->{$jenis} += $nominal;
            } else {
                $up->saldo_awal += $nominal;
                $up->{$jenis} += $nominal;
            }
        }

        $up->save();
    }

    function balikSaldo($akun = '', $nominal = 0, $jenis = '')
    {
        $up = Akun::find($akun);
        $kategori_debet = ['Activa Tetap', 'Activa Lancar', 'Harga Pokok Penjualan', 'Beban'];
        $kategori_kredit = ['Kewajiban', 'Modal', 'Pendapatan'];
        if (in_array($up->kategori, $kategori_debet)) {
            if ($jenis == "debet") {
                $up->saldo_awal -= $nominal;
                $up->{$jenis} -= $nominal;
            } else {
                $up->saldo_awal += $nominal;
                $up->{$jenis} -= $nominal;
            }
        } else {
            // kebalikan 
            if ($jenis == "debet") {
                $up->saldo_awal += $nominal;
                $up->{$jenis} -= $nominal;
            } else {
                $up->saldo_awal -= $nominal;
                $up->{$jenis} -= $nominal;
            }
        }

        $up->save();
    }

    public function edit(Request $req)
    {
        $q = Pembayaran::with('users', 'service.pelanggan', 'service.teknisi', 'detail.barang', 'detail.jenisjasa')
            // ->whereHas('service.pelanggan', function ($query) {
            //     $query->select(DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal_service'));
            // })
            ->select(
                "*",
                DB::raw('DATE_FORMAT(pembayarans.created_at, "%d-%m-%Y") as tanggal_format')
            )
            ->where('id', $req->id)
            ->first();
        return response()->json($q);
    }
}
