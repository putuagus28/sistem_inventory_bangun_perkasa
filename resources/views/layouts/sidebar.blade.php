<aside class="main-sidebar sidebar-dark-info elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url(auth()->user()->role == 'mahasiswa' ? 'setprivilege' : '') }}" class="brand-link bg-info">
        <img src="{{ asset('assets/dist/img/favicon-32x32.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-bold">{{ config('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image my-auto">
                @if (auth()->user()->foto == null)
                    <i class="fas fa-user-circle text-white fa-3x"></i>
                @else
                    <img src="{{ asset('users/' . auth()->user()->foto . '') }}" class="brand-image img-circle">
                @endif
            </div>
            <div class="info">
                <a href="#" class="d-block font-weight-bold">
                    @auth
                        @if (Auth::guard('user')->check())
                            {{ auth()->user()->name }}
                        @elseif(Auth::guard('pelanggan')->check())
                            {{ auth()->user()->nama }}
                        @endif
                    @endauth
                </a>
                <span class="badge badge-pill badge-info">
                    {{ auth()->user()->role == 'mahasiswa' ? Session::get('jabatan') : auth()->user()->role }}
                </span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item has-treeview {{ request()->is('dashboard') ? 'menu-open' : '' }}">
                    <a href="{{ url('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @if (in_array(auth()->user()->role, ['admin']))
                    <li class="nav-item {{ request()->is(['master/*']) ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('laporan/*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file"></i>
                            <p>
                                Master Data
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('master/akun') }}"
                                    class="nav-link {{ request()->is('master/akun') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Data Akun</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('master/pelanggan') }}"
                                    class="nav-link {{ request()->is('master/pelanggan') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Pelanggan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('master/jenisjasa') }}"
                                    class="nav-link {{ request()->is('master/jenisjasa') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Jenis Jasa</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('master/barang') }}"
                                    class="nav-link {{ request()->is('master/barang') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Barang</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('master/user') }}"
                                    class="nav-link {{ request()->is('master/user') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>User</p>
                                </a>
                            </li>
                        </ul>
                    </li>


                    <li class="nav-item has-treeview">
                        <a href="{{ url('jurnal') }}" class="nav-link {{ request()->is('jurnal') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-book"></i>
                            <p>
                                Jurnal Umum
                            </p>
                        </a>
                    </li>

                    <li class="nav-item has-treeview {{ request()->is('service/data') ? 'menu-open' : '' }}">
                        <a href="{{ url('service/data') }}"
                            class="nav-link {{ request()->is('service/data') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-edit"></i>
                            <p>
                                Permintaan Service
                            </p>
                        </a>
                    </li>
                    {{-- transaksi --}}
                    <li class="nav-item {{ request()->is('transaksi/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('transaksi/*') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-edit"></i>
                            <p>
                                Transaksi
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('transaksi/pembayaran') }}"
                                    class="nav-link {{ request()->is('transaksi/pembayaran') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Pembayaran</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('transaksi/pengeluaran') }}"
                                    class="nav-link {{ request()->is('transaksi/pengeluaran') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Pengeluaran</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif


                @if (in_array(auth()->user()->role, ['admin', 'owner']))
                    <li class="nav-item {{ request()->is('laporan/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('laporan/*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-print"></i>
                            <p>
                                Laporan Service
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('laporan/service') }}"
                                    class="nav-link {{ request()->is('laporan/service') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Data Service</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item {{ request()->is('laporan_keuangan/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('laporan_keuangan/*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-print"></i>
                            <p>
                                Laporan Keuangan
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('laporan_keuangan/jurnal') }}"
                                    class="nav-link {{ request()->is('laporan_keuangan/jurnal') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Laporan Jurnal Umum</p>
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                                <a href="{{ url('laporan_keuangan/pemasukan') }}"
                                    class="nav-link {{ request()->is('laporan_keuangan/pemasukan') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Laporan Pemasukan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('laporan_keuangan/pengeluaran') }}"
                                    class="nav-link {{ request()->is('laporan_keuangan/pengeluaran') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Laporan Pengeluaran</p>
                                </a>
                            </li> --}}
                            <li class="nav-item">
                                <a href="{{ url('laporan_keuangan/buku_besar') }}"
                                    class="nav-link {{ request()->is('laporan_keuangan/buku_besar') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Laporan Buku Besar</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('laporan_keuangan/neraca_saldo') }}"
                                    class="nav-link {{ request()->is('laporan_keuangan/neraca_saldo') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Laporan Neraca Saldo</p>
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                                <a href="{{ url('laporan_keuangan/neraca') }}"
                                    class="nav-link {{ request()->is('laporan_keuangan/neraca') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Laporan Neraca</p>
                                </a>
                            </li> --}}
                            <li class="nav-item">
                                <a href="{{ url('laporan_keuangan/labarugi') }}"
                                    class="nav-link {{ request()->is('laporan_keuangan/labarugi') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Laporan Laba Rugi</p>
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                                <a href="{{ url('laporan_keuangan/perubahan_modal') }}"
                                    class="nav-link {{ request()->is('laporan_keuangan/perubahan_modal') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Laporan Perubahan Modal</p>
                                </a>
                            </li> --}}
                            {{-- <li class="nav-item">
                                <a href="{{ url('laporan_keuangan/jurnal_penutup') }}"
                                    class="nav-link {{ request()->is('laporan_keuangan/jurnal_penutup') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Laporan Jurnal Penutup</p>
                                </a>
                            </li> --}}
                            <li class="nav-item">
                                <a href="{{ url('laporan_keuangan/arus_kas') }}"
                                    class="nav-link {{ request()->is('laporan_keuangan/arus_kas') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Laporan Arus Kas</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if (in_array(auth()->user()->role, ['teknisi']))
                    <li class="nav-item has-treeview {{ request()->is('service/data/open') ? 'menu-open' : '' }}">
                        <a href="{{ url('service/data/open') }}"
                            class="nav-link {{ request()->is('service/data/open') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-list-alt"></i>
                            <p>
                                Daftar Antrean Tiket
                            </p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview {{ request()->is('service/data/close') ? 'menu-open' : '' }}">
                        <a href="{{ url('service/data/close') }}"
                            class="nav-link {{ request()->is('service/data/close') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-history"></i>
                            <p>
                                Riwayat Service
                            </p>
                        </a>
                    </li>
                @endif

                @if (in_array(auth()->user()->role, ['pelanggan']))
                    <li class="nav-item has-treeview {{ request()->is('service/data/open') ? 'menu-open' : '' }}">
                        <a href="{{ url('service/data/open') }}"
                            class="nav-link {{ request()->is('service/data/open') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-list-alt"></i>
                            <p>
                                Data Service
                            </p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview {{ request()->is('service/data/close') ? 'menu-open' : '' }}">
                        <a href="{{ url('service/data/close') }}"
                            class="nav-link {{ request()->is('service/data/close') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-history"></i>
                            <p>
                                Riwayat Service
                            </p>
                        </a>
                    </li>
                @endif
                {{-- <li class="nav-item has-treeview {{ request()->is('profile') ? 'menu-open' : '' }}">
                    <a href="{{ url('profile') }}" class="nav-link active">
                        <i class="nav-icon fas fa-key"></i>
                        <p>
                            Reset Password
                        </p>
                    </a>
                </li> --}}
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
