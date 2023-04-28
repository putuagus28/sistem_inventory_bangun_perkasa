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
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
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
                    <div class="card card-dark">
                        <div class="card-header d-flex flex-row align-items-center">
                            <h3 class="card-title">FILTER</h3>
                        </div>
                        <form method="POST">
                            @csrf
                            <input type="hidden" name="jenis" value="laba_rugi">
                            <div class="card-body row">
                                <div class="col-12 col-md-3 my-auto">
                                    <label for="">Dari Bulan</label>
                                    <select class="form-control" name="bulan_awal" id="bulan_awal">
                                        <option value="" disabled selected>Pilih</option>
                                        @foreach ($bulan as $no => $item)
                                            <option value="{{ $no + 1 }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-3 my-auto">
                                    <label for="">Sampai Bulan</label>
                                    <select class="form-control" name="bulan_akhir" id="bulan_akhir">
                                        <option value="" disabled selected>Pilih</option>
                                        @foreach ($bulan as $no => $item)
                                            <option value="{{ $no + 1 }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-3 my-auto">
                                    <label for="">Tahun</label>
                                    <select class="form-control" name="tahun" id="tahun">
                                        <option value="" disabled selected>Pilih</option>
                                        @foreach ($tahun as $no => $item)
                                            <option value="{{ $item->tahun }}">{{ $item->tahun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer d-flex flex-row align-items-center">
                                <button type="submit" id="lihat" class="btn btn-dark">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered" id="table1">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nama Akun</th>
                                        <th>No Reff</th>
                                        <th>Debet</th>
                                        <th>Kredit</th>
                                        <th>Keterangan</th>
                                        <th>User Added</th>
                                    </tr>
                                </thead>
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
    <script src="{{ asset('assets/plugins/datatables/dataTables.rowsGroup.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- Jquery Validate -->
    <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- Sweetalert2 -->
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <!-- JQuery mask -->
    <script src="{{ asset('assets/plugins/jquery-mask/jquery.mask.min.js') }}"></script>
    <!-- SELECT2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        /* Fungsi formatRupiah */
        function formatRupiah(num) {
            var p = num.toFixed(0).split(".");
            return "Rp " + p[0].split("").reverse().reduce(function(acc, num, i, orig) {
                return num + (num != "-" && i && !(i % 3) ? "." : "") + acc;
            }, "");
        }

        $(document).ready(function() {
            var role = "{{ auth()->user()->role }}";
            $.fn.modal.Constructor.prototype._enforceFocus = function() {};

            $('form select').select2({
                theme: 'bootstrap4',
                container: 'body'
            });

            // remove invalid in change
            $('select').on('change', function(e) {
                e.preventDefault();
                var id = $(this).val();
                if (id != null) {
                    $(this).removeClass('is-invalid');
                }
            });

            var table = $('#table1').DataTable({
                processing: true,
                serverSide: true,
                // responsive: true,
                autoWidth: false,
                // aLengthMenu: [
                //     [25, 50, 100, 200, -1],
                //     [25, 50, 100, 200, "All"]
                // ],
                // iDisplayLength: -1,
                language: {
                    'loadingRecords': '&nbsp;',
                    'processing': '<i class="fas fa-spinner"></i>'
                },
                ajax: "{{ route('json.tutup_buku') }}",
                columns: [{
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'akun.akun',
                        render: function(data, type, row, meta) {
                            var css = '';
                            if (row.debet == 'Rp 0') {
                                var css = 'text-right';
                            } else {
                                var css = 'text-left';
                            }

                            return '<span class="d-block ' + css + '">' + row.akun.akun +
                                '</span>';
                        },
                    },
                    {
                        data: 'akun.no_reff',
                        name: 'akun.no_reff'
                    },
                    {
                        data: 'debet',
                        name: 'debet'
                    },
                    {
                        data: 'kredit',
                        name: 'kredit'
                    },
                    {
                        data: 'keterangan',
                        // name: 'keterangan'
                        render: function(data, type, row, meta) {
                            var btn = '';
                            if (row.keterangan == 'penjualan') {
                                btn = '<span class="badge badge-success">' + row.keterangan +
                                    '</span>';
                            } else if (row.keterangan == 'pengeluaran') {
                                btn = '<span class="badge badge-danger">' + row.keterangan +
                                    '</span>';
                            } else {
                                btn = '<span class="badge badge-success">' + row.keterangan +
                                    '</span>';
                            }
                            return btn;
                        },
                    },
                    {
                        data: 'users.name',
                        name: 'users.name',
                    },
                    // {
                    //     data: 'created_at',
                    //     name: 'created_at'
                    // },
                ],
                'rowsGroup': [0, 5],
                'createdRow': function(row, data, dataIndex) {
                    $('td:eq(0)', row).addClass('align-middle');
                    $('td:eq(5)', row).addClass('align-middle');
                },
                order: [
                    [6, "asc"]
                ],
                paging: false,
                columnDefs: [{
                    orderable: false,
                    targets: [1, 2, 3, 4, 5, 6]
                }],
            });

            // finish cart
            var validator = $("form").validate({
                rules: {
                    bulan_awal: {
                        required: true,
                    },
                    bulan_akhir: {
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
                    $.ajax({
                        url: "{{ route('insert.tutup_buku') }}",
                        type: "POST",
                        dataType: "JSON",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: new FormData(form),
                        success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    html: response.message,
                                    showCancelButton: false,
                                    showConfirmButton: true
                                }).then(function() {
                                    table.ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    html: response.message,
                                    showCancelButton: false,
                                    showConfirmButton: true
                                }).then(function() {
                                    table.ajax.reload();
                                });
                            }
                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Opps!',
                                text: 'server error!'
                            });
                            console.log(response);
                        }
                    });
                }
            });
        });
    </script>
@endsection
