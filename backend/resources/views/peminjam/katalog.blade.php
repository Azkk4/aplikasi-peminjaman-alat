@extends('layouts.app')

@section('title', 'Katalog Alat')
@section('header-title', 'Katalog Alat')
@section('content')
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
    <div><p class="text-sm font-medium text-emerald-600">Temukan alat yang kamu perlukan</p><h1 class="mt-1 text-2xl font-bold text-gray-900">Katalog alat</h1></div>
    <a href="{{ route('peminjam.peminjaman.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">Lihat peminjaman saya &rarr;</a>
 </div>
<form method="GET" class="mb-6 grid grid-cols-1 gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_220px_auto]">
    <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama alat..." class="rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
    <select name="kategori_id" class="rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"><option value="">Semua kategori</option>@foreach($kategoris as $kategori)<option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>@endforeach</select>
    <button class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-700">Cari</button>
 </form>
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
    @forelse($alats as $alat)
        <article class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <img src="{{ $alat->image_url }}" alt="{{ $alat->nama_alat }}" class="h-40 w-full object-cover bg-gray-100">
            <div class="p-5"><div class="flex items-start justify-between gap-3"><div><p class="text-xs font-medium text-emerald-600">{{ $alat->kategori->nama_kategori ?? 'Tanpa kategori' }}</p><h2 class="mt-1 text-lg font-bold text-gray-900">{{ $alat->nama_alat }}</h2></div><span class="whitespace-nowrap text-xs font-semibold text-emerald-700">{{ $alat->stok }} tersedia</span></div><div class="mt-4">@include('components.kondisi-badge', ['kondisi' => $alat->status_kondisi])</div><a href="{{ route('peminjam.katalog.show', $alat) }}" class="mt-5 block rounded-lg border border-gray-300 px-4 py-2.5 text-center text-sm font-semibold text-gray-700 transition hover:border-emerald-600 hover:text-emerald-700">Lihat detail</a></div>
        </article>
    @empty
        <div class="col-span-full rounded-lg border border-gray-200 bg-white">@include('components.empty-state', ['title' => 'Alat tidak ditemukan', 'message' => 'Coba ubah kata kunci atau kategori pencarian.'])</div>
    @endforelse
 </div>
<div class="mt-6">{{ $alats->links() }}</div>
@endsection