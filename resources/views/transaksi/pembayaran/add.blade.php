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
        .ttl {
            font-size: 80px;
        }

        .tableFixHead {
            overflow: auto;
        }

        .tableFixHead thead th {
            position: sticky;
            top: 0;
            padding: 0.75rem;
            background: white;
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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
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
                            <button type="button" onclick="window.location.href='{{ route('transaksi.pembayaran') }}'"
                                class="btn btn-info font-weight-bolder mr-auto " data-toggle="collapse"
                                data-target="#collapseTambah" aria-expanded="false" aria-controls="collapseTambah">
                                </span><i class="fa fa-angle-left" aria-hidden="true"></i> Kembali
                            </button>
                        </div>
                        <div class="card-body">
                            {{-- detail data service --}}
                            <div class="row">
                                <div class="col-12 my-auto">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="">Pilih Data Service</label>
                                                <select name="services_id" id="services_id"
                                                    class="form-control form-control-sm">
                                                    <option value="">Pilih</option>
                                                    @foreach ($service as $item)
                                                        <option data-barcode="" value="{{ $item->id }}">
                                                            {{ $item->no_antrean }} -
                                                            {{ $item->pelanggan->nama }} -
                                                            {{ $item->pelanggan->no_telp }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group row">
                                                <label for="" class="col-sm-3 col-form-label">No Tiket</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" readonly name="d_no_antrean"
                                                        id="d_no_antrean">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="" class="col-sm-3 col-form-label">Nama Admin</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" readonly name="d_admin"
                                                        id="d_admin">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group row">
                                                <label for="" class="col-sm-3 col-form-label">Nama Pelanggan</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" readonly name="d_plg"
                                                        id="d_plg">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="" class="col-sm-3 col-form-label">Alamat</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" readonly name="d_alamat"
                                                        id="d_alamat">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h6 class="mt-3 bg-dark p-2 cartb_title rounded">
                                <i class="fa fa-cart-plus mr-2" aria-hidden="true"></i>
                                Cart Pembayaran
                            </h6>
                            {{-- barang --}}
                            <div class="row">
                                <div class="col-12 col-sm-6 my-auto">
                                    <form action="" method="post" id="formCartBarang">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12 col-md-8">
                                                <div class="form-group">
                                                    <label for="">Pilih Barang</label>
                                                    <select name="jasa_barang" id="jasa_barang1"
                                                        class="form-control form-control-sm">
                                                        <option value="" disabled selected>Pilih</option>
                                                        @foreach ($brg as $item)
                                                            <option data-barcode="" value="{{ $item->id }}">
                                                                {{ $item->nama }} - (Rp
                                                                {{ number_format($item->harga, 0, ',', '.') }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <div class="form-group">
                                                    <label for="">Qty</label>
                                                    <input type="number" min="1" name="qty" id="qty"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <div class="form-group">
                                                    <label for="" class="text-white">button</label><br>
                                                    <button type="submit" class="btn btn-info">
                                                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                                <div class="col-12 col-sm-6 my-auto">
                                    <form action="" method="post" id="formCartJasa">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12 col-md-8">
                                                <div class="form-group">
                                                    <label for="">Pilih Jasa</label>
                                                    <select name="jasa_barang" id="jasa_barang2"
                                                        class="form-control form-control-sm">
                                                        <option value="" disabled selected>Pilih</option>
                                                        @foreach ($jasa as $item)
                                                            <option data-barcode="" value="{{ $item->id }}">
                                                                {{ $item->nama }} - (Rp
                                                                {{ number_format($item->harga, 0, ',', '.') }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <div class="form-group">
                                                    <label for="">Qty</label>
                                                    <input type="number" min="1" name="qty" id="qty"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <div class="form-group">
                                                    <label for="" class="text-white">button</label><br>
                                                    <button type="submit" class="btn btn-info">
                                                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="table-responsive tableFixHead" style="max-height: 300px">
                                <table class="table table-striped" id="cartBarang">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>No</th>
                                            <th>Barang/Jasa</th>
                                            <th>Harga</th>
                                            <th>Qty</th>
                                            <th>Harga Jual</th>
                                            <th class="text-right">Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-4 ml-auto">
                                    <form method="POST" id="formBayar" class="d-none p-4">
                                        @csrf
                                        <div class="form-group">
                                            <label for="">Total item</label>
                                            <input type="number" name="total_item" readonly id="total_item"
                                                class="form-control" placeholder="Rp 0" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Total Jasa</label>
                                            <input type="number" name="total_jasa" readonly id="total_jasa"
                                                class="form-control" placeholder="Rp 0" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Total Tagihan</label>
                                            <input type="text" name="total_tagihan" readonly id="total_tagihan"
                                                class="form-control" placeholder="Rp 0" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Uang Muka</label>
                                            <input type="text" name="uang_muka" readonly id="uang_muka"
                                                class="form-control" placeholder="Rp 0" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Sisa Tagihan</label>
                                            <input type="text" name="sisa_tagihan" readonly id="sisa_tagihan"
                                                class="form-control" placeholder="Rp 0" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Pilih Tanggal</label>
                                            <input type="date" name="tgl_bayar" class="form-control" required>
                                        </div>
                                        <div class="w-100"></div>
                                        <button type="button" class="btn btn-danger btn-lg btn-block" id="batalkan">
                                            <i class="fa fa-window-close" aria-hidden="true"></i> BATALKAN
                                        </button>

                                        <button type="submit" class="btn btn-info btn-lg btn-block">
                                            <i class="fa fa-check" aria-hidden="true"></i> SIMPAN
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/. container-fluid -->
    </section>

@endsection

@section('script')
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
        const rupiah = (number) => {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR"
            }).format(number);
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

        function ucwords(str, force) {
            str = force ? str.toLowerCase() : str;
            return str.replace(/(\b)([a-zA-Z])/g,
                function(firstLetter) {
                    return firstLetter.toUpperCase();
                });
        }


        $(document).scannerDetection({
            timeBeforeScanTest: 200, // wait for the next character for upto 200ms
            avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms
            // preventDefault: true,
            endChar: [13],
            onComplete: function(barcode, qty) {
                    validScan = true;
                    if ($("#barang option[data-barcode='" + barcode + "']").length == 1) {
                        $("#barang option[data-barcode='" + barcode + "']").attr("selected", "selected");
                        $("#barang").trigger("change");
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Opps!',
                            text: 'Barcode tidak terdaftar'
                        });
                    }
                } // main callback function	,
                ,
            onError: function(string, qty) {
                console.log(string);
            }
        });

        function listCart() {
            $.get("{{ route('listcart.pembayaran') }}",
                function(data, textStatus, jqXHR) {
                    $('table#cartBarang tbody').html(data.html);
                    $('.ttl').text(data.total);
                    $('#total_item').val(data.total_barang);
                    $('#total_jasa').val(data.total_jasa);
                    $('#total_tagihan').val(data.total_tagihan);
                    $('#uang_muka').val(data.uang_muka);
                    $('#sisa_tagihan').val(data.sisa_tagihan);
                    if (data.status) {
                        $('#formBayar').removeClass('d-none');
                    } else {
                        $('#formBayar').addClass('d-none');
                    }
                },
                "JSON"
            );
        }

        $(document).ready(function() {
            $.fn.modal.Constructor.prototype._enforceFocus = function() {};
            listCart();

            // remove invalid in change
            $('select').on('change', function(e) {
                e.preventDefault();
                var id = $(this).val();
                if (id != null) {
                    $(this).removeClass('is-invalid');
                }
            });

            $('select').select2({
                theme: 'bootstrap4',
                width: '100%'
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
                            stok_sistem = data;
                            $('#qty_lama').val(data);
                        },
                        "JSON"
                    );
                }
            });

            $('select#services_id').change(function(e) {
                e.preventDefault();
                var id = $(this).val();
                if (id != null || id != "") {
                    $(this).removeClass('is-invalid');
                    $.get("{{ url('detail_service') }}", {
                            'id': id
                        },
                        function(res, textStatus, jqXHR) {
                            $('#d_no_antrean').val(res.no_antrean);
                            $('#uang_muka').val(res.uang_muka);
                            $('#d_admin').val(res.users.name);
                            $('#d_plg').val(res.pelanggan.nama);
                            $('#d_alamat').val(res.pelanggan.alamat);
                        },
                        "JSON"
                    );

                    // set uang muka
                    $.ajax({
                        type: "POST",
                        url: "{{ route('setUangMuka.pembayaran') }}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'id': id,
                        },
                        dataType: "JSON",
                        success: function(res) {
                            listCart();
                        }
                    });
                }
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

            $('#batalkan').on('click', function() {
                if (confirm('Klik Ok untuk membatalkan semua transaksi ?')) {
                    $.get("{{ route('removeall.pembayaran') }}",
                        function(data, textStatus, jqXHR) {
                            if (data.status) {
                                listCart();
                            }
                        },
                        "JSON"
                    );
                }
            });

            $('table').on('click', '#delete', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var cart = $(this).data('cart');
                if (confirm('Klik Ok untuk membatalkan item ini')) {
                    $.get("{{ route('removeone.pembayaran') }}", {
                            'id': id,
                            'cart': cart,
                        },
                        function(data, textStatus, jqXHR) {
                            if (data.status) {
                                listCart();
                            }
                        },
                        "JSON"
                    );
                }
            });

            // tambah cart penjualan
            var validator1 = $("#formCartBarang").validate({
                rules: {
                    jasa_barang: {
                        required: true,
                    },
                    qty: {
                        required: true,
                        min: 1
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
                    var text = $('#formCartBarang').find('button[type="submit"]').html();
                    $('#formCartBarang').find('button[type="submit"]').text('Loading...').attr(
                        'disabled', true);
                    $.ajax({
                        url: "{{ route('addtocart.pembayaran') }}",
                        type: "POST",
                        dataType: "JSON",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: new FormData(form),
                        success: function(response) {
                            if (response.status) {
                                listCart();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Opps!',
                                    text: response.message
                                });
                            }
                            $('#formCartBarang')
                                .find("input,textarea,select")
                                .not('input[name="_token"]')
                                .val('')
                                .end()
                                .find("input[type=checkbox], input[type=radio]")
                                .prop("checked", "")
                                .end();
                            $("#barang").val('').change();
                            $("#formCartBarang").validate().resetForm();
                            $('#formCartBarang').find('button[type="submit"]').html(text)
                                .attr(
                                    'disabled', false);
                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Opps!',
                                text: 'server error!'
                            });
                            console.log(response);
                            $('#formCartBarang').find('button[type="submit"]').html(text)
                                .attr(
                                    'disabled', false);
                        }
                    });
                }
            });

            var validator1 = $("#formCartJasa").validate({
                rules: {
                    jasa_barang: {
                        required: true,
                    },
                    qty: {
                        required: true,
                        min: 1
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
                    var text = $('#formCartJasa').find('button[type="submit"]').html();
                    $('#formCartJasa').find('button[type="submit"]').text('Loading...').attr(
                        'disabled', true);
                    $.ajax({
                        url: "{{ route('addtocart.pembayaran') }}",
                        type: "POST",
                        dataType: "JSON",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: new FormData(form),
                        success: function(response) {
                            if (response.status) {
                                listCart();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Opps!',
                                    text: response.message
                                });
                            }
                            $('#formCartJasa')
                                .find("input,textarea,select")
                                .not('input[name="_token"]')
                                .val('')
                                .end()
                                .find("input[type=checkbox], input[type=radio]")
                                .prop("checked", "")
                                .end();
                            $("#barang").val('').change();
                            $("#formCartJasa").validate().resetForm();
                            $('#formCartJasa').find('button[type="submit"]').html(text)
                                .attr(
                                    'disabled', false);
                        },
                        error: function(response) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Opps!',
                                text: 'server error!'
                            });
                            console.log(response);
                            $('#formCartJasa').find('button[type="submit"]').html(text)
                                .attr(
                                    'disabled', false);
                        }
                    });
                }
            });

            // finish cart
            var validator3 = $("#formBayar").validate({
                rules: {
                    tgl_bayar: {
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
                    $('#kembalian').select();
                    var text = $('#formBayar').find('button[type="submit"]').text();
                    var text_b = $('#formCartBaran,#formCartJasa').find('button[type="submit"]').text();
                    $('#formBayar,#formCartBaran,#formCartJasa').find('button[type="submit"]').attr(
                            'disabled', true)
                        .text(
                            'Loading...');
                    Swal.fire({
                        title: 'Yakin untuk menyimpan ?',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Simpan!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route('finish.pembayaran') }}",
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
                                            title: response.message
                                        });
                                        listCart();
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Opps!',
                                            text: response.message
                                        });
                                    }
                                    $('#formBayar')[0].reset();
                                    invoice();
                                    $('#formBayar').find('button[type="submit"]')
                                        .text(text)
                                        .attr(
                                            'disabled', false);
                                    $('#formCartBarang,#formCartJasa').find(
                                            'button[type="submit"]').text(text_b)
                                        .attr(
                                            'disabled', false);
                                },
                                error: function(response) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Opps!',
                                        text: 'server error!'
                                    });
                                    console.log(response);
                                    $('#formBayar').find('button[type="submit"]')
                                        .text(text)
                                        .attr(
                                            'disabled', false);
                                    $('#formCartBarang,#formCartJasa').find(
                                            'button[type="submit"]').text(text_b)
                                        .attr(
                                            'disabled', false);
                                }
                            });
                        } else {
                            $('#formCartBarang,#formCartJasa').find(
                                    'button[type="submit"]').text('Tambah')
                                .attr(
                                    'disabled', false);
                            $('#formBayar').find(
                                    'button[type="submit"]').text(text_b)
                                .attr(
                                    'disabled', false);
                        }
                        // window.location.reload();
                    });
                }
            });

        });
    </script>
@endsection
