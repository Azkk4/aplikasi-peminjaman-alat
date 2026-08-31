@extends('layouts.app')

@section('title', 'Kelola Alat - Panel Admin')
@section('header-title', 'Manajemen Data Alat')

@section('content')
@if(session('success'))
<div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
    <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
        <h3 class="text-lg font-bold text-gray-800">Daftar Alat Laboratorium</h3>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Form Search -->
            <form action="{{ route('admin.alat.index') }}" method="GET" class="flex w-full md:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alat, kategori..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.alat.index') }}"
                        class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                        Reset
                    </a>
                @endif
            </form>

            <!-- Tombol Tambah -->
            <a href="{{ route('admin.alat.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
                + Tambah Alat
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b-2 border-gray-300">
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Gambar</th>
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Alat</th>
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kategori</th>
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Stok</th>
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kondisi</th>
                    <th class="py-4 px-5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($alats as $alat)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-3 px-4 border-b">
                        <img src="{{ $alat->image_url }}" alt="{{ $alat->nama_alat }}" class="w-12 h-12 object-cover rounded-lg border"
                             onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">
                    </td>
                    <td class="py-3 px-4 border-b font-medium text-gray-900">{{ $alat->nama_alat }}</td>
                    <td class="py-3 px-4 border-b">{{ $alat->kategori->nama_kategori ?? '-' }}</td>
                    <td class="py-3 px-4 border-b font-semibold">{{ $alat->stok }}</td>
                    <td class="py-3 px-4 border-b">
                        @include('components.kondisi-badge', ['kondisi' => $alat->status_kondisi])
                    </td>
                    <td class="py-3 px-4 border-b">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.alat.edit', $alat->id) }}"
                                class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                Edit
                            </a>
                            <form action="{{ route('admin.alat.destroy', $alat->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus alat ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-4 text-center text-gray-500">Belum ada data alat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-gray-200 bg-gray-50">
        {{ $alats->links() }}
    </div>
</div>
@endsection