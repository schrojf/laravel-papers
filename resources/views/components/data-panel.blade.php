@unless(empty($title))
    <h2 class="mb-1.5 text-lg font-medium text-gray-900">{{ $title }}</h2>
@endunless

<div class="grid grid-cols-1 md:grid-cols-[minmax(0,max-content)_1fr] gap-x-3 text-gray-900">
    @foreach($items as $item)
        <div class="font-medium text-gray-700 after:content-[':']">{{ $item['label'] }}</div>
        <div>{{ $item['value'] }}</div>
    @endforeach
</div>
