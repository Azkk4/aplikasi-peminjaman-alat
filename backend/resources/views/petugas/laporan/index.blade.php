@extends('layouts.app')

@section('title', 'Laporan Transaksi Peminjaman')
@section('header-title', 'Laporan Peminjaman & Pengembalian Alat')
@section('body-class', 'report-page')

@push('head')
    @include('components.report-styles')
@endpush

@section('content')
<div class="report-actions">
    <a href="{{ route('petugas.laporan.index') }}" class="report-button report-button-secondary">Reset Filter</a>
    <button type="button" class="report-button" onclick="window.print()">Cetak Laporan</button>
</div>

<form action="{{ route('petugas.laporan.index') }}" method="GET" class="report-filters">
    <label>Tanggal mulai<input type="date" name="tgl_mulai" value="{{ $tglMulai }}"></label>
    <label>Tanggal selesai<input type="date" name="tgl_selesai" value="{{ $tglSelesai }}"></label>
    <label>Status
        <select name="status">
            <option value="">Semua status</option>
            @foreach(['diajukan', 'dipinjam', 'telat', 'selesai', 'ditolak'] as $option)
                <option value="{{ $option }}" {{ $status === $option ? 'selected' : '' }}>{{ ucfirst($option) }}</option>
            @endforeach
        </select>
    </label>
    <button type="submit" class="report-button">Terapkan Filter</button>
</form>

<main class="report-sheet">
    <header class="report-masthead">
        <div>
            <p class="report-organization">Sistem Peminjaman Alat</p>
            <h1 class="report-title">Laporan Transaksi Peminjaman</h1>
        </div>
        <p class="report-generated">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    </header>

    <section class="report-meta" aria-label="Informasi laporan">
        <span><strong>Periode:</strong>
            @if($tglMulai || $tglSelesai)
                {{ $tglMulai ? \Carbon\Carbon::parse($tglMulai)->format('d/m/Y') : 'Awal data' }}
                sampai
                {{ $tglSelesai ? \Carbon\Carbon::parse($tglSelesai)->format('d/m/Y') : 'Hari ini' }}
            @else
                Semua periode
            @endif
        </span>
        <span><strong>Status:</strong> {{ $status ? ucfirst(str_replace('_', ' ', $status)) : 'Semua status' }}</span>
        <span><strong>Jumlah transaksi:</strong> {{ number_format($laporans->count(), 0, ',', '.') }}</span>
    </section>

    <div class="report-table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th class="column-number">No.</th>
                    <th class="column-name">Nama Peminjam</th>
                    <th class="column-date">Tanggal Pinjam</th>
                    <th class="column-date">Rencana Kembali</th>
                    <th class="column-tools">Alat dan Jumlah</th>
                    <th class="column-status">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporans as $index => $item)
                    <tr>
                        <td class="column-number">{{ $index + 1 }}</td>
                        <td>{{ $item->user->name ?? 'User tidak tersedia' }}</td>
                        <td>{{ $item->tgl_pinjam?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ $item->tgl_kembali_plan?->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            <ul class="tool-list">
                                @forelse($item->detailPinjam as $detail)
                                    <li>{{ $detail->alat->nama_alat ?? 'Alat tidak tersedia' }} <strong>({{ number_format($detail->jumlah, 0, ',', '.') }} unit)</strong></li>
                                @empty
                                    <li>Detail alat tidak tersedia</li>
                                @endforelse
                            </ul>
                        </td>
                        <td><span class="status-label">{{ ucfirst(str_replace('_', ' ', $item->display_status)) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="report-empty">Tidak ada data transaksi untuk filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="report-total">Total {{ number_format($laporans->count(), 0, ',', '.') }} transaksi</p>
</main>
@endsection