<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;

    // Jika tabel di database memiliki nama berbeda dari model
    protected $table = 'peserta';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // Kolom yang dapat diisi (mass assignable)
    protected $fillable = ['id', 'no_excel', 'nama_lengkap', 'usia', 'no_whatsapp', 'jenis_kelamin', 'domisili', 'is_hadir', 'created_at'];

}
