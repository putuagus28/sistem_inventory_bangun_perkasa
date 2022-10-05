<aside class="main-sidebar sidebar-dark-info elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url(auth()->user()->role == 'mahasiswa' ? 'setprivilege' : '') }}" class="brand-link bg-info">
        <img src="{{ asset('assets/dist/img/favicon-32x32.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-bold">CV. ADITYA BANGUN PERKASA</span>
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
                        {{ strtoupper(auth()->user()->role == 'mahasiswa' ? auth()->user()->mhs->name : auth()->user()->name) }}
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
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @if (in_array(auth()->user()->role, ['admin']))
                    <li
                        class="nav-item {{ request()->is(['user', 'jenis', 'barang', 'customer', 'supplier']) ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('laporan/*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file"></i>
                            <p>
                                Master Data
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('user') }}"
                                    class="nav-link {{ request()->is('user') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>User</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('jenis') }}"
                                    class="nav-link {{ request()->is('jenis') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Jenis Barang</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('barang') }}"
                                    class="nav-link {{ request()->is('barang') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Barang</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('customer') }}"
                                    class="nav-link {{ request()->is('customer') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Customer</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('supplier') }}"
                                    class="nav-link {{ request()->is('supplier') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Supplier</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-treeview {{ request()->is('stokopname') ? 'menu-open' : '' }}">
                        <a href="{{ route('stokopname') }}"
                            class="nav-link {{ request()->is('stokopname') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-box-open"></i>
                            <p>
                                Stok Opname
                            </p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('transaksi/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('transaksi/*') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-cart-plus"></i>
                            <p>
                                Transaksi
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('transaksi.pembelian') }}"
                                    class="nav-link {{ request()->is('transaksi/pembelian') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Pembelian</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('transaksi.penjualan') }}"
                                    class="nav-link {{ request()->is('transaksi/penjualan') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Penjualan</p>
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                                <a href="{{ url('pengeluaran') }}"
                                    class="nav-link {{ request()->is('transaksi/pengeluaran') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Purchase Order</p>
                                </a>
                            </li> --}}
                        </ul>
                    </li>

                    <li class="nav-item {{ request()->is('laporan/*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->is('laporan/*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-print"></i>
                            <p>
                                Laporan
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('laporan/pembelian') }}"
                                    class="nav-link {{ request()->is('laporan/pembelian') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Pembelian</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('laporan/penjualan') }}"
                                    class="nav-link {{ request()->is('laporan/penjualan') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Penjualan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('laporan/stok') }}"
                                    class="nav-link {{ request()->is('laporan/stok') ? 'active' : '' }}">
                                    <i class="fa fa-angle-right nav-icon"></i>
                                    <p>Stok</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
