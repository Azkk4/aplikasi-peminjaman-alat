@extends('layouts.app')

@section('title', 'Pemantauan Pengembalian')
@section('header-title', 'Pemantauan & Pengembalian Alat')

@section('content')
<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
    <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h3 class="text-lg font-bold text-gray-800">Daftar Peminjaman Aktif (Belum Kembali)</h3>

        <!-- Form Pencarian Peminjam -->
        <form action="{{ route('petugas.pengembalian.index') }}" method="GET" class="flex w-full md:w-80">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam..."
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('petugas.pengembalian.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Daftar Pengembalian -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-300">
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Peminjam</th>
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal Pinjam</th>
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tenggat Kembali</th>
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Alat yang Dipinjam</th>
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    <th class="py-4 px-5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($peminjamans as $item)
                    @php($returnRequested = (bool) $item->pengembalian_diajukan_at)
                    @php($isOldReturn = (string) old('_return_id') === (string) $item->id)
                    <tr class="align-top transition hover:bg-gray-50">
                        <td class="border-b px-4 py-3 font-medium text-gray-900">{{ $item->user->name ?? 'User Dihapus' }}</td>
                        <td class="border-b px-4 py-3 whitespace-nowrap">{{ $item->tgl_pinjam?->format('d M Y') }}</td>
                        <td class="border-b px-4 py-3 whitespace-nowrap">{{ $item->tgl_kembali_plan?->format('d M Y') }}</td>
                        <td class="border-b px-4 py-3">
                            <ul class="space-y-1 text-xs">
                                @foreach($item->detailPinjam as $detail)
                                    <li><span class="font-semibold">{{ $detail->alat->nama_alat ?? 'Alat dihapus' }}</span> ({{ $detail->jumlah }} unit)</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="border-b px-4 py-3">@include('components.status-badge', ['status' => $item->display_status])</td>
                        <td class="border-b px-4 py-3">
                            <details name="return-details" class="min-w-44" @if((string) old('_return_id') === (string) $item->id) open @endif>
                                <summary class="cursor-pointer list-none rounded-md border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">
                                    <span class="detail-open-label">Lihat Detail</span>
                                    <span class="detail-close-label">Tutup Detail</span>
                                </summary>
                                <div class="mt-2 min-w-64 rounded-md border border-gray-200 bg-gray-50 p-3 text-left">
                                    <p class="text-xs font-semibold text-gray-700">{{ $item->user->name ?? 'User dihapus' }}</p>
                                    <ul class="mt-1 space-y-1 text-xs text-gray-600">
                                        @foreach($item->detailPinjam as $detail)
                                            <li>{{ $detail->alat->nama_alat ?? 'Alat dihapus' }} · {{ $detail->jumlah }} unit</li>
                                        @endforeach
                                    </ul>
                                    @if($returnRequested)
                                        <form action="{{ route('petugas.pengembalian.proses', $item->id) }}" method="POST" class="mt-3 space-y-3" data-confirm="true" data-confirm-title="Terima pengembalian alat ini?" data-confirm-message="Peminjam: {{ $item->user->name ?? 'User dihapus' }}. Alat: {{ $item->detailPinjam->map(fn ($detail) => ($detail->alat->nama_alat ?? 'Alat dihapus') . ' (' . $detail->jumlah . ' unit)')->join(', ') }}." data-confirm-accept="Terima Pengembalian">
                                            @csrf
                                            <input type="hidden" name="_return_id" value="{{ $item->id }}">
                                            <div>
                                                <label for="kondisi_kembali_{{ $item->id }}" class="block text-xs font-semibold text-gray-700">Kondisi Kembali</label>
                                                <select id="kondisi_kembali_{{ $item->id }}" name="kondisi_kembali" data-confirm-detail="Kondisi Kembali" required class="mt-1 w-full rounded-md border border-gray-300 bg-white px-2 py-1.5 text-sm">
                                                    @foreach(['Baik', 'Rusak Ringan', 'Rusak Berat'] as $condition)
                                                        <option value="{{ $condition }}" {{ old('kondisi_kembali', $isOldReturn ? 'Baik' : null) === $condition ? 'selected' : '' }}>{{ $condition }}</option>
                                                    @endforeach
                                                </select>
                                                @if($isOldReturn)
                                                    @include('components.field-error', ['field' => 'kondisi_kembali'])
                                                @endif
                                            </div>
                                            <div>
                                                <label for="denda_{{ $item->id }}" class="block text-xs font-semibold text-gray-700">Denda</label>
                                                <div class="mt-1 flex items-center rounded-md border border-gray-300 bg-white px-2">
                                                    <span class="text-sm text-gray-500">Rp</span>
                                                    <input id="denda_{{ $item->id }}" type="number" name="denda" data-confirm-detail="Denda" data-confirm-format="rupiah" min="0" step="1" inputmode="numeric" value="{{ old('denda', 0) }}" class="w-full border-0 px-2 py-1.5 text-sm focus:outline-none focus:ring-0">
                                                </div>
                                                @if($isOldReturn)
                                                    @include('components.field-error', ['field' => 'denda'])
                                                @endif
                                            </div>
                                            <button type="submit" class="w-full rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">Terima Pengembalian</button>
                                        </form>
                                    @else
                                        <p class="mt-3 text-xs text-gray-500">Belum ada pengajuan pengembalian dari peminjam. Stok belum dipulihkan.</p>
                                    @endif
                                </div>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">
                            Tidak ada alat yang sedang dipinjam saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection