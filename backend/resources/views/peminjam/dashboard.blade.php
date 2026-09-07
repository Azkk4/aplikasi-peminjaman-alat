@extends('layouts.app')

@section('title', 'Dashboard Peminjam')
@section('header-title', 'Dashboard Peminjam')

@section('content')
<div class="mb-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
    <div>
        <p class="text-sm font-medium text-emerald-600">Selamat datang kembali</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h1>
        <p class="mt-1 text-sm text-gray-500">Temukan alat yang kamu perlukan dan pantau pengajuanmu.</p>
    </div>
    <a href="{{ route('peminjam.katalog.index') }}" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Cari alat</a>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    @foreach([['Menunggu persetujuan', $peminjamananMenunggu, 'amber'], ['Sedang dipinjam', $peminjamanBerlangsung, 'blue'], ['Peminjaman selesai', $peminjamanSelesai, 'emerald']] as [$label, $value, $color])
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-bold text-{{ $color }}-600">{{ $value }}</p>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 gap-6 xl:grid-cols-5">
    <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm xl:col-span-3">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <h2 class="font-bold text-gray-900">Peminjaman terbaru</h2>
            <a href="{{ route('peminjam.peminjaman.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">Lihat semua</a>
        </div>
        @forelse($peminjamanTerbaru as $peminjaman)
            <a href="{{ route('peminjam.peminjaman.show', $peminjaman) }}" class="flex items-center justify-between gap-4 border-b border-gray-100 px-5 py-4 transition last:border-0 hover:bg-gray-50">
                <div>
                    <p class="font-semibold text-gray-900">{{ $peminjaman->detailPinjam->pluck('alat.nama_alat')->filter()->join(', ') ?: 'Detail alat tidak tersedia' }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ $peminjaman->tgl_pinjam?->format('d M Y') }} &middot; kembali {{ $peminjaman->tgl_kembali_plan?->format('d M Y') }}</p>
                </div>
                @include('components.status-badge', ['status' => $peminjaman->status])
            </a>
        @empty
            @include('components.empty-state', ['title' => 'Belum ada peminjaman', 'message' => 'Pengajuan yang kamu buat akan tampil di sini.', 'action' => 'Lihat katalog', 'actionUrl' => route('peminjam.katalog.index')])
        @endforelse
    </section>

    <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm xl:col-span-2">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Alat tersedia</h2>
            <a href="{{ route('peminjam.katalog.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">Katalog</a>
        </div>
        <div class="space-y-3">
            @forelse($alatTersedia as $alat)
                <a href="{{ route('peminjam.katalog.show', $alat) }}" class="block rounded-lg border border-gray-100 p-3 transition hover:border-emerald-200 hover:bg-emerald-50">
                    <div class="flex items-center justify-between gap-3">
                        <div><p class="font-semibold text-gray-900">{{ $alat->nama_alat }}</p><p class="text-xs text-gray-500">{{ $alat->kategori->nama_kategori ?? 'Tanpa kategori' }}</p></div>
                        <span class="text-xs font-semibold text-emerald-700">{{ $alat->stok }} tersedia</span>
                    </div>
                </a>
            @empty
                <p class="py-8 text-center text-sm text-gray-500">Belum ada alat tersedia.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
