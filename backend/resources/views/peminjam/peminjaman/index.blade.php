@extends('layouts.app')

@section('title', 'Peminjaman Saya')
@section('header-title', 'Peminjaman Saya')

@section('content')
<div class="mb-6 flex items-end justify-between gap-4"><div><p class="text-sm font-medium text-emerald-600">Pantau pengajuanmu</p><h1 class="mt-1 text-2xl font-bold text-gray-900">Peminjaman saya</h1></div><a href="{{ route('peminjam.katalog.index') }}" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Ajukan baru</a></div>
<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto"><table class="w-full min-w-[700px] text-left text-sm"><thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500"><tr><th class="px-5 py-4">Tanggal pinjam</th><th class="px-5 py-4">Alat</th><th class="px-5 py-4">Rencana kembali</th><th class="px-5 py-4">Status</th><th class="px-5 py-4"></th></tr></thead><tbody class="divide-y divide-gray-100">
@forelse($peminjamans as $peminjaman)
<tr class="hover:bg-gray-50"><td class="px-5 py-4 text-gray-700">{{ $peminjaman->tgl_pinjam?->format('d M Y') }}</td><td class="px-5 py-4"><p class="font-semibold text-gray-900">{{ $peminjaman->detailPinjam->pluck('alat.nama_alat')->filter()->join(', ') }}</p><p class="mt-1 text-xs text-gray-500">{{ $peminjaman->detailPinjam->sum('jumlah') }} unit</p></td><td class="px-5 py-4 text-gray-700">{{ $peminjaman->tgl_kembali_plan?->format('d M Y') }}</td><td class="px-5 py-4">@include('components.status-badge', ['status' => $peminjaman->status])</td><td class="px-5 py-4 text-right"><a href="{{ route('peminjam.peminjaman.show', $peminjaman) }}" class="font-semibold text-emerald-600 hover:text-emerald-700">Detail</a></td></tr>
@empty
<tr><td colspan="5">@include('components.empty-state', ['title' => 'Belum ada pengajuan', 'message' => 'Pilih alat dari katalog untuk membuat pengajuan pertama.', 'action' => 'Buka katalog', 'actionUrl' => route('peminjam.katalog.index')])</td></tr>
@endforelse
    </tbody></table></div>
</div><div class="mt-5">{{ $peminjamans->links() }}</div>
@endsection
