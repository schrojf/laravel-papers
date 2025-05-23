<section class="mb-4 p-3 border border-red-200 rounded-md bg-red-50">
    <div class="text-sm text-red-700">
        <span class="font-bold">{{ count($section['content']) }}</span> {{ Str::plural('cell', count($section['content'])) }} could not be run due to exception in previous cell:
        <ul class="list-disc list-inside mt-2">
            @foreach($section['content'] as $section)
                <li>{{ $section }}</li>
            @endforeach
        </ul>
    </div>
</section>
