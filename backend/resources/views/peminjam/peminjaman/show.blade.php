@extends('layouts.app')

@section('title', 'Detail Peminjaman')
@section('header-title', 'Detail Peminjaman')

@section('content')
<a href="{{ route('peminjam.peminjaman.index') }}" class="mb-5 inline-flex text-sm font-semibold text-gray-500 hover:text-gray-900">&larr; Kembali ke peminjaman saya</a>
<div class="mb-6 flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between"><div><p class="text-sm text-gray-500">Pengajuan #{{ $peminjaman->id }}</p><p class="mt-1 text-sm text-gray-700">Dibuat {{ $peminjaman->tgl_pinjam?->format('d M Y, H:i') }}</p></div>@include('components.status-badge', ['status' => $peminjaman->status])</div>
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm lg:col-span-2"><h2 class="mb-4 font-bold text-gray-900">Alat yang dipinjam</h2><div class="divide-y divide-gray-100">@foreach($peminjaman->detailPinjam as $detail)<div class="flex items-center justify-between gap-4 py-4 first:pt-0"><div><p class="font-semibold text-gray-900">{{ $detail->alat->nama_alat ?? 'Alat dihapus' }}</p><p class="text-sm text-gray-500">{{ $detail->alat->kategori->nama_kategori ?? 'Tanpa kategori' }}</p></div><span class="font-semibold text-gray-700">{{ $detail->jumlah }} unit</span></div>@endforeach</div></section>
    <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm"><h2 class="mb-4 font-bold text-gray-900">Jadwal</h2><dl class="space-y-4 text-sm"><div><dt class="text-gray-500">Tanggal pinjam</dt><dd class="mt-1 font-semibold text-gray-900">{{ $peminjaman->tgl_pinjam?->format('d M Y') }}</dd></div><div><dt class="text-gray-500">Rencana kembali</dt><dd class="mt-1 font-semibold text-gray-900">{{ $peminjaman->tgl_kembali_plan?->format('d M Y') }}</dd></div></dl>@if($peminjaman->pengembalian)<div class="mt-6 border-t border-gray-100 pt-4"><h3 class="font-semibold text-gray-900">Pengembalian</h3><p class="mt-2 text-sm text-gray-600">Dikembalikan {{ $peminjaman->pengembalian->tgl_kembali?->format('d M Y, H:i') }}</p><p class="mt-1 text-sm text-gray-600">Kondisi: {{ $peminjaman->pengembalian->kondisi_kembali }}</p></div>@endif</section>
</div>
@endsection
