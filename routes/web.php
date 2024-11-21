<?php

use App\Exports\PesertaExport;
use App\Http\Controllers\PesertaController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/import-peserta', function () {
    return Excel::download(new PesertaExport, 'peserta_report.xlsx');
});

Route::get('/peserta', [PesertaController::class, 'index']);
Route::get('/scanner', [PesertaController::class, 'scan']);
Route::get('/peserta/get_data', [PesertaController::class, 'get_data']);
Route::get('/peserta/qr/{id}', [PesertaController::class, 'downloadQrCode']);
Route::get('/peserta/hadir/{id}', [PesertaController::class, 'hadir']);
