@extends('layouts.app')

@section('title', $title)

@section('css')
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- DatePicker -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.min.css') }}">
    <style>
        @media print {
            .row {
                display: -ms-flexbox;
                display: flex;
                -ms-flex-wrap: wrap;
                flex-wrap: wrap;
                margin-right: -7.5px;
                margin-left: -7.5px;
            }

            .col-sm-3 {
                -ms-flex: 0 0 25%;
                flex: 0 0 25%;
                max-width: 25%;
            }

            .col-sm-6 {
                -ms-flex: 0 0 50%;
                flex: 0 0 50%;
                max-width: 50%;
            }
        }
    </style>
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            @if (session('info'))
                <div class="alert alert-danger">
                    <strong><i class="fas fa-exclamation-triangle"></i></strong>
                    {{ session('info') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">{{ $title }}</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header d-flex flex-row align-items-center">
                            <h3 class="card-title">Laporan</h3>
                        </div>
                        <form method="POST">
                            @csrf
                            <input type="hidden" name="jenis" value="{{ $jenis }}">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" name="pilihan" id="pilihan1"
                                                    value="tanggal" checked> Periode Tanggal
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" name="pilihan" id="pilihan2"
                                                    value="bulan"> Bulan
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="radio" name="pilihan" id="pilihan3"
                                                    value="tahun"> Tahun
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-100 my-3"></div>
                                <div class="row" id="d_1">
                                    <div class="col-12 col-md-1 my-auto">
                                        <label for="">Tgl Awal</label>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <input type="date" class="form-control" name="tgl_awal" id="tgl_awal">
                                    </div>
                                    <div class="col-12 col-md-1 my-auto">
                                        <label for="">Tgl Akhir</label>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <input type="date" class="form-control" name="tgl_akhir" id="tgl_akhir">
                                    </div>
                                </div>

                                <div class="row d-none" id="d_2">
                                    <div class="col-12 col-md-1 my-auto">
                                        <label for="">Pilih Bulan</label>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <select class="form-control" name="bulan" id="bulan">
                                            <option value="" disabled selected>Pilih</option>
                                            @foreach ($bulan as $no => $item)
                                                <option value="{{ $no + 1 }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-1 my-auto">
                                        <label for="">Pilih Tahun</label>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <select class="form-control" name="tahun" id="tahun2">
                                            <option value="" disabled selected>Pilih</option>
                                            @foreach ($tahun as $item)
                                                <option value="{{ $item->tahun }}">{{ $item->tahun }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row d-none" id="d_3">
                                    <div class="col-12 col-md-1 my-auto">
                                        <label for="">Pilih Tahun</label>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <select class="form-control" name="tahun" id="tahun">
                                            <option value="" disabled selected>Pilih</option>
                                            @foreach ($tahun as $item)
                                                <option value="{{ $item->tahun }}">{{ $item->tahun }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer d-flex flex-row align-items-center">
                                <button type="submit" id="lihat" class="btn btn-info">Tampilkan</button>
                                <button type="button" id="cetak" class="btn btn-dark mx-1"><i class="fa fa-print"
                                        aria-hidden="true"></i> Cetak</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row" id="printable">
                {{-- Laporan Transaksi Simpanan --}}
                <div class="col-12 col-sm-12">
                    <div class="card card-dark">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-sm-3">
                                    <img src="{{ asset('assets/dist/img/logo.png') }}" alt="AdminLTE Logo"
                                        class="brand-image" width="100">
                                    <br>
                                </div>
                                <div class="col-12 col-sm-6 my-auto">
                                    <h4 class="text-center">{{ $title }}<br>{{ config('app.name') }}</h4>
                                    <p class="text-center" id="text_tanggal">Berdasarkan Tanggal : <span
                                            id="d1"></span>/<span id="d2"></span>
                                    <p class="text-center d-none" id="text_bulan">Berdasarkan Bulan : <span
                                            id="d1"></span>
                                    <p class="text-center d-none" id="text_tahun">Berdasarkan Tahun : <span
                                            id="d1"></span>
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <table>
                                <tr>
                                    <td><i class="fa fa-check-circle text-info" aria-hidden="true"></i> Total Service
                                        Selesai</td>
                                    <td class="px-2">:</td>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <td><i class="fa fa-check-circle text-danger" aria-hidden="true"></i> Total Service
                                        Belum Selesai</td>
                                    <td class="px-2">:</td>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <td><i class="fa fa-check-circle text-success" aria-hidden="true"></i> Total Service
                                    </td>
                                    <td class="px-2">:</td>
                                    <td>0</td>
                                </tr>
                            </table>
                            <br>
                            <table class="table table-bordered table-striped" id="table1">
                                <thead>
                                    <tr class="text-uppercase">
                                        <th>no</th>
                                        <th>no antrean</th>
                                        <th>uang muka</th>
                                        <th>jenis barang</th>
                                        <th>tgl</th>
                                        <th>teknisi</th>
                                        <th>nama plg</th>
                                        <th>no telp</th>
                                        <th>status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="9" class="text-center">No data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--/. container-fluid -->
    </section>

@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- Jquery Validate -->
    <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- Sweetalert2 -->
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <!-- DatePicker -->
    <script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- SELECT2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/print/jQuery.print.js') }}"></script>
    <script>
        $(document).ready(function() {
            function formatMoney(num) {
                var p = num.toFixed(0).split(".");
                return "Rp " + p[0].split("").reverse().reduce(function(acc, num, i, orig) {
                    return num + (num != "-" && i && !(i % 3) ? "." : "") + acc;
                }, "");
            }

            function openInNewTab(url) {
                window.open(url, '_blank').focus();
            }

            function lastdigit(strs) {
                str = strs.toString();
                str = str.slice(0, -3);
                str = parseInt(str);
                return str;
            }

            $(function() {
                $("#cetak").on('click', function() {
                    $.print("#printable");
                });
            });


            $('form select').select2({
                theme: 'bootstrap4',
            });

            // remove invalid in change
            $('select').on('change', function(e) {
                e.preventDefault();
                var id = $(this).val();
                if (id != null) {
                    $(this).removeClass('is-invalid');
                }
            });


            $('[name*="pilihan"]').change(function(e) {
                e.preventDefault();
                var v = $(this).val();
                if (v == 'tanggal') {
                    $('#d_1').removeClass('d-none');
                    $('#d_2, #d_3').addClass('d-none');
                } else if (v == 'bulan') {
                    $('#d_2').removeClass('d-none');
                    $('#d_1, #d_3').addClass('d-none');
                } else if (v == 'tahun') {
                    $('#d_3').removeClass('d-none');
                    $('#d_1, #d_2').addClass('d-none');
                }
            });


            var validator = $("form").validate({
                rules: {
                    tgl_awal: {
                        required: true,
                    },
                    tgl_akhir: {
                        required: true,
                    },
                    bulan: {
                        required: true,
                    },
                    tahun: {
                        required: true,
                    },
                },
                errorElement: "div",
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.input-group, .form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    var data = $(form).serialize();
                    var pilihan = $('[name="pilihan"]:checked').val();
                    var d1 = $('#tgl_awal').val();
                    var d2 = $('#tgl_akhir').val();
                    var bulan = $('#bulan option:selected').text();
                    var tahun2 = $('#tahun2 option:selected').val();
                    var tahun = $('#tahun').val();

                    if (pilihan == 'tanggal') {
                        $('#text_tanggal').removeClass('d-none');
                        $('#text_bulan, #text_tahun').addClass('d-none');
                        $('#text_tanggal #d1').text(d1);
                        $('#text_tanggal #d2').text(d2);
                    } else if (pilihan == 'bulan') {
                        $('#text_bulan').removeClass('d-none');
                        $('#text_tanggal, #text_tahun').addClass('d-none');
                        $('#text_bulan #d1').text(bulan + ' Tahun : ' + tahun2);
                    } else if (pilihan == 'tahun') {
                        $('#text_tahun').removeClass('d-none');
                        $('#text_tanggal, #text_bulan').addClass('d-none');
                        $('#text_tahun #d1').text(tahun);
                    }
                    $.ajax({
                        type: "POST",
                        url: "{{ route('post.laporan') }}",
                        data: data,
                        dataType: "json",
                        success: function(res) {
                            if (res.query) {
                                $('#table1').find('tbody').html('');
                                var tb1 = $('table').eq(0);
                                var html = '';
                                var done = 0;
                                var pending = 0;
                                if (res.query.length >= 1) {
                                    var subtotal = 0;
                                    $.each(res.query, function(i, val) {
                                        subtotal += val.uang_muka;
                                        html += '<tr>';
                                        html += '<td>' + (i + 1) + '</td>';
                                        html += '<td>' + val.no_antrean + '</td>';
                                        html += '<td>' + formatMoney(parseInt(val
                                            .uang_muka)) + '</td>';
                                        html += '<td>' + val.jenis_barang + '</td>';
                                        html += '<td>' + val.tanggal + '</td>';
                                        html += '<td>' + val.teknisi.name + '</td>';
                                        html += '<td>' + val.pelanggan.nama +
                                            '</td>';
                                        html += '<td>' + val.pelanggan.no_telp +
                                            '</td>';
                                        html += '<td>' + val.status + '</td>';
                                        html += '</tr>';

                                        done += val.status == 'close' ? 1 : 0;
                                        pending += val.status == 'open' ? 1 : 0;
                                    });

                                    html += '<tr>';
                                    html += '<td></td>';
                                    html += '<td class="font-weight-bold">Subtotal</td>';
                                    html += '<td class="font-weight-bold">' + formatMoney(
                                            parseInt(subtotal)) +
                                        '</td>';
                                    html += '<td></td>';
                                    html += '<td></td>';
                                    html += '<td></td>';
                                    html += '<td></td>';
                                    html += '<td></td>';
                                    html += '<td></td>';
                                    html += '</tr>';
                                } else {
                                    html += '<tr>';
                                    html +=
                                        '<td colspan="9" class="text-center">No Data</td>';
                                    html += '</tr>';
                                }


                                tb1.find('tr').eq(0).find('td').eq(2).text(done);
                                tb1.find('tr').eq(1).find('td').eq(2).text(pending);
                                tb1.find('tr').eq(2).find('td').eq(2).text(done + pending);

                                $('#table1').find('tbody').append(html);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
