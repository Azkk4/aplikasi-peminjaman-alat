{{-- Table Header Component --}}
{{-- Usage: @include('components.table-header', ['headers' => ['Name', 'Email', 'Action']]) --}}
<thead>
    <tr class="bg-gray-100 border-b-2 border-gray-300">
        @foreach($headers as $header)
            <th class="py-4 px-5 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">
                {{ $header }}
            </th>
        @endforeach
    </tr>
</thead>
