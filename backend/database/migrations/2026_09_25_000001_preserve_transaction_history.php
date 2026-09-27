<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE peminjaman MODIFY status ENUM('diajukan', 'dipinjam', 'selesai', 'telat', 'ditolak') NOT NULL DEFAULT 'diajukan'");
        }

        Schema::table('alat', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->foreign('kategori_id')->references('id')->on('kategori')->restrictOnDelete();
        });

        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });

        Schema::table('detail_pinjam', function (Blueprint $table) {
            $table->dropForeign(['peminjaman_id']);
            $table->dropForeign(['alat_id']);
            $table->foreign('peminjaman_id')->references('id')->on('peminjaman')->restrictOnDelete();
            $table->foreign('alat_id')->references('id')->on('alat')->restrictOnDelete();
        });

        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropForeign(['peminjaman_id']);
            $table->dropForeign(['petugas_id']);
            $table->foreign('peminjaman_id')->references('id')->on('peminjaman')->restrictOnDelete();
            $table->foreign('petugas_id')->references('id')->on('users')->restrictOnDelete();
            $table->unique('peminjaman_id');
        });
    }

    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropUnique(['peminjaman_id']);
            $table->dropForeign(['peminjaman_id']);
            $table->dropForeign(['petugas_id']);
            $table->foreign('peminjaman_id')->references('id')->on('peminjaman')->cascadeOnDelete();
            $table->foreign('petugas_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('detail_pinjam', function (Blueprint $table) {
            $table->dropForeign(['peminjaman_id']);
            $table->dropForeign(['alat_id']);
            $table->foreign('peminjaman_id')->references('id')->on('peminjaman')->cascadeOnDelete();
            $table->foreign('alat_id')->references('id')->on('alat')->cascadeOnDelete();
        });

        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('alat', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->foreign('kategori_id')->references('id')->on('kategori')->cascadeOnDelete();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE peminjaman MODIFY status ENUM('diajukan', 'dipinjam', 'selesai', 'telat') NOT NULL DEFAULT 'diajukan'");
        }
    }
};
