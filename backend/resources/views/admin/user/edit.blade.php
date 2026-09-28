@extends('layouts.app')

@section('title', 'Edit User - Panel Admin')
@section('header-title', 'Edit Data Pengguna')

@section('content')
<div class="max-w-xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <form action="{{ route('admin.user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @include('components.field-error', ['field' => 'name'])
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @include('components.field-error', ['field' => 'email'])
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Password Baru
                <span class="text-xs text-gray-400 font-normal">(Kosongkan jika tidak ingin mengubah password)</span></label>
            <input type="password" name="password"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @include('components.field-error', ['field' => 'password'])
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Role / Hak Akses</label>
            <select name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="peminjam" {{ old('role', $user->role) == 'peminjam' ? 'selected' : '' }}>Peminjam</option>
                <option value="petugas" {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }}>Petugas</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @include('components.field-error', ['field' => 'role'])
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">No. HP</label>
            <input type="text" name="no_hp" inputmode="numeric" value="{{ old('no_hp', $user->no_hp) }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @include('components.field-error', ['field' => 'no_hp'])
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Alamat</label>
            <textarea name="alamat" maxlength="1000" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('alamat', $user->alamat) }}</textarea>
            @include('components.field-error', ['field' => 'alamat'])
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Foto Profil</label>
            <div class="mb-2 flex items-center gap-3">
                <img src="{{ $user->profile_photo_url }}" data-crop-preview="foto_profile" alt="Foto profil {{ $user->name }}" class="h-14 w-14 rounded-full object-cover">
                <span class="text-xs text-gray-500">Kosongkan jika tidak ingin mengganti foto.</span>
            </div>
            <input type="file" name="foto_profile" accept="image/jpeg,image/png,image/webp" data-crop-input="foto_profile" data-crop-aspect="square" class="w-full text-sm">
            @include('components.field-error', ['field' => 'foto_profile'])
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.user.index') }}"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Perbarui</button>
        </div>
    </form>
</div>
@endsection