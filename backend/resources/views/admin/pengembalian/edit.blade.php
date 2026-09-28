@extends('layouts.app')

@section('title', 'Edit Pengembalian - Panel Admin')
@section('header-title', 'Koreksi Data Pengembalian')

@section('content')
<div class="mx-auto max-w-xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <div class="mb-5">
        <p class="text-sm text-gray-500">Peminjam</p>
        <p class="font-semibold text-gray-900">{{ $pengembalian->peminjaman->user->name ?? 'User dihapus' }}</p>
        <p class="mt-2 text-sm text-gray-600">
            {{ $pengembalian->peminjaman->detailPinjam->map(fn ($detail) => ($detail->alat->nama_alat ?? 'Alat dihapus') . ' (' . $detail->jumlah . ' unit)')->join(', ') }}
        </p>
    </div>

    <form action="{{ route('admin.pengembalian.update', $pengembalian) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="kondisi_kembali" class="mb-2 block text-sm font-semibold text-gray-700">Kondisi Kembali</label>
            <select id="kondisi_kembali" name="kondisi_kembali" required class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
                @foreach(['Baik', 'Rusak Ringan', 'Rusak Berat'] as $condition)
                    <option value="{{ $condition }}" {{ old('kondisi_kembali', $pengembalian->kondisi_kembali) === $condition ? 'selected' : '' }}>{{ $condition }}</option>
                @endforeach
            </select>
            @include('components.field-error', ['field' => 'kondisi_kembali'])
        </div>
        <div class="mb-6">
            <label for="denda" class="mb-2 block text-sm font-semibold text-gray-700">Denda (Rupiah)</label>
            <div class="flex items-center rounded-lg border border-gray-300 px-3">
                <span class="text-sm text-gray-500">Rp</span>
                <input id="denda" type="number" name="denda" min="0" step="1" inputmode="numeric" value="{{ old('denda', $pengembalian->denda) }}" required class="w-full border-0 px-2 py-2.5 text-sm focus:outline-none focus:ring-0">
            </div>
            @include('components.field-error', ['field' => 'denda'])
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.pengembalian.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
