{{-- Empty State Component --}}
{{-- Usage: @include('components.empty-state', ['title' => 'No Data', 'message' => 'Belum ada data tersedia', 'action' => 'Create New']) --}}
<div class="py-12 text-center">
    <div class="flex justify-center mb-4">
        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
    </div>
    <h3 class="text-lg font-semibold text-gray-600 mb-2">{{ $title ?? 'Tidak ada data' }}</h3>
    <p class="text-gray-500 text-sm mb-4">{{ $message ?? 'Belum ada data yang sesuai dengan kriteria filter Anda.' }}</p>
    @if(isset($action) && isset($actionUrl))
        <a href="{{ $actionUrl }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
            <span>{{ $action }}</span>
        </a>
    @endif
</div>
