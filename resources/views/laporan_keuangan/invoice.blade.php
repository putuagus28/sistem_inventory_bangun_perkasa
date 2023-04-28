<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
    <style>
        @media print {

            .hidden-print,
            .hidden-print * {
                display: none !important;
            }
        }

        .tb1 {
            width: 100%;
            /* border: 1px solid rgb(87, 87, 87); */
            border-collapse: collapse;
        }

        .tb1 tr th,
        .tb1 tr td {
            /* border: 1px solid rgb(87, 87, 87); */
            padding: 5px 0px 5px 0px;
        }

        .table2 {
            width: 100%;
            border: 1px solid #b3adad;
            border-collapse: collapse;
            padding: 5px;
        }

        .table2 th {
            border: 1px solid #b3adad;
            padding: 5px;
            background: #d6d6d6;
            color: #313030;
        }

        .table2 td {
            border: 1px solid #b3adad;
            /* text-align: center; */
            padding: 5px;
            background: #ffffff;
            color: #313030;
        }
    </style>
</head>

<body class="p-2">
    <button type="button"class="btn btn-danger hidden-print" id="btnPrint">Print</button>

    <h4 class="text-center">
        {{ strtoupper(config('app.name')) }} <br>
        INVOICE <br>
    </h4>
    <h6 class="text-center">
        No : {{ $noInvoice }}
    </h6>

    <div class="my-4 p-1"></div>
    <div class="row">
        <div class="col-8">
            <table>
                <tr>
                    <td class="py-2">Nama Pelanggan</td>
                    <td class="px-4">:</td>
                    <td>{{ $row->pelanggan->nama }}</td>
                </tr>
                <tr>
                    <td class="py-2">Telepon</td>
                    <td class="px-4">:</td>
                    <td>{{ $row->pelanggan->telepon }}</td>
                </tr>
                <tr>
                    <td class="py-2">Tanggal Transaksi</td>
                    <td class="px-4">:</td>
                    <td>{{ date('d-F-Y', strtotime($row->tanggal)) }}</td>
                </tr>
                <tr>
                    <td class="py-2">Tanggal Jatuh Tempo</td>
                    <td class="px-4">:</td>
                    <td>{{ date('d-F-Y', strtotime($row->jatuh_tempo)) }}</td>
                </tr>
            </table>
        </div>
        <div class="col-4">
            <table>
                <tr>
                    <td class="py-2">Status Pembayaran</td>
                    <td class="px-4">:</td>
                    <td>{{ $row->status ? 'LUNAS' : 'BELUM LUNAS' }}</td>
                </tr>
                <tr>
                    <td class="py-2">Tanggal Print</td>
                    <td class="px-4">:</td>
                    <td>{{ date('d-F-Y') }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="my-4 p-1"></div>

    <table class="table2">
        <tr>
            <th>Produk</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Total</th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <td>{{ $row->produk->nama_produk }}</td>
            <td>{{ 'Rp ' . number_format($row->harga, 0, ',', '.') }}</td>
            <td>{{ $row->jumlah }}</td>
            <td>{{ 'Rp ' . number_format($row->harga * $row->jumlah, 0, ',', '.') }}</td>
            <td>{{ $row->keterangan }}</td>
        </tr>
    </table>
    <div class="my-4 p-1"></div>
    <div class="row">
        <div class="w-25 ml-auto text-center">
            <h6>
                {{ ucwords(config('app.name')) }} <br>
            </h6>
            <div class="my-5 p-1"></div>
            <p>({{ $row->users->name }})</p>
        </div>
    </div>


    {{-- <button id="btnPrint" class="hidden-print">Print</button> --}}
    <script>
        const $btnPrint = document.querySelector("#btnPrint");
        $btnPrint.addEventListener("click", () => {
            window.print();
        });
    </script>
</body>

</html>
