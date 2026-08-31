@extends('layouts.app')

@section('title', 'Persetujuan Peminjaman')
@section('header-title', 'Daftar Pengajuan Peminjaman Alat')

@section('content')
@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
    <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h3 class="text-lg font-bold text-gray-800">Menunggu Verifikasi Persetujuan</h3>

        <!-- Form Pencarian Peminjam -->
        <form action="{{ route('petugas.peminjaman.index') }}" method="GET" class="flex w-full md:w-80">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam..."
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('petugas.peminjaman.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Daftar Pengajuan -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-300">
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Peminjam</th>
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal Pinjam</th>
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Rencana Kembali</th>
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Detail Alat</th>
                    <th class="py-4 px-5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($peminjamans as $item)
                    <tr class="hover:bg-gray-50 transition align-top">
                        <td class="py-3 px-4 border-b font-medium text-gray-900">
                            {{ $item->user->name ?? 'User Dihapus' }}
                        </td>
                        <td class="py-3 px-4 border-b">{{ $item->tgl_pinjam }}</td>
                        <td class="py-3 px-4 border-b">{{ $item->tgl_kembali_plan }}</td>
                        <td class="py-3 px-4 border-b">
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                @foreach($item->detailPinjam as $detail)
                                    <li>
                                        <span class="font-semibold">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                        (Jumlah: {{ $detail->jumlah }})
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="py-3 px-4 border-b">
                            @if($item->status == 'diajukan')
                                <div class="flex flex-col gap-2 w-full">
                                    <!-- Tombol Setujui -->
                                    <form action="{{ route('petugas.peminjaman.setujui', $item->id) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Setujui peminjaman alat ini?')"
                                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm inline-flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Setujui</span>
                                        </button>
                                    </form>

                                    <!-- Tombol Tolak -->
                                    <form action="{{ route('petugas.peminjaman.tolak', $item->id) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Yakin ingin menolak pengajuan peminjaman ini?')"
                                            class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm inline-flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Tolak</span>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-blue-50 text-blue-700 rounded-full">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                                    </svg>
                                    {{ ucfirst($item->status) }}
                                </span>
                                    {{ ucfirst($item->status) }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500">
                            Tidak ada pengajuan peminjaman baru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection