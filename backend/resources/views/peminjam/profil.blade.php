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

    @if(session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form
        action="{{ route('peminjam.profil.update') }}"
        method="POST"
        class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
    >

        @csrf
        @method('PUT')

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