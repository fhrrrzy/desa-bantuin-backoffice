<?php

namespace Database\Seeders;

use App\Models\Information;
use App\Models\LaporanType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $laporanTypes = LaporanType::all();

        if ($laporanTypes->isEmpty()) {
            return;
        }

        $sampleInformation = [
            [
                'title' => 'Cara Membuat Surat Pengantar',
                'description' => '<h3>Cara Membuat Surat Pengantar</h3><p>Berikut adalah langkah-langkah untuk membuat surat pengantar:</p><ol><li>Siapkan dokumen yang diperlukan</li><li>Isi formulir dengan lengkap</li><li>Serahkan ke kantor desa</li><li>Tunggu proses verifikasi</li></ol><p><strong>Catatan:</strong> Pastikan semua dokumen sudah lengkap sebelum mengajukan.</p>',
            ],
            [
                'title' => 'Persyaratan Surat Keterangan Domisili',
                'description' => '<h3>Persyaratan Surat Keterangan Domisili</h3><p>Untuk mendapatkan surat keterangan domisili, Anda perlu menyiapkan:</p><ul><li>KTP Asli dan Fotokopi</li><li>Surat Pengantar RT/RW</li><li>Bukti Kepemilikan Rumah</li><li>Pas Foto 3x4 (2 lembar)</li></ul><p><em>Proses pengurusan memakan waktu 1-3 hari kerja.</em></p>',
            ],
            [
                'title' => 'Informasi Pengurusan SKCK',
                'description' => '<h3>Informasi Pengurusan SKCK</h3><p>Surat Keterangan Catatan Kepolisian (SKCK) dapat diurus dengan syarat:</p><ul><li>Berusia minimal 17 tahun</li><li>Membawa KTP dan KK</li><li>Surat pengantar dari desa</li><li>Pas foto 4x6 (2 lembar)</li></ul><p><strong>Biaya:</strong> Gratis</p><p><strong>Waktu:</strong> 3-5 hari kerja</p>',
            ],
            [
                'title' => 'Panduan Pengurusan Izin Usaha',
                'description' => '<h3>Panduan Pengurusan Izin Usaha</h3><p>Untuk mengurus izin usaha, ikuti langkah berikut:</p><ol><li>Persiapkan dokumen usaha</li><li>Isi formulir pendaftaran</li><li>Serahkan ke kantor desa</li><li>Verifikasi lokasi usaha</li><li>Penerbitan izin</li></ol><p><strong>Dokumen yang diperlukan:</strong></p><ul><li>Fotokopi KTP</li><li>Surat keterangan RT/RW</li><li>Denah lokasi usaha</li></ul>',
            ],
            [
                'title' => 'Cara Mengurus Surat Nikah',
                'description' => '<h3>Cara Mengurus Surat Nikah</h3><p>Prosedur pengurusan surat nikah:</p><ol><li>Siapkan dokumen calon mempelai</li><li>Kunjungi kantor desa</li><li>Isi formulir permohonan</li><li>Verifikasi dokumen</li><li>Penerbitan surat nikah</li></ol><p><strong>Dokumen yang diperlukan:</strong></p><ul><li>KTP dan KK kedua calon</li><li>Surat keterangan belum menikah</li><li>Surat izin orang tua (jika diperlukan)</li><li>Akta kelahiran</li></ul>',
            ],
        ];

        foreach ($laporanTypes as $index => $laporanType) {
            if (isset($sampleInformation[$index])) {
                Information::create([
                    'title' => $sampleInformation[$index]['title'],
                    'description' => $sampleInformation[$index]['description'],
                    'laporan_type_id' => $laporanType->id,
                ]);
            }
        }
    }
}
