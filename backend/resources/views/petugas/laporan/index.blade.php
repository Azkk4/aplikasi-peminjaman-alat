@extends('layouts.app')

@section('title', 'Laporan Transaksi Peminjaman')
@section('header-title', 'Laporan Peminjaman & Pengembalian Alat')
@section('body-class', 'report-page')

@push('head')
    @include('components.report-styles')
@endpush

@section('content')
<div class="report-screen space-y-6">
    <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-5 py-4">
            <h2 class="text-lg font-bold text-gray-800">Filter Laporan</h2>
        </div>
        <form action="{{ route('petugas.laporan.index') }}" method="GET" class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 xl:grid-cols-4 xl:items-end">
            <div>
                <label for="status" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Status Peminjaman</label>
                <select id="status" name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    <option value="">Semua Status</option>
                    @foreach(['diajukan', 'dipinjam', 'telat', 'selesai', 'ditolak'] as $option)
                        <option value="{{ $option }}" {{ $status === $option ? 'selected' : '' }}>{{ ucfirst($option) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="tgl_mulai" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Tanggal Mulai</label>
                <input id="tgl_mulai" type="date" name="tgl_mulai" value="{{ $tglMulai }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
            </div>
            <div>
                <label for="tgl_selesai" class="mb-1 block text-xs font-semibold uppercase text-gray-600">Tanggal Selesai</label>
                <input id="tgl_selesai" type="date" name="tgl_selesai" value="{{ $tglSelesai }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-900">Filter</button>
                <a href="{{ route('petugas.laporan.index') }}" class="rounded-lg border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-200">Reset</a>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-200 bg-gray-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Hasil Rekap Laporan</h2>
                <p class="mt-1 text-sm text-gray-500">{{ number_format($laporans->count(), 0, ',', '.') }} transaksi</p>
            </div>
            <button type="button" onclick="window.print()" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">Cetak / Print Laporan</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] border-collapse text-left text-sm">
                <thead class="bg-gray-100 text-xs uppercase tracking-wide text-gray-600">
                    <tr>
                        <th class="border-b border-gray-200 px-4 py-3 text-center">No.</th>
                        <th class="border-b border-gray-200 px-4 py-3">Peminjam</th>
                        <th class="border-b border-gray-200 px-4 py-3">Tgl Pinjam</th>
                        <th class="border-b border-gray-200 px-4 py-3">Rencana Kembali</th>
                        <th class="border-b border-gray-200 px-4 py-3">Status</th>
                        <th class="border-b border-gray-200 px-4 py-3">Detail Alat</th>
                        <th class="border-b border-gray-200 px-4 py-3 text-right">Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($laporans as $index => $item)
                        <tr class="align-top hover:bg-gray-50">
                            <td class="px-4 py-3 text-center text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $item->user->name ?? 'User tidak tersedia' }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $item->tgl_pinjam?->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="whitespace-nowrap px-4 py-3">{{ $item->tgl_kembali_plan?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-4 py-3">@include('components.status-badge', ['status' => $item->display_status])</td>
                            <td class="px-4 py-3">
                                <ul class="space-y-1">
                                    @forelse($item->detailPinjam as $detail)
                                        <li>{{ $detail->alat->nama_alat ?? 'Alat tidak tersedia' }} <span class="text-xs text-gray-500">({{ number_format($detail->jumlah, 0, ',', '.') }} unit)</span></li>
                                    @empty
                                        <li class="text-gray-500">Detail alat tidak tersedia</li>
                                    @endforelse
                                </ul>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">{{ $item->pengembalian ? 'Rp ' . number_format($item->pengembalian->denda ?? 0, 0, ',', '.') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">Tidak ada data transaksi untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@include('components.report-document')
@endsection