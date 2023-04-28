<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan {{ strtoupper($jenis) }}</title>
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
            border-collapse: collapse;
            border-spacing: 0px;
            width: 100%;
        }

        .table2 td,
        .table2 th {
            background-color: transparent;
            border: 1px solid silver;
            padding: 5px 8px;
        }
    </style>
</head>

<body class="p-2">
    <button type="button"class="btn btn-danger hidden-print" id="btnPrint">Print</button>

    <h4 class="text-center">Laporan Arus Kas <br> {{ strtoupper(config('app.name')) }}
    </h4>
    <h5 class="text-center">Periode {{ $periode }}</h5>
    <div class="mt-5 border p-3" style="background-color: #fff8ea;">
        <table class="w-100">
            <tr>
                <td colspan="3" class="font-weight-bold">Arus Kas Masuk</td>
            </tr>
            @php
                $total_m = 0;
            @endphp
            @foreach ($query1 as $item)
                <tr>
                    <td class="pl-5">{{ $item->tanggal }}</td>
                    <td>{{ ucwords($item->keterangan) }}</td>
                    <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @php
                    $total_m += $item->total;
                @endphp
            @endforeach
            <tr>
                <td colspan="" class="font-weight-bold py-3"></td>
                <td class="font-weight-bold py-3">Total Arus Kas Masuk</td>
                <td class="font-weight-bold">Rp {{ number_format($total_m, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" class="py-2"></td>
            </tr>
            <tr>
                <td colspan="3" class="font-weight-bold">Arus Kas Keluar</td>
            </tr>
            @php
                $total_k = 0;
            @endphp
            @foreach ($query2 as $item)
                <tr>
                    <td class="pl-5">{{ $item->tanggal }}</td>
                    <td>{{ ucwords($item->keterangan) }}</td>
                    <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @php
                    $total_k += $item->total;
                @endphp
            @endforeach
            <tr>
                <td colspan="" class="font-weight-bold py-3"></td>
                <td class="font-weight-bold py-3">Total Arus Kas Keluar</td>
                <td class="font-weight-bold">Rp {{ number_format($total_k, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" class="py-2"></td>
            </tr>
            <tr>
                <td colspan="2" class="font-weight-bold">Sisa Kas</td>
                <td class="font-weight-bold">Rp {{ number_format($total_m - $total_k, 0, ',', '.') }}</td>
            </tr>
        </table>
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
