<div x-data='{
    open: false,
    search: "",
    options: @json($options ?? []),
    list: [],
    selected: "{{ $selected ?? "" }}",
    init() { this.list = Object.keys(this.options).map(k => ({ id: k, label: this.options[k] })); },
    get display() { return this.options[this.selected] ?? "{{ $placeholder ?? 'Select' }}"; },
    get filtered() { if (! this.search) return this.list; return this.list.filter(i => i.label.toLowerCase().includes(this.search.toLowerCase())); },
    select(id) { this.selected = id; $wire.set("state.{{ $name }}", id); this.open = false; this.search = ""; }
}' x-init="init()" class="relative">
    <button type="button" @click="open = !open" class="mt-1 w-full text-left rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-gray-100 px-3 py-3 text-sm flex items-center justify-between">
        <span x-text="display" :class="selected === '' ? 'text-gray-400 dark:text-zinc-500' : 'text-gray-700 dark:text-gray-100'"></span>
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>

    <div x-show="open" x-cloak @click.outside="open = false" class="absolute z-50 mt-1 w-full bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 rounded shadow-lg">
        <div class="px-3 py-2 border-b border-gray-100 dark:border-zinc-600">
            <div class="relative">
                <span class="absolute left-3 top-3 text-zinc-400"><flux:icon name="search" class="w-4 h-4" /></span>
                <input type="search" x-model="search" placeholder="Search..." class="w-full pl-10 pr-3 py-2 rounded text-sm bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 border border-gray-200 dark:border-zinc-600 focus:outline-none" />
            </div>
        </div>
        <ul class="max-h-56 overflow-auto">
            <template x-for="item in filtered" :key="item.id">
                <li @click.prevent="select(item.id)" class="px-3 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-600 text-sm text-gray-700 dark:text-gray-200" x-text="item.label"></li>
            </template>
            <div x-show="filtered.length === 0" class="px-4 py-3 text-sm text-gray-500 dark:text-zinc-500">No results</div>
        </ul>
    </div>
</div>
