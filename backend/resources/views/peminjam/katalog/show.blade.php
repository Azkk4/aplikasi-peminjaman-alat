@extends('layouts.app')

@section('title', $alat->nama_alat)
@section('header-title', 'Detail Alat')

@section('content')
<a href="{{ route('peminjam.katalog.index') }}" class="mb-5 inline-flex text-sm font-semibold text-gray-500 hover:text-gray-900">&larr; Kembali ke katalog</a>
<div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm lg:col-span-2">
        <div class="flex aspect-[4/3] items-center justify-center bg-gray-100">
            <img src="{{ $alat->image_url }}" alt="{{ $alat->nama_alat }}" class="h-full w-full object-cover">
        </div>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm lg:col-span-3">
        <p class="text-sm font-medium text-emerald-600">{{ $alat->kategori->nama_kategori ?? 'Tanpa kategori' }}</p>
        <h1 class="mt-1 text-3xl font-bold text-gray-900">{{ $alat->nama_alat }}</h1>
        <div class="mt-5 flex flex-wrap gap-2">
            @include('components.kondisi-badge', ['kondisi' => $alat->status_kondisi])
            <span class="rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-800">{{ $alat->stok }} tersedia</span>
        </div>
        @if($alat->deskripsi)
            <div class="mt-6 border-t border-gray-100 pt-5"><h2 class="text-sm font-bold uppercase tracking-wide text-gray-500">Deskripsi</h2><p class="mt-2 leading-7 text-gray-600">{{ $alat->deskripsi }}</p></div>
        @endif
        @if($alat->stok > 0 && $alat->status_kondisi === 'Baik')
            <a href="{{ route('peminjam.peminjaman.create', ['alat_id' => $alat->id]) }}" class="mt-7 inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Ajukan peminjaman</a>
        @else
            <span class="mt-7 inline-flex w-full items-center justify-center rounded-lg bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-500">Stok sedang habis</span>
        @endif
    </div>
</div>
@endsection
