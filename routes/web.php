<?php

use App\Exports\PesertaExport;
use App\Http\Controllers\PesertaController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;


Route::get('/import-peserta', function () {
    return Excel::download(new PesertaExport, 'peserta_report.xlsx');
});

Route::get('/peserta-panitia', [PesertaController::class, 'index']);
Route::get('/scanner-panitia', [PesertaController::class, 'scan']);
Route::get('/', [PesertaController::class, 'get_barcode_by_name']);
Route::post('/peserta/get_data', [PesertaController::class, 'get_barcode']);
Route::get('/peserta/qr/{id}', [PesertaController::class, 'downloadQrCode']);
Route::get('/peserta/hadir/{id}', [PesertaController::class, 'hadir']);
