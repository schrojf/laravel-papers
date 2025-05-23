@extends('papers::app')

@section('title', $paper::name())

@section('content')
    <div class="px-4 py-6 mx-auto max-w-4xl">
        <div class="border-b border-gray-200 pb-4 mb-4">
            <h1 class="text-xl font-semibold text-gray-900">{{ $paper::name() }}</h1>
            @if($paper::description())
                <div class="mt-2 text-gray-600 text-sm">{{ $paper::description() }}</div>
            @endif
        </div>

        @forelse($content as $section)
            @if($section['type'] === 'section')
                @include('papers::components.section', ['section' => $section])
            @elseif($section['type'] === 'error')
                @include('papers::components.cells.error', ['section' => $section])
            @else
                @include('papers::components.cells.unknown', ['section' => $section])
            @endif
        @empty
            <section class="mb-4 p-3 border border-blue-100 bg-blue-50 rounded-md overflow-hidden">
                <div class="text-sm text-blue-700 flex items-start space-x-3">
                    <svg class="w-5 h-5 flex-shrink-0 text-blue-400 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                    </svg>
                    <div>
                        <p class="font-medium mb-1">Nothing to show here… yet.</p>
                        <p class="text-blue-600">This area is currently empty. Start by defining sections to populate content.</p>
                    </div>
                </div>
            </section>
        @endforelse

    </div>
@endsection
