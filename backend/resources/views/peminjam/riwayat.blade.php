@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')
@section('header-title', 'Riwayat Peminjaman')

@section('content')
<div class="mb-6"><p class="text-sm font-medium text-emerald-600">Catatan aktivitasmu</p><h1 class="mt-1 text-2xl font-bold text-gray-900">Riwayat peminjaman</h1><p class="mt-1 text-sm text-gray-500">Peminjaman yang sudah selesai atau melewati batas waktu.</p></div>
<div class="space-y-3">@forelse($peminjamans as $peminjaman)<a href="{{ route('peminjam.peminjaman.show', $peminjaman) }}" class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-5 shadow-sm transition hover:border-emerald-200 hover:shadow-md sm:flex-row sm:items-center sm:justify-between"><div><p class="font-semibold text-gray-900">{{ $peminjaman->detailPinjam->pluck('alat.nama_alat')->filter()->join(', ') }}</p><p class="mt-1 text-sm text-gray-500">{{ $peminjaman->tgl_pinjam?->format('d M Y') }} &middot; rencana kembali {{ $peminjaman->tgl_kembali_plan?->format('d M Y') }}</p></div><div class="flex items-center gap-3">@include('components.status-badge', ['status' => $peminjaman->status])<span class="text-gray-400">&rarr;</span></div></a>@empty<div class="rounded-lg border border-gray-200 bg-white">@include('components.empty-state', ['title' => 'Belum ada riwayat', 'message' => 'Riwayat peminjaman selesai akan muncul di sini.'])</div>@endforelse</div>
<div class="mt-5">{{ $peminjamans->links() }}</div>
@endsection
