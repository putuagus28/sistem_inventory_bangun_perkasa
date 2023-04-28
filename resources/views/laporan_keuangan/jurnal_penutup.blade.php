@extends('layouts.app')

@section('title', $title)

@section('css')
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <!-- DatePicker -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/ekko-lightbox/ekko-lightbox.css') }}">
    <style>
        table {
            width: 100%;
            border: 1px solid #b3adad;
            border-collapse: collapse;
            padding: 5px;
        }

        table th {
            border: 1px solid #b3adad;
            padding: 5px;
            background: #d6d6d6;
            color: #313030;
        }

        table td {
            border: 1px solid #b3adad;
            text-align: left;
            padding: 5px;
            background: #ffffff;
            color: #313030;
            font-weight: 500;
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
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Home</a></li>
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
                    <div class="card card-danger">
                        <div class="card-header d-flex flex-row align-items-center">
                            <h3 class="card-title">FILTER</h3>
                        </div>
                        <form method="POST">
                            @csrf
                            <div class="card-body row">
                                <div class="col-12 col-md-2 my-auto">
                                    <label for="">Pilih Periode</label>
                                </div>
                                <div class="col-12 col-md-3">
                                    <input type="hidden" name="jenis" value="jurnal_penutup">
                                    <select class="form-control" name="periode" id="periode">
                                        <option value="" disabled selected>Pilih</option>
                                        @foreach ($periode as $item)
                                            <option value="{{ $item->bulan }}/{{ $item->tahun }}">
                                                {{ $bulan[$item->bulan - 1] }}
                                                {{ $item->tahun }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer d-flex flex-row align-items-center">
                                {{-- <button type="button" id="cetak" class="btn btn-danger"><i class="fa fa-print"
                                        aria-hidden="true"></i> Cetak</button> --}}
                                <button type="submit" id="lihat" class="btn btn-danger">Preview</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- Laporan Transaksi Simpanan --}}
                <div class="col-12 col-md-12">
                    <div class="card card-dark">
                        {{-- <div class="card-header d-flex flex-row align-items-center">
                            <h3 class="card-title">Laporan Penanggung Jawab Kegiatan <span id="periode"></span></h3>
                        </div> --}}
                        <div class="card-body" id="table_view">
                            <h4 class="text-center pb-2">{{ $title }}</h4>
                            <table id="table1">
                                <thead>
                                    <tr class="text-left">
                                        <th>Keterangan</th>
                                        <th>Debit</th>
                                        <th>Kredit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="3">No Data</td>
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
    <!-- ChartJS -->
    <script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
    <!-- SELECT2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/ekko-lightbox/ekko-lightbox.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('select').select2({
                theme: 'bootstrap4',
                container: 'body'
            });

            /* Fungsi formatRupiah */
            function formatRupiah(num) {
                var p = num.toFixed(0).split(".");
                return "Rp " + p[0].split("").reverse().reduce(function(acc, num, i, orig) {
                    return num + (num != "-" && i && !(i % 3) ? "." : "") + acc;
                }, "");
            }

            function yyyymmdd(dateIn) {
                return new Date(dateIn).toUTCString()
            }


            function openInNewTab(url) {
                window.open(url, '_blank').focus();
            }

            // remove invalid in change
            $('select').on('change', function(e) {
                e.preventDefault();
                var id = $(this).val();
                if (id != null) {
                    $(this).removeClass('is-invalid');
                }
            });


            var validator = $("form").validate({
                rules: {
                    periode: {
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
                    var periode = $(form).find('#periode').val();
                    $('span#periode').text(periode);
                    $.ajax({
                        type: "POST",
                        url: "{{ route('post.laporan_keuangan') }}",
                        data: data,
                        dataType: "json",
                        success: function(res) {
                            if (res) {
                                $('#table_view').html('');
                                $('#table_view').append(res.html);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
