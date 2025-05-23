<div class="border border-red-600 rounded-md break-words overflow-hidden">
    <div class="px-3 py-2 bg-red-600 text-white font-medium">
        {{ $name }}
    </div>
    @if($message)
        <div class="px-3 py-2 bg-red-50 text-red-700 text-sm font-medium">
            {{ $message }}
        </div>
    @endif
</div>
