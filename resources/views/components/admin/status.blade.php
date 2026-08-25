@if (session('status'))
    <p class="mb-6 rounded-md bg-emerald-50 px-3 py-2 text-sm text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
        {{ session('status') }}
    </p>
@endif
