<label class="relative block">
    <span class="sr-only">Filter pages</span>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 size-4 text-gray-400">
        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
    </svg>
    <input
        type="search"
        placeholder="Filter pages"
        autocomplete="off"
        spellcheck="false"
        aria-controls="{{ $list }}"
        data-papers-filter
        class="peer block w-full rounded-md border-gray-300 py-1.5 pl-8 pr-8 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:ring-blue-500"
    >
    <kbd class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 hidden peer-placeholder-shown:block peer-focus:hidden rounded border border-gray-300 px-1.5 font-sans text-xs text-gray-500">/</kbd>
</label>
