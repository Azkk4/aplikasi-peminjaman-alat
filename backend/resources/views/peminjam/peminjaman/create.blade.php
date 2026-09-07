@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')
@section('header-title', 'Ajukan Peminjaman')

@section('content')
<div class="mx-auto max-w-2xl">
    <div class="mb-6"><p class="text-sm font-medium text-emerald-600">Katalog alat</p><h1 class="mt-1 text-2xl font-bold text-gray-900">Buat pengajuan baru</h1><p class="mt-1 text-sm text-gray-500">Pilih alat dan tentukan kapan alat akan dikembalikan.</p></div>
    @if($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700"><ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form action="{{ route('peminjam.peminjaman.store') }}" method="POST" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        <label for="alat_id" class="block text-sm font-semibold text-gray-700">Alat yang dipinjam</label>
        <select id="alat_id" name="alat_id" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
            <option value="">Pilih alat</option>
            @foreach($alats as $alat)<option value="{{ $alat->id }}" {{ old('alat_id', $selectedAlat?->id) == $alat->id ? 'selected' : '' }}>{{ $alat->nama_alat }} ({{ $alat->stok }} tersedia)</option>@endforeach
        </select>
        <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div><label for="jumlah" class="block text-sm font-semibold text-gray-700">Jumlah</label><input id="jumlah" name="jumlah" type="number" min="1" value="{{ old('jumlah', 1) }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"></div>
            <div><label for="tgl_kembali_plan" class="block text-sm font-semibold text-gray-700">Rencana tanggal kembali</label><input id="tgl_kembali_plan" name="tgl_kembali_plan" type="date" min="{{ now()->addDay()->format('Y-m-d') }}" value="{{ old('tgl_kembali_plan') }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"></div>
        </div>
        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><a href="{{ route('peminjam.katalog.index') }}" class="rounded-lg px-4 py-2.5 text-center text-sm font-semibold text-gray-600 hover:bg-gray-100">Batal</a><button class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">Kirim pengajuan</button></div>
    </form>
</div>
@endsection
