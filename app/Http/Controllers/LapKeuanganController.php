<?php

namespace App\Http\Controllers;

use App\Pembelian;
use App\Penjualan;
use App\Jurnal;
use App\Akun;
use App\Exports\JurnalExport;
use App\Pemasukan;
use App\Pengeluaran;
use App\Saldo_awal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Illuminate\Support\Facades\DB;


class LapKeuanganController extends Controller
{
    function strposa(string $haystack, array $needles, int $offset = 0): bool
    {
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle, $offset) !== false) {
                return true; // stop on first true result
            }
        }

        return false;
    }

    public function index($jenis = "")
    {
        $data['jenis'] = $jenis;
        if ($jenis == "pemasukan") {
            $data = [
                'title' => 'Laporan ' . ucwords($jenis),
                'tahun' => Pemasukan::select(
                    "id",
                    DB::raw("(DATE_FORMAT(tanggal, '%Y')) as tahun")
                )
                    ->orderBy('tanggal')
                    ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y')"))
                    ->get()
            ];
        } elseif ($jenis == "pengeluaran") {
            $data = [
                'title' => 'Laporan ' . ucwords($jenis),
                'tahun' => Pengeluaran::select(
                    "id",
                    DB::raw("(DATE_FORMAT(tanggal, '%Y')) as tahun")
                )
                    ->orderBy('tanggal')
                    ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y')"))
                    ->get()
            ];
        } elseif ($jenis == "jurnal") {
            $data = [
                'title' => '' . ucwords($jenis) . " Umum",
                'akun' => Akun::all(),
                'tahun' => Jurnal::select(
                    "id",
                    DB::raw("(DATE_FORMAT(tanggal, '%Y')) as tahun")
                )
                    ->orderBy('tanggal')
                    ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y')"))
                    ->get()
            ];
        } elseif ($jenis == "buku_besar") {
            $data = [
                'title' => 'Laporan ' . ucwords(str_replace("_", " ", $jenis)),
                'akun' => Akun::all(),
                'periode' => Jurnal::select(
                    "id",
                    DB::raw("(DATE_FORMAT(tanggal, '%m')) as bulan,(DATE_FORMAT(tanggal, '%Y')) as tahun")
                )
                    ->orderBy('tanggal')
                    ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y')"))
                    ->get(),
                'tahun' => Jurnal::select(
                    "id",
                    DB::raw("(DATE_FORMAT(tanggal, '%Y')) as tahun")
                )
                    ->orderBy('tanggal')
                    ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y')"))
                    ->get()
            ];
        } elseif ($jenis == "neraca_saldo") {
            $data = [
                'title' => 'Laporan ' . ucwords(str_replace("_", " ", $jenis)),
                'akun' => Akun::all(),
                'periode' => Jurnal::selectRaw("
                    tanggal,
                    YEAR(tanggal) AS tahun, 
                    MONTH(tanggal) AS bulan
                ")->groupBy(DB::raw('MONTH("tanggal"), YEAR("tanggal")'))->get(),
                'tahun' => Jurnal::select(
                    "id",
                    DB::raw("(DATE_FORMAT(tanggal, '%Y')) as tahun")
                )
                    ->orderBy('tanggal')
                    ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y')"))
                    ->get()
            ];
        } elseif ($jenis == "neraca") {
            $data = [
                'title' => 'Laporan ' . ucwords(str_replace("_", " ", $jenis)),
                'akun' => Akun::all(),
                'periode' => Jurnal::selectRaw("
                    tanggal,
                    YEAR(tanggal) AS tahun, 
                    MONTH(tanggal) AS bulan
                ")->groupBy(DB::raw('MONTH("tanggal"), YEAR("tanggal")'))->get(),
                'tahun' => Jurnal::select(
                    "id",
                    DB::raw("(DATE_FORMAT(tanggal, '%Y')) as tahun")
                )
                    ->orderBy('tanggal')
                    ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y')"))
                    ->get()
            ];
        } elseif ($jenis == "labarugi") {
            $data = [
                'title' => 'Laporan ' . ucwords($jenis),
                'tahun' => Jurnal::select(
                    "id",
                    DB::raw("(DATE_FORMAT(tanggal, '%Y')) as tahun")
                )
                    ->orderBy('tanggal')
                    ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y')"))
                    ->get()
            ];
        } elseif ($jenis == "perubahan_modal") {
            $data = [
                'title' => 'Laporan ' . ucwords($jenis),
                'tahun' => Jurnal::select(
                    "id",
                    DB::raw("(DATE_FORMAT(tanggal, '%Y')) as tahun")
                )
                    ->orderBy('tanggal')
                    ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y')"))
                    ->get()
            ];
        } elseif ($jenis == "arus_kas") {
            $data = [
                'title' => 'Laporan ' . ucwords(str_replace("_", " ", $jenis)),
                'tahun' => Jurnal::select(
                    "id",
                    DB::raw("(DATE_FORMAT(tanggal, '%Y')) as tahun")
                )
                    ->orderBy('tanggal')
                    ->groupBy(DB::raw("DATE_FORMAT(tanggal, '%Y')"))
                    ->get()
            ];
        } elseif ($jenis == "jurnal_penutup") {
            $data = [
                'title' => 'Laporan ' . ucwords(str_replace("_", " ", $jenis)),
                'akun' => Akun::all(),
                'periode' => Jurnal::selectRaw("
                    tanggal,
                    YEAR(tanggal) AS tahun, 
                    MONTH(tanggal) AS bulan
                ")->groupBy(DB::raw('MONTH("tanggal"), YEAR("tanggal")'))->get()
            ];
        }
        $data['bulan'] = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
            "September", "Oktober", "November", "Desember"
        ];
        $data['jenis'] = $jenis;
        return view('laporan_keuangan.' . $jenis, $data);

        return abort(404);
    }

    public function getLaporan(Request $req)
    {
        if ($req->jenis == "jurnal") {
            $query = Jurnal::with('akun')
                ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereMonth('tanggal', '>=', $req->bulan_awal)
                ->whereMonth('tanggal', '<=', $req->bulan_akhir)
                ->whereYear('tanggal', '=', $req->tahun)
                ->orderBy('created_at', 'asc')
                ->get();
            $data = [
                'query' => $query,
                'jenis' => $req->jenis
            ];
        } else if ($req->jenis == "pemasukan") {

            $query = Pemasukan::with("akuns", "users")
                ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereMonth('tanggal', '>=', $req->bulan_awal)
                ->whereMonth('tanggal', '<=', $req->bulan_akhir)
                ->whereYear('tanggal', '=', $req->tahun)
                ->orderBy('tanggal', 'desc')
                ->get();

            $data = [
                'query' => $query,
                'jenis' => $req->jenis
            ];
        } else if ($req->jenis == "pengeluaran") {
            $query = Pengeluaran::with("akuns", "users")
                ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereMonth('tanggal', '>=', $req->bulan_awal)
                ->whereMonth('tanggal', '<=', $req->bulan_akhir)
                ->whereYear('tanggal', '=', $req->tahun)
                ->orderBy('tanggal', 'desc')
                ->get();

            $data = [
                'query' => $query,
                'jenis' => $req->jenis
            ];
        } else if ($req->jenis == "buku_besar") {
            $b = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
                "September", "Oktober", "November", "Desember"
            ];

            $kategori = ['Activa Lancar', 'Activa Tetap', 'Kewajiban', 'Modal', 'Pendapatan', 'Harga Pokok Penjualan', 'Beban'];
            $akn = Akun::orderBy('no_reff', 'asc')->get();
            $arr_id_akun = [];
            foreach ($akn as $key => $v) {
                $arr_id_akun[] = $v->id;
            }

            // get akun
            $akun = Jurnal::with('akun')
                ->whereIn('akuns_id', $arr_id_akun)
                ->whereMonth('tanggal', '>=', $req->bulan_awal)
                ->whereMonth('tanggal', '<=', $req->bulan_akhir)
                ->whereYear('tanggal', '=', $req->tahun)
                ->orderBy('no_reff', 'asc')
                ->groupBy('akuns_id')->get();

            $arr_akun = [];
            foreach ($akun as $a) {
                $arr_akun[] = [
                    'id' => $a->akun->id,
                    'no_reff' => $a->akun->no_reff,
                    'kategori' => $a->akun->kategori,
                    'akun' => $a->akun->akun,
                ];
            }

            // dd($arr_akun);

            // get jurnal by akun 
            $html = '<h4 class="text-center">
            <strong>' . strtoupper(config('app.name')) . '</strong></h4>';
            $html .= '<h5 class="text-center pb-2">Laporan Buku Besar</h5>
            <h6 class="text-center pb-2">Periode ' . $b[$req->bulan_awal - 1] . ' - ' . $b[$req->bulan_akhir - 1] . ' ' . $req->tahun . '</h6>';
            foreach ($arr_akun as $item) {
                $html .= '<h5 class="font-weight-bold">' . $item['akun'] . '</h5>';
                $html .= '<table class="mb-5" id="table1">
                <thead>
                    <tr class="text-center">
                        <th rowspan="2" width="100">Tanggal</th>
                        <th rowspan="2" width="200">Keterangan</th>
                        <th rowspan="2" width="150">Debit</th>
                        <th rowspan="2" width="150">Kredit</th>
                        <th colspan="2">Saldo</th>
                    </tr>
                    <tr class="text-center">
                        <th>Debit</th>
                        <th>Kredit</th>
                    </tr>
                </thead>
                <tbody>';

                // get jurnal
                $jurnal = Jurnal::with('akun')->where('akuns_id', $item['id'])->orderBy('created_at', 'asc')->get();
                $saldo = 0;
                $row = [];
                $jenis = '';
                $jml = 0;
                foreach ($jurnal as $no => $j) {
                    // simpan array
                    $row[] = $j->debet == 0 ? 'kredit' : 'debet';
                    // debet / kredit
                    $jenis = $j->debet == 0 ? 'kredit' : 'debet';

                    // jika array pertama maka tambah
                    if ($no == 0) {
                        if ($j->debet == 0) {
                            $saldo += $j->kredit;
                        } else {
                            $saldo += $j->debet;
                        }
                    } else {
                        if (in_array($item['kategori'], ['Activa Lancar', 'Activa Tetap', 'Harga Pokok Penjualan', 'Beban'])) {
                            if ($row[$no - 1] == "debet") {
                                if ($j->debet == 0) {
                                    $saldo -= ($j->debet == 0 ? $j->kredit : $j->debet);
                                } else {
                                    $saldo += ($j->debet == 0 ? $j->kredit : $j->debet);
                                }
                            } else {
                                if ($j->debet == 0) {
                                    $saldo -= ($j->debet == 0 ? $j->kredit : $j->debet);
                                } else {
                                    $saldo += ($j->debet == 0 ? $j->kredit : $j->debet);
                                }
                            }
                        } else {
                            if ($row[$no - 1] == "debet") {
                                if ($j->debet == 0) {
                                    $saldo += ($j->debet == 0 ? $j->kredit : $j->debet);
                                } else {
                                    $saldo -= ($j->debet == 0 ? $j->kredit : $j->debet);
                                }
                            } else {
                                if ($j->debet == 0) {
                                    $saldo += ($j->debet == 0 ? $j->kredit : $j->debet);
                                } else {
                                    $saldo -= ($j->debet == 0 ? $j->kredit : $j->debet);
                                }
                            }
                        }
                    }


                    // $debet = $saldo > 0 ? 'Rp. ' . str_replace('', '', number_format($saldo, 0, ',', '.')) : '-';
                    // $kredit = $saldo < 0 ? 'Rp. ' . str_replace('', '', number_format($saldo, 0, ',', '.')) : '-';


                    $html .= '<tr>';
                    $html .= '<td>' . $j->tanggal . '</td>';
                    $html .= '<td>' . $j->keterangan . '</td>';
                    $html .= '<td class="text-left">Rp. ' . number_format($j->debet, 0, ',', '.') . '</td>';
                    $html .= '<td class="text-left">Rp. ' . number_format($j->kredit, 0, ',', '.') . '</td>';
                    if (in_array($item['kategori'], ['Activa Lancar', 'Activa Tetap', 'Harga Pokok Penjualan', 'Beban'])) {
                        $html .= '<td class="text-left">' . 'Rp. ' . number_format($saldo, 0, ',', '.') . '</td>';
                        $html .= '<td class="text-left">-</td>';
                    } else {
                        $html .= '<td class="text-left">-</td>';
                        $html .= '<td class="text-left">' . 'Rp. ' . number_format($saldo, 0, ',', '.') . '</td>';
                    }
                    $html .= '</tr>';

                    $jml++;
                }

                if ($saldo < 0 && $jml > 0) {
                    $color = 'text-danger font-weight-bold';
                }

                if ($saldo > 0 && $jml == 1) {
                    $color = 'text-danger font-weight-bold';
                }

                if ($saldo > 0 && $jml > 0) {
                    $color = 'text-success font-weight-bold';
                }

                $html .= '<tr>';
                $html .= '<td colspan="4">Total</td>';
                if (in_array($item['kategori'], ['Activa Lancar', 'Activa Tetap', 'Harga Pokok Penjualan', 'Beban'])) {
                    $html .= '<td class="text-left"><span class="' . $color . '">Rp. ' . str_replace('', '', number_format($saldo, 0, ',', '.')) . '</span></td>';
                    $html .= '<td class="text-left">-</td>';
                } else {
                    $html .= '<td class="text-left">-</td>';
                    $html .= '<td class="text-left"><span class="' . $color . '">Rp. ' . str_replace('', '', number_format($saldo, 0, ',', '.')) . '</span></td>';
                }
                $html .= '</tr>';
                $html .= '</tbody>
            </table>';
            }

            $data = [
                'html' => $html
            ];
        } else if ($req->jenis == "neraca_saldo") {
            $b = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
                "September", "Oktober", "November", "Desember"
            ];

            // get akun
            $jurnal = Jurnal::with('akun')
                ->whereMonth('tanggal', '>=', $req->bulan_awal)
                ->whereMonth('tanggal', '<=', $req->bulan_akhir)
                ->whereYear('tanggal', '=', $req->tahun)
                ->groupBy('akuns_id')->get();
            $arr_akun = [];
            foreach ($jurnal as $a) {
                $arr_akun[] = [
                    'id' => $a->akun->id,
                    'kategori' => $a->akun->kategori,
                    'akun' => $a->akun->akun,
                ];
            }

            // get jurnal by akun 
            $html = '<h4 class="text-center">
            <strong>' . strtoupper(config('app.name')) . '</strong></h4>';
            $html .= '<h5 class="text-center pb-2">Laporan Neraca Saldo</h5>
            <h6 class="text-center pb-2">Periode ' . $b[$req->bulan_awal - 1] . ' - ' . $b[$req->bulan_akhir - 1] . ' ' . $req->tahun . '</h6>';
            $html .= '<table id="table1">
            <thead>
                <tr class="text-center">
                    <th>No. Akun</th>
                    <th>Nama Akun</th>
                    <th>Debit</th>
                    <th>Kredit</th>
                </tr>
            </thead>
            <tbody>';

            $kategori = ['Activa Lancar', 'Activa Tetap', 'Kewajiban', 'Modal', 'Pendapatan', 'Harga Pokok Penjualan', 'Beban'];
            $debet = 0;
            $kredit = 0;
            foreach ($kategori as $k) {
                $akun = Akun::where('kategori', $k)
                    ->orderBy('no_reff', 'asc')
                    ->get();
                foreach ($akun as $item) {
                    $html .= '<tr>';
                    $html .= '<td class="text-left">' . $item->no_reff . '</td>';
                    $html .= '<td class="text-left">' . $item->akun . '</td>';
                    if (in_array($item->kategori, ['Activa Lancar', 'Activa Tetap', 'Harga Pokok Penjualan', 'Beban']) && $item->id != "f1cd888a-84f7-4318-9ec9-6b989c4a7ab3") {
                        $html .= '<td class="text-right">Rp. ' . str_replace('', '', number_format($this->get_buku_besar($item->akun, $req->bulan_awal, $req->bulan_akhir, $req->tahun), 0, ',', '.')) . '</td>';
                        $html .= '<td class="text-right">-</td>';
                        $debet += $this->get_buku_besar($item->akun, $req->bulan_awal, $req->bulan_akhir, $req->tahun);
                    } else {
                        if ($item->id == "f30ecc37-5681-491c-be4e-7840438f1e80") {
                            $html .= '<td class="text-right">Rp. ' . str_replace('', '', number_format($this->get_buku_besar($item->akun, $req->bulan_awal, $req->bulan_akhir, $req->tahun), 0, ',', '.')) . '</td>';
                            $html .= '<td class="text-right">-</td>';
                            $debet += $this->get_buku_besar($item->akun, $req->bulan_awal, $req->bulan_akhir, $req->tahun);
                        } else {
                            $html .= '<td class="text-right">-</td>';
                            $html .= '<td class="text-right">Rp. ' . str_replace('', '', number_format($this->get_buku_besar($item->akun, $req->bulan_awal, $req->bulan_akhir, $req->tahun), 0, ',', '.')) . '</td>';
                            $kredit += $this->get_buku_besar($item->akun, $req->bulan_awal, $req->bulan_akhir, $req->tahun);
                        }
                    }
                    $html .= '</tr>';
                }
            }
            $html .= '<tr>';
            $html .= '<td class="text-center font-weight-bold h5" colspan="2">Total</td>';
            $html .= '<td class="text-center font-weight-bold h5">Rp. ' . str_replace('', '', number_format($debet, 0, ',', '.')) . '</td>';
            $html .= '<td class="text-center font-weight-bold h5">Rp. ' . str_replace('', '', number_format($kredit, 0, ',', '.')) . '</td>';
            $html .= '</tr>';

            // cek balance
            if (
                str_replace('-', '', number_format($debet, 0, ',', '.')) ==
                str_replace('-', '', number_format($kredit, 0, ',', '.'))
            ) {
                $color = 'bg-success';
                $text = 'SEIMBANG';
            } else {
                $color = 'bg-danger';
                $text = 'TIDAK SEIMBANG';
            }

            $html .= '<tr>';
            $html .= '<td colspan="4" class="text-center font-weight-bold h5 ' . $color . '">' . $text . '</td>';
            $html .= '</tr>';
            $html .= '<tbody></table>';

            $data = [
                'html' => $html
            ];
        } else if ($req->jenis == "neraca") {
            $b = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
                "September", "Oktober", "November", "Desember"
            ];

            // get akun
            $jurnal = Jurnal::with("akun")
                ->whereMonth('tanggal', '>=', $req->bulan_awal)
                ->whereMonth('tanggal', '<=', $req->bulan_akhir)
                ->whereYear('tanggal', '=', $req->tahun)
                ->groupBy('akuns_id')->get();
            $arr_akun = [];
            foreach ($jurnal as $a) {
                $arr_akun[] = [
                    'id' => $a->akun->id,
                    'kategori' => $a->akun->kategori,
                    'akun' => $a->akun->akun,
                ];
            }

            // get jurnal by akun 
            $html = '<h4 class="text-center">
            <strong>' . strtoupper(config('app.name')) . '</strong></h4>';
            $html .= '<h5 class="text-center pb-2">Laporan Neraca</h5>
            <h6 class="text-center pb-2">Periode ' . $b[$req->bulan_awal - 1] . ' - ' . $b[$req->bulan_akhir - 1] . ' ' . $req->tahun . '</h6>';
            $html .= '<div class="d-flex"><table id="table1">
            <thead>
                <tr class="text-center">
                <th height="30">No. Akun</th>
                <th height="30">Nama Akun</th>
                <th height="30">Nominal</th>
                </tr>
            </thead>
            <tbody>';

            $kategori = ['Activa Lancar', 'Activa Tetap'];
            $notIn = ['2258fa19-9845-4f64-b217-44b5a58e8199', 'f30ecc37-5681-491c-be4e-7840438f1e80']; // hpp dan prive tidak masuk ke neraca
            $debet = 0;
            $tb1_count = 0;
            foreach ($kategori as $k) {
                $akun = Akun::where('kategori', $k)
                    ->whereNotIn('id', $notIn)
                    ->orderBy('no_reff', 'asc')
                    ->get();
                foreach ($akun as $item) {
                    $tb1_count++;
                    $html .= '<tr style="height:10px">';
                    $html .= '<td class="text-left">' . $item->no_reff . '</td>';
                    $html .= '<td class="text-left">' . $item->akun . '</td>';
                    if (in_array($item->kategori, ['Activa Lancar', 'Activa Tetap', 'Harga Pokok Penjualan', 'Beban'])) {
                        $html .= '<td class="text-right">Rp. ' . str_replace('', '', number_format($this->get_buku_besar($item->akun, $req->bulan_awal, $req->bulan_akhir, $req->tahun), 0, ',', '.')) . '</td>';
                        $debet += $this->get_buku_besar($item->akun, $req->bulan_awal, $req->bulan_akhir, $req->tahun);
                    }
                    $html .= '</tr>';
                }
            }

            $html .= '<tr>';
            $html .= '<td class="text-center font-weight-bold h5" colspan="2">Total</td>';
            $html .= '<td class="text-center font-weight-bold h5">Rp. ' . str_replace('', '', number_format($debet, 0, ',', '.')) . '</td>';
            $html .= '</tr>';

            $html .= '<tbody></table>';


            $html .= '<table id="table2">
            <thead>
                <tr class="text-center">
                <th height="30">No. Akun</th>
                <th height="30">Nama Akun</th>
                <th height="30">Nominal</th>
                </tr>
            </thead>
            <tbody>';

            $kategori = ['Kewajiban', 'Modal'];
            $notIn = ['2258fa19-9845-4f64-b217-44b5a58e8199', 'f30ecc37-5681-491c-be4e-7840438f1e80']; // hpp dan prive tidak masuk ke neraca
            $kredit = 0;
            $tb2_count = 0;
            foreach ($kategori as $k) {
                $akun = Akun::where('kategori', $k)
                    ->whereNotIn('id', $notIn)
                    ->orderBy('no_reff', 'asc')
                    ->get();
                foreach ($akun as $item) {
                    $tb2_count++;
                    $html .= '<tr style="height:10px">';
                    $html .= '<td class="text-left">' . $item->no_reff . '</td>';
                    $html .= '<td class="text-left">' . $item->akun . '</td>';
                    if (in_array($item->kategori, ['Kewajiban', 'Modal'])) {
                        $html .= '<td class="text-right">Rp. ' . str_replace('', '', number_format($this->get_buku_besar($item->akun, $req->bulan_awal, $req->bulan_akhir, $req->tahun), 0, ',', '.')) . '</td>';
                        $kredit += $this->get_buku_besar($item->akun, $req->bulan_awal, $req->bulan_akhir, $req->tahun);
                    }
                    $html .= '</tr>';
                }
            }

            for ($i = 0; $i < ($tb1_count - $tb2_count); $i++) {
                $html .= '<tr style="height:10px">';
                $html .= '<td class="text-center">&nbsp;</td>';
                $html .= '<td class="text-center">&nbsp;</td>';
                $html .= '<td class="text-center">&nbsp;</td>';
                $html .= '</tr>';
            }

            $html .= '<tr>';
            $html .= '<td class="text-center font-weight-bold h5" colspan="2">Total</td>';
            $html .= '<td class="text-center font-weight-bold h5">Rp. ' . str_replace('', '', number_format($kredit, 0, ',', '.')) . '</td>';
            $html .= '</tr>';

            // cek balance
            if (
                str_replace('', '', number_format($debet, 0, ',', '.')) ==
                str_replace('', '', number_format($kredit, 0, ',', '.'))
            ) {
                $color = 'bg-success';
                $text = 'SEIMBANG';
            } else {
                $color = 'bg-danger';
                $text = 'TIDAK SEIMBANG';
            }

            // $html .= '<tr>';
            // $html .= '<td colspan="6" class="text-center font-weight-bold h5 ' . $color . '">' . $text . '</td>';
            // $html .= '</tr>';
            $html .= '<tbody></table></div>';

            $data = [
                'html' => $html
            ];
        } else if ($req->jenis == "laba_rugi") {
            $query1 = Jurnal::with('akun')->whereHas('akun', function ($q) {
                $q->where('kategori', 'Pendapatan');
            })
                ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal, sum(debet) as debet, sum(kredit) as kredit'))
                ->whereMonth('tanggal', '>=', $req->bulan_awal)
                ->whereMonth('tanggal', '<=', $req->bulan_akhir)
                ->whereYear('tanggal', '=', $req->tahun)
                ->orderBy('tanggal', 'desc')
                ->groupBy('akuns_id')
                ->get();

            $query2 = Jurnal::with('akun')->whereHas('akun', function ($q) {
                $q->where('kategori', 'Harga Pokok Penjualan');
            })
                ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal, sum(debet) as debet, sum(kredit) as kredit'))
                ->whereMonth('tanggal', '>=', $req->bulan_awal)
                ->whereMonth('tanggal', '<=', $req->bulan_akhir)
                ->whereYear('tanggal', '=', $req->tahun)
                ->orderBy('tanggal', 'desc')
                ->groupBy('akuns_id')
                ->get();

            $query3 = Jurnal::with('akun')->whereHas('akun', function ($q) {
                $q->where('kategori', 'Beban');
            })
                ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal, sum(debet) as debet, sum(kredit) as kredit'))
                ->whereMonth('tanggal', '>=', $req->bulan_awal)
                ->whereMonth('tanggal', '<=', $req->bulan_akhir)
                ->whereYear('tanggal', '=', $req->tahun)
                ->orderBy('tanggal', 'desc')
                ->groupBy('akuns_id')
                ->get();

            $b = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
                "September", "Oktober", "November", "Desember"
            ];

            $html = '<h4 class="text-center">
                <strong>' . strtoupper(config('app.name')) . '</strong></h4>';
            $html .= '<h5 class="text-center pb-2">Laporan Laba Rugi</h5>
                <h6 class="text-center pb-2">Periode ' . $b[$req->bulan_awal - 1] . ' - ' . $b[$req->bulan_akhir - 1] . ' ' . $req->tahun . '</h6>';
            $html .= '<table id="table1">
                <thead>
                    <tr class="text-left">
                        <th>No. Akun</th>
                        <th>Nama Akun</th>
                        <th class="text-right">Nominal</th>
                        <th class="text-right">Nominal</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>';

            $akun_p = Akun::select('id', 'no_reff', 'akun')->where('kategori', 'Pendapatan')->get();
            $akun_b = Akun::select('id', 'no_reff', 'akun')->where('kategori', 'Beban')->get();

            $p_total = 0;
            foreach ($query1 as $i => $v) {
                $html .= '<tr>';
                $html .= '<td class="text-left">' . $v->akun->no_reff . '</td>';
                $html .= '<td class="text-left">' . $v->akun->akun . '</td>';
                $html .= '<td class="text-right">' . ($v->debet == 0 ? '' : 'Rp ' . number_format($v->debet, 0, ',', '.')) . '</td>';
                $html .= '<td class="text-right">' . ($v->kredit == 0 ? '' : 'Rp ' . number_format($v->kredit, 0, ',', '.')) . '</td>';
                $html .= '<td class="text-right"></td>';
                $html .= '</tr>';
                $p_total += $v->debet == 0 ? $v->kredit : $v->debet;
            }
            $html .= '<tr>';
            $html .= '<td colspan="2" class="font-weight-bold text-right">Total Pendapatan</td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($p_total, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $hpp_total = 0;
            foreach ($query2 as $i => $v) {
                $html .= '<tr>';
                $html .= '<td class="text-left">' . $v->akun->no_reff . '</td>';
                $html .= '<td class="text-left">' . $v->akun->akun . '</td>';
                $html .= '<td class="text-right">' . ($v->debet == 0 ? '' : 'Rp ' . number_format($v->debet, 0, ',', '.')) . '</td>';
                $html .= '<td class="text-right">' . ($v->kredit == 0 ? '' : 'Rp ' . number_format($v->kredit, 0, ',', '.')) . '</td>';
                $html .= '<td class="text-right"></td>';
                $html .= '</tr>';
                $hpp_total += $v->debet == 0 ? $v->kredit : $v->debet;
            }

            $beban_total = 0;
            foreach ($query3 as $i => $v) {
                $html .= '<tr>';
                $html .= '<td class="text-left">' . $v->akun->no_reff . '</td>';
                $html .= '<td class="text-left">' . $v->akun->akun . '</td>';
                $html .= '<td class="text-right">' . ($v->debet == 0 ? '' : 'Rp ' . number_format($v->debet, 0, ',', '.')) . '</td>';
                $html .= '<td class="text-right">' . ($v->kredit == 0 ? '' : 'Rp ' . number_format($v->kredit, 0, ',', '.')) . '</td>';
                $html .= '<td class="text-right"></td>';
                $html .= '</tr>';
                $beban_total += $v->debet == 0 ? $v->kredit : $v->debet;
            }
            $html .= '<tr>';
            $html .= '<td colspan="2" class="font-weight-bold text-right">Total Biaya</td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($hpp_total + $beban_total, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td>&nbsp;</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="2" class="font-weight-bold text-right">Total Laba Rugi</td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($p_total - ($hpp_total + $beban_total), 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $html .= '</tbody>';
            $html .= '</table>';
            $data = [
                'html' => $html,
            ];
        } else if ($req->jenis == "perubahan_modal") {

            $b = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
                "September", "Oktober", "November", "Desember"
            ];

            $prive_id = 'f30ecc37-5681-491c-be4e-7840438f1e80';
            $modal_id = '367db022-ed50-4671-9e6b-ca3efc3ea78b';
            $akun_prive = Akun::find($prive_id);
            $prive = $this->get_buku_besar($akun_prive->akun,  $req->bulan_awal,  $req->bulan_awal, $req->tahun);

            $modal = Saldo_awal::where('akuns_id', $modal_id)
                ->whereMonth('created_at', $req->bulan_awal)
                ->whereYear('created_at', $req->tahun)
                ->first();
            // $saldo_modal = empty($modal) ? 0 : $modal->kredit;
            $mdl = Akun::find($modal_id);
            $saldo_modal = $this->get_buku_besar($mdl->akun, $req->bulan_awal, $req->bulan_awal, $req->tahun);
            $saldo_laba_rugi = $this->laba_rugi($req->bulan_awal, $req->tahun);

            $date_now = date('Y-m-d');
            $tanggal_modal = date('Y-m-d', strtotime($req->tahun . '-' . $req->bulan_awal . '-01'));

            $html = '<h4 class="text-center">
                <strong>' . strtoupper(config('app.name')) . '</strong></h4>';
            $html .= '<h5 class="text-center pb-2">Laporan Perubahan Modal</h5>
                <h6 class="text-center pb-2">Periode ' . $b[$req->bulan_awal - 1] . ' - ' . $req->tahun . '</h6>';
            $html .= '<table id="table1">
                <thead>
                    <tr class="text-left">
                        <th>Keterangan</th>
                        <th class="text-right">Nominal</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>';

            $html .= '<tr>';
            $html .= '<td class="text-left">Modal ' . ucwords(config('app.name')) . ' ' . date('j', strtotime($tanggal_modal)) . ' ' . $b[date('n', strtotime($tanggal_modal)) - 1] . ' ' . date('Y', strtotime($tanggal_modal)) . '</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($saldo_modal, 0, ',', '.') . '</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format(0, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td class="text-left">Laba bersih ' . date('j', strtotime($date_now)) . ' ' . $b[$req->bulan_awal - 1] . ' ' . date('Y', strtotime($date_now)) . '</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($saldo_laba_rugi, 0, ',', '.') . '</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format(0, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $total1 = $saldo_modal + $saldo_laba_rugi;

            $html .= '<tr>';
            $html .= '<td class="text-left">Total</td>';
            $html .= '<td class="text-right font-weight-bold"></td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($total1, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td class="text-left">Prive Pemilik</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($prive, 0, ',', '.') . '</td>';
            $html .= '<td class="text-right font-weight-bold"></td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td>&nbsp;</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td class="text-left">Modal ' . ucwords(config('app.name')) . ' ' . date('t', strtotime($tanggal_modal)) . ' ' . $b[date('n', strtotime($tanggal_modal)) - 1] . ' ' . date('Y', strtotime($tanggal_modal)) . '</td>';
            $html .= '<td class="text-right font-weight-bold"></td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($total1 - $prive, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $html .= '</tbody>';
            $html .= '</table>';
            $data = [
                'html' => $html,
            ];
        } else if ($req->jenis == "jurnal_penutup") {
            $b = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
                "September", "Oktober", "November", "Desember"
            ];
            $p = explode("/", $req->periode);
            $bulan = $p[0];
            $tahun = $p[1];

            // get akun
            $jurnal = Jurnal::with('akun')->groupBy('akuns_id')->get();
            $arr_akun = [];
            foreach ($jurnal as $a) {
                $arr_akun[] = [
                    'id' => $a->akun->id,
                    'kategori' => $a->akun->kategori,
                    'akun' => $a->akun->akun,
                ];
            }

            // get jurnal by akun 
            $html = '<h4 class="text-center pb-2">Laporan Jurnal Penutup</h4>
            <h6 class="text-center pb-2">Periode ' . $b[$bulan - 1] . ' ' . $tahun . '</h6>';
            $html .= '<table id="table1">
            <thead>
                <tr class="text-left">
                    <th>Keterangan</th>
                    <th>Debit</th>
                    <th>Kredit</th>
                </tr>
            </thead>
            <tbody>';


            $debet = 0;
            $kredit = 0;
            $query1 = Jurnal::with('akun')->whereHas('akun', function ($q) {
                $q->where('kategori', 'Pendapatan');
            })
                ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal', 'desc')
                ->get();
            $pendapatan = 0;
            foreach ($query1 as $p) {
                $pendapatan += ($p->debet == 0 ? $p->kredit : $p->debet);
                $debet = $pendapatan;
                $kredit = $pendapatan;
            }

            $query2 = Jurnal::with('akun')->whereHas('akun', function ($q) {
                $q->where('kategori', 'Beban');
            })
                ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal', 'desc')
                ->get();
            $beban1 = 0;
            foreach ($query2 as $p) {
                $beban1 += ($p->debet == 0 ? $p->kredit : $p->debet);
            }
            $debet += $beban1;

            $query3 = Jurnal::with('akun')->whereHas('akun', function ($q) {
                $q->where('kategori', 'Biaya Operasional');
            })
                ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->orderBy('tanggal', 'desc')
                ->get();
            $beban2 = 0;
            foreach ($query3 as $p) {
                $beban2 += ($p->debet == 0 ? $p->kredit : $p->debet);
            }
            $debet += $beban2;



            $html .= '<tr>';
            $html .= '<td>Pendapatan Jasa</td>';
            $html .= '<td>Rp ' . number_format($pendapatan, 0, ',', '.') . '</td>';
            $html .= '<td></td>';
            $html .= '</tr>';


            $html .= '<tr>';
            $html .= '<td class="pl-5">Ikhtisar Laba/Rugi</td>';
            $html .= '<td></td>';
            $html .= '<td>Rp ' . number_format($pendapatan, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            // akhir pendapatan jasa
            $html .= '<tr>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td>&nbsp;</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td>Ikhtisar Laba/Rugi</td>';
            $html .= '<td>Rp ' . number_format($beban1 + $beban2, 0, ',', '.') . '</td>';
            $html .= '<td></td>';
            $html .= '</tr>';


            foreach ($query2 as $p) {
                $saldo = ($p->debet == 0 ? $p->kredit : $p->debet);
                $html .= '<tr>';
                $html .= '<td class="pl-5">' . $p->akun->akun . '</td>';
                $html .= '<td></td>';
                $html .= '<td>Rp ' . number_format($saldo, 0, ',', '.') . '</td>';
                $html .= '</tr>';
                $kredit += $saldo;
            }
            foreach ($query3 as $p) {
                $saldo = ($p->debet == 0 ? $p->kredit : $p->debet);
                $html .= '<tr>';
                $html .= '<td class="pl-5">' . $p->akun->akun . '</td>';
                $html .= '<td></td>';
                $html .= '<td>Rp ' . number_format($saldo, 0, ',', '.') . '</td>';
                $html .= '</tr>';
                $kredit += $saldo;
            }

            // akhir beban

            $html .= '<tr>';
            $html .= '<td class="text-left font-weight-bold h5">Jumlah</td>';
            $html .= '<td class="text-left font-weight-bold h5">Rp. ' . str_replace('-', '', number_format($debet, 0, ',', '.')) . '</td>';
            $html .= '<td class="text-left font-weight-bold h5">Rp. ' . str_replace('-', '', number_format($kredit, 0, ',', '.')) . '</td>';
            $html .= '</tr>';

            $html .= '<tbody></table>';

            $data = [
                'html' => $html
            ];
        } else if ($req->jenis == "arus_kas") {
            $query1 = Jurnal::with('akun')->whereHas('akun', function ($q) {
                $q->where('kategori', 'Pendapatan');
            })
                ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereMonth('tanggal', '>=', $req->bulan_awal)
                ->whereMonth('tanggal', '<=', $req->bulan_akhir)
                ->whereYear('tanggal', '=', $req->tahun)
                ->orderBy('tanggal', 'desc')
                ->get();

            $query2 = Jurnal::with('akun')->whereHas('akun', function ($q) {
                $q->where('kategori', 'Harga Pokok Penjualan');
            })
                ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereMonth('tanggal', '>=', $req->bulan_awal)
                ->whereMonth('tanggal', '<=', $req->bulan_akhir)
                ->whereYear('tanggal', '=', $req->tahun)
                ->orderBy('tanggal', 'desc')
                ->get();

            $query3 = Jurnal::with('akun')->whereHas('akun', function ($q) {
                $q->where('kategori', 'Beban');
            })
                ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
                ->whereMonth('tanggal', '>=', $req->bulan_awal)
                ->whereMonth('tanggal', '<=', $req->bulan_akhir)
                ->whereYear('tanggal', '=', $req->tahun)
                ->orderBy('tanggal', 'desc')
                ->get();

            $query4 = Jurnal::where('akuns_id', '37ef5388-1bb4-40fb-95e2-45b5e1d22939')
                ->whereMonth('tanggal', '>=', $req->bulan_awal)
                ->whereMonth('tanggal', '<=', $req->bulan_akhir)
                ->whereYear('tanggal', '=', $req->tahun)
                ->sum('debet');
            $b = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
                "September", "Oktober", "November", "Desember"
            ];

            $html = '<h4 class="text-center">
                <strong>' . strtoupper(config('app.name')) . '</strong></h4>';
            $html .= '<h5 class="text-center pb-2">Laporan Arus Kas</h5>
                <h6 class="text-center pb-2">Periode ' . $b[$req->bulan_awal - 1] . ' - ' . $b[$req->bulan_akhir - 1] . ' ' . $req->tahun . '</h6>';
            $html .= '<table id="table1">';

            // pertama
            // beban perlengkapan
            $id_bp = '82bdb25b-4986-4221-aae8-87c0490b1f1d';
            $akun_bp = Akun::find($id_bp);

            $html .= '<tr>';
            $html .= '<th colspan="3">Arus Kas dari Aktivitas Operasi</th>';
            $html .= '</tr>';

            $laba = $this->laba_rugi_custom($req->bulan_awal, $req->bulan_akhir, $req->tahun);
            $html .= '<tr>';
            $html .= '<td class="text-left" width="100">&nbsp;</td>';
            $html .= '<td class="text-left">- Laba (Rugi) Periode Berjalan</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($laba, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $bp = $this->get_buku_besar($akun_bp->akun, $req->bulan_awal, $req->bulan_akhir, $req->tahun);
            $html .= '<tr>';
            $html .= '<td class="text-left" width="100">&nbsp;</td>';
            $html .= '<td class="text-left">- Peningkatan (Penurunan) Perlengkapan Kantor</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($bp, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $total1 = $laba - $bp;
            $html .= '<tr>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td class="text-left font-weight-bold">Total</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($total1, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td>&nbsp;</td>';
            $html .= '</tr>';

            // kedua
            $id_penyusutan = 'f1cd888a-84f7-4318-9ec9-6b989c4a7ab3';
            $akun_penyusutan = Akun::find($id_penyusutan);

            $html .= '<tr>';
            $html .= '<th colspan="3">Arus Kas dari Aktivitas Investasi</th>';
            $html .= '</tr>';

            $ppk = $this->get_buku_besar($akun_penyusutan->akun, $req->bulan_awal, $req->bulan_akhir, $req->tahun);

            $html .= '<tr>';
            $html .= '<td class="text-left" width="100">&nbsp;</td>';
            $html .= '<td class="text-left">- Peningkatan (Penurunan) Peralatan Kantor</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($ppk, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $total2 = $ppk;
            $html .= '<tr>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td class="text-left font-weight-bold">Total</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($total2, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td>&nbsp;</td>';
            $html .= '</tr>';

            // ketiga 
            $id_modal = '367db022-ed50-4671-9e6b-ca3efc3ea78b';
            $akun_modal = Akun::find($id_modal);

            $id_prive = 'f30ecc37-5681-491c-be4e-7840438f1e80';
            $akun_prive = Akun::find($id_prive);

            $html .= '<tr>';
            $html .= '<th colspan="3">Arus Kas dari Aktivitas Pendanaan</th>';
            $html .= '</tr>';

            $b_modal = $this->get_perubahan_modal($req->bulan_awal, $req->bulan_akhir, $req->tahun);

            $html .= '<tr>';
            $html .= '<td class="text-left" width="100">&nbsp;</td>';
            $html .= '<td class="text-left">- Peningkatan (Penurunan) Modal </td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($b_modal, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $b_prive = $this->get_buku_besar($akun_prive->akun, $req->bulan_awal, $req->bulan_akhir, $req->tahun);

            $html .= '<tr>';
            $html .= '<td class="text-left" width="100">&nbsp;</td>';
            $html .= '<td class="text-left">- Prive</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($b_prive, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $total3 = $b_modal - $b_prive;
            $html .= '<tr>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td class="text-left font-weight-bold">Total</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($total3, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td>&nbsp;</td>';
            $html .= '<td>&nbsp;</td>';
            $html .= '</tr>';

            $kas_bersih = ($total1 - $total2) + $total3;

            $html .= '<tr>';
            $html .= '<td colspan="2" class="font-weight-bold text-right">Kenaikan (Penurunan) Kas Bersih</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($kas_bersih, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="2" class="font-weight-bold text-right">Kas Awal Periode</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($query4, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $html .= '<tr>';
            $html .= '<td colspan="2" class="font-weight-bold text-right">Kas Akhir Periode</td>';
            $html .= '<td class="text-right font-weight-bold">Rp ' . number_format($kas_bersih, 0, ',', '.') . '</td>';
            $html .= '</tr>';

            $html .= '</tbody>';
            $html .= '</table>';
            $data = [
                'html' => $html,
            ];
        }
        return response()->json($data);
    }

    public function invoice($id)
    {
        $row = Penjualan::with('pelanggan', 'produk', 'akun', 'users')
            ->where('id', $id)
            ->first();
        $data = [
            'title' => 'Invoice',
        ];
        $kalimat = strtolower($row->produk->nama_produk);
        $kode = '';
        if ($this->strposa($kalimat, ["web"])) {
            $kode = "WEB";
        } elseif ($this->strposa($kalimat, ["aplikasi"])) {
            $kode = "APK";
        } elseif ($this->strposa($kalimat, ["desain"])) {
            $kode = "DSG";
        } elseif ($this->strposa($kalimat, ["video", "animasi"])) {
            $kode = "ANI";
        } else {
            $kode = "NOT_FOUND";
        }
        $data['noInvoice'] = 'INV-' . $kode . '-' . date('ymd', strtotime($row->tanggal));
        $data['row'] = $row;
        return view('laporan.invoice', $data);
    }

    public function export($jenis = '')
    {
        if ($jenis == 'jurnal') {
            $data['bulan_awal'] = '01';
            $data['bulan_akhir'] = '03';
            $data['tahun'] = '2023';
            return Excel::download(new JurnalExport($data), 'jurnal.xlsx');
        }
    }

    function get_buku_besar($namaakun = '', $bulan_awal = 0, $bulan_akhir = 0, $tahun = 0)
    {
        $b = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
            "September", "Oktober", "November", "Desember"
        ];

        // get akun
        $akun = Jurnal::with('akun')
            ->whereMonth('tanggal', '>=', $bulan_awal)
            ->whereMonth('tanggal', '<=', $bulan_akhir)
            ->whereYear('tanggal', '=', $tahun)
            ->groupBy('akuns_id')->get();
        $arr_akun = [];
        foreach ($akun as $a) {
            $arr_akun[] = [
                'id' => $a->akun->id,
                'kategori' => $a->akun->kategori,
                'akun' => $a->akun->akun,
            ];
        }

        rsort($arr_akun);
        $akun_saldo = [];
        foreach ($arr_akun as $item) {
            // get jurnal
            $jurnal = Jurnal::with('akun')->where('akuns_id', $item['id'])->orderBy('created_at', 'asc')->get();
            $saldo = 0;
            $row = [];
            $jenis = '';
            $jml = 0;
            foreach ($jurnal as $no => $j) {
                // simpan array
                $row[] = $j->debet == 0 ? 'kredit' : 'debet';
                // debet / kredit
                $jenis = $j->debet == 0 ? 'kredit' : 'debet';

                // jika array pertama maka tambah
                if ($no == 0) {
                    if ($j->debet == 0) {
                        $saldo += $j->kredit;
                    } else {
                        $saldo += $j->debet;
                    }
                } else {
                    if (in_array($item['kategori'], ['Activa Lancar', 'Activa Tetap', 'Harga Pokok Penjualan', 'Beban'])) {
                        if ($row[$no - 1] == "debet") {
                            if ($j->debet == 0) {
                                $saldo -= ($j->debet == 0 ? $j->kredit : $j->debet);
                            } else {
                                $saldo += ($j->debet == 0 ? $j->kredit : $j->debet);
                            }
                        } else {
                            if ($j->debet == 0) {
                                $saldo -= ($j->debet == 0 ? $j->kredit : $j->debet);
                            } else {
                                $saldo += ($j->debet == 0 ? $j->kredit : $j->debet);
                            }
                        }
                    } else {
                        if ($row[$no - 1] == "debet") {
                            if ($j->debet == 0) {
                                $saldo += ($j->debet == 0 ? $j->kredit : $j->debet);
                            } else {
                                $saldo -= ($j->debet == 0 ? $j->kredit : $j->debet);
                            }
                        } else {
                            if ($j->debet == 0) {
                                $saldo += ($j->debet == 0 ? $j->kredit : $j->debet);
                            } else {
                                $saldo -= ($j->debet == 0 ? $j->kredit : $j->debet);
                            }
                        }
                    }
                }

                $jml++;
            }

            $akun_saldo[$item['akun']] = $saldo;
        }
        return empty($akun_saldo[$namaakun]) ? 0 : $akun_saldo[$namaakun];
    }

    function laba_rugi($bulan, $tahun)
    {
        $query1 = Jurnal::with('akun')->whereHas('akun', function ($q) {
            $q->where('kategori', 'Pendapatan');
        })
            ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
            ->whereMonth('tanggal', '=', $bulan)
            ->whereYear('tanggal', '=', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $query2 = Jurnal::with('akun')->whereHas('akun', function ($q) {
            $q->where('kategori', 'Harga Pokok Penjualan');
        })
            ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
            ->whereMonth('tanggal', '=', $bulan)
            ->whereYear('tanggal', '=', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $query3 = Jurnal::with('akun')->whereHas('akun', function ($q) {
            $q->where('kategori', 'Beban');
        })
            ->select('*', DB::raw('DATE_FORMAT(tanggal, "%d-%m-%Y") as tanggal'))
            ->whereMonth('tanggal', '=', $bulan)
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

    function get_perubahan_modal($bulan_awal, $bulan_akhir, $tahun)
    {

        $prive_id = 'f30ecc37-5681-491c-be4e-7840438f1e80';
        $modal_id = '367db022-ed50-4671-9e6b-ca3efc3ea78b';
        $akun_prive = Akun::find($prive_id);
        $prive = $this->get_buku_besar($akun_prive->akun,  $bulan_awal,  $bulan_akhir, $tahun);

        $mdl = Akun::find($modal_id);
        $saldo_modal = $this->get_buku_besar($mdl->akun, $bulan_awal, $bulan_akhir, $tahun);
        $saldo_laba_rugi = $this->laba_rugi($bulan_akhir, $tahun);

        $total1 = $saldo_modal + $saldo_laba_rugi;
        return ($total1 - $prive);
    }
}
