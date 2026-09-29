<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>

    <!-- Memuat Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/js/app.js')
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
                <span class="block text-white font-semibold">{{ Auth::user()->name }}</span>
                <span class="mt-1 inline-flex rounded-full bg-gray-800 px-2 py-0.5 text-xs font-semibold text-emerald-300">
                    {{ Auth::user()->isSuperAdmin() ? 'Super Admin' : ucfirst(Auth::user()->role) }}
                </span>
            </div>
        </aside>

        <!-- MAIN CONTENT CONTAINER -->
        <div class="flex-1 flex flex-col overflow-y-auto">

            <!-- NAVBAR ATAS -->
            <header class="bg-white shadow-sm min-h-16 flex items-center justify-between px-4 sm:px-6 py-4 z-10 print-hidden">
                <div class="flex items-center gap-3">
                    @if(auth()->user()->role === 'admin')
                        <span class="hidden text-xs font-semibold text-gray-500 sm:inline">{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Admin' }}</span>
                    @endif
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
                    <form action="{{ route('logout') }}" method="POST" data-confirm="true" data-confirm-title="Keluar dari aplikasi?" data-confirm-message="Apakah Anda yakin ingin keluar?" data-confirm-accept="Logout">
                        @csrf
                        <button
                            type="submit"
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
                        <a class="block rounded px-3 py-2 hover:bg-gray-800" href="{{ route('admin.pengembalian.index') }}">Kelola Pengembalian</a>
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
            <main class="relative flex-1 p-4 sm:p-6">
                <div id="page-loading-skeleton" class="pointer-events-none absolute inset-0 z-20 hidden bg-white/90 p-6" aria-hidden="true">
                    <div class="animate-pulse space-y-4">
                        <div class="h-8 w-1/3 rounded bg-gray-200"></div>
                        <div class="h-12 rounded bg-gray-100"></div>
                        <div class="h-12 rounded bg-gray-100"></div>
                        <div class="h-12 rounded bg-gray-100"></div>
                        <div class="h-12 rounded bg-gray-100"></div>
                    </div>
                </div>
                @yield('content')
            </main>

        </div>
    </div>

    <div id="confirmation-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-950/50 p-4 opacity-0 transition-opacity duration-200" aria-hidden="true">
        <section class="confirmation-panel w-full max-w-md scale-95 rounded-lg bg-white p-5 shadow-xl transition-transform duration-200" role="dialog" aria-modal="true" aria-labelledby="confirmation-title" aria-describedby="confirmation-message">
            <h2 id="confirmation-title" class="text-lg font-bold text-gray-900">Konfirmasi</h2>
            <p id="confirmation-message" class="mt-2 text-sm text-gray-600"></p>
            <div class="mt-6 flex justify-end gap-2">
                <button id="confirmation-cancel" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                <button id="confirmation-accept" type="button" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Konfirmasi</button>
            </div>
        </section>
    </div>

    <div id="image-crop-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-gray-950/70 p-4" role="dialog" aria-modal="true" aria-labelledby="image-crop-title">
        <section class="w-full max-w-3xl rounded-lg bg-white p-4 shadow-xl sm:p-6">
            <div class="mb-4 flex items-center justify-between gap-4">
                <h2 id="image-crop-title" class="text-lg font-bold text-gray-900">Potong gambar</h2>
                <button id="image-crop-cancel" type="button" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-semibold text-gray-700">Batal</button>
            </div>
            <div class="max-h-[65vh] overflow-hidden bg-gray-100">
                <img id="image-crop-source" alt="Pratinjau area crop" class="block max-h-[65vh] max-w-full">
            </div>
            <div class="mt-4 flex justify-end">
                <button id="image-crop-apply" type="button" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Gunakan Hasil Crop</button>
            </div>
        </section>
    </div>

    <script>
        const menuButton = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        menuButton?.addEventListener('click', () => {
            const isHidden = mobileMenu.classList.toggle('hidden');
            menuButton.setAttribute('aria-expanded', String(!isHidden));
        });
        window.setTimeout(() => document.getElementById('global-notification')?.remove(), 5000);

        const confirmationModal = document.getElementById('confirmation-modal');
        const confirmationPanel = confirmationModal.querySelector('.confirmation-panel');
        const confirmationTitle = document.getElementById('confirmation-title');
        const confirmationMessage = document.getElementById('confirmation-message');
        const confirmationAccept = document.getElementById('confirmation-accept');
        const confirmationCancel = document.getElementById('confirmation-cancel');
        let pendingConfirmationForm = null;

        const closeConfirmation = () => {
            confirmationModal.classList.add('opacity-0');
            confirmationPanel.classList.add('scale-95');
            confirmationModal.setAttribute('aria-hidden', 'true');
            window.setTimeout(() => {
                confirmationModal.classList.add('hidden');
                confirmationModal.classList.remove('flex');
                confirmationAccept.disabled = false;
            }, 200);
            pendingConfirmationForm = null;
        };

        document.addEventListener('submit', (event) => {
            const form = event.target.closest('form[data-confirm="true"]');
            if (!form) return;

            event.preventDefault();
            pendingConfirmationForm = form;
            confirmationTitle.textContent = form.dataset.confirmTitle || 'Konfirmasi tindakan';
            const details = Array.from(form.querySelectorAll('[data-confirm-detail]')).map((field) => {
                const label = field.dataset.confirmDetail;
                const value = field.dataset.confirmFormat === 'rupiah'
                    ? `Rp ${Number(field.value || 0).toLocaleString('id-ID')}`
                    : field.value;
                return `${label}: ${value}`;
            });
            confirmationMessage.textContent = [form.dataset.confirmMessage || 'Apakah Anda yakin ingin melanjutkan?', ...details].join('\n');
            confirmationAccept.textContent = form.dataset.confirmAccept || 'Konfirmasi';
            confirmationModal.classList.remove('hidden');
            confirmationModal.classList.add('flex');
            confirmationAccept.disabled = false;
            confirmationModal.setAttribute('aria-hidden', 'false');
            requestAnimationFrame(() => {
                confirmationModal.classList.remove('opacity-0');
                confirmationPanel.classList.remove('scale-95');
            });
            confirmationCancel.focus();
        });

        confirmationCancel.addEventListener('click', closeConfirmation);
        confirmationModal.addEventListener('click', (event) => {
            if (event.target === confirmationModal) closeConfirmation();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !confirmationModal.classList.contains('hidden')) closeConfirmation();
        });
        confirmationAccept.addEventListener('click', () => {
            if (!pendingConfirmationForm) return;
            const form = pendingConfirmationForm;
            confirmationAccept.disabled = true;
            form.removeAttribute('data-confirm');
            form.requestSubmit();
        });
    </script>
    <style>
        .detail-close-label { display: none; }
        details[open] .detail-open-label { display: none; }
        details[open] .detail-close-label { display: inline; }
        #global-notification { animation: notification-in 180ms ease-out both; }
        #mobile-menu:not(.hidden) { animation: menu-in 160ms ease-out both; }
        html { scrollbar-width: thin; scrollbar-color: #6b7280 #e5e7eb; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #e5e7eb; }
        ::-webkit-scrollbar-thumb { background: #6b7280; border: 2px solid #e5e7eb; border-radius: 999px; }
        @keyframes notification-in {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes menu-in {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media print {
            .print-hidden { display: none !important; }
            body { background: white !important; }
            main { padding: 0 !important; }
        }
    </style>

</body>
</html>