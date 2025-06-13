<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LaporanType;

class LaporanTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'KTP',
            'KK',
            'Buku Nikah',
            'Akta Nikah',
            'Akta Lahir',
            'Surat Kematian',
            'KIA (Kartu identitas anak)',
            'KIS (Kartu Indonesia Sehat)'
        ];

        foreach ($types as $type) {
            LaporanType::create(['name' => $type]);
        }
    }
}
