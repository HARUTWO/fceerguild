<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
    @fluxAppearance
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
@php
    $user = auth()->user();
@endphp

<flux:sidebar
    sticky
    collapsible
    class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700"
>

    {{-- HEADER --}}
    @php
        $logoLight = file_exists(public_path('light.svg')) ? asset('light.svg') : null;
        $logoDark = file_exists(public_path('dark.svg')) ? asset('dark.svg') : null;
    @endphp
    <flux:sidebar.header>
        @if ($logoLight)
            <flux:sidebar.brand href="{{ route('dashboard') }}"
                logo="{{ $logoLight }}"
                @if($logoDark) logo:dark="{{ $logoDark }}" @endif
                name="SJDM FCEER Guild" />
        @else
            <flux:sidebar.brand href="{{ route('dashboard') }}" class="w-full flex items-center px-2 in-data-flux-sidebar-on-desktop:in-data-flux-sidebar-collapsed-desktop:justify-center in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:justify-start">
                <x-app-logo />
            </flux:sidebar.brand>
        @endif

        <flux:sidebar.collapse
            class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2"
        />
    </flux:sidebar.header>

    {{-- NAV --}}
    <flux:sidebar.nav>
        <flux:sidebar.item
            icon="home"
            :href="route('dashboard')"
            :current="request()->routeIs('dashboard')"
            wire:navigate
        >
            Home
        </flux:sidebar.item>

        {{-- Students Section --}}
        @can('viewRoster')
        <flux:sidebar.group :heading="__('Students')">
            <flux:sidebar.item
                icon="academic-cap"
                :href="route('roster.students')"
                :current="request()->routeIs('roster.students')"
                wire:navigate
            >
                {{ __('Student Roster') }}
            </flux:sidebar.item>
            @can('viewAnyAttendance')
            <flux:sidebar.item
                icon="clipboard-document-check"
                :href="route('attendance.students')"
                :current="request()->routeIs('attendance.students')"
                wire:navigate
            >
                {{ __('Student Attendance') }}
            </flux:sidebar.item>
            @endcan
        </flux:sidebar.group>
        @endcan

        {{-- Volunteers Section --}}
        @can('viewRoster')
        <flux:sidebar.group :heading="__('Volunteers')">
            <flux:sidebar.item
                icon="users"
                :href="route('roster.volunteers')"
                :current="request()->routeIs('roster.volunteers')"
                wire:navigate
            >
                {{ __('Volunteer Roster') }}
            </flux:sidebar.item>
            @can('viewAnyAttendance')
            <flux:sidebar.item
                icon="clipboard-document-check"
                :href="route('attendance.volunteers')"
                :current="request()->routeIs('attendance.volunteers')"
                wire:navigate
            >
                {{ __('Volunteer Attendance') }}
            </flux:sidebar.item>
            @endcan
        </flux:sidebar.group>
        @endcan

        {{-- User Section --}}
        <flux:sidebar.group :heading="__('User')">
            <flux:sidebar.item
            icon="user-circle"
            :href="route('profile.show', ['user' => auth()->user()->id])"
            :current="request()->routeIs('profile.show*')"
            wire:navigate
            >
            {{ __('My Profile') }}
            </flux:sidebar.item>
            <flux:sidebar.item
                icon="clipboard-document-check"
                :href="route('attendance.user')"
                :current="request()->routeIs('attendance.user')"
                wire:navigate
            >
                {{ __('My Attendance') }}
            </flux:sidebar.item>
        </flux:sidebar.group>

        {{-- Database Section - Hidden from Students and Instructors --}}
        @if(auth()->user() && !in_array(auth()->user()->role_id, [4, 5]))
        <flux:sidebar.group :heading="__('Database')">
            <flux:sidebar.item
                icon="table-cells"
                :href="route('database')"
                :current="request()->routeIs('database*')"
                wire:navigate
            >
                {{ __('Reference Tables') }}
            </flux:sidebar.item>
            @can('manageReviewSeason')
            <flux:sidebar.item
                icon="calendar"
                :href="route('reviewseason')"
                :current="request()->routeIs('reviewseason')"
                wire:navigate
            >
                {{ __('Review Seasons') }}
            </flux:sidebar.item>
            @endcan
        </flux:sidebar.group>
        @endif

    </flux:sidebar.nav>

    {{-- Icon-only items when sidebar is collapsed --}}
    <flux:sidebar.nav class="hidden in-data-flux-sidebar-on-desktop:in-data-flux-sidebar-collapsed-desktop:flex flex-col gap-1">
        @can('viewRoster')
        <flux:sidebar.item
            icon="academic-cap"
            :href="route('roster.students')"
            wire:navigate
            aria-hidden="true"
        >
            <span class="sr-only">{{ __('Student Roster') }}</span>
        </flux:sidebar.item>
        @can('viewAnyAttendance')
        <flux:sidebar.item
            icon="clipboard-document-check"
            :href="route('attendance.students')"
            wire:navigate
            aria-hidden="true"
        >
            <span class="sr-only">{{ __('Student Attendance') }}</span>
        </flux:sidebar.item>
        @endcan
        
        <flux:sidebar.item
            icon="users"
            :href="route('roster.volunteers')"
            wire:navigate
            aria-hidden="true"
        >
            <span class="sr-only">{{ __('Volunteer Roster') }}</span>
        </flux:sidebar.item>
        @can('viewAnyAttendance')
        <flux:sidebar.item
            icon="clipboard-document-check"
            :href="route('attendance.volunteers')"
            wire:navigate
            aria-hidden="true"
        >
            <span class="sr-only">{{ __('Volunteer Attendance') }}</span>
        </flux:sidebar.item>
        @endcan
        @endcan
        
        <flux:sidebar.item
            icon="user-circle"
            :href="route('profile.show')"
            wire:navigate
            aria-hidden="true"
        >
            <span class="sr-only">{{ __('My Profile') }}</span>
        </flux:sidebar.item>
        <flux:sidebar.item
            icon="clipboard-document-check"
            :href="route('attendance.user')"
            wire:navigate
            aria-hidden="true"
        >
            <span class="sr-only">{{ __('My Attendance') }}</span>
        </flux:sidebar.item>
        
        @if(auth()->user() && !in_array(auth()->user()->role_id, [4, 5]))
        <flux:sidebar.item
            icon="table-cells"
            :href="route('database')"
            wire:navigate
            aria-hidden="true"
        >
            <span class="sr-only">{{ __('Reference Tables') }}</span>
        </flux:sidebar.item>
        @can('manageReviewSeason')
        <flux:sidebar.item
            icon="calendar"
            :href="route('reviewseason')"
            wire:navigate
            aria-hidden="true"
        >
            <span class="sr-only">{{ __('Review Seasons') }}</span>
        </flux:sidebar.item>
        @endcan
        @endif
    </flux:sidebar.nav>

    <flux:spacer />

    <flux:sidebar.nav>
        <flux:sidebar.item
            icon="folder-git-2"
            href="https://github.com/laravel/livewire-starter-kit"
            target="_blank"
        >
            {{ __('Repository') }}
        </flux:sidebar.item>

        <flux:sidebar.item
            icon="book-open-text"
            href="https://laravel.com/docs/starter-kits#livewire"
            target="_blank"
        >
            {{ __('Documentation') }}
        </flux:sidebar.item>
    </flux:sidebar.nav>
    
    <flux:separator />

        {{-- USER MENU --}}
            <flux:dropdown class="hidden lg:block mt-auto w-full" position="bottom" align="start">
                <button slot="trigger" type="button" class="w-full flex items-center rounded-lg p-1">
                    {{-- Expanded variant (visible when sidebar is expanded) --}}
                    <span class="profile-expanded hidden in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:flex items-center gap-2 w-full px-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">{{ auth()->user()->initials() }}</span>
                        <span class="flex-1 text-sm leading-tight text-start">
                            <span class="font-semibold truncate block">{{ auth()->user()->name }}</span>
                        </span>
                        <span class="opacity-60">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <polyline points="17 11 12 6 7 11"></polyline>
                                <polyline points="17 18 12 13 7 18"></polyline>
                            </svg>
                        </span>
                    </span>

                    {{-- Collapsed variant (visible when sidebar is collapsed) --}}
                    <span class="profile-collapsed hidden in-data-flux-sidebar-on-desktop:in-data-flux-sidebar-collapsed-desktop:flex h-8 w-8 items-center justify-center rounded-lg bg-neutral-700 text-white mx-auto">
                        {{ auth()->user()->initials() }}
                    </span>
                </button>

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">{{ auth()->user()->initials() }}</span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
</flux:sidebar>

<flux:main class="!p-0">
    @php
        // Dynamically build breadcrumbs for reference table pages to avoid repeating
        // a large mapping. For pages under `pages.reference_tables.*`, derive the
        // table key and map it to a section label where appropriate.
        $topbarLinks = [];

    if (request()->routeIs('pages.reference_tables.*')) {
        $routeName = request()->route() ? request()->route()->getName() : null;
        $parts = $routeName ? explode('.', $routeName) : [];
        $key = $parts[2] ?? null;

        // Map specific reference keys to their section labels shown in the Database page
        $sectionMap = [
            // Address
            'provinces' => 'Address',
            'cities' => 'Address',
            'barangays' => 'Address',

            // Educational
            'degree_fields' => 'Educational Records',
            'degree_levels' => 'Educational Records',
            'degree_programs' => 'Educational Records',
            'degree_types' => 'Educational Records',
            'universities' => 'Educational Records',

            // Professional
            'prefix_titles' => 'Professional Credentials',
            'suffix_titles' => 'Professional Credentials',
            'fields_of_work' => 'Professional Credentials',

            // Highschool
            'highschools' => 'Highschool Records',
            'highschool_subjects' => 'Highschool Records',

            // FCEER
            'volunteer_subjects' => 'FCEER Records',
            'committees' => 'FCEER Records',
            'positions' => 'FCEER Records',
            'classrooms' => 'FCEER Records',
            'review_seasons' => 'FCEER Records',
            'fceer_batches' => 'FCEER Records',
            'user_attendance_statuses' => 'FCEER Records',
            'user_roles' => 'FCEER Records',
        ];

        $section = $sectionMap[$key] ?? 'Database';

        $label = $key ? \Illuminate\Support\Str::title(str_replace('_', ' ', $key)) : 'Reference';

        $topbarLinks = [
            ['label' => 'Dashboard', 'href' => route('dashboard'), 'current' => false],
            ['label' => $section === 'Database' ? 'Database' : "Database ({$section})", 'href' => route('database'), 'current' => false],
            ['label' => $label, 'href' => $routeName ? route($routeName) : route('database'), 'current' => true],
        ];
    } elseif (request()->routeIs('database*')) {
        $topbarLinks = [
            ['label' => 'Dashboard', 'href' => route('dashboard'), 'current' => false],
            ['label' => 'Database', 'href' => route('database'), 'current' => true],
        ];
    } elseif (request()->routeIs('roster.*')) {
        // Roster pages
        if (request()->routeIs('roster.volunteers')) {
            $topbarLinks = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'current' => false],
                ['label' => 'Volunteers', 'href' => route('roster.volunteers'), 'current' => false],
                ['label' => 'Volunteer Roster', 'href' => route('roster.volunteers'), 'current' => true],
            ];
        } elseif (request()->routeIs('roster.students')) {
            $topbarLinks = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'current' => false],
                ['label' => 'Students', 'href' => route('roster.students'), 'current' => false],
                ['label' => 'Student Roster', 'href' => route('roster.students'), 'current' => true],
            ];
        }
    } elseif (request()->routeIs('attendance.*')) {
        // Attendance pages
        if (request()->routeIs('attendance.volunteers')) {
            $topbarLinks = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'current' => false],
                ['label' => 'Volunteers', 'href' => route('roster.volunteers'), 'current' => false],
                ['label' => 'Volunteer Attendance', 'href' => route('attendance.volunteers'), 'current' => true],
            ];
        } elseif (request()->routeIs('attendance.students')) {
            $topbarLinks = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'current' => false],
                ['label' => 'Students', 'href' => route('roster.students'), 'current' => false],
                ['label' => 'Student Attendance', 'href' => route('attendance.students'), 'current' => true],
            ];
        } elseif (request()->routeIs('attendance.user')) {
            $topbarLinks = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'current' => false],
                ['label' => 'User', 'href' => route('profile.show'), 'current' => false],
                ['label' => 'My Attendance', 'href' => route('attendance.user'), 'current' => true],
            ];
        }
    } elseif (request()->routeIs('reviewseason')) {
        // Review season page
        $topbarLinks = [
            ['label' => 'Dashboard', 'href' => route('dashboard'), 'current' => false],
            ['label' => 'Database', 'href' => route('database'), 'current' => false],
            ['label' => 'Review Seasons', 'href' => route('reviewseason'), 'current' => true],
        ];
    } elseif (request()->routeIs('profile.*')) {
        // Settings routes
        if (request()->routeIs('profile.edit')) {
            $topbarLinks = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'current' => false],
                ['label' => 'Settings', 'href' => route('profile.edit'), 'current' => false],
                ['label' => 'Profile', 'href' => route('profile.edit'), 'current' => true],
            ];
        } elseif (request()->routeIs('user-password.edit')) {
            $topbarLinks = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'current' => false],
                ['label' => 'Settings', 'href' => route('profile.edit'), 'current' => false],
                ['label' => 'Password', 'href' => route('user-password.edit'), 'current' => true],
            ];
        } elseif (request()->routeIs('appearance.edit')) {
            $topbarLinks = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'current' => false],
                ['label' => 'Settings', 'href' => route('profile.edit'), 'current' => false],
                ['label' => 'Appearance', 'href' => route('appearance.edit'), 'current' => true],
            ];
        } elseif (request()->routeIs('two-factor.show')) {
            $topbarLinks = [
                ['label' => 'Dashboard', 'href' => route('dashboard'), 'current' => false],
                ['label' => 'Settings', 'href' => route('profile.edit'), 'current' => false],
                ['label' => 'Two Factor Authentication', 'href' => route('two-factor.show'), 'current' => true],
            ];
        } elseif (request()->routeIs('profile.show*')) {
            // Profile pages with sections
            $section = request()->route('section');
            $sectionLabels = [
                'personal' => 'Personal Records',
                'account' => 'Account',
                'fceer' => 'FCEER Records',
                'credentials' => 'Credentials',
            ];
            
            if ($section && isset($sectionLabels[$section])) {
                $topbarLinks = [
                    ['label' => 'Dashboard', 'href' => route('dashboard'), 'current' => false],
                    ['label' => 'Profile', 'href' => route('profile.show'), 'current' => false],
                    ['label' => $sectionLabels[$section], 'href' => request()->url(), 'current' => true],
                ];
            } else {
                $topbarLinks = [
                    ['label' => 'Dashboard', 'href' => route('dashboard'), 'current' => false],
                    ['label' => 'Profile', 'href' => route('profile.show'), 'current' => true],
                ];
            }
        }
    }
@endphp

    @if ($topbarLinks)
        <x-layouts.app.top-header :breadcrumbs="$topbarLinks" />
    @else
        <x-layouts.app.top-header />
    @endif

    <div class="px-2 pb-2">
        {{ $slot }}
    </div>
</flux:main>

{{-- Toaster hub for livewire-toaster package --}}
<x-toaster-hub />

@fluxScripts
</body>
</html>
