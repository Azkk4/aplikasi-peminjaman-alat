<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pengembalian; 

class PengembalianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pengembalian = [ 
            [ 
                'peminjaman_id' => 1, 
                'tgl_kembali' => '2026-06-04 15:30:00', 
                'kondisi_kembali' => 'Lengkap dan Berfungsi Baik', 
                'denda' => 0, 
                'petugas_id' => 2, // Arif (Petugas) 
            ], 
            [ 
                'peminjaman_id' => 2, 
                'tgl_kembali' => '2026-06-05 14:20:00', 
                'kondisi_kembali' => 'Lengkap dan Berfungsi Baik', 
                'denda' => 0, 
                'petugas_id' => 2, 
            ], 
            [ 
                'peminjaman_id' => 3, 
                'tgl_kembali' => '2026-06-09 10:45:00', // Telat 3 hari dari tgl 6 
                'kondisi_kembali' => 'Lengkap, Casing Sedikit Tergores', 
                'denda' => 30000, // Asumsi denda per hari 10rb 
                'petugas_id' => 2, 
            ], 
        ]; 
 
        foreach ($pengembalian as $kembali) { 
            Pengembalian::create($kembali); 
        }
    }
}
