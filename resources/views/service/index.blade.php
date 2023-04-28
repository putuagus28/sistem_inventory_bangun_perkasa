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
    <link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote-bs4.min.css') }}">
    <style>
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
                        <li class="breadcrumb-item active">User</li>
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
                            <h3 class="card-title"><i class="fas fa-cogs"></i> </h3>
                            @if (auth()->user()->role == 'admin')
                                <button class="btn btn-info ml-auto" id="tambah">Tambah</button>
                            @endif
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="table1">
                                <thead>
                                    <tr class="text-uppercase">
                                        <th>no antrean</th>
                                        <th>uang muka</th>
                                        <th>tgl</th>
                                        <th>teknisi</th>
                                        <th>nama plg</th>
                                        <th>no telp</th>
                                        <th>status</th>
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
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" id="modalForm">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">ID Permintaan Service</label>
                                    <input type="text" name="kode" readonly id="kode" class="form-control">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">No Antrean</label>
                                    <input type="text" name="no_antrean" readonly id="no_antrean" class="form-control">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">Tanggal Daftar</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Nama Pelanggan</label>
                                    <select name="pelanggans_id" id="pelanggans_id" class="form-control">
                                        <option value=""></option>
                                        @foreach ($pelanggan as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">Uang Muka</label>
                                    <input type="text" name="uang_muka" id="uang_muka" class="form-control">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">Jenis Antrean</label>
                                    <select name="jenis_antrean" id="jenis_antrean" class="form-control">
                                        <option value=""></option>
                                        <option value="tunggu ditempat">tunggu ditempat</option>
                                        <option value="dititipkan di toko">dititipkan di toko</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Nama Teknisi</label>
                                    <select name="teknisi_id" id="teknisi_id" class="form-control">
                                        <option value=""></option>
                                        @foreach ($teknisi as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Jenis Barang</label>
                                    <input type="text" name="jenis_barang" id="jenis_barang" class="form-control">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Lama Service</label>
                                    <input type="text" name="lama_service" id="lama_service" class="form-control">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Keluhan</label>
                                    <textarea name="keluhan" id="keluhan" class="form-control"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <table class="table2">
                                <tr class="text-uppercase">
                                    <td>no antrean</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                                <tr class="text-uppercase {{ auth()->user()->role == 'pelanggan' ? 'd-none' : '' }}">
                                    <td>id service</td>
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
                                    <td>tanggal daftar</td>
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
                                <tr class="text-uppercase">
                                    <td>uang muka</td>
                                    <td>:</td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-12">
                            <div class="form-group mt-4">
                                <label for="">Detail Keluhan/Kerusakan</label>
                                <div id="detail_keluhan" class="p-3 rounded" style="background: #e9ecef;"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <form action="" method="post" id="formProgress">
                                <input type="hidden" name="id" id="id">
                                @csrf
                                @php
                                    $css = auth()->user()->role == 'teknisi' ? '' : 'readonly';
                                @endphp

                                @if (auth()->user()->role == 'teknisi')
                                    <div class="form-group">
                                        <label for="">Riwayat Pengerjaan (Teknisi)</label>
                                        <textarea class="form-control" name="riwayat" id="detail_riwayat2" {{ $css }} rows="5"></textarea>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="status"
                                                id="status" value="open">
                                            Selesai
                                        </label>
                                    </div>
                                    <div class="form-group mt-2">
                                        <button type="submit" class="btn btn-success">Update Progress</button>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label for="">Riwayat Pengerjaan (Teknisi)</label>
                                        <textarea class="form-control" name="riwayat" id="detail_riwayat1" readonly rows="5"></textarea>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
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
    <!-- Sweetalert2 -->
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <!-- JQuery mask -->
    <script src="{{ asset('assets/plugins/jquery-mask/jquery.mask.min.js') }}"></script>
    <!-- SELECT2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('assets/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script>
        function getkode() {
            $.get("{{ url('kode/service') }}",
                function(data, textStatus, jqXHR) {
                    $('#kode').val(data);
                },
            );
        }

        function no_antrean() {
            $.get("{{ url('kode/no_antrean_service') }}",
                function(data, textStatus, jqXHR) {
                    $('#no_antrean').val(data);
                },
            );
        }

        function formatRupiah(num) {
            var p = num.toFixed(0).split(".");
            return "Rp " + p[0].split("").reverse().reduce(function(acc, num, i, orig) {
                return num + (num != "-" && i && !(i % 3) ? "." : "") + acc;
            }, "");
        }


        $(document).ready(function() {
            var role = "{{ auth()->user()->role }}";
            $.fn.modal.Constructor.prototype._enforceFocus = function() {};

            var table = $('#table1').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    'type': 'GET',
                    'url': '{{ route('service') }}',
                    'data': {
                        status: '{{ $status }}',
                    },
                },
                columns: [
                    // {
                    //     data: null,
                    //     render: function(data, type, row, meta) {
                    //         return meta.row + meta.settings._iDisplayStart + 1;
                    //     },
                    // },
                    {
                        data: 'no_antrean',
                        name: 'no_antrean'
                    },
                    {
                        data: 'uang_muka',
                        render: function(data, type, row, meta) {
                            return formatRupiah(parseInt(row.uang_muka));
                        },
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'teknisi.name',
                        name: 'teknisi.name'
                    },
                    {
                        data: 'pelanggan.nama',
                        name: 'pelanggan.nama'
                    },
                    {
                        data: 'pelanggan.no_telp',
                        name: 'pelanggan.no_telp'
                    },
                    {
                        data: 'status',
                        render: function(data, type, row, meta) {
                            if (row.status == 'open') {
                                var css = 'success';
                            } else {
                                var css = 'danger';
                            }
                            return '<span class="badge badge-pill badge-' + css + '">' + row
                                .status + '</span>';
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
                    [1, "desc"]
                ]
            });

            $('#uang_muka').keyup(function(event) {
                // skip for arrow keys
                if (event.which >= 37 && event.which <= 40) return;
                // format number
                $(this).val(function(index, v) {
                    return v.toString()
                        .replace(/\D/g, "")
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                });
            });



            $('textarea').not('#detail_keluhan').summernote({
                height: 150,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    // ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    // ['height', ['height']],
                    // ['insert', ['link']],
                    // ['view', ['fullscreen', 'codeview']],
                ],
            });

            $('form select').select2({
                placeholder: 'Pilih',
                theme: 'bootstrap4',
                container: 'body'
            });

            // open modal tambah
            $('#tambah').click(function(e) {
                e.preventDefault();
                $('#modal').modal('show');
                $('#modal').find('.modal-title').text('Form Tambah');
                getkode();
                no_antrean();
            });

            // remove invalid in change
            $('select').on('change', function(e) {
                e.preventDefault();
                var id = $(this).val();
                if (id != null) {
                    $(this).removeClass('is-invalid');
                }
            });


            // reset all input in form after clicking modal
            $('#modal').on('hidden.bs.modal', function(e) {
                validator.resetForm();
                $("#modal").find('.is-invalid').removeClass('is-invalid');
                $(this)
                    .find("input,textarea,select")
                    .not('input[name="_token"],#kode')
                    .val('')
                    .end()
                    .find("input[type=checkbox], input[type=radio]")
                    .prop("checked", "")
                    .end();
            });

            // modal show 
            $('#modal').on('shown.bs.modal', function() {
                $(this).find('#nama').focus();
            });

            // edit
            table.on("click", "#edit", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#modal').modal('show');
                $('#modal').find('.modal-title').text('Form Edit');
                $('#modal form').show().find('#id').val(id);
                $.ajax({
                    url: "{{ route('edit.service') }}",
                    type: "GET",
                    dataType: "JSON",
                    cache: false,
                    data: {
                        'id': id
                    },
                    success: function(data) {
                        $('#modal form').find('#kode').val(data.kode);
                        $('#modal form').find('#no_antrean').val(data.no_antrean);
                        $('#modal form').find('#tanggal').val(data.tanggal);
                        $('#modal form').find('#pelanggans_id').val(data.pelanggans_id)
                            .change();
                        $('#modal form').find('#uang_muka').val(data.uang_muka);
                        $('#modal form').find('#jenis_antrean').val(data.jenis_antrean)
                            .change();
                        $('#modal form').find('#teknisi_id').val(data.teknisi_id).change();
                        $('#modal form').find('#jenis_barang').val(data.jenis_barang);
                        $('#modal form').find('#lama_service').val(data.lama_service);
                        $('#modal form').find('#keluhan').summernote("code", data.keluhan);
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

            // detail
            table.on("click", "#detail", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#modalDetail').modal('show');
                $('#modalDetail').find('.modal-title').text(
                    'Detail Service {{ strtoupper(config('app.name')) }}');
                $.ajax({
                    url: "{{ route('edit.service') }}",
                    type: "GET",
                    dataType: "JSON",
                    cache: false,
                    data: {
                        'id': id
                    },
                    success: function(res) {
                        var modal = $('#modalDetail');
                        var tb1 = modal.find('table').eq(0);
                        var tb2 = modal.find('table').eq(1);
                        tb1.find('tr').eq(0).find('td').eq(2).text(res.no_antrean);
                        tb1.find('tr').eq(1).find('td').eq(2).text(res.kode);
                        tb1.find('tr').eq(2).find('td').eq(2).text(res.pelanggan.nama);
                        tb1.find('tr').eq(3).find('td').eq(2).text(res.teknisi.name);
                        tb1.find('tr').eq(4).find('td').eq(2).text(res.tanggal);
                        tb2.find('tr').eq(0).find('td').eq(2).text(res.pelanggan.no_telp);
                        tb2.find('tr').eq(1).find('td').eq(2).text(res.jenis_barang);
                        tb2.find('tr').eq(2).find('td').eq(2).text(res.lama_service);
                        if (res.status == "open") {
                            var status =
                                '<span class="badge badge-pill badge-success">open</span>';
                        } else {
                            var status =
                                '<span class="badge badge-pill badge-danger">close</span>';
                        }
                        tb2.find('tr').eq(3).find('td').eq(2).html(status);
                        tb2.find('tr').eq(4).find('td').eq(2).text(formatRupiah(parseInt(res
                            .uang_muka)));

                        modal.find('#detail_keluhan').html(res.keluhan);
                        modal.find('#detail_riwayat1').summernote("code", res.riwayat)
                        modal.find('#detail_riwayat1').summernote('disable');
                        modal.find('#detail_riwayat2').summernote("code", res.riwayat);
                        modal.find('#id').val(res.id);
                        if (res.status == "close") {
                            modal.find('#detail_riwayat2').summernote('disable');
                            modal.find('button[type="submit"]').closest('.form-group').hide();
                            modal.find('#status').prop('checked', true).attr('disabled', true);
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
            });

            // deleted
            table.on("click", "#delete", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin hapus ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('delete.service') }}",
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
                                    title: 'Deleted!',
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
                                table.ajax.reload();
                            }

                        });

                    }
                })
            });

            // tambah data
            var validator = $("#modalForm").validate({
                rules: {
                    tanggal: {
                        required: true,
                    },
                    pelanggans_id: {
                        required: true,
                    },
                    // uang_muka: {
                    //     required: true,
                    // },
                    jenis_antrean: {
                        required: true,
                    },
                    teknisi_id: {
                        required: true,
                    },
                    jenis_barang: {
                        required: true,
                    },
                    lama_service: {
                        required: true,
                    },
                    keluhan: {
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
                    $.ajax({
                        url: "{{ route('submit.service') }}",
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
            });

            var validator = $("#formProgress").validate({
                rules: {
                    // tanggal: {
                    //     required: true,
                    // },
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
                    $.ajax({
                        url: "{{ route('update_progress.service') }}",
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
                                    $('.modal').modal('hide');
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
            });
        });
    </script>
@endsection
