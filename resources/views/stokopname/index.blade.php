@extends('layouts.app')

@section('title', 'Data StokOpname')

@section('css')
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
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
                    <h1 class="m-0 text-dark">StokOpname</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Stok Opname</li>
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
                            <button type="button" onclick="window.location.href='{{ route('stokopname.add') }}'"
                                class="btn btn-info ml-auto" data-toggle="collapse" data-target="#collapseTambah"
                                aria-expanded="false" aria-controls="collapseTambah">
                                </span><i class="fa fa-plus" aria-hidden="true"></i> Tambah
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Barang</th>
                                        <th>Stok Nyata</th>
                                        <th>Stok Sistem</th>
                                        <th>Selisih</th>
                                        <th>User Created</th>
                                        <th>Date Added</th>
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
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Barang</label>
                                    <select name="barang" id="barang" class="form-control">
                                        <option value="" disabled selected>Pilih</option>
                                        @foreach ($brg as $item)
                                            <option data-barcode="{{ $item->barcode }}" value="{{ $item->id }}">
                                                {{ $item->nama_barang }} (Rp
                                                {{ number_format($item->harga_jual, 0, ',', '.') }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">Stok Comp</label>
                                    <input type="number" name="stok_comp" id="stok_comp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">Stok Nyata</label>
                                    <input type="number" name="stok_nyata" id="stok_nyata" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">Selisih</label>
                                    <input type="number" name="selisih" id="selisih" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Simpan</button>
                    </div>
                </form>
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
            var table = $('table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('json.stokopname') }}",
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                    },
                    {
                        data: 'barang.nama_barang',
                        name: 'barang.nama_barang'
                    },
                    {
                        data: 'stok_nyata',
                        name: 'stok_nyata'
                    },
                    {
                        data: 'stok_comp',
                        name: 'stok_comp'
                    },
                    {
                        data: 'selisih',
                        render: function(data, type, row, meta) {
                            return parseInt(row.stok_nyata) - parseInt(row.stok_comp);
                        },
                    },
                    {
                        data: 'user.name',
                        render: function(data, type, row, meta) {
                            return row.user.name + " (<strong>" + row.user.role + "</strong>)";
                        },
                    },
                    {
                        data: 'created_at',
                        render: function(data, type, row, meta) {
                            var myDate = new Date(row.created_at);
                            var displayDate = myDate.getMonth() + 1 + '/' + myDate.getDate() + '/' +
                                myDate.getFullYear() + ' ' + formatAMPM(myDate);
                            return displayDate;
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
                    [6, "desc"]
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
                validator.resetForm();
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

            $('#stok_nyata').on('keyup', function(e) {
                e.preventDefault();
                var val = $(this).val();
                var selisih = $('#selisih');
                var hitung = parseInt(val) - parseInt(stok_sistem);
                if (isNaN(hitung)) {
                    hitung = 0;
                }
                selisih.val(hitung);
            });

            // modal show 
            $('#modal').on('shown.bs.modal', function() {
                $(this).find('#nama').select();
            });

            // open modal edit
            table.on("click", "#edit", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#modal').modal('show');
                $('#modal').find('.modal-title').text('Form Edit');
                $('#modal form').show().find('#id').val(id);
                $.ajax({
                    url: "{{ route('stokopname.edit') }}",
                    type: "GET",
                    dataType: "JSON",
                    cache: false,
                    data: {
                        'id': id
                    },
                    success: function(data) {
                        stok_sistem = data.stok_comp;
                        $('#modal form').find('#barang').val(data.barangs_id).change().attr(
                            'disabled', true);
                        $('#modal form').find('#stok_comp').val(data.stok_comp);
                        $('#modal form').find('#stok_nyata').val(data.stok_nyata);
                        var selisih = parseInt(data.stok_nyata) -
                            parseInt(data.stok_comp);
                        $('#modal form').find('#selisih').val(selisih);
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
                            url: "{{ route('stokopname.update') }}",
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
