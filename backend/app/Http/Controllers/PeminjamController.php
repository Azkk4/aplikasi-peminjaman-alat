<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class PeminjamController extends Controller
{
    public function dashboard()
    {
        $userId = auth()->id();
        $peminjamananMenunggu = Peminjaman::where('user_id', $userId)->where('status', 'diajukan')->count();
        $peminjamanBerlangsung = Peminjaman::where('user_id', $userId)->where('status', 'dipinjam')->count();
        $peminjamanSelesai = Peminjaman::where('user_id', $userId)->where('status', 'selesai')->count();
        $peminjamanTerbaru = Peminjaman::with('detailPinjam.alat')->where('user_id', $userId)->latest()->limit(5)->get();
        $alatTersedia = Alat::with('kategori')->tersedia()->latest()->limit(4)->get();

        return view('peminjam.dashboard', compact(
            'peminjamananMenunggu', 'peminjamanBerlangsung', 'peminjamanSelesai',
            'peminjamanTerbaru', 'alatTersedia'
        ));
    }

    // Melihat daftar/katalog alat yang tersedia
    public function katalogAlat(Request $request)
    {
        $alats = Alat::with('kategori')
            ->tersedia()
            ->when($request->filled('search'), fn ($query) => $query->where('nama_alat', 'like', '%' . $request->search . '%'))
            ->when($request->filled('kategori_id'), fn ($query) => $query->where('kategori_id', $request->kategori_id))
            ->latest()
            ->paginate(12)
            ->withQueryString();
        $kategoris = \App\Models\Kategori::orderBy('nama_kategori')->get();

        return view('peminjam.katalog', compact('alats', 'kategoris'));
    }

    public function detailAlat(Alat $alat)
    {
        $alat->load('kategori');
        return view('peminjam.katalog.show', compact('alat'));
    }

    public function createPeminjaman(Request $request)
    {
        $alats = Alat::with('kategori')->tersedia()->orderBy('nama_alat')->get();
        $selectedAlat = $request->filled('alat_id') ? $alats->firstWhere('id', (int) $request->alat_id) : null;

        return view('peminjam.peminjaman.create', compact('alats', 'selectedAlat'));
    }

    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'tgl_kembali_plan' => ['required', 'date', 'after:today'],
            'alat_id' => ['required', 'exists:alat,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            $alat = Alat::whereKey($request->alat_id)->lockForUpdate()->firstOrFail();
            if ($alat->status_kondisi !== 'Baik') {
                throw new \RuntimeException('Alat yang dipilih tidak tersedia untuk dipinjam.');
            }

            if ($alat->stok < $request->jumlah) {
                throw new \RuntimeException('Jumlah yang diajukan melebihi stok tersedia.');
            }

            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'tgl_pinjam' => now(),
                'tgl_kembali_plan' => Carbon::parse($request->tgl_kembali_plan)->endOfDay(),
                'status' => 'diajukan',
            ]);

            DetailPinjam::create([
                'peminjaman_id' => $peminjaman->id,
                'alat_id' => $alat->id,
                'jumlah' => $request->jumlah,
            ]);

            DB::commit();

            return redirect()
                ->route('peminjam.peminjaman.index')
                ->with('success', 'Pengajuan peminjaman berhasil dikirim.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    // Melihat riwayat peminjaman user yang sedang login
    public function riwayatPeminjaman()
    {
        $peminjamans = Peminjaman::with('detailPinjam.alat')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['selesai', 'telat'])
            ->latest()
            ->paginate(10);

        return view('peminjam.riwayat', compact('peminjamans'));
    }

    public function peminjamanSaya()
    {
        $peminjamans = Peminjaman::with('detailPinjam.alat')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('peminjam.peminjaman.index', compact('peminjamans'));
    }

    public function detailPeminjaman(Peminjaman $peminjaman)
    {
        abort_unless($peminjaman->user_id === auth()->id(), 403);
        $peminjaman->load(['detailPinjam.alat.kategori', 'pengembalian.petugas']);

        return view('peminjam.peminjaman.show', compact('peminjaman'));
    }

    public function profil()
    {
        return view('peminjam.profil');
    }

    public function updateProfil(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);
        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}