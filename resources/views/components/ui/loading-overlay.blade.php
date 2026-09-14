<div x-show="loading" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40" style="display: none;" >
    <div class="flex flex-col items-center gap-3 rounded-xl bg-white px-8 py-6 shadow-2xl">
        <svg class="h-10 w-10 animate-spin text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" ></circle>
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
            </path>
        </svg>

        <span
            x-text="loadingMessage"
            class="text-sm font-medium text-gray-700"
        ></span>
    </div>
</div>