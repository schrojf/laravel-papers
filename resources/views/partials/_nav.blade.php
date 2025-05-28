<button id="menu-backdrop" class="hidden lg:hidden fixed inset-0 z-20 bg-black/50" tabindex="-1" aria-hidden="true"></button>

<aside>
    <nav id="mobile-menu" role="navigation" aria-label="Main navigation" class="hidden fixed bottom-0 top-0 w-full max-w-80 z-30 lg:block bg-white border-r border-gray-200">
        <div class="flex flex-col h-full">
            <header class="px-4 py-3 h-14 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900">Pages</h2>
                <div class="lg:hidden">
                    <button id="menu-close" aria-label="Close menu" class="block p-1 rounded hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                <ul class="p-4 break-words">
                    @foreach($papers as $paper)
                        @php
                            $isActive = request()->routeIs('papers.show') && request()->route('paper') === $paper::handler();
                        @endphp

                        <li class="mb-2">
                            <a
                                href="{{ route('papers.show', ['paper' => $paper::handler()]) }}"
                                class="{{ $isActive ? 'font-medium text-gray-900' : 'text-gray-600' }} hover:text-gray-900 hover:underline"
                            >
                                {{ $paper::name() }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </main>

            @includeWhen(\Schrojf\Papers\Papers::shouldShowSponsorBadge(), 'papers::partials._support')
        </div>
    </nav>
</aside>
