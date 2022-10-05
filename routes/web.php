<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});


/** semua */
Route::middleware(['auth:user', 'ceklevel:admin'])->group(function () {
    /** dashboard */
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('chart', 'DashboardController@chart')->name('chart');
    Route::get('chart2', 'DashboardController@chart2')->name('chart2');
    /** user*/
    Route::get('user', 'UserController@index')->name('user');
    Route::post('user', 'UserController@index')->name('json.user');
    Route::post('user/add', 'UserController@add')->name('insert.user');
    Route::get('user/edit', 'UserController@edit')->name('edit.user');
    Route::post('user/update', 'UserController@update')->name('update.user');
    /** barang */
    Route::get('barang', 'BarangController@index')->name('barang');
    Route::post('barang/insert', 'BarangController@insert')->name('insert.barang');
    Route::get('barang/edit', 'BarangController@edit')->name('edit.barang');
    Route::post('barang/update', 'BarangController@update')->name('update.barang');
    Route::get('barang/delete', 'BarangController@delete')->name('delete.barang');
    /** jenis */
    Route::get('jenis', 'JenisController@index')->name('jenis');
    Route::post('jenis/insert', 'JenisController@insert')->name('insert.jenis');
    Route::get('jenis/edit', 'JenisController@edit')->name('edit.jenis');
    Route::post('jenis/update', 'JenisController@update')->name('update.jenis');
    Route::get('jenis/delete', 'JenisController@delete')->name('delete.jenis');
    /** customer */
    Route::get('customer', 'CustomerController@index')->name('customer');
    Route::post('customer/insert', 'CustomerController@insert')->name('insert.customer');
    Route::get('customer/edit', 'CustomerController@edit')->name('edit.customer');
    Route::post('customer/update', 'CustomerController@update')->name('update.customer');
    Route::get('customer/delete', 'CustomerController@delete')->name('delete.customer');
    /** supplier */
    Route::get('supplier', 'SupplierController@index')->name('supplier');
    Route::post('supplier/insert', 'SupplierController@insert')->name('insert.supplier');
    Route::get('supplier/edit', 'SupplierController@edit')->name('edit.supplier');
    Route::post('supplier/update', 'SupplierController@update')->name('update.supplier');
    Route::get('supplier/delete', 'SupplierController@delete')->name('delete.supplier');
    /** stokopname */
    Route::get('stokopname', 'StokOpnameController@index')->name('stokopname');
    Route::get('stokopname/add', 'StokOpnameController@stokAdd')->name('stokopname.add');
    Route::get('json-stokopname', 'StokOpnameController@index')->name('json.stokopname');
    Route::get('edit-stokopname', 'StokOpnameController@edit')->name('stokopname.edit');
    Route::get('delete-stokopname', 'StokOpnameController@delete')->name('stokopname.delete');
    Route::post('update-stokopname', 'StokOpnameController@update')->name('stokopname.update');
    Route::get('listcart_stokopname', 'StokOpnameController@listCart')->name('listcart.stokopname');
    Route::post('addtocart_stokopname', 'StokOpnameController@addToCart')->name('addtocart.stokopname');
    Route::get('removeall_stokopname', 'StokOpnameController@removeAll')->name('removeall.stokopname');
    Route::get('removeone_stokopname', 'StokOpnameController@removeOne')->name('removeone.stokopname');
    Route::post('finish_stokopname', 'StokOpnameController@finish')->name('finish.stokopname');
    Route::get('getstok', 'StokOpnameController@get_stok')->name('getstok');
    /** pembelian */
    Route::get('transaksi/pembelian', 'PembelianController@index')->name('transaksi.pembelian');
    Route::get('pembelian/add', 'PembelianController@add')->name('pembelian.add');
    Route::get('json-pembelian', 'PembelianController@index')->name('json.pembelian');
    Route::get('edit-pembelian', 'PembelianController@edit')->name('pembelian.edit');
    Route::get('delete-pembelian', 'PembelianController@delete')->name('pembelian.delete');
    Route::post('update-pembelian', 'PembelianController@update')->name('pembelian.update');
    Route::get('listcart_pembelian', 'PembelianController@listCart')->name('listcart.pembelian');
    Route::post('addtocart_pembelian', 'PembelianController@addToCart')->name('addtocart.pembelian');
    Route::get('removeall_pembelian', 'PembelianController@removeAll')->name('removeall.pembelian');
    Route::get('removeone_pembelian', 'PembelianController@removeOne')->name('removeone.pembelian');
    Route::post('finish_pembelian', 'PembelianController@finish')->name('finish.pembelian');
    /** penjualan */
    Route::get('transaksi/penjualan', 'PenjualanController@index')->name('transaksi.penjualan');
    Route::get('penjualan/add', 'PenjualanController@add')->name('penjualan.add');
    Route::get('json-penjualan', 'PenjualanController@index')->name('json.penjualan');
    Route::get('edit-penjualan', 'PenjualanController@edit')->name('penjualan.edit');
    Route::get('delete-penjualan', 'PenjualanController@delete')->name('penjualan.delete');
    Route::post('update-penjualan', 'PenjualanController@update')->name('penjualan.update');
    Route::get('listcart_penjualan', 'PenjualanController@listCart')->name('listcart.penjualan');
    Route::post('addtocart_penjualan', 'PenjualanController@addToCart')->name('addtocart.penjualan');
    Route::get('removeall_penjualan', 'PenjualanController@removeAll')->name('removeall.penjualan');
    Route::get('removeone_penjualan', 'PenjualanController@removeOne')->name('removeone.penjualan');
    Route::post('finish_penjualan', 'PenjualanController@finish')->name('finish.penjualan');
    Route::post('set/status', 'PenjualanController@kirim')->name('penjualan.kirim');
    /** laporan */
    Route::get('laporan/{jenis}', 'LaporanController@index')->name('laporan');
    Route::post('laporan/', 'LaporanController@getLaporan')->name('post.laporan');
});


// Login
Route::middleware(['guest'])->group(function () {
    Route::get('login', 'LoginController@getLogin')->name('login');
    Route::post('login', 'LoginController@postLogin');
});

Route::middleware(['auth:user'])->group(function () {
    Route::get('logout', 'LoginController@logout')->name('logout');
});
