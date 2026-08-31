<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'diajukan'");
        DB::statement("UPDATE peminjaman SET status = 'selesai' WHERE status = 'dikembalikan'");
        DB::statement("UPDATE peminjaman SET status = 'diajukan' WHERE status NOT IN ('diajukan', 'dipinjam', 'selesai', 'telat')");

        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN tgl_pinjam DATETIME NOT NULL");
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN tgl_kembali_plan DATETIME NOT NULL");
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status ENUM('diajukan', 'dipinjam', 'selesai', 'telat') NOT NULL DEFAULT 'diajukan'");

        DB::statement("ALTER TABLE pengembalian MODIFY COLUMN tgl_kembali DATETIME NOT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'diajukan'");
        DB::statement("UPDATE peminjaman SET status = 'dikembalikan' WHERE status = 'selesai'");
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN tgl_pinjam DATE NOT NULL");
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN tgl_kembali_plan DATE NOT NULL");
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status ENUM('diajukan', 'dipinjam', 'dikembalikan', 'telat') NOT NULL DEFAULT 'diajukan'");

        DB::statement("ALTER TABLE pengembalian MODIFY COLUMN tgl_kembali DATE NOT NULL");
    }
};
