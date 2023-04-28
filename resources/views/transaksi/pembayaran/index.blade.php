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

        .table2 {
            border-collapse: collapse;
        }

        .table2 tr td {
            border-collapse: collapse;
            padding: 3px 5px;
        }

        .table2 tr td:first-child {
            font-weight: bold
        }

        .styled-table {
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.9em;
            font-family: sans-serif;
            min-width: 400px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        }

        .styled-table thead tr {
            background-color: #2d849f;
            color: #ffffff;
            text-align: left;
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
        }

        .styled-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #2d849f;
        }

        .styled-table tbody tr.active-row {
            font-weight: bold;
            color: #009879;
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
                            <button type="button" onclick="window.location.href='{{ route('pembayaran.add') }}'"
                                class="btn btn-info ml-auto" data-toggle="collapse" data-target="#collapseTambah"
                                aria-expanded="false" aria-controls="collapseTambah">
                                </span><i class="fa fa-plus" aria-hidden="true"></i> Tambah
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="tb_view">
                                <thead>
                                    <tr class="text-uppercase">
                                        <th>no</th>
                                        <th>id pembayaran</th>
                                        <th>no tiket</th>
                                        <th>pelanggan</th>
                                        <th>tgl pembayaran</th>
                                        <th>total tagihan</th>
                                        <th>added by</th>
                                        <th>opsi</th>
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
    <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <table class="table2">
                                <tr class="text-uppercase">
                                    <td>id pembayaran</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                                <tr class="text-uppercase">
                                    <td>no antrean</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                                <tr class="text-uppercase">
                                    <td>nama pelanggan</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                                <tr class="text-uppercase">
                                    <td>nama teknisi</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                                <tr class="text-uppercase">
                                    <td>tanggal service</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                                <tr class="text-uppercase">
                                    <td>tanggal bayar</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-6">
                            <table class="table2">
                                <tr class="text-uppercase">
                                    <td>no telp</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                                <tr class="text-uppercase">
                                    <td>jenis barang</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                                <tr class="text-uppercase">
                                    <td>lama service</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                                <tr class="text-uppercase">
                                    <td>status service</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <br>
                    <table class="styled-table w-100">
                        <thead class="thead-inverse">
                            <tr>
                                <th>No</th>
                                <th>Barang/Jasa</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Harga Jual</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-12 col-md-4 ml-auto">
                            <table class="table2">
                                <tr class="text-uppercase">
                                    <td>total item</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                                <tr class="text-uppercase">
                                    <td>total jasa</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                                <tr class="text-uppercase">
                                    <td>total tagihan</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                                <tr class="text-uppercase">
                                    <td>uang muka</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                                <tr class="text-uppercase">
                                    <td>sisa tagihan</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

        function formatDate(date) {
            const today = new Date(date);
            const yyyy = today.getFullYear();
            let mm = today.getMonth() + 1; // Months start at 0!
            let dd = today.getDate();

            if (dd < 10) dd = '0' + dd;
            if (mm < 10) mm = '0' + mm;

            const formattedToday = dd + '-' + mm + '-' + yyyy;
            return formattedToday;
        }

        $(document).ready(function() {
            $.fn.modal.Constructor.prototype._enforceFocus = function() {};
            var stok_sistem = 0;
            var table = $('#tb_view').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('transaksi.pembayaran') }}",
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                    },
                    {
                        data: 'kode',
                        name: 'kode'
                    },
                    {
                        data: 'service.no_antrean',
                        name: 'service.no_antrean'
                    },
                    {
                        data: 'service.pelanggan.nama',
                        name: 'service.pelanggan.nama'
                    },
                    {
                        data: 'tgl_bayar',
                        name: 'tgl_bayar'
                    },
                    {
                        data: 'total_tagihan',
                        name: 'total_tagihan'
                    },
                    {
                        data: 'users.name',
                        render: function(data, type, row, meta) {
                            return row.users.name + " (<strong>" + row.users.role + "</strong>)";
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
                    [1, "asc"]
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
                    $.get("{{ url('getstok') }}", {
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
                $('#modalDetail').modal('show');
                $('#modalDetail').find('.modal-title').html(
                    '<i class="fa fa-check-circle text-info" aria-hidden="true"></i> Detail Pembayaran');
                $.ajax({
                    url: "{{ route('pembayaran.edit') }}",
                    type: "GET",
                    dataType: "JSON",
                    cache: false,
                    data: {
                        'id': id
                    },
                    success: function(res) {
                        var tb1 = $('#modalDetail').find('table').eq(0).find('tbody');
                        var tb2 = $('#modalDetail').find('table').eq(1).find('tbody');
                        var tb3 = $('#modalDetail').find('table').eq(2).find('tbody');
                        var tb4 = $('#modalDetail').find('table').eq(3).find('tbody');
                        var html = '';
                        var subtotal = 0;

                        tb1.find('tr').eq(0).find('td').eq(2).text(res.kode);
                        tb1.find('tr').eq(1).find('td').eq(2).text(res.service.no_antrean);
                        tb1.find('tr').eq(2).find('td').eq(2).text(res.service.pelanggan.nama);
                        tb1.find('tr').eq(3).find('td').eq(2).text(res.service.teknisi.name);
                        tb1.find('tr').eq(4).find('td').eq(2).text(formatDate(res.service
                            .tanggal));
                        tb1.find('tr').eq(5).find('td').eq(2).text(res.tanggal_format);

                        tb2.find('tr').eq(0).find('td').eq(2).text(res.service.pelanggan
                            .no_telp);
                        tb2.find('tr').eq(1).find('td').eq(2).text(res.service.jenis_barang);
                        tb2.find('tr').eq(2).find('td').eq(2).text(res.service.lama_service);
                        var css = '';
                        if (res.service.status == "close") {
                            css = 'danger';
                        } else {
                            css = 'success';
                        }
                        status = '<span class="badge badge-pill badge-' + css + '">' + res
                            .service.status + '</span>';
                        tb2.find('tr').eq(3).find('td').eq(2).html(status);

                        var html = '';
                        var jml_item = 0;
                        var jml_jasa = 0;
                        var subtotal = 0;
                        $.each(res.detail, function(i, v) {
                            var item = '';
                            var harga = 0;
                            var total = 0;
                            if (v.barang != null) {
                                item = v.barang.nama;
                                harga = v.harga;
                                total = v.harga * v.qty;
                                jml_item++;
                            } else {
                                item = v.jenisjasa.nama;
                                harga = v.harga;
                                total = v.harga * v.qty;
                                jml_jasa++;
                            }
                            subtotal += total;
                            html += '<tr>';
                            html += '<td>' + (i + 1) + '</td>';
                            html += '<td>' + item + '</td>';
                            html += '<td>' + formatMoney(parseInt(harga)) + '</td>';
                            html += '<td>' + v.qty + '</td>';
                            html += '<td>' + formatMoney(parseInt(total)) + '</td>';
                            html += '</tr>';
                        });

                        tb3.html(html);

                        tb4.find('tr').eq(0).find('td').eq(2).text(jml_item);
                        tb4.find('tr').eq(1).find('td').eq(2).text(jml_jasa);
                        tb4.find('tr').eq(2).find('td').eq(2).text(formatMoney(parseInt(
                            subtotal)));
                        tb4.find('tr').eq(3).find('td').eq(2).text(formatMoney(parseInt(
                            res.service.uang_muka)));
                        tb4.find('tr').eq(4).find('td').eq(2).text(formatMoney(parseInt(
                            subtotal -
                            res.service.uang_muka)));


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
                    title: 'Yakin untuk untuk menghapus ini ?',
                    // text: 'Jika anda menghapus maka, stok sebelumnya akan kembali seperti sebelum anda melakukan penjualan',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('pembayaran.delete') }}",
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
                            url: "{{ url('pembayaran.update') }}",
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
