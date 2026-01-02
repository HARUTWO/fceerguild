<x-layouts.app :title="__('Volunteer Attendance')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        @livewire('attendance.volunteers')
        
        {{-- Modals --}}
        @livewire('attendance.export-modal')
        @livewire('attendance.review-season-modal')
    </div>
</x-layouts.app>
