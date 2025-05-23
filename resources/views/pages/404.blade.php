@extends('papers::app')

@section('title', 'Paper not found')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-xl">
        <section class="border border-blue-100 bg-blue-50 rounded-xl px-5 py-6 text-center text-sm text-blue-600">
            <h3 class="text-base font-semibold mb-2">404 – Paper Not Found</h3>
            <p class="text-blue-500 mb-4">Sorry, the paper you’re looking for doesn’t exist or has been removed.</p>
            <a href="{{ route('papers.index') }}" class="inline-flex items-center px-4 py-2 rounded bg-white text-blue-600 border border-blue-100 hover:bg-blue-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>Back to index</span>
            </a>
        </section>
    </div>
@endsection
