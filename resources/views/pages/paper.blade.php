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
    </div>
@endsection
