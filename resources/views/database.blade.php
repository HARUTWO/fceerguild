<x-layouts.app :title="__('Database')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold mb-7">FCEER Guild Database</h1>
            </div>

            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">For Address</h2>
                <div class="border-b border-gray-200 dark:border-gray-700"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">Provinces</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.provinces') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">Cities</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.cities') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">Barangays</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.barangays') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 mb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">For Educational Records</h2>
                <div class="border-b border-gray-200 dark:border-gray-700"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">Degree Levels</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.degree_levels') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">Degree Types</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.degree_types') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">Degree Fields</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.degree_fields') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700 md:col-span-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">Universities</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.universities') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700 md:col-span-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">Degree Programs</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.degree_programs') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-6 mb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">For Professional Credentials</h2>
                <div class="border-b border-gray-200 dark:border-gray-700"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium">Prefix Titles</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pages.reference_tables.prefix_titles') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                    <flux:icon name="eye" />
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium">Suffix Titles</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pages.reference_tables.suffix_titles') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                    <flux:icon name="eye" />
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium">Fields of Work</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pages.reference_tables.fields_of_work') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                    <flux:icon name="eye" />
                                </a>
                            </div>
                        </div>
                    </div>
            </div>

            <div class="mt-6 mb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">For Highschool Records</h2>
                <div class="border-b border-gray-200 dark:border-gray-700"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium">Highschool Subjects</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pages.reference_tables.highschool_subjects') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                    <flux:icon name="eye" />
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium">Highschools</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pages.reference_tables.highschools') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                    <flux:icon name="eye" />
                                </a>
                            </div>
                        </div>
                    </div>
            </div>

            <div class="mt-6 mb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">For FCEER Records</h2>
                <div class="border-b border-gray-200 dark:border-gray-700"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">Volunteer Subjects</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.volunteer_subjects') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">Committees</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.committees') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">Positions</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.committee_positions') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">Classroom Positions</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.classroom_positions') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">classrooms</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.classrooms') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">Review Seasons</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.review_seasons') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">FCEER Batches</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.fceer_batches') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">User Attendance Statuses</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.user_attendance_statuses') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 border border-neutral-200 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium">User Roles</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Reference table</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pages.reference_tables.user_roles') }}" wire:navigate class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white" title="Open">
                                <flux:icon name="eye" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            </div>


        </div>
    </div>
</x-layouts.app>
