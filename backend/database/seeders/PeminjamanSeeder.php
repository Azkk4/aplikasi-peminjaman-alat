<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Peminjaman; 

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $peminjaman = [ 
            [
                'user_id' => 3, // Rian (Peminjam) 
                'tgl_pinjam' => '2026-06-01 10:00:00', 
                'tgl_kembali_plan' => '2026-06-04 23:59:59', 
                'status' => 'selesai', 
            ], 
            [ 
                'user_id' => 4, // Siti (Peminjam) 
                'tgl_pinjam' => '2026-06-02 09:30:00', 
                'tgl_kembali_plan' => '2026-06-05 23:59:59', 
                'status' => 'selesai', 
            ], 
            [ 
                'user_id' => 5, // Eka (Peminjam) 
                'tgl_pinjam' => '2026-06-03', 
                'tgl_kembali_plan' => '2026-06-06', 
                'status' => 'telat', 
            ], 
            [ 
                'user_id' => 3, 
                'tgl_pinjam' => '2026-06-08', 
                'tgl_kembali_plan' => '2026-06-11', 
                'status' => 'dipinjam', 
            ], 
            [ 
                'user_id' => 4, 
                'tgl_pinjam' => '2026-06-09', 
                'tgl_kembali_plan' => '2026-06-12', 
                'status' => 'diajukan', 
            ], 
        ]; 
 
        foreach ($peminjaman as $pinjam) { 
            Peminjaman::create($pinjam); 
        } 
    } 
}
