<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>

    <!-- Memuat Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div class="flex min-h-screen overflow-hidden">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-gray-900 text-white flex-col hidden md:flex print-hidden">
            @if(auth()->user()->role === 'admin')
                <div class="p-5 text-xl font-bold tracking-wider border-b border-gray-800">
                    PANEL ADMIN
                </div>
            @elseif(auth()->user()->role === 'petugas')
                <div class="p-5 text-xl font-bold tracking-wider border-b border-gray-800">
                    PANEL PETUGAS
                </div>
            @elseif(auth()->user()->role === 'peminjam')
                <div class="p-5 text-xl font-bold tracking-wider border-b border-gray-800">
                    PANEL PEMINJAM
                </div>
            @endif

            <nav class="flex-1 p-4 space-y-2">
                <!-- MENU KHUSUS ADMIN -->
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}"
                       class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('admin.user.index') }}"
                       class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.user.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Kelola User
                    </a>

                    <a href="{{ route('admin.kategori.index') }}"
                       class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.kategori.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Kelola Kategori
                    </a>

                    <a href="{{ route('admin.alat.index') }}"
                       class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.alat.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Kelola Alat
                    </a>

                    <a href="{{ route('admin.peminjaman.index') }}" 
                       class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.peminjaman.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Kelola Peminjaman
                    </a>

                    <a href="{{ route('admin.pengembalian.index') }}" 
                       class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.pengembalian.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Kelola Pengembalian
                    </a>
                @elseif(auth()->user()->role === 'petugas')
                    <!-- MENU KHUSUS PETUGAS -->
                    <a href="{{ route('petugas.peminjaman.index') }}"
                       class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.peminjaman.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Persetujuan Peminjaman
                    </a>

                    <a href="{{ route('petugas.pengembalian.index') }}"
                       class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.pengembalian.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Pemantauan Pengembalian
                    </a>

                    <a href="{{ route('petugas.laporan.index') }}"
                       class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.laporan.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Cetak Laporan
                    </a>
                @elseif(auth()->user()->role === 'peminjam')
                    <a href="{{ route('peminjam.dashboard') }}" class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('peminjam.dashboard') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">Dashboard</a>
                    <a href="{{ route('peminjam.katalog.index') }}" class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('peminjam.katalog.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">Katalog Alat</a>
                    <a href="{{ route('peminjam.peminjaman.index') }}" class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('peminjam.peminjaman.*') && request('from') !== 'riwayat' ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">Peminjaman Saya</a>
                    <a href="{{ route('peminjam.riwayat') }}" class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('peminjam.riwayat') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">Riwayat Peminjaman</a>
                @endif
            </nav>

            <div class="p-4 border-t border-gray-800 text-sm text-gray-400">
                Logged in as:
                <span class="text-white font-semibold">
                    {{ Auth::user()->name }}
                </span>
            </div>
        </aside>

        <!-- MAIN CONTENT CONTAINER -->
        <div class="flex-1 flex flex-col overflow-y-auto">

            <!-- NAVBAR ATAS -->
            <header class="bg-white shadow-sm min-h-16 flex items-center justify-between px-4 sm:px-6 py-4 z-10 print-hidden">
                <div class="flex items-center gap-3">
                    <button type="button" id="mobile-menu-toggle" aria-controls="mobile-menu" aria-expanded="false" class="md:hidden rounded-lg border border-gray-300 px-3 py-2 text-gray-700" aria-label="Buka menu">
                        &#9776;
                    </button>
                    <div class="text-lg font-semibold text-gray-800">
                    @yield('header-title', 'Dashboard')
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if(auth()->user()->role === 'peminjam')
                        <a href="{{ route('peminjam.profil') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 transition hover:border-emerald-500 hover:text-emerald-600">Profil</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            onclick="return confirm('Yakin ingin keluar dari aplikasi?')"
                            class="inline-flex items-center rounded-lg bg-red-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-600">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <div id="mobile-menu" class="hidden border-b border-gray-200 bg-gray-900 p-4 text-white md:hidden print-hidden">
                <nav class="space-y-2">
                    @if(auth()->user()->role === 'admin')
                        <a class="block rounded px-3 py-2 hover:bg-gray-800" href="{{ route('admin.dashboard') }}">Dashboard</a>
                        <a class="block rounded px-3 py-2 hover:bg-gray-800" href="{{ route('admin.user.index') }}">Kelola User</a>
                        <a class="block rounded px-3 py-2 hover:bg-gray-800" href="{{ route('admin.kategori.index') }}">Kelola Kategori</a>
                        <a class="block rounded px-3 py-2 hover:bg-gray-800" href="{{ route('admin.alat.index') }}">Kelola Alat</a>
                        <a class="block rounded px-3 py-2 hover:bg-gray-800" href="{{ route('admin.peminjaman.index') }}">Kelola Peminjaman</a>
                    @elseif(auth()->user()->role === 'petugas')
                        <a class="block rounded px-3 py-2 hover:bg-gray-800" href="{{ route('petugas.peminjaman.index') }}">Persetujuan Peminjaman</a>
                        <a class="block rounded px-3 py-2 hover:bg-gray-800" href="{{ route('petugas.pengembalian.index') }}">Pemantauan Pengembalian</a>
                        <a class="block rounded px-3 py-2 hover:bg-gray-800" href="{{ route('petugas.laporan.index') }}">Cetak Laporan</a>
                    @else
                        <a class="block rounded px-3 py-2 hover:bg-gray-800" href="{{ route('peminjam.dashboard') }}">Dashboard</a>
                        <a class="block rounded px-3 py-2 hover:bg-gray-800" href="{{ route('peminjam.katalog.index') }}">Katalog Alat</a>
                        <a class="block rounded px-3 py-2 hover:bg-gray-800" href="{{ route('peminjam.peminjaman.index') }}">Peminjaman Saya</a>
                        <a class="block rounded px-3 py-2 hover:bg-gray-800" href="{{ route('peminjam.riwayat') }}">Riwayat Peminjaman</a>
                    @endif
                </nav>
            </div>

            @if(session('success') || session('error') || session('info') || session('warning'))
                <div id="global-notification" class="mx-4 mt-4 rounded-lg border p-4 text-sm {{ session('error') ? 'border-red-200 bg-red-50 text-red-800' : (session('warning') ? 'border-amber-200 bg-amber-50 text-amber-800' : 'border-emerald-200 bg-emerald-50 text-emerald-800') }}" role="status">
                    <div class="flex items-start justify-between gap-4">
                        <span>{{ session('error') ?? session('warning') ?? session('info') ?? session('success') }}</span>
                        <button type="button" onclick="this.closest('#global-notification').remove()" aria-label="Tutup notifikasi">&times;</button>
                    </div>
                </div>
            @endif

            <!-- KONTEN UTAMA HALAMAN -->
            <main class="flex-1 p-4 sm:p-6">
                @yield('content')
            </main>

        </div>
    </div>

    <script>
        const menuButton = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        menuButton?.addEventListener('click', () => {
            const isHidden = mobileMenu.classList.toggle('hidden');
            menuButton.setAttribute('aria-expanded', String(!isHidden));
        });
        window.setTimeout(() => document.getElementById('global-notification')?.remove(), 5000);
    </script>
    <style>
        @media print {
            .print-hidden { display: none !important; }
            body { background: white !important; }
            main { padding: 0 !important; }
        }
    </style>

</body>
</html>