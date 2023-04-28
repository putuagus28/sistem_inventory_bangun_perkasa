@extends('layouts.app')

@section('title', $title)

@section('css')
    <style>
        .bg-success {
            background-color: #16a085 !important;
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
                    {!! session('info') !!}
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
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            @if ($role == 'pelanggan')
                <!-- Info boxes -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 col-sm-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $t_service }}</h3>
                                        <p>Total Service Anda</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-cogs" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-sm-6">
                                <div class="small-box bg-dark">
                                    <div class="inner">
                                        <h3>{{ $t_done }}</h3>
                                        <p>Total Riwayat Service</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-file-alt" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-sm-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $t_done }}</h3>
                                        <p>Total Service Selesai</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 col-sm-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $t_on_progress }}</h3>
                                        <p>Total Service OnProgress</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-hourglass-half" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($role == 'teknisi')
                <div class="row">

                    <div class="col-6 col-sm-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $t_service }}</h3>
                                <p>Total Antrean Tiket Anda</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-cogs" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-6">
                        <div class="small-box bg-purple">
                            <div class="inner">
                                <h3>{{ $t_riwayat }}</h3>
                                <p>Total Riwayat Service</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-history" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>


                </div>
            @else
                <!-- Info boxes -->
                <div class="row">

                    <div class="col-6 col-sm-6 col-md-3">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $t_user }}</h3>
                                <p>Total Users</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-user" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-6 col-md-3">
                        <div class="small-box bg-dark">
                            <div class="inner">
                                <h3>{{ $t_service }}</h3>
                                <p>Total Service</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-cogs text-muted" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-6 col-md-3">
                        <div class="small-box bg-purple">
                            <div class="inner">
                                <h3>{{ $t_barang }}</h3>
                                <p>Total Barang</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-box-open" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-sm-6 col-md-3">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $t_pelanggan }}</h3>
                                <p>Total Pelanggan</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">

                    <div class="col-12 col-sm-6 col-md-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>Rp {{ number_format($pendapatan, 0, ',', '.') }}</h3>
                                <p>Total Pendapatan</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-cart-plus" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>Rp {{ number_format($pengeluaran, 0, ',', '.') }}</h3>
                                <p>Total Pengeluaran</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-cart-arrow-down" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>

                </div>
            @endif


            @if ($role != 'pelanggan')
                {{-- Chart --}}
                <div class="row">
                    <div class="col-12 col-md-8">
                        <div class="card card-dark">
                            <!-- /.card-header -->
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold"><i class="fas fa-chart-bar"></i> <span
                                        id="chart_title"></span> Grafik Pendapatan & Pengeluaran Tahun
                                    {{ date('Y') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="chart">
                                    <canvas id="Chart"
                                        style="min-height: 350px; height: 350px; max-height: 350px;max-width: 100%;"></canvas>
                                </div>
                            </div>
                            <!-- ./card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card card-dark">
                            <!-- /.card-header -->
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold"><i class="fas fa-chart-pie"></i> <span
                                        id="chart_title"></span> Presentase Barang Terlaris
                                    {{ date('Y') }}</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="Chart2"
                                    style="min-height: 350px; height: 350px; max-height: 350px;max-width: 100%;"></canvas>
                            </div>
                            <!-- ./card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
            @endif


        </div>
        <!--/. container-fluid -->
    </section>
@endsection

@section('script')
    <!-- ChartJS -->
    <script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var url = "{{ route('chart') }}";
            var Title = [];
            var Title2 = [];
            var Total = [];
            var Total2 = [];
            var Bulan = [];
            $.get(url, function(response) {
                $.each(response, function(index, data) {
                    Title.push(data.title);
                    Total.push(data.total);
                    Title2.push(data.title2);
                    Total2.push(data.total2);
                    Bulan.push(data.bulan);
                });

                var areaChartData = {
                    labels: Bulan[0],
                    datasets: [{
                            label: Title,
                            backgroundColor: '#16a085',
                            borderColor: '#16a085',
                            pointRadius: false,
                            pointColor: '#16a085',
                            pointStrokeColor: '#16a085',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: '#16a085',
                            data: Total[0]
                        },
                        {
                            label: Title2,
                            backgroundColor: '#dc3545',
                            borderColor: '#dc3545',
                            pointRadius: false,
                            pointColor: '#dc3545',
                            pointStrokeColor: '#c1c7d1',
                            pointHighlightFill: '#fff',
                            pointHighlightStroke: 'rgba(220,220,220,1)',
                            data: Total2[0]
                        },
                    ]
                }
                //-------------
                //- BAR CHART -
                //-------------
                var barChartCanvas = $('#Chart').get(0).getContext('2d');
                var barChartData = $.extend(true, {}, areaChartData);
                var temp0 = areaChartData.datasets[0]
                var temp1 = areaChartData.datasets[1]
                barChartData.datasets[0] = temp0
                barChartData.datasets[1] = temp1

                var barChartOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    datasetFill: false,
                    // tooltips: {
                    //     callbacks: {
                    //         label: function(tooltipItem, data) {
                    //             return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g,
                    //                 '$&,');
                    //         }
                    //     }
                    // }

                }
                Chart.scaleService.updateScaleDefaults('linear', {
                    ticks: {
                        callback: function(tick) {
                            return 'Rp' + tick.toLocaleString();
                        }
                    }
                });
                Chart.defaults.global.tooltips.callbacks.label = function(tooltipItem, data) {
                    var dataset = data.datasets[tooltipItem.datasetIndex];
                    var datasetLabel = dataset.label || '';
                    return datasetLabel + ": Rp " + dataset.data[tooltipItem.index].toLocaleString();
                };

                new Chart(barChartCanvas, {
                    type: 'bar',
                    data: barChartData,
                    options: barChartOptions
                });

            });

        });
    </script>

    <script>
        function getRandomColor() {
            return '#' + parseInt(Math.random() * 0xffffff).toString(16);
        }

        $.getJSON("{{ route('chart2') }}", function(data) {
            //array untuk chart label dan chart data
            var isi_labels = [];
            var isi_data = [];
            var TotalJml = 0;
            //menghitung total jumlah item
            data.forEach(function(obj) {
                TotalJml += Number(obj["total"]);
            });


            //push ke dalam array isi label dan isi data
            var JmlItem = 0;
            var color = [];
            $(data).each(function(i) {
                isi_labels.push(data[i].bulan);
                //jml item dalam persentase
                isi_data.push(((data[i].total / TotalJml) * 100).toFixed(2));
                color.push(getRandomColor());
            });


            //deklarasi chartjs untuk membuat grafik 2d di id mychart   
            var ctx = document.getElementById('Chart2').getContext('2d');

            var myPieChart = new Chart(ctx, {
                //chart akan ditampilkan sebagai pie chart
                type: 'pie',
                data: {
                    //membuat label chart
                    labels: isi_labels,
                    datasets: [{
                        label: 'Data Kematian',
                        //isi chart
                        data: isi_data,
                        //membuat warna pada chart
                        backgroundColor: color,
                        //borderWidth: 0, //this will hide border
                    }]
                },
                options: {
                    //konfigurasi tooltip
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var dataset = data.datasets[tooltipItem.datasetIndex];
                                var labels = data.labels[tooltipItem.index];
                                var currentValue = dataset.data[tooltipItem.index];
                                return labels + ": " + currentValue + " %";
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
