@extends('layouts.app')

@section('title', 'Profil')
@section('header-title', 'Profil Saya')

@section('content')

<div class="mx-auto max-w-2xl">

    <div class="mb-6">
        <p class="text-sm font-medium text-emerald-600">
            Akun peminjam
        </p>

        <h1 class="mt-1 text-2xl font-bold text-gray-900">
            Profil Saya
        </h1>
    </div>

    <form
        action="{{ route('peminjam.profil.update') }}"
        method="POST"
        enctype="multipart/form-data"
        class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
    >

        @csrf
        @method('PUT')

        <div class="mb-5 flex items-center gap-4">
            <img src="{{ auth()->user()->profile_photo_url }}" alt="Foto profil {{ auth()->user()->name }}" class="h-20 w-20 rounded-full object-cover ring-2 ring-emerald-100">
            <div class="flex-1">
                <label for="foto_profile" class="block text-sm font-semibold text-gray-700">Foto profil</label>
                <input id="foto_profile" name="foto_profile" type="file" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm text-gray-600">
                @include('components.field-error', ['field' => 'foto_profile'])
            </div>
        </div>

        <div class="mb-5">
            <label
                for="name"
                class="block text-sm font-semibold text-gray-700"
            >
                Nama
            </label>

            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', auth()->user()->name) }}"
                required
                class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
            >
            @include('components.field-error', ['field' => 'name'])
        </div>

        <div class="mb-6">
            <label
                for="email"
                class="block text-sm font-semibold text-gray-700"
            >
                Email
            </label>

            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', auth()->user()->email) }}"
                required
                class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
            >
            @include('components.field-error', ['field' => 'email'])
        </div>

        <div class="mb-5">
            <label for="no_hp" class="block text-sm font-semibold text-gray-700">Nomor HP</label>
            <input id="no_hp" name="no_hp" type="text" inputmode="numeric" value="{{ old('no_hp', auth()->user()->no_hp) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">
            @include('components.field-error', ['field' => 'no_hp'])
        </div>

        <div class="mb-6">
            <label for="alamat" class="block text-sm font-semibold text-gray-700">Alamat</label>
            <textarea id="alamat" name="alamat" rows="3" maxlength="1000" class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm">{{ old('alamat', auth()->user()->alamat) }}</textarea>
            @include('components.field-error', ['field' => 'alamat'])
        </div>

        <div class="flex justify-end">
            <button
                type="submit"
                class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
            >
                Simpan Perubahan
            </button>
        </div>

    </form>

</div>

@endsection