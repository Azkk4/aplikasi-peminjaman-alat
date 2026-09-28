@extends('layouts.app')

@section('title', 'Tambah Kategori - Panel Admin')
@section('header-title', 'Tambah Kategori Alat')

@section('content')
<div class="max-w-xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
	<form action="{{ route('admin.kategori.store') }}" method="POST">
		@csrf
		<div class="mb-4">
			<label for="nama_kategori" class="mb-2 block text-sm font-semibold text-gray-700">Nama Kategori</label>
			<input id="nama_kategori" type="text" name="nama_kategori" value="{{ old('nama_kategori') }}" maxlength="255" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
			@include('components.field-error', ['field' => 'nama_kategori'])
		</div>
		<div class="flex justify-end gap-2">
			<a href="{{ route('admin.kategori.index') }}" class="rounded-lg bg-gray-300 px-4 py-2 text-sm font-semibold text-gray-800">Batal</a>
			<button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan</button>
		</div>
	</form>
</div>
@endsection
