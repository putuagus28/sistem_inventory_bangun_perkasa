@extends('layouts.app')

@section('title', $title)

@section('css')
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
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
                    <div class="card">
                        <div class="card-header d-flex flex-row align-items-center">
                            <h3 class="card-title"><i class="fa fa-list" aria-hidden="true"></i></h3>
                            <a href="{{ url('master/akun/saldo') }}" class="btn btn-warning ml-auto text-white">Tambah
                                Saldo</a>
                            <button class="btn btn-dark ml-1" id="tambah"><i class="fa fa-plus-circle"
                                    aria-hidden="true"></i>
                                Tambah Akun</button>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No Reff</th>
                                        <th>Nama Akun</th>
                                        <th>Saldo</th>
                                        <th>Date Added</th>
                                        <th>Added By</th>
                                        <th width="80" class="text-center"><i class="fa fa-cogs" aria-hidden="true"></i>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        use App\Akun;
                                        
                                        function formatUang($uang)
                                        {
                                            return 'Rp ' . number_format($uang, 0, ',', '.');
                                        }
                                    @endphp
                                    @foreach ($kategori as $no => $item)
                                        <tr>
                                            <td colspan="9" class="">
                                                <strong>{{ $item }}</strong>
                                            </td>
                                        </tr>
                                        @php
                                            $data = Akun::with('users')
                                                ->where('kategori', $item)
                                                ->orderBy('no_reff', 'asc')
                                                ->get();
                                        @endphp
                                        @foreach ($data as $v)
                                            @php
                                                $no_reff = $v->no_reff;
                                            @endphp
                                            <tr>
                                                <td>{{ $no_reff }}</td>
                                                <td>{{ $v->akun }}</td>
                                                <td>{{ formatUang($v->saldo_awal) }}</td>
                                                <td>{{ $v->created_at }}</td>
                                                <td>{{ $v->users->name }}</td>
                                                <td>
                                                    {!! '<a href="javascript:void(0)" data-id="' .
                                                        $v->id .
                                                        '" class="text-success mx-1" id="edit"><i class="fas fa-edit"></i></a>' !!}
                                                    {!! '<a href="javascript:void(0)" data-id="' .
                                                        $v->id .
                                                        '" class="text-danger mx-1" id="hapus"><i class="fa fa-trash" aria-hidden="true"></i></a>' !!}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
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
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Tipe Akun</label>
                                    <select name="kategori" id="kategori" class="form-control">
                                        <option value="" selected disabled>Pilih</option>
                                        @foreach ($kategori as $item)
                                            <option value="{{ $item }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-3">
                                <div class="form-group">
                                    <label for="">No Akun</label>
                                    <input type="number" name="no_reff_awal" id="no_reff_awal" min="1"
                                        class="form-control" placeholder="1">
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="form-group">
                                    <label for="">&nbsp;</label>
                                    <input type="number" name="no_reff" id="no_reff" class="form-control"
                                        placeholder="1000">
                                </div>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Nama Akun</label>
                                    <input type="text" name="akun" id="akun" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-dark">Simpan</button>
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
    <!-- Sweetalert2 -->
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <!-- JQuery mask -->
    <script src="{{ asset('assets/plugins/jquery-mask/jquery.mask.min.js') }}"></script>
    <script>
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function money(uang = 0) {
            return uang.toString()
                .replace(/\D/g, "")
                .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }


        $(document).ready(function() {
            var role = "{{ auth()->user()->role }}";
            $.fn.modal.Constructor.prototype._enforceFocus = function() {};

            $('#saldo_awal').keyup(function(event) {
                // skip for arrow keys
                if (event.which >= 37 && event.which <= 40) return;
                // format number
                $(this).val(function(index, v) {
                    return v.toString()
                        .replace(/\D/g, "")
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                });
            }).blur(function(e) {
                e.preventDefault();
                $(this).val(function(index, v) {
                    return v.toString()
                        .replace(/\D/g, "")
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                });
            });

            // open modal tambah
            $('#tambah').click(function(e) {
                e.preventDefault();
                $('#modal').modal('show');
                $('#modal').find('.modal-title').text('Form Tambah');
                if (role == "kepala_toko") {
                    $('#modal form').show().find('#sec_saldo').show();
                } else {
                    $('#modal form').show().find('#sec_saldo').hide();
                }
            });

            $('#kategori').change(function(e) {
                e.preventDefault();
                var arr = ['Activa Tetap', 'Activa Lancar', 'Kewajiban', 'Modal', 'Pendapatan', 'Beban'];
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
                $('#modal #v_detail').addClass('d-none');
            });

            // modal show 
            $('#modal').on('shown.bs.modal', function() {
                $(this).find('#nama').focus();
            });

            // delete data
            $('body').on("click", "#hapus", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin untuk menghapus akun ini ?',
                    showDenyButton: true,
                    showCancelButton: false,
                    showConfirmButton: true,
                    confirmButtonText: `Hapus`,
                    denyButtonText: `Batal`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('delete.akun') }}",
                            type: "GET",
                            dataType: "JSON",
                            data: {
                                'id': id
                            },
                            cache: false,
                            success: function(response) {
                                if (response.status) {
                                    // Swal.fire({
                                    //     icon: 'success',
                                    //     title: response.message,
                                    //     showCancelButton: false,
                                    //     showConfirmButton: true
                                    // }).then(function() {
                                    // table.ajax.reload();
                                    window.location.href = "./akun";
                                    // });
                                }
                            },
                            error: function(response) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Opps!',
                                    text: 'server error!'
                                });
                            }
                        });
                    }
                })
            });

            // open modal edit
            $('body').on("click", "#edit", function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#modal #v_detail').removeClass('d-none');
                $('#modal').modal('show');
                $('#modal').find('.modal-title').text('Form Edit');
                $('#modal form').show().find('#id').val(id);
                if (role == "kepala_toko") {
                    $('#modal form').show().find('#sec_saldo').show();
                } else {
                    $('#modal form').show().find('#sec_saldo').hide();
                }
                $.ajax({
                    url: "{{ route('edit.akun') }}",
                    type: "GET",
                    dataType: "JSON",
                    cache: false,
                    data: {
                        'id': id
                    },
                    success: function(data) {
                        const arr = ['Activa Tetap', 'Activa Lancar', 'Kewajiban', 'Modal'];
                        var no_reff = data.no_reff.split("-");
                        $('#modal form').find('#no_reff_awal').val(no_reff[0]);
                        $('#modal form').find('#no_reff').val(no_reff[1]);
                        $('#modal form').find('#kategori').val(data.kategori).change();
                        $('#modal form').find('#akun').val(data.akun);
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
                    no_reff_awal: {
                        required: true,
                    },
                    no_reff: {
                        required: true,
                    },
                    kategori: {
                        required: true,
                    },
                    akun: {
                        required: true,
                    },
                    // saldo_awal: {
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
                    if (id == "") {
                        $.ajax({
                            url: "{{ route('insert.akun') }}",
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
                                        // table.ajax.reload();
                                        window.location.href = "./akun";
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        showCancelButton: false,
                                        showConfirmButton: true
                                    }).then(function() {
                                        // table.ajax.reload();
                                        window.location.href = "./akun";
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
                    } else {
                        $.ajax({
                            url: "{{ route('update.akun') }}",
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
                                        // table.ajax.reload();
                                        window.location.href = "./akun";
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        showCancelButton: false,
                                        showConfirmButton: true
                                    }).then(function() {
                                        // table.ajax.reload();
                                        window.location.href = "./akun";
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
