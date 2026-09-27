<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use App\Models\Pengembalian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // Menampilkan Dashboard Admin & Log Aktivitas
    public function index()
    {
        // Statistics
        $totalAlat = Alat::count();
        $totalPeminjaman = Peminjaman::count();
        $peminjamananAktif = Peminjaman::where('status', 'dipinjam')->count();
        $pengembalianPending = Peminjaman::where('status', 'dipinjam')->count();
        $totalUser = User::where('role', '!=', 'admin')->count();
        
        // Recent logs
        $logs = LogAktivitas::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact('logs', 'totalAlat', 'totalPeminjaman', 'peminjamananAktif', 'pengembalianPending', 'totalUser'));
    }

    // CRUD Alat: Menampilkan daftar alat
    public function indexAlat(Request $request)
    {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                return $query->where('nama_alat', 'like', "%{$search}%")
                    ->orWhere('status_kondisi', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama_kategori', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.alat.index', compact('alats', 'search'));
    }

    // 2. Menampilkan form tambah alat
    public function createAlat()
    {
        $kategoris = Kategori::all();
        return view('admin.alat.create', compact('kategoris'));
    }

    // 3. Menyimpan alat baru
    public function storeAlat(Request $request)
    {
        $request->validate([
            'nama_alat'      => 'required|string|max:255',
            'kategori_id'    => 'required|exists:kategori,id',
            'stok'           => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi'      => 'nullable|string',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle Upload Gambar jika ada
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        Alat::create($data);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil ditambahkan.');
    }

    // 4. Menampilkan form edit alat
    public function editAlat($id)
    {
        $alat = Alat::findOrFail($id);
        $kategoris = Kategori::all();
        return view('admin.alat.edit', compact('alat', 'kategoris'));
    }

    // 5. Memperbarui data alat
    public function updateAlat(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'nama_alat'      => 'required|string|max:255',
            'kategori_id'    => 'required|exists:kategori,id',
            'stok'           => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi'      => 'nullable|string',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle Update Gambar jika ada file baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($alat->gambar && file_exists(public_path($alat->gambar))) {
                unlink(public_path($alat->gambar));
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }

        $alat->update($data);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil diperbarui.');
    }

    // 6. Menghapus data alat
    public function destroyAlat($id)
    {
        $alat = Alat::findOrFail($id);

        if ($alat->detailPinjam()->exists()) {
            return redirect()->route('admin.alat.index')
                ->with('error', 'Alat tidak dapat dihapus karena sudah digunakan dalam riwayat peminjaman.');
        }

        // Hapus file gambar fisik jika ada
        if ($alat->gambar && file_exists(public_path($alat->gambar))) {
            unlink(public_path($alat->gambar));
        }

        $alat->delete();

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil dihapus.');
    }

    // CRUD User (Manajemen User Admin, Petugas, Peminjam)
    public function indexUser(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%");
        })
            ->latest()
            ->paginate(10) // Tampilkan 10 data per halaman
            ->withQueryString(); // Memastikan parameter search tetap ada saat pindah halaman

        return view('admin.user.index', compact('users', 'search'));
    }

    public function createUser()
    {
        return view('admin.user.create');
    }

    // Menyimpan user baru ke database
    public function storeUser(Request $request)
    {
        $allowedRoles = $request->user()->isSuperAdmin()
            ? ['admin', 'petugas', 'peminjam']
            : ['petugas', 'peminjam'];
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in($allowedRoles)],
            'no_hp' => ['nullable', 'string', 'regex:/^[0-9+() .-]{8,20}$/'],
            'alamat' => 'nullable|string|max:1000',
            'foto_profile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ];
        if ($request->hasFile('foto_profile')) {
            $directory = public_path('storage/profile');
            if (! is_dir($directory)) mkdir($directory, 0755, true);
            $filename = $request->file('foto_profile')->hashName();
            $request->file('foto_profile')->move($directory, $filename);
            $data['foto_profile'] = 'storage/profile/' . $filename;
        }
        User::create($data);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    // Memperbarui data user
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $actor = $request->user();
        if ($user->isSuperAdmin() && ! $actor->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat mengubah Super Admin.');
        }
        if ($user->id === $actor->id && $request->input('role') !== $user->role) {
            abort(403, 'Anda tidak dapat mengubah role diri sendiri.');
        }
        $allowedRoles = $actor->isSuperAdmin()
            ? ['admin', 'petugas', 'peminjam']
            : ['petugas', 'peminjam'];

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => ['required', Rule::in($allowedRoles)],
            'no_hp' => ['nullable', 'string', 'regex:/^[0-9+() .-]{8,20}$/'],
            'alamat' => 'nullable|string|max:1000',
            'foto_profile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto_profile')) {
            $directory = public_path('storage/profile');
            if (! is_dir($directory)) mkdir($directory, 0755, true);
            if ($user->foto_profile && file_exists(public_path($user->foto_profile))) unlink(public_path($user->foto_profile));
            $filename = $request->file('foto_profile')->hashName();
            $request->file('foto_profile')->move($directory, $filename);
            $data['foto_profile'] = 'storage/profile/' . $filename;
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    // Menghapus user
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === request()->user()->id || ($user->isSuperAdmin() && ! request()->user()->isSuperAdmin())) {
            abort(403, 'Akun ini tidak dapat dihapus oleh Anda.');
        }
        if ($user->peminjaman()->whereIn('status', ['diajukan', 'dipinjam', 'telat'])->exists()) {
            return back()->with('error', 'User tidak dapat dihapus karena masih memiliki transaksi aktif.');
        }
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }

    public function indexKategori(Request $request)
    {
        $search = $request->input('search');

        $kategoris = Kategori::when($search, function ($query, $search) {
            return $query->where('nama_kategori', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(5)
        ->withQueryString();

        return view('admin.kategori.index', compact('kategoris', 'search'));
    }

    // 2. Menampilkan form tambah kategori
    public function createKategori()
    {
        return view('admin.kategori.create');
    }

    // 3. Menyimpan kategori baru
    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // 4. Menampilkan form edit kategori
    public function editKategori($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }

    // 5. Memperbarui kategori
    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    // 6. Menghapus kategori
    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        // Opsional: Cek apakah kategori masih dipakai oleh alat
        if ($kategori->alats()->count() > 0) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    // 1. Menampilkan daftar peminjaman
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->when($search, function ($query, $search) {
                return $query->where('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.peminjaman.index', compact('peminjamans', 'search'));
    }

    // 2. Menampilkan form tambah peminjaman
    public function createPeminjaman()
    {
        $users = User::where('role', 'peminjam')->get(); // Atau ambil semua user jika bebas
        $alats = Alat::where('stok', '>', 0)->get();

        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    // 3. Menyimpan data peminjaman baru
    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'tgl_pinjam'       => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id'          => 'required|array',
            'alat_id.*'        => 'distinct|exists:alat,id',
            'jumlah'           => 'required|array',
            'jumlah.*'         => 'integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Buat transaksi utama peminjaman
            $peminjaman = Peminjaman::create([
                'user_id'          => $request->user_id,
                'tgl_pinjam' => now(),
                'tgl_kembali_plan' => Carbon::parse($request->tgl_kembali_plan)->endOfDay(),
                'status'           => 'diajukan', // Status awal
            ]);

            // Simpan detail alat yang dipinjam
            foreach ($request->alat_id as $index => $alatId) {
                $jumlahPinjam = $request->jumlah[$index];
                $alat = Alat::findOrFail($alatId);

                // Validasi stok
                if ($alat->stok < $jumlahPinjam) {
                    throw new Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi.");
                }

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id'       => $alatId,
                    'jumlah'        => $jumlahPinjam,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil diajukan.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // 4. Memperbarui status peminjaman (Misal: dari diajukan -> dipinjam / selesai)
    public function updateStatusPeminjaman(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diajukan,dipinjam,selesai,telat,ditolak',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $peminjaman = Peminjaman::with('detailPinjam')
                    ->lockForUpdate()
                    ->findOrFail($id);
                $transitions = [
                    'diajukan' => ['dipinjam', 'ditolak'],
                    'dipinjam' => ['selesai', 'telat'],
                    'telat' => ['selesai'],
                    'selesai' => [],
                    'ditolak' => [],
                ];
                $statusLama = $peminjaman->status;
                $statusBaru = $request->status;
                if (!in_array($statusBaru, $transitions[$statusLama] ?? [], true)) {
                    throw new \RuntimeException('Perubahan status tidak diizinkan.');
                }

                if ($statusBaru === 'dipinjam') {
                    foreach ($peminjaman->detailPinjam->sortBy('alat_id') as $detail) {
                        $alat = Alat::lockForUpdate()->findOrFail($detail->alat_id);
                        if ($alat->status_kondisi !== 'Baik' || $alat->stok < $detail->jumlah) {
                            throw new Exception("Stok alat ({$alat->nama_alat}) tidak mencukupi.");
                        }
                        $alat->decrement('stok', $detail->jumlah);
                    }
                }

                if ($statusBaru === 'selesai') {
                    throw new \RuntimeException('Gunakan proses pengembalian untuk menyelesaikan peminjaman.');
                }

                $peminjaman->update(['status' => $statusBaru]);
            });
            return redirect()->route('admin.peminjaman.index')->with('success', 'Status peminjaman berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->with('error', $e instanceof \RuntimeException ? $e->getMessage() : 'Status peminjaman gagal diperbarui.');
        }
    }

    // 5. Menghapus data peminjaman
    public function destroyPeminjaman($id)
    {
        $peminjaman = Peminjaman::with('pengembalian')->findOrFail($id);

        if ($peminjaman->pengembalian()->exists() || in_array($peminjaman->status, ['dipinjam', 'selesai', 'telat'], true)) {
            return back()->with('error', 'Peminjaman yang sudah diproses tidak dapat dihapus karena diperlukan untuk histori dan laporan.');
        }

        $peminjaman->delete();

        return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }

    // ============================================
    // KELOLA PENGEMBALIAN - Monitoring & History
    // ============================================

    // Menampilkan daftar pengembalian alat
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian.petugas'])
            ->whereHas('pengembalian') // Hanya tampilkan peminjaman yang sudah dikembalikan
            ->when($search, function ($query, $search) {
                return $query->where('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('detailPinjam.alat', function ($q) use ($search) {
                        $q->where('nama_alat', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengembalian.index', compact('peminjamans', 'search'));
    }
}