<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
    }

    // Menolak Peminjaman (Menghapus pengajuan agar siswa bisa mengajukan ulang)
    public function tolakPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Pastikan statusnya memang masih diajukan
            if ($peminjaman->status == 'diajukan') {
                $peminjaman->delete();
                return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil ditolak.');
            }

            return redirect()->back()->with('error', 'Status peminjaman sudah berubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menyetujui Peminjaman (Mengubah status & mengurangi stok alat)
    public function setujuiPeminjaman($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $peminjaman = Peminjaman::with('detailPinjam')
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($peminjaman->status !== 'diajukan') {
                    throw new \RuntimeException('Status peminjaman sudah berubah.');
                }

                foreach ($peminjaman->detailPinjam->sortBy('alat_id') as $detail) {
                    $alat = Alat::lockForUpdate()->findOrFail($detail->alat_id);

                    if ($alat->status_kondisi !== 'Baik' || $alat->stok < $detail->jumlah) {
                        throw new \RuntimeException("Stok alat {$alat->nama_alat} tidak mencukupi.");
                    }

                    $alat->decrement('stok', $detail->jumlah);
                }

                $peminjaman->update(['status' => 'dipinjam']);
            });

            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPinjam')
                ->findOrFail($peminjamanId);

            // Simpan data pengembalian
            Pengembalian::create([
                'peminjaman_id'    => $peminjaman->id,
                'tgl_kembali'      => now(),
                'kondisi_kembali'  => $request->kondisi_kembali,
                'denda'            => $request->denda ?? 0,
                'petugas_id'       => auth()->id(),
            ]);

            $peminjaman->update([
                'status' => 'selesai'
            ]);

            // Kembalikan stok alat ke inventaris
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            DB::commit();

            return redirect()->back()->with(
                'success',
                'Pengembalian berhasil dicatat dan stok dipulihkan.'
            );
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with(
                'error',
                'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }

    // Menampilkan daftar pemantauan peminjaman/pengembalian alat
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'dipinjam')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('petugas.pengembalian.index', compact('peminjamans', 'search'));
    }

    // Menampilkan laporan peminjaman & pengembalian dengan filter
    public function indexLaporan(Request $request)
    {
        $tglMulai = $request->input('tgl_mulai');
        $tglSelesai = $request->input('tgl_selesai');
        $status = $request->input('status');

        $laporans = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])
            ->when($tglMulai, function ($query, $tglMulai) {
                return $query->whereDate('tgl_pinjam', '>=', $tglMulai);
            })
            ->when($tglSelesai, function ($query, $tglSelesai) {
                return $query->whereDate('tgl_pinjam', '<=', $tglSelesai);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->get();

        return view('petugas.laporan.index', compact('laporans', 'tglMulai', 'tglSelesai', 'status'));
    }
}