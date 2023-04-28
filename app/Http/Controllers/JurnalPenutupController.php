<?php

namespace App\Http\Controllers;

use App\Akun;
use App\Cart;
use App\Jurnal;
use App\TutupBuku;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class JurnalPenutupController extends Controller
{
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

    function getAkun(Request $req)
    {
        return Akun::find($req->id);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TutupBuku::with('users', 'akun')
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
            'title' => 'Jurnap Tutup Buku',
            'akun' => Akun::all(),
            'tahun' => Jurnal::select(
                "id",
                DB::raw("(DATE_FORMAT(tanggal, '%Y')) as tahun")
            )
                ->orderBy('tanggal')
                ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y')"))
                ->get()
        ];
        $data['bulan'] = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
            "September", "Oktober", "November", "Desember"
        ];
        return view('jurnal.penutup', $data);
    }

    public function insert(Request $req)
    {
        try {
            $bulan = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
                "September", "Oktober", "November", "Desember"
            ];
            // Mengalami laba 
            // Laba rugi D
            // Laba ditahan K

            // Mengalami rugi
            // Laba ditahan D
            // Laba ruugi K

            $id_labarugi = 'b7f2e10b-dc07-44ee-9049-52cd7cb354f0';
            $id_labaditahan = '5c692233-77ec-4426-ac12-110fa428f1ce';

            $total_labarugi = $this->laba_rugi_custom($req->bulan_awal, $req->bulan_akhir, $req->tahun);
            $kode = Str::random(6);
            $keterangan = 'Tutup Buku periode ' . $bulan[$req->bulan_awal - 1] . ' s/d ' . $bulan[$req->bulan_akhir - 1] . ' tahun ' . $req->tahun;
            $cek = TutupBuku::where('keterangan', $keterangan)->get()->count() >= 1;
            if ($cek) {
                return response()->json(['status' => false, 'message' => '<h4 class="font-weight-bold">' . $keterangan . ' sudah dilakukan sebelumnya!</h4>']);
                return false;
            }
            // laba
            if ($total_labarugi > 0) {
                for ($i = 0; $i < 2; $i++) {
                    $type = '';
                    if ($i == 0) {
                        $akun_d = Akun::find($id_labarugi);
                        $type = 'debet';
                    } elseif ($i == 1) {
                        $akun_d = Akun::find($id_labaditahan);
                        $type = 'kredit';
                    }

                    $jurnal = new TutupBuku;
                    $jurnal->no_reff = $akun_d->no_reff;
                    $jurnal->{$type} = $total_labarugi;
                    $jurnal->tanggal = date('Y-m-d');
                    $jurnal->akuns_id = $akun_d->id;
                    $jurnal->id_transaksi = $kode;
                    $jurnal->keterangan = $keterangan;
                    $jurnal->akuns_id = $akun_d->id;
                    $jurnal->users_id = auth()->user()->id;
                    if ($i > 0) {
                        $jurnal->created_at = date('Y-m-d H:i:s', strtotime('+' . $i . ' sec'));
                    }
                    $jurnal->save();

                    $this->updateSaldoAkun($akun_d->id, $total_labarugi, $type);
                }
            }
            // rigi
            else {
                for ($i = 0; $i < 2; $i++) {
                    $type = '';
                    if ($i == 0) {
                        $akun_d = Akun::find($id_labaditahan);
                        $type = 'debet';
                    } elseif ($i == 1) {
                        $akun_d = Akun::find($id_labarugi);
                        $type = 'kredit';
                    }

                    $jurnal = new TutupBuku;
                    $jurnal->no_reff = $akun_d->no_reff;
                    $jurnal->{$type} = $total_labarugi;
                    $jurnal->tanggal = date('Y-m-d');
                    $jurnal->akuns_id = $akun_d->id;
                    $jurnal->id_transaksi = $kode;
                    $jurnal->keterangan = $keterangan;
                    $jurnal->akuns_id = $akun_d->id;
                    $jurnal->users_id = auth()->user()->id;
                    if ($i > 0) {
                        $jurnal->created_at = date('Y-m-d H:i:s', strtotime('+' . $i . ' sec'));
                    }
                    $jurnal->save();

                    $this->updateSaldoAkun($akun_d->id, $total_labarugi, $type);
                }
            }

            return response()->json(['status' => true, 'message' => '<h4 class="font-weight-bold">Tersimpan <br>' . $keterangan . '</h4>']);
        } catch (\Exception $err) {
            return response()->json(['status' => false, 'message' => $err->getMessage()]);
        }
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

    function laba_rugi_custom($bulan1, $bulan2, $tahun)
    {
        $query1 = Jurnal::with('akun')->whereHas('akun', function ($q) {
            $q->where('kategori', 'Pendapatan');
        })
            ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
            ->whereMonth('tanggal', '>=', $bulan1)
            ->whereMonth('tanggal', '<=', $bulan2)
            ->whereYear('tanggal', '=', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $query2 = Jurnal::with('akun')->whereHas('akun', function ($q) {
            $q->where('kategori', 'Harga Pokok Penjualan');
        })
            ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
            ->whereMonth('tanggal', '>=', $bulan1)
            ->whereMonth('tanggal', '<=', $bulan2)
            ->whereYear('tanggal', '=', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $query3 = Jurnal::with('akun')->whereHas('akun', function ($q) {
            $q->where('kategori', 'Beban');
        })
            ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
            ->whereMonth('tanggal', '>=', $bulan1)
            ->whereMonth('tanggal', '<=', $bulan2)
            ->whereYear('tanggal', '=', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $p_total = 0;
        foreach ($query1 as $i => $v) {
            $p_total += $v->debet == 0 ? $v->kredit : $v->debet;
        }

        $hpp_total = 0;
        foreach ($query2 as $i => $v) {
            $hpp_total += $v->debet == 0 ? $v->kredit : $v->debet;
        }

        $beban_total = 0;
        foreach ($query3 as $i => $v) {
            $beban_total += $v->debet == 0 ? $v->kredit : $v->debet;
        }

        return $p_total - ($hpp_total + $beban_total);
    }
}
