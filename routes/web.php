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

/** ajax */
Route::get('profile', 'UserController@profile')->name('profile');
Route::get('kode/{id}', 'Ajax@kode')->name('kode');
Route::get('detail_service', 'Ajax@detail_service')->name('detail_service');

/** semua */
Route::middleware(['auth:user,pelanggan', 'ceklevel:admin,teknisi,pelanggan,owner'])->group(function () {
    /** dashboard */
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('chart', 'DashboardController@chart')->name('chart');
    Route::get('chart2', 'DashboardController@chart2')->name('chart2');
    /** pelanggan */
    Route::get('master/pelanggan', 'PelangganController@index')->name('pelanggan');
    Route::post('pelanggan/submit', 'PelangganController@submit')->name('submit.pelanggan');
    Route::get('pelanggan/edit', 'PelangganController@edit')->name('edit.pelanggan');
    Route::post('pelanggan/delete', 'PelangganController@delete')->name('delete.pelanggan');
    /** jenisjasa */
    Route::get('master/jenisjasa', 'JenisJasaController@index')->name('jenisjasa');
    Route::post('jenisjasa/submit', 'JenisJasaController@submit')->name('submit.jenisjasa');
    Route::get('jenisjasa/edit', 'JenisJasaController@edit')->name('edit.jenisjasa');
    Route::post('jenisjasa/delete', 'JenisJasaController@delete')->name('delete.jenisjasa');
    /** barang */
    Route::get('master/barang', 'BarangController@index')->name('barang');
    Route::post('barang/submit', 'BarangController@submit')->name('submit.barang');
    Route::get('barang/edit', 'BarangController@edit')->name('edit.barang');
    Route::post('barang/delete', 'BarangController@delete')->name('delete.barang');
    /** user*/
    Route::get('master/user', 'UserController@index')->name('user');
    Route::post('master/user', 'UserController@index')->name('json.user');
    Route::post('user/add', 'UserController@add')->name('insert.user');
    Route::get('user/edit', 'UserController@edit')->name('edit.user');
    Route::post('user/update', 'UserController@update')->name('update.user');
    Route::post('user/delete', 'UserController@delete')->name('delete.user');
    /** service */
    Route::get('service/data/{status?}', 'ServiceController@index')->name('service');
    Route::post('service/submit', 'ServiceController@submit')->name('submit.service');
    Route::post('service/update_progress', 'ServiceController@update_progress')->name('update_progress.service');
    Route::get('service/edit', 'ServiceController@edit')->name('edit.service');
    Route::post('service/delete', 'ServiceController@delete')->name('delete.service');


    /** akun akutansi */
    Route::get('master/akun', 'AkunController@index')->name('akun');
    Route::post('akun', 'AkunController@index')->name('json.akun');
    Route::post('akun/insert', 'AkunController@insert')->name('insert.akun');
    Route::get('akun/edit', 'AkunController@edit')->name('edit.akun');
    Route::get('akun/delete', 'AkunController@delete')->name('delete.akun');
    Route::post('akun/update', 'AkunController@update')->name('update.akun');
    /** saldo awal */
    Route::get('master/akun/saldo', 'SaldoAwaController@index')->name('saldo_awal');
    Route::post('saldo_awal', 'SaldoAwaController@index')->name('json.saldo_awal');
    Route::post('saldo_awal/insert', 'SaldoAwaController@submit')->name('submit.saldo_awal');
    Route::get('saldo_awal/edit', 'SaldoAwaController@edit')->name('edit.saldo_awal');
    Route::get('saldo_awal/delete', 'SaldoAwaController@delete')->name('delete.saldo_awal');

    /** jurnal akutansi */
    Route::get('jurnal', 'JurnalController@index')->name('jurnal');
    Route::get('get/akun', 'JurnalController@getAkun')->name('getakun');
    Route::post('jurnal', 'JurnalController@index')->name('json.jurnal');
    Route::post('jurnal/insert', 'JurnalController@insert')->name('insert.jurnal');
    Route::get('jurnal/edit', 'JurnalController@edit')->name('edit.jurnal');
    Route::get('jurnal/delete', 'JurnalController@delete')->name('delete.jurnal');
    Route::post('jurnal/update', 'JurnalController@update')->name('update.jurnal');
    Route::get('jurnal/cart', 'JurnalController@list')->name('json.cart');
    Route::post('jurnal/cart/insert', 'JurnalController@add')->name('insert.cart');
    Route::get('jurnal/cart/delete', 'JurnalController@del_cart')->name('delete.cart');

    /** pembayaran */
    Route::get('transaksi/pembayaran', 'PembayaranController@index')->name('transaksi.pembayaran');
    Route::get('pembayaran/add', 'PembayaranController@add')->name('pembayaran.add');
    Route::get('edit-pembayaran', 'PembayaranController@edit')->name('pembayaran.edit');
    Route::get('delete-pembayaran', 'PembayaranController@delete')->name('pembayaran.delete');
    Route::post('update-pembayaran', 'PembayaranController@update')->name('pembayaran.update');
    Route::get('listcart_pembayaran', 'PembayaranController@listCart')->name('listcart.pembayaran');
    Route::post('addtocart_pembayaran', 'PembayaranController@addToCart')->name('addtocart.pembayaran');
    Route::get('removeall_pembayaran', 'PembayaranController@removeAll')->name('removeall.pembayaran');
    Route::get('removeone_pembayaran', 'PembayaranController@removeOne')->name('removeone.pembayaran');
    Route::post('finish_pembayaran', 'PembayaranController@finish')->name('finish.pembayaran');
    Route::post('setUangMuka', 'PembayaranController@setUangMuka')->name('setUangMuka.pembayaran');

    /** pengeluaran */
    Route::get('transaksi/pengeluaran', 'PengeluaranController@index')->name('transaksi.pengeluaran');
    Route::post('pengeluaran/insert', 'PengeluaranController@submit')->name('submit.pengeluaran');
    Route::get('pengeluaran/edit', 'PengeluaranController@edit')->name('edit.pengeluaran');
    Route::get('pengeluaran/delete', 'PengeluaranController@delete')->name('delete.pengeluaran');

    /** laporan  */
    Route::get('laporan/{jenis}', 'LaporanController@index')->name('laporan');
    Route::post('laporan/getdata', 'LaporanController@getLaporan')->name('post.laporan');

    /** laporan keuangan */
    Route::get('laporan_keuangan/{jenis}', 'LapKeuanganController@index')->name('laporan_keuangan');
    Route::post('laporan_keuangan/', 'LapKeuanganController@getLaporan')->name('post.laporan_keuangan');
});


// Login
Route::middleware(['guest'])->group(function () {
    Route::get('login', 'LoginController@getLogin')->name('login');
    Route::post('login', 'LoginController@postLogin');
});

Route::middleware(['auth:user,pelanggan'])->group(function () {
    Route::get('logout', 'LoginController@logout')->name('logout');
});
