<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserRequest;
use App\Models\User;
use App\Models\LaporanType;

class UserRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'warga')->get();
        $laporanTypes = LaporanType::all();

        if ($users->isEmpty() || $laporanTypes->isEmpty()) {
            $this->command->warn('Users or LaporanTypes not found. Please run UserSeeder and LaporanTypeSeeder first.');
            return;
        }

        $sampleRequests = [
            // KTP Requests
            [
                'description' => 'Saya ingin mengajukan pembuatan KTP baru karena KTP lama sudah rusak dan tidak bisa digunakan.',
                'type' => 'permintaan',
                'status' => 'onprocess',
                'laporan_type_name' => 'KTP',
            ],
            [
                'description' => 'KTP saya hilang saat perjalanan, mohon bantuan untuk pembuatan KTP pengganti.',
                'type' => 'permintaan',
                'status' => 'accepted',
                'return_message' => 'KTP pengganti sudah siap diambil di kantor desa.',
                'laporan_type_name' => 'KTP',
            ],
            [
                'description' => 'Melaporkan kehilangan KTP untuk keperluan administrasi.',
                'type' => 'pelaporan',
                'status' => 'rejected',
                'return_message' => 'Dokumen pendukung tidak lengkap, silakan lengkapi terlebih dahulu.',
                'laporan_type_name' => 'KTP',
            ],

            // KK Requests
            [
                'description' => 'Mengajukan pembuatan KK baru karena ada penambahan anggota keluarga.',
                'type' => 'permintaan',
                'status' => 'onprocess',
                'laporan_type_name' => 'KK',
            ],
            [
                'description' => 'KK rusak terkena air, mohon bantuan untuk pembuatan KK baru.',
                'type' => 'permintaan',
                'status' => 'accepted',
                'return_message' => 'KK baru sudah siap diambil.',
                'laporan_type_name' => 'KK',
            ],

            // Buku Nikah Requests
            [
                'description' => 'Mengajukan pembuatan buku nikah untuk keperluan administrasi.',
                'type' => 'permintaan',
                'status' => 'onprocess',
                'laporan_type_name' => 'Buku Nikah',
            ],
            [
                'description' => 'Buku nikah hilang, mohon bantuan untuk pembuatan buku nikah pengganti.',
                'type' => 'pelaporan',
                'status' => 'accepted',
                'return_message' => 'Buku nikah pengganti sudah siap diambil.',
                'laporan_type_name' => 'Buku Nikah',
            ],

            // Akta Lahir Requests
            [
                'description' => 'Mengajukan pembuatan akta lahir untuk anak yang baru lahir.',
                'type' => 'permintaan',
                'status' => 'onprocess',
                'laporan_type_name' => 'Akta Lahir',
            ],
            [
                'description' => 'Akta lahir anak rusak, mohon bantuan untuk pembuatan akta lahir baru.',
                'type' => 'permintaan',
                'status' => 'rejected',
                'return_message' => 'Dokumen kelahiran tidak lengkap, silakan lengkapi terlebih dahulu.',
                'laporan_type_name' => 'Akta Lahir',
            ],

            // Surat Kematian Requests
            [
                'description' => 'Mengajukan pembuatan surat kematian untuk anggota keluarga yang telah meninggal.',
                'type' => 'permintaan',
                'status' => 'accepted',
                'return_message' => 'Surat kematian sudah siap diambil.',
                'laporan_type_name' => 'Surat Kematian',
            ],

            // KIA Requests
            [
                'description' => 'Mengajukan pembuatan KIA untuk anak yang belum memiliki identitas.',
                'type' => 'permintaan',
                'status' => 'onprocess',
                'laporan_type_name' => 'KIA (Kartu identitas anak)',
            ],

            // KIS Requests
            [
                'description' => 'Mengajukan pembuatan KIS untuk keperluan kesehatan keluarga.',
                'type' => 'permintaan',
                'status' => 'accepted',
                'return_message' => 'KIS sudah siap diambil.',
                'laporan_type_name' => 'KIS (Kartu Indonesia Sehat)',
            ],
            [
                'description' => 'KIS hilang, mohon bantuan untuk pembuatan KIS pengganti.',
                'type' => 'pelaporan',
                'status' => 'onprocess',
                'laporan_type_name' => 'KIS (Kartu Indonesia Sehat)',
            ],
        ];

        foreach ($sampleRequests as $requestData) {
            $laporanType = $laporanTypes->where('name', $requestData['laporan_type_name'])->first();

            if (!$laporanType) {
                continue;
            }

            UserRequest::create([
                'user_id' => $users->random()->id,
                'laporan_type_id' => $laporanType->id,
                'type' => $requestData['type'],
                'description' => $requestData['description'],
                'status' => $requestData['status'],
                'return_message' => $requestData['return_message'] ?? null,
                'lampiran' => null, // No attachments for sample data
            ]);
        }

        $this->command->info('UserRequest seeder completed successfully!');
    }
}
