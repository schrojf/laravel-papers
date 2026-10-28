<div class="overflow-x-auto">
    <div class="inline-block {{ $fit ? 'min-w-full' : 'min-w-max' }} align-middle bg-gray-200 rounded-md overflow-hidden">
        <table class="text-sm text-left text-gray-800 w-full">
            <thead class="bg-gray-300 text-gray-900 font-semibold">
            <tr>
                {{-- Fitted headers wrap between words only, so columns stay as wide as their longest header word. --}}
                @foreach($headers as $header)
                    <th class="px-4 py-2 {{ $fit ? '' : 'whitespace-nowrap' }}">{{ $header }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr class="even:bg-gray-300/50 hover:bg-gray-100 transition-colors">
                        @foreach($row as $cell)
                            <td class="px-4 py-2 {{ $fit ? '[overflow-wrap:anywhere]' : 'whitespace-nowrap' }}">{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
