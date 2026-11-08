<form
    method="POST"
    action="{{ $action }}"
    class="flex flex-col gap-3 max-w-lg"
    @if($multipart) enctype="multipart/form-data" @endif
    @if($confirm) data-papers-confirm="{{ $confirm }}" @endif
>
    @csrf
    @foreach($hidden as $name => $value)
        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
    @endforeach

    @foreach($fields as $field)
        @php
            $control = 'block w-full rounded-md text-sm text-gray-900 shadow-xs '.($field['error'] ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500');
        @endphp

        @if($field['type'] === 'checkbox')
            <label class="block text-sm">
                <span class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        name="{{ $field['name'] }}"
                        value="1"
                        class="rounded-sm {{ $field['error'] ? 'border-red-400' : 'border-gray-300' }} text-blue-600 focus:ring-blue-500"
                        @checked($field['value'])
                        @if($field['error']) aria-invalid="true" @endif
                        {{ $field['attributes'] }}
                    >
                    <span class="text-gray-700">{{ $field['label'] }}</span>
                </span>
                @if($field['error'])
                    <span class="block mt-1 text-red-600">{{ $field['error'] }}</span>
                @endif
            </label>
        @else
            <label class="block text-sm">
                <span class="block mb-1 font-medium text-gray-700">{{ $field['label'] }}</span>
                @if($field['type'] === 'textarea')
                    <textarea
                        name="{{ $field['name'] }}"
                        rows="3"
                        class="{{ $control }}"
                        @if($field['error']) aria-invalid="true" @endif
                        {{ $field['attributes'] }}
                    >{{ $field['value'] }}</textarea>
                @elseif($field['type'] === 'select')
                    <select
                        name="{{ $field['name'] }}"
                        class="{{ $control }}"
                        @if($field['error']) aria-invalid="true" @endif
                        {{ $field['attributes'] }}
                    >
                        @foreach($field['options'] as $optionValue => $optionLabel)
                            <option value="{{ $optionValue }}" @selected(in_array((string) $optionValue, array_map('strval', (array) $field['value']), true))>{{ $optionLabel }}</option>
                        @endforeach
                    </select>
                @else
                    <input
                        type="{{ $field['type'] }}"
                        name="{{ $field['name'] }}"
                        @unless($field['type'] === 'file') value="{{ $field['value'] }}" @endunless
                        class="{{ $field['type'] === 'file' ? 'block w-full text-sm text-gray-700' : $control }}"
                        @if($field['error']) aria-invalid="true" @endif
                        {{ $field['attributes'] }}
                    >
                @endif
                @if($field['error'])
                    <span class="block mt-1 text-red-600">{{ $field['error'] }}</span>
                @endif
            </label>
        @endif
    @endforeach

    <div class="flex flex-wrap gap-2">
        @forelse($buttons as $button)
            <button
                type="submit"
                class="papers-button papers-button-{{ $button['style'] }}"
                @unless(is_null($button['name'])) name="{{ $button['name'] }}" value="{{ $button['value'] }}" @endunless
            >{{ $button['label'] }}</button>
        @empty
            <button type="submit" class="papers-button papers-button-primary">Submit</button>
        @endforelse
    </div>
</form>
