<div class="overflow-x-auto">
    <div class="inline-block min-w-max align-middle bg-gray-200 rounded-md overflow-hidden">
        <table class="text-sm text-left text-gray-800 w-full">
            <thead class="bg-gray-300 text-gray-900 font-semibold">
            <tr>
                @foreach($headers as $header)
                    <th class="px-4 py-2 whitespace-nowrap">{{ $header }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr class="even:bg-gray-300/50 hover:bg-gray-100 transition-colors">
                        @foreach($row as $cell)
                            <td class="px-4 py-2 whitespace-nowrap">{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
