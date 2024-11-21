<?php

namespace App\Exports;

use App\Models\Peserta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PesertaExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    use RegistersEventListeners;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Peserta::all();
    }

    public function headings(): array
    {
        return [
            'No Excel',
            'Nama Lengkap',
            'Usia',
            'No. WhatsApp',
            'Jenis Kelamin',
            'Domisili',
            'QR Code Aktivasi',
        ];
    }

    public function map($user): array
    {
        // Buat URL aktivasi
        $activationUrl = $user->id;

        // Generate QR code as image in memory
        $qrCode = QrCode::format('png')->size(200)->margin(2)->generate($activationUrl);
        
        // Save the QR code to a temporary location (in memory)
        $qrCodePath = storage_path('app/temp/qrcodes/' . $user->id . '.png');
        file_put_contents($qrCodePath, $qrCode); // Save to temporary location

        return [
            $user->no_excel,
            $user->nama_lengkap,
            $user->usia,
            $user->no_whatsapp,
            $user->jenis_kelamin,
            $user->domisili,
            $qrCodePath, // Path is used to reference the image in afterSheet
        ];
    }

    /**
     * Embed images in the export.
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     * @return void
     */
    public static function afterSheet($event)
    {
        $sheet = $event->sheet->getDelegate(); // Get the actual sheet

        // Loop through each user and add their QR code to the Excel sheet
        $pesertas = Peserta::all(); // Get the collection of Peserta

        foreach ($pesertas as $index => $user) {
            // Get the QR code image path for the user
            $imagePath = storage_path('app/temp/qrcodes/' . $user->id . '.png');

            // Create an image object and embed it into the Excel sheet
            $drawing = new Drawing();
            $drawing->setName('QR Code')
                ->setDescription('QR Code')
                ->setPath($imagePath)
                ->setWidthAndHeight(200, 200) // Set width and height dynamically according to the QR code size
                ->setCoordinates('G' . ($index + 2)); // Set the cell coordinates (adjust as needed)

            // Add the image to the sheet
            $drawing->setWorksheet($sheet);
        }
    }
}
