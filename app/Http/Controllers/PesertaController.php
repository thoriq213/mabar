<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;

class PesertaController extends Controller
{
    //
    public function index()
    {
        return view('peserta');
    }

    public function get_data()
    {
        $users = Peserta::select(['id', 'no_excel', 'nama_lengkap', 'jenis_kelamin', 'domisili', 'is_hadir']);

        return DataTables::of($users)
            ->addColumn('action', function ($user) {
                $string = '<a href="/peserta/qr/' . $user->id . '" class="btn btn-sm btn-primary">Download QR</a>';
                if ($user->is_hadir == 0) {
                    $string .= '<div onclick="hadir(' . $user->id . ')" class="btn btn-sm btn-danger mt-2">Present</div>';
                }
                return $string;
            })
            ->addColumn('status', function ($user) {
                if ($user->is_hadir == 1) {
                    $string = '<span class="text-success">HADIR</span>';
                } else {
                    $string = '<span class="text-danger">BELUM HADIR</span>';
                }

                return $string;
                // return '<a href="/users/edit/' . $user->id . '" class="btn btn-sm btn-primary">Edit</a>';
            })
            ->addColumn('qr', function ($user) {
                return QrCode::generate(
                    $user->id,
                );
                // return '<a href="/users/edit/' . $user->id . '" class="btn btn-sm btn-primary">Edit</a>';
            })
            ->rawColumns(['action', 'qr', 'status']) // Pastikan kolom 'action' dirender sebagai HTML
            ->make(true);
    }

    public function downloadQrCode($data)
    {
        // Generate QR Code dalam format PNG tanpa menyimpannya di server
        $qrCode = QrCode::format('png')->size(300)->margin(2)->generate($data);

        // Mengirimkan QR Code sebagai response
        return Response::make($qrCode, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="qrcode.png"'
        ]);
    }

    public function scan()
    {
        return view('scan');
    }

    public function hadir($id)
    {
        $get_peserta = Peserta::find($id);

        if ($get_peserta) {
            if ($get_peserta->is_hadir == 0) {
                $get_peserta->is_hadir = 1;
                $get_peserta->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Barcode berhasil Digunakan!',
                    'peserta' => $get_peserta,
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Barcode Telah Digunakan!',
                ], 404);
            }
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Barcode Tidak Ditemukan!',
        ], 404);
    }

    public function get_barcode_by_name()
    {
        return view('form');
    }

    public function get_barcode(Request $request)
    {
        $nama = $request->nama;
        $domisili = $request->domisili;

        $get_peserta = Peserta::where('nama_lengkap', $nama)->where('domisili', $domisili)->first();
        if ($get_peserta) {
            $data = json_encode($get_peserta);

            $qrCode = QrCode::format('png')->size(300)->margin(2)->generate($data);

            // Mengirimkan QR Code sebagai response
            return Response::make($qrCode, 200, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="qrcode.png"'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ada',
            ], 404);
        }
    }
}
