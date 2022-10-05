@extends('layouts.app')

@section('title', $title)

@section('css')
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        .tb1 tr td {
            padding: 5px 8px;
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
                    <div class="card">
                        <div class="card-header d-flex flex-row align-items-center">
                            <h3 class="card-title"><i class="fas fa-box-open"></i></h3>
                            <button type="button" onclick="window.location.href='{{ route('penjualan.add') }}'"
                                class="btn btn-info ml-auto" data-toggle="collapse" data-target="#collapseTambah"
                                aria-expanded="false" aria-controls="collapseTambah">
                                </span><i class="fa fa-plus" aria-hidden="true"></i> Tambah
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="tb_view">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NoPenjualan</th>
                                        <th>Tanggal Penjualan</th>
                                        <th>Total</th>
                                        <th>Customer Name</th>
                                        <th>User Added</th>
                                        <th>Delivery Status</th>
                                        <th>Opsi</th>
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

    <!-- Modal -->
    <div class="modal fade" id="modal" data-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="tb1">
                        <tr>
                            <td><strong>No Pembelian</strong></td>
                            <td>:</td>
                            <td class="font-weight-bold"></td>
                        </tr>
                        <tr>
                            <td><strong>Nama Customer</strong></td>
                            <td>:</td>
                            <td class="font-weight-bold"></td>
                        </tr>
                    </table>
                    <br>
                    <table class="table table-striped" id="tb_detail">
                        <thead class="thead-inverse">
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Satuan</th>
                                <th>Qty Pesan</th>
                                <th>Harga</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <!-- DataTables -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- Jquery Validate -->
    <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- JQuery mask -->
    <script src="{{ asset('assets/plugins/jquery-mask/jquery.mask.min.js') }}"></script>
    <!-- BARCODE SCANNER -->
    <script src="{{ asset('assets/plugins/jquery-scanner-detection-master/jquery.scannerdetection.compatibility.js') }}">
    </script>
    <script src="{{ asset('assets/plugins/jquery-scanner-detection-master/jquery.scannerdetection.js') }}"></script>
    <!-- Sweetalert2 -->
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <!-- SELECT2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        function formatMoney(num) {
            var p = num.toFixed(0).split(".");
            return "Rp " + p[0].split("").reverse().reduce(function(acc, num, i, orig) {
                return num + (num != "-" && i && !(i % 3) ? "." : "") + acc;
            }, "");
        }

        function formatAMPM(date) { // This is to display 12 hour format like you asked
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            var strTime = hours + ':' + minutes + ' ' + ampm;
            return strTime;
        }

        $(document).ready(function() {
            $.fn.modal.Constructor.prototype._enforceFocus = function() {};
            var stok_sistem = 0;
            var table = $('#tb_view').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('json.penjualan') }}",
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                    },
                    {
                        data: 'no',
                        name: 'no'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'subtotal',
                        name: 'subtotal'
                    },
                    {
                        data: 'customers.nama',
                        name: 'customers.nama'
                    },
                    {
                        data: 'user.name',
                        render: function(data, type, row, meta) {
                            return row.user.name + " (<strong>" + row.user.role + "</strong>)";
                        },
                    },
                    {
                        data: 'delivery',
                        render: function(data, type, row, meta) {
                            if (row.delivery == 1) {
                                return '<span class="px-2 py-1 bg-success text-white"><i class="fas fa-truck"></i> dikirim</span>';
                            } else {
                                return '<span class="px-2 py-1 bg-dark text-white">belum dikirim</span>';
                            }
                        },
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [2, "desc"]
                ]
            });

            $('#barang').select2({
                theme: 'bootstrap4',
            });

            $('select#barang').change(function(e) {
                e.preventDefault();
                var id = $(this).val();
                if (id != null || id != "") {
                    $(this).removeClass('is-invalid');
                    $.get("{{ route('getstok') }}", {
                            'id': id
                        },
                        function(data, textStatus, jqXHR) {
                            // stok_sistem = data;
                            // $('#stok_comp').val(data);
                            $('#stok_nyata').select();
                        },
                        "JSON"
                    );
                }
            });

            // open modal tambah
            $('#tambah').click(function(e) {
                e.preventDefault();
                $('#modal').modal('show');
                $('#modal').find('.modal-title').text('Form Tambah');
            });

            // reset all input in form after clicking modal
            $('#modal').on('hidden.bs.modal', function(e) {
                // validator.resetForm();
                $("#modal").find('.is-invalid').removeClass('is-invalid');
                $(this)
                    .find("input,textarea,select")
                    .not('input[name="_token"]')
                    .val('')
                    .end()
                    .find("input[type=checkbox], input[type=radio]")
                    .prop("checked", "")
                    .end();
            });

            // modal show 
            $('#modal').on('shown.bs.modal', function() {
                $(this).find('#nama').select();
            });

            // open modal detail
            table.on("click", "#detail", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var no = $(this).data('no');
                var customers = $(this).data('customers');
                $('#modal').modal('show');
                $('#modal').find('.modal-title').text('Detail Penjualan');
                $('#modal form').show().find('#id').val(id);
                $('#modal').find('table').eq(0).find('tr:nth-child(1) td').eq(2).text(no);
                $('#modal').find('table').eq(0).find('tr:nth-child(2) td').eq(2).text(customers);
                $.ajax({
                    url: "{{ route('penjualan.edit') }}",
                    type: "GET",
                    dataType: "JSON",
                    cache: false,
                    data: {
                        'id': id
                    },
                    success: function(data) {
                        var tb = $('#modal').find('table').eq(1).find('tbody');
                        var html = '';
                        var subtotal = 0;
                        $.each(data.detail, function(i, v) {
                            html += '<tr>';
                            html += '<td>' + (i + 1) + '</td>';
                            html += '<td>' + v.barang.nama_barang + '</td>';
                            html += '<td>' + v.barang.satuan + '</td>';
                            html += '<td>' + v.qty + '</td>';
                            html += '<td>' + formatMoney(v.barang.harga) + '</td>';
                            html += '<td>' + formatMoney(v.qty * v.barang.harga) +
                                '</td>';
                            html += '</tr>';
                            subtotal += (v.qty * v.barang.harga);
                        });
                        html += '<tr>';
                        html += '<td colspan="4"></td>';
                        html += '<td><b>SubTotal</b></td>';
                        html += '<td><b>' + formatMoney(subtotal) +
                            '</b></td>';
                        html += '</tr>';
                        tb.html(html);
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
            });

            // open modal detail
            table.on("click", "#delete", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin untuk untuk menghapus pembelian ini ?',
                    text: 'Jika anda menghapus maka, stok sebelumnya akan kembali seperti sebelum anda melakukan penjualan',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('penjualan.delete') }}",
                            type: "GET",
                            dataType: "JSON",
                            cache: false,
                            data: {
                                'id': id
                            },
                            success: function(data) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                });
                                table.ajax.reload();
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

            // open modal terkirim
            table.on("click", "#terkirim", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah barang akan dikirim ?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Kirim!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('penjualan.kirim') }}",
                            type: "POST",
                            dataType: "JSON",
                            cache: false,
                            data: {
                                'id': id,
                                "_token": "{{ csrf_token() }}",
                            },
                            success: function(data) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                });
                                table.ajax.reload();
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

            // tambah data
            var validator = $("#modalForm").validate({
                rules: {
                    barang: {
                        required: true,
                    },
                    stok_nyata: {
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
                    var id = $(form).find('#id').val();
                    if (id != "") {
                        $.ajax({
                            url: "{{ route('penjualan.update') }}",
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
                                        title: response.message,
                                        showCancelButton: false,
                                        showConfirmButton: true
                                    }).then(function() {
                                        $('#modal').modal('hide');
                                        table.ajax.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
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
                }
            });
        });
    </script>
@endsection
