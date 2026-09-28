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
            ->where('status', 'selesai')
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

    public function kembalikanPeminjaman(Request $request, Peminjaman $peminjaman)
    {
        try {
            DB::transaction(function () use ($peminjaman) {
                $locked = Peminjaman::lockForUpdate()->findOrFail($peminjaman->id);

                if ($locked->user_id !== auth()->id()) {
                    abort(403);
                }
                if (! in_array($locked->status, ['dipinjam', 'telat'], true) || $locked->pengembalian_diajukan_at || $locked->pengembalian()->exists()) {
                    throw new \RuntimeException('Pengajuan pengembalian sudah dikirim atau peminjaman tidak memenuhi syarat.');
                }

                $locked->update(['pengembalian_diajukan_at' => now()]);
            });

            return redirect()->route('peminjam.peminjaman.show', $peminjaman)->with('success', 'Pengajuan pengembalian berhasil dikirim kepada petugas.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
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
            'no_hp' => ['nullable', 'digits_between:11,13'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        unset($data['foto_profile']);
        if ($request->hasFile('foto_profile')) {
            $directory = public_path('storage/profile');
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            if ($user->foto_profile && file_exists(public_path($user->foto_profile))) {
                unlink(public_path($user->foto_profile));
            }

            $filename = $request->file('foto_profile')->hashName();
            $request->file('foto_profile')->move($directory, $filename);
            $data['foto_profile'] = 'storage/profile/' . $filename;
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}