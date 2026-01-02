@props(['breadcrumbs' => []])

<div class="sticky top-0 z-50 w-full bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
    <div class="w-full flex items-center justify-between px-6 py-3">
        <!-- Breadcrumbs -->
        <div class="flex items-center space-x-2 text-sm">
            @if(count($breadcrumbs) > 0)
                @foreach($breadcrumbs as $index => $crumb)
                    @if($index > 0)
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    @endif
                    
                    @if(isset($crumb['url']) && $index < count($breadcrumbs) - 1)
                        <a href="{{ $crumb['url'] }}" 
                           class="text-blue-600 dark:text-blue-400 hover:underline whitespace-nowrap"
                           wire:navigate>
                            {{ $crumb['label'] }}
                        </a>
                    @elseif(isset($crumb['href']) && !($crumb['current'] ?? false))
                        <a href="{{ $crumb['href'] }}" 
                           class="text-blue-600 dark:text-blue-400 hover:underline whitespace-nowrap"
                           wire:navigate>
                            {{ $crumb['label'] }}
                        </a>
                    @else
                        <span class="text-gray-900 dark:text-gray-100 font-medium whitespace-nowrap">
                            {{ $crumb['label'] ?? $crumb }}
                        </span>
                    @endif
                @endforeach
            @else
                <span class="text-gray-900 dark:text-gray-100 font-medium">
                    {{ $title ?? 'Dashboard' }}
                </span>
            @endif
        </div>

        <!-- Search Bar -->
        <div class="w-1/2">
            <div x-data="{ 
                    open: false,
                    query: '',
                    openSearch() {
                        this.open = true;
                        document.body.style.overflow = 'hidden';
                        $nextTick(() => $refs.searchInput.focus());
                    },
                    closeSearch() {
                        this.open = false;
                        document.body.style.overflow = '';
                        this.query = '';
                    },
                    links: [
                        // Profile Section
                        { section: 'Profile', title: 'My Profile', href: '{{ route('profile.show', ['user' => auth()->user()->id]) }}' },
                        { section: 'Profile', title: 'Personal Records', href: '{{ route('profile.show.section', ['user' => Auth::id(), 'section' => 'personal']) }}' },
                        { section: 'Profile', title: 'Account', href: '{{ route('profile.show.section', ['user' => Auth::id(), 'section' => 'account']) }}' },
                        { section: 'Profile', title: 'FCEER Records', href: '{{ route('profile.show.section', ['user' => Auth::id(), 'section' => 'fceer']) }}' },
                        { section: 'Profile', title: 'Credentials', href: '{{ route('profile.show.section', ['user' => Auth::id(), 'section' => 'credentials']) }}' },
                        
                        // Settings Section
                        { section: 'Settings', title: 'Profile Settings', href: '{{ route('profile.edit') }}' },
                        { section: 'Settings', title: 'Password', href: '{{ route('user-password.edit') }}' },
                        { section: 'Settings', title: 'Appearance', href: '{{ route('appearance.edit') }}' },
                        { section: 'Settings', title: 'Two Factor Authentication', href: '{{ route('two-factor.show') }}' },
                        
                        // User Attendance
                        { section: 'User', title: 'My Attendance', href: '{{ route('attendance.user') }}' },
                        
                        @can('viewRoster')
                        // Students Section
                        { section: 'Students', title: 'Student Roster', href: '{{ route('roster.students') }}' },
                        @can('viewAnyAttendance')
                        { section: 'Students', title: 'Student Attendance', href: '{{ route('attendance.students') }}' },
                        @endcan
                        
                        // Volunteers Section
                        { section: 'Volunteers', title: 'Volunteer Roster', href: '{{ route('roster.volunteers') }}' },
                        @can('viewAnyAttendance')
                        { section: 'Volunteers', title: 'Volunteer Attendance', href: '{{ route('attendance.volunteers') }}' },
                        @endcan
                        @endcan
                        
                        // Database Section - Hidden from Students and Instructors
                        @if(auth()->user() && !in_array(auth()->user()->role_id, [4, 5]))
                        { section: 'Database', title: 'Reference Tables', href: '{{ route('database') }}' },
                        { section: 'Database', title: 'Provinces', href: '{{ route('pages.reference_tables.provinces') }}' },
                        { section: 'Database', title: 'Cities', href: '{{ route('pages.reference_tables.cities') }}' },
                        { section: 'Database', title: 'Barangays', href: '{{ route('pages.reference_tables.barangays') }}' },
                        { section: 'Database', title: 'Degree Fields', href: '{{ route('pages.reference_tables.degree_fields') }}' },
                        { section: 'Database', title: 'Degree Levels', href: '{{ route('pages.reference_tables.degree_levels') }}' },
                        { section: 'Database', title: 'Degree Types', href: '{{ route('pages.reference_tables.degree_types') }}' },
                        { section: 'Database', title: 'Degree Programs', href: '{{ route('pages.reference_tables.degree_programs') }}' },
                        { section: 'Database', title: 'Universities', href: '{{ route('pages.reference_tables.universities') }}' },
                        { section: 'Database', title: 'High Schools', href: '{{ route('pages.reference_tables.highschools') }}' },
                        { section: 'Database', title: 'High School Subjects', href: '{{ route('pages.reference_tables.highschool_subjects') }}' },
                        { section: 'Database', title: 'Prefix Titles', href: '{{ route('pages.reference_tables.prefix_titles') }}' },
                        { section: 'Database', title: 'Suffix Titles', href: '{{ route('pages.reference_tables.suffix_titles') }}' },
                        { section: 'Database', title: 'Fields of Work', href: '{{ route('pages.reference_tables.fields_of_work') }}' },
                        { section: 'Database', title: 'Volunteer Subjects', href: '{{ route('pages.reference_tables.volunteer_subjects') }}' },
                        { section: 'Database', title: 'Committees', href: '{{ route('pages.reference_tables.committees') }}' },
                        { section: 'Database', title: 'Classroom Positions', href: '{{ route('pages.reference_tables.classroom_positions') }}' },
                        { section: 'Database', title: 'Committee Positions', href: '{{ route('pages.reference_tables.committee_positions') }}' },
                        { section: 'Database', title: 'Classrooms', href: '{{ route('pages.reference_tables.classrooms') }}' },
                        { section: 'Database', title: 'FCEER Batches', href: '{{ route('pages.reference_tables.fceer_batches') }}' },
                        { section: 'Database', title: 'User Attendance Statuses', href: '{{ route('pages.reference_tables.user_attendance_statuses') }}' },
                        { section: 'Database', title: 'User Roles', href: '{{ route('pages.reference_tables.user_roles') }}' },
                        @can('manageReviewSeason')
                        { section: 'Database', title: 'Review Seasons', href: '{{ route('reviewseason') }}' },
                        @endcan
                        @endif
                    ],
                    get filteredLinks() {
                        if (!this.query) return this.links;
                        const q = this.query.toLowerCase();
                        return this.links.filter(link => 
                            link.title.toLowerCase().includes(q) || 
                            link.section.toLowerCase().includes(q)
                        );
                    },
                    get groupedLinks() {
                        const grouped = {};
                        this.filteredLinks.forEach(link => {
                            if (!grouped[link.section]) {
                                grouped[link.section] = [];
                            }
                            grouped[link.section].push(link);
                        });
                        return grouped;
                    }
                 }"
                 @keydown.window.prevent.slash="openSearch()"
                 @keydown.window.escape="closeSearch()"
                 class="relative">
                
                <!-- Search Trigger Button -->
                <button @click="openSearch()" 
                        class="w-full flex items-center space-x-2 px-4 py-1.5 text-sm border border-zinc-300 dark:border-zinc-600 rounded-md hover:border-zinc-400 dark:hover:border-zinc-500 transition bg-white dark:bg-zinc-800">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="text-gray-500 dark:text-gray-400">Type</span>
                    <kbd class="px-2 py-0.5 text-xs font-semibold text-gray-800 dark:text-gray-200 bg-gray-100 dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded">
                        /
                    </kbd>
                    <span class="text-gray-500 dark:text-gray-400">to search</span>
                </button>

                <!-- Search Modal -->
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="closeSearch()"
                     class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm"
                     style="display: none;">
                    
                    <div @click.stop 
                         class="relative top-20 mx-auto max-w-2xl bg-white dark:bg-zinc-800 rounded-lg shadow-2xl border border-zinc-200 dark:border-zinc-700">
                        
                        <!-- Search Input -->
                        <div class="flex items-center border-b border-zinc-200 dark:border-zinc-700 px-4 py-3">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input x-ref="searchInput"
                                   x-model="query"
                                   type="text"
                                   placeholder="Search pages..."
                                   class="flex-1 bg-transparent border-none outline-none text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500">
                            <button @click="closeSearch()" 
                                    class="ml-2 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                <kbd class="px-2 py-0.5 text-xs font-semibold bg-gray-100 dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded">
                                    ESC
                                </kbd>
                            </button>
                        </div>

                        <!-- Search Results -->
                        <div class="p-2 max-h-96 overflow-y-auto">
                            <template x-if="query.length === 0">
                                <div class="py-1">
                                    <!-- Show all links when no query -->
                                </div>
                            </template>
                            
                            <template x-if="query.length > 0 && Object.keys(groupedLinks).length === 0">
                                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <p class="text-sm">No results found for "<span x-text="query" class="font-semibold"></span>"</p>
                                </div>
                            </template>

                            <!-- Grouped Results -->
                            <template x-for="(sectionLinks, section) in groupedLinks" :key="section">
                                <div class="mb-3">
                                    <!-- Section Header -->
                                    <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        <span x-text="section"></span>
                                    </div>
                                    
                                    <!-- Section Links -->
                                    <template x-for="link in sectionLinks" :key="link.href">
                                        <a :href="link.href"
                                           @click="closeSearch()"
                                           class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-md transition group">
                                            <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span x-text="link.title"></span>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
