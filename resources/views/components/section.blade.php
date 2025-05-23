<section class="mb-4 border border-gray-200 rounded-md overflow-hidden">
    <div class="px-3 py-2 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">{{ $section['name'] }}</h2>
    </div>
    <div class="p-3 @if(empty($section['content'])) bg-gray-50 text-center text-gray-500 text-sm rounded-sm @endif">
        @if(empty($section['content']))
            Empty cell
        @else
            @foreach($section['content'] as $content)
                {{ $content }}
            @endforeach
        @endif
    </div>
</section>
