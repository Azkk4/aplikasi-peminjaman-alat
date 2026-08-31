@extends('layouts.app')

@section('title', 'Cetak Laporan')
@section('header-title', 'Laporan Peminjaman & Pengembalian Alat')

@section('content')
<div class="space-y-6">
    <!-- Card Filter Laporan -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Filter Periode & Status Laporan</h3>
        
        <form action="{{ route('petugas.laporan.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tanggal Mulai</label>
                <input type="date" name="tgl_mulai" value="{{ request('tgl_mulai') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tanggal Selesai</label>
                <input type="date" name="tgl_selesai" value="{{ request('tgl_selesai') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status Transaksi</label>
                <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Status</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="telat" {{ request('status') == 'telat' ? 'selected' : '' }}>Telat</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-lg transition shadow-sm">
                    Filter
                </button>
                <button type="button" onclick="window.print()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-semibold rounded-lg transition shadow-sm">
                    Cetak
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Data Rekapitulasi Laporan -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-base font-bold text-gray-800">Rekap Data Peminjaman</h3>
            <span class="text-xs text-gray-500">Total: {{ count($laporans ?? []) }} Data Ditemukan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">No</th>
                        <th class="py-3 px-4 border-b">Nama Peminjam</th>
                        <th class="py-3 px-4 border-b">Tgl Pinjam</th>
                        <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        <th class="py-3 px-4 border-b">Detail Alat</th>
                        <th class="py-3 px-4 border-b text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($laporans ?? [] as $index => $item)
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="py-3 px-4 border-b text-xs text-gray-500">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 border-b font-medium text-gray-900">
                                {{ $item->user->name ?? 'User Dihapus' }}
                            </td>
                            <td class="py-3 px-4 border-b text-xs">{{ $item->tgl_pinjam }}</td>
                            <td class="py-3 px-4 border-b text-xs">{{ $item->tgl_kembali_plan }}</td>
                            <td class="py-3 px-4 border-b">
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    @foreach($item->detailPinjam as $detail)
                                        <li>
                                            {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} 
                                            <span class="text-gray-500 font-semibold">({{ $detail->jumlah }} pcs)</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3 px-4 border-b text-center">
                                @if($item->status == 'dipinjam')
                                    <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded">Dipinjam</span>
                                @elseif($item->status == 'selesai')
                                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded">Selesai</span>
                                @elseif($item->status == 'telat')
                                    <span class="text-xs font-semibold text-red-700 bg-red-50 px-2.5 py-1 rounded">Telat</span>
                                @else
                                    <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-2.5 py-1 rounded">{{ ucfirst($item->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">
                                Tidak ada data laporan yang sesuai kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection