@extends('papers::app')

@section('title', 'Papers')

@section('content')
    @empty($papers)
        @include('papers::components.empty')
    @else
        <div class="px-4 py-6 max-w-4xl mx-auto">
            <div class="border-b border-gray-200 pb-4 mb-4">
                <h1 class="text-xl font-semibold text-gray-900">No paper class is selected</h1>
                <p class="mt-1 text-sm text-gray-600">Select which page you want to see.</p>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-md p-4 mb-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16" class="text-blue-500">
                            <path fill-rule="evenodd" d="M8 1.5a6.5 6.5 0 100 13 6.5 6.5 0 000-13zM0 8a8 8 0 1116 0A8 8 0 010 8zm6.5-.25A.75.75 0 017.25 7h1a.75.75 0 01.75.75v2.75h.25a.75.75 0 010 1.5h-2a.75.75 0 010-1.5h.25v-2h-.25a.75.75 0 01-.75-.75zM8 6a1 1 0 100-2 1 1 0 000 2z" fill="currentColor"></path>
                        </svg>
                    </div>
                    <div class="ml-2">
                        <p class="text-sm text-gray-700">
                            Select a page from the sidebar or choose from the list below.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-2">
                <h2 class="text-sm font-semibold text-gray-700">Pages</h2>
            </div>

            <div class="border border-gray-200 rounded-md overflow-hidden">
                <ul class="divide-y divide-gray-200">
                    @foreach($papers as $paper)
                        <li>
                            <a href="#" class="block px-4 py-3 group hover:bg-gray-100">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-gray-500 mr-2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                    <span class="text-sm group-hover:text-gray-950 text-gray-900">{{ $paper::name() }}</span>
                                </div>
                                @if($paper::description())
                                    <div class="pl-6 pt-1 text-xs text-gray-600">{{ $paper::description() }}</div>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endempty
@endsection
